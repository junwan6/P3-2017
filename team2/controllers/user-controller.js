/******************************************************************************
  user-controller.js

This module handles web requests related to user accounts and authentication.
******************************************************************************/

var fs = require('fs');
var nodemailer = require('nodemailer');
var usersModel = require('../models/users');

// Load app server configuration
var appConfig = JSON.parse(fs.readFileSync(__dirname + '/../config/app-config.json', 'utf8'));
// Load mail server configuration
var mailConfig = JSON.parse(fs.readFileSync(__dirname + '/../config/mail-config.json', 'utf8'));

module.exports.handleLogin = function(req, res, next) {
    if (!req.body.remember_me) { return next(); }

        usersModel.issueRememberMeToken(req.user, function(err, token) {
            if (err) { return next(err); }
            res.cookie('remember_me', token, { path: '/', httpOnly: true, maxAge: 604800000 });
            return next();
        });
}

module.exports.handleLogout = function(req, res) {
    usersModel.clearRememberMeToken(req.user, function() {
        res.clearCookie('remember_me');
        req.logout();
        res.redirect('/');
    });
}

module.exports.handleRecoverAccount = function (req, res) {
    res.render('recoverAccount.html', {});
}

module.exports.handlePasswordReset = function (req, res) {
    if (req.body.email === undefined) {
        res.writeHead(400);
        res.end('Client error');
        return;
    }

    // Verify that a user exists with the given email
    usersModel.findByEmail(req.body.email, function (err, user) {
        if (err) {
            res.writeHead(400);
            res.end('Client error');
            return;
        }

        usersModel.issuePasswordResetCode(user.id, function (err, code) {

            if (err) {
                res.writeHead(500);
                res.end('Server error');
                return;
            }

            var emailTemplateData = new Object();
            emailTemplateData.firstName = user.firstName;
            emailTemplateData.lastName = user.lastName;
            // This URL hardcodes the transport protocol and routing information.
            // If (and we should) we migrate to HTTPS, we will need to change this URL.
            if (appConfig.portRequired) {
                emailTemplateData.passwordResetLink = "http://" + appConfig.hostname + ":" + appConfig.port + "/new-password?code=" + code;
            }
            else {
                emailTemplateData.passwordResetLink = "http://" + appConfig.hostname + "/new-password?code=" + code;
            }

            // Use the template engine to render the email body. The callback
            // allows us to direct the template output to an email.
            res.render('passwordResetEmailBody', emailTemplateData, function (err, output) {
                if (err) {
                    res.writeHead(500);
                    res.end("Server error");
                    return;
                }

                var transporter = nodemailer.createTransport(mailConfig.nodemailerConfig);

                transporter.sendMail({
                    from: 'Passionate People Project <' + mailConfig.address + '>',
                    to: user.firstName + ' ' + user.lastName + '<' + user.email + '>',
                    subject: 'Reset your password',
                    html: output
                }, function (err) {
                    if (err) {
                        console.log(err);
                        res.writeHead(500);
                        res.end('Server error');
                        return;
                    }

                    var pageTemplateData = new Object();
                    pageTemplateData.email = user.email;

                    res.render('passwordReset.html', pageTemplateData);
                });  
            });
        });
    });
};

module.exports.handleNewPassword = function (req, res) {
    if (!('code' in req.query)) {
        res.writeHead(400);
        res.end('Client error');
        return;
    }
    
    usersModel.lookupPasswordResetCode(req.query.code, function (err, row) {
        if (err) {
            res.writeHead(500);
            res.end('Server error');
            return;
        }

        var templateData = new Object();
        templateData.code = req.query.code;
        templateData.badCode = false;
        templateData.expiredCode = false;
        
        if (row === null) {
            // Password reset code never used
            templateData.badCode = true;
        }
        else if (row.expires.getTime() < Date.now()) {
            // Password reset expired
            templateData.expiredCode = true;
        }

        res.render('newPassword.html', templateData);
    });
};

module.exports.handleSetPassword = function(req, res) {
    if (!('code' in req.body) ||
        !('password' in req.body) ||
        !('verifypassword' in req.body)) {
        // Missing query parameter
        res.writeHead(400);
        res.end('Client error');
        return;
    }

    if (req.body.password != req.body.verifypassword) {
        // Bad password
        res.writeHead(400);
        res.end('Client error');
        return;
    }

    usersModel.lookupPasswordResetCode(req.body.code, function(err, row) {
        if (err) {
            res.writeHead(400);
            res.end('Client error');
            return;
        }

        // Verify the code again, just to be safe
        if (row === null) {
            // Password reset code never used
            res.render('newPassword.html', {badCode: true});
            return;
        }
        else if (row.expires.getTime() < Date.now()) {
            // Password reset expired
            res.render('newPassword.html', {expiredCode: true});
            return;
        }

        usersModel.setPassword(row.id, req.body.password, function(err) {
            if (err) {
                res.writeHead(500);
                res.end('Server error');
                return;
            }

            res.render('setPassword.html', {});
            return;
        });
    });
};
