/******************************************************************************
  users.js

This module handles queries for any data related to user account data.
******************************************************************************/

var crypto = require('crypto');
var mysql = require('mysql');
var fs = require('fs');
var randomMethods = require('../util/random.js');
var randomstring = require('randomstring');

// Load the database configuration
var config = JSON.parse(fs.readFileSync(__dirname + '/../config/db-config.json', 'utf8'));

// Parameters for PBKDF2. hashSize and saltSize are in bytes.
var hashConfig = { hashSize: 32,
                   saltSize: 16,
                   iterations: 10000 }

module.exports.findById = function(id, next) {
    var connection = mysql.createConnection(config);
    connection.connect();

    connection.query("SELECT * FROM Users WHERE id = '" + id + "';", function(err, rows, fields) {
        if (err === null && rows.length == 1) {
            next(null, rows[0]);
        } else {
            next(new Error('User ' + id + ' does not exist'));
        }
    });

    connection.end();

}

module.exports.findByEmail = function(email, next) {
    var connection = mysql.createConnection(config);
    connection.connect();

    connection.query("SELECT * FROM Users WHERE email = ?", [email], function(err, rows, fields) {
        if (err == null && rows.length == 1) {
            next(null, rows[0]);
        }
        else if (err != null) {
            next(err);
        }
        else {
            next(new Error('User with email not found'));
        }
    });

    connection.end();
}

module.exports.consumeRememberMeToken = function(token, done) {
    var connection = mysql.createConnection(config);
    connection.connect();

    connection.query("SELECT * FROM RememberMeTokens WHERE token = '" + token + "';", function(err, rows, fields) {
        if (err === null && rows.length == 1) {

            connection.query("DELETE FROM RememberMeTokens WHERE token = '" + token + "';", function(err2, rows2, fields2) {
                if (err2 === null) {

                    return done(null, rows[0].id);
                    connection.end();



                } else {
                    return done(err2);
                    connection.end();

                }
            });

        } else {
            return done(new Error('Invalid token'));
            connection.end();
        }
    });
}

module.exports.clearRememberMeToken = function(user, next) {
    var connection = mysql.createConnection(config);
    connection.connect();

    connection.query("DELETE FROM RememberMeTokens WHERE id = '" + user.id + "';", function(err, rows, fields) {
        if (err === null) {
            next();
        } else {
            res.writeHead(500);
            res.end('Server error');
        }
    });

    connection.end();
}


//TODO: prevent duplicates?
module.exports.saveRememberMeToken = function(token, id, done) {

    var connection = mysql.createConnection(config);
    connection.connect();

    connection.query("INSERT INTO RememberMeTokens VALUES('" + token + "', '" + id + "');", function(err, rows, fields) {
        if (err === null) {
            return done();
        } else {
            return done(err);
        }
    });

}

module.exports.issueRememberMeToken = function (user, done) {
  var token = randomMethods.randomString(64);
  module.exports.saveRememberMeToken(token, user.id, function(err) {
    if (err) { return done(err); }
    return done(null, token);
  });
}

module.exports.signUp = function(req, email, password, done) {

    req.flash('signup', 'yes');

    if (req.body.firstname == '' || req.body.lastname == '' || req.body.verifypassword == '') {
        req.flash('emptyField', 'yes');
        return done(null, false);
    }

    var connection = mysql.createConnection(config);
    connection.connect();

    // First check to see if the email is already taken
    connection.query("SELECT * FROM Users WHERE email = ?", [email], function(err, rows) {
        if (err) {
            connection.end();
            return done(err); //database error
        }
        if (rows.length) {
            connection.end();
            req.flash('emailTaken', 'yes');
            return done(null, false); //email taken
        }
        if (password != req.body.verifypassword) {
            connection.end();
            req.flash('passwordConflict', 'yes');
            return done(null, false);
        }

        // Generate a random salt
        hashPassword(password, function (err, hash, salt) {
            if (err) {
                connection.end();
                /// TODO: Add some user feedback for this error
                return done(null, false);
            }

            connection.beginTransaction(function (err) {
                
                if (err) {
                    connection.end();
                    /// TODO: Add some user feedback for this error
                    return done(null, false);
                }
                
                // Insert into Users...
                connection.query("INSERT INTO Users(firstName, lastName, email) VALUES(?, ?, ?);", [req.body.firstName, req.body.lastName, email], function (err, userInsertResult) {
                    
                    if (err) {
                        connection.end();
                        /// TODO: Add some user feedback for this error
                        return done(null, false);
                    }
                    
                    // ...then into UserPasswords...
                    connection.query("INSERT INTO UserPasswords(hash, salt, id) VALUES(?, ?, LAST_INSERT_ID());", [hash, salt], function (err, userPasswordInsertResult) {
                        if (err) {
                            connection.end();
                            /// TODO: Add some user feedback for this error
                            return done(null, false);
                        }
                        
                        // ...finally, commit the transaction
                        connection.commit(function (err) {
                            connection.end();
                            
                            if (err) {
                                /// TODO: Add some user feedback for this error
                                return done(null, false);
                            }
                            
                            // Return the new user object
                            var newUser = new Object();
                            newUser.firstName = req.body.firstName;
                            newUser.lastName = req.body.lastName;
                            newUser.email = email;
                            newUser.id = userInsertResult.insertId;
                            
                            return done(null, newUser);
                        });
                    });
                });
            });
        });
    });
}


module.exports.login = function(req, email, password, done) {

    req.flash('login', 'yes');

    var connection = mysql.createConnection(config);
    connection.connect();

    // Look up the user associated with the email address
    connection.query("SELECT * FROM Users WHERE email = ?", [email], function(err, userRows) {
        if (err) {
            connection.end();
            return done(err); //database error
        }
        if (!userRows.length) {
            req.flash('incorrectEmail', 'yes');
            connection.end();
            return done(null, false); //no matching email
        }

        // Look up the hashed password and salt for the user
        connection.query("SELECT * FROM UserPasswords WHERE id = ?", [userRows[0].id], function (err, userPasswordRows) {
            if (err) {
                connection.end();
                return done(err);
            }
            if (!userPasswordRows.length) {
                // Not a user that logs in with a password
                connection.end();
                return done(null, false);
            }
            
            // Hash the password that the user is trying to authenticate with
            crypto.pbkdf2(password, userPasswordRows[0].salt, hashConfig.iterations, hashConfig.hashSize, function (err, proposedHash) {
                if (err) {
                    connection.end();
                    return done(err);
                }

                if (proposedHash.toString('hex') !== userPasswordRows[0].hash) {
                    // Wrong password
                    req.flash('incorrectPassword', 'yes');
                    return done(null, false);
                }

                req.flash('success', 'yes');

                // Authentication successful, return the user object
                return done(null, userRows[0]);
            });
        });
    });
}

module.exports.fbAuthenticate = function(accessToken, refreshToken, profile, done) {

    var connection = mysql.createConnection(config);
    connection.connect();

    connection.query("SELECT * FROM FBUsers WHERE fbId = ?", [profile.id], function(err, rows) {
        if (err)
            return done(err);
        if (rows.length) {
            // Join with the Users table to get the object we need to return
            connection.query("SELECT * FROM Users WHERE id = ?", [rows[0].userId], function(err, rows) {
                if (err) {
                    return done(err);
                }
                if (rows.length == 0) {
                    return done(err);
                }

                return done(null, rows[0]);
            });
        } else {
            // Create a new user. Perform this in a transaction because we need to update two tables
            connection.beginTransaction(function (err) {

                if (err) {
                    return done(err);
                }

                // Insert it into Users...
                connection.query("INSERT INTO Users(firstName, lastName, email) VALUES(?, ?, ?);", [profile.name.givenName, profile.name.familyName, profile.emails[0].value], function (err, userInsertResult) {
                    if (err) {
                        return done(err);
                    }

                    // Now insert into FBUsers...
                    connection.query("INSERT INTO FBUsers(fbId, userId) VALUES(?, LAST_INSERT_ID());", [profile.id], function(err, fbInsertResult) {
                        if (err) {
                            return done(err);
                        }

                        // Finally commit
                        connection.commit(function (err) {
                            if (err) {
                                return done(err);
                            }

                            // Return the user object
                            var newUser = new Object();
                            newUser.firstname = profile.name.givenName;
                            newUser.lastname = profile.name.familyName;
                            newUser.email = profile.emails[0].value;
                            newUser.id = userInsertResult.insertId;

                            return done(null, newUser);
                        });
                    });
                });
            });
        }
    });
}

module.exports.liAuthenticate = function(token, tokenSecret, profile, done) {

    var connection = mysql.createConnection(config);
    connection.connect();

    connection.query("SELECT * FROM LIUsers WHERE liId = ?", profile.id, function(err, rows) {
        if (err)
            return done(err);
        if (rows.length) {
            // Join with the Users table to get the object we need to return
            connection.query("SELECT * FROM Users WHERE id = ?", [rows[0].userId], function(err, rows) {
                if (err) {
                    return done(err);
                }
                if (rows.length == 0) {
                    return done(err);
                }

                return done(null, rows[0]);
            });
        } else {
            // Create a new user. Perform this in a transaction because we need to update two tables
            connection.beginTransaction(function (err) {
                if (err) {
                    return done(err);
                }

                // Insert into Users...
                connection.query("INSERT INTO Users(firstName, lastName, email) VALUES(?, ?, ?);", [profile.name.givenName, profile.name.familyName, profile.emails[0].value], function (err, userInsertResult) {

                    if (err) {
                        return done(err);
                    }
                    
                    // Insert into LIUsers...
                    connection.query("INSERT INTO LIUsers(liId, userId) VALUES(?, LAST_INSERT_ID());", [profile.id], function (err, liInsertResult) {

                        if (err) {
                            return done(err);
                        }

                        // Finally commit the transaction
                        connection.commit(function (err) {
                            
                            if (err) {
                                return done(err);
                            }

                            // Return the user object
                            var newUser = new Object();
                            newUser.firstname = profile.name.givenName;
                            newUser.lastname = profile.name.familyName;
                            newUser.email = profile.emails[0].value;
                            newUser.id = userInsertResult.insertId;

                            return done(null, newUser);
                        });
                    });
                });
            });
        }
    });
}

module.exports.issuePasswordResetCode = function(id, next) {
    var connection = mysql.createConnection(config);
    connection.connect();

    // Determine if there's already a pending password reset for this user, if
    // so make sure to update the table rather than insert
    connection.query("SELECT * FROM PendingPasswordReset WHERE id = ?;", [id], function (err, rows, fields) {
        if (err) {
            connection.end();
            next(err);
            return;
        }

        var passwordResetExists = (rows.length != 0);
        // TECH DEBT: We don't handle collisions in the code. The probability
        // of a collision is very low for the expected user count, so currently
        // it's not implemented.
        var code = randomstring.generate(24);
        // Give a 30 minute expiration time on the password reset code
        var expires = new Date(Date.now() + 1000 * 60 * 30);

        if (passwordResetExists) {
            connection.query("UPDATE PendingPasswordReset SET code = ?, expires = ? WHERE id = ?", [code, expires, id], function (err, rows, fields) {
                connection.end();

                if (err) {
                    next(err);
                    return;
                }
                next(null, code);
            });
        }
        else {
            connection.query("INSERT INTO PendingPasswordReset(id, code, expires) VALUES(?, ?, ?)", [id, code, expires], function (err, rows, fields) {
                connection.end();

                if (err) {
                    next(err);
                    return;
                }
                next(null, code);
            });
        }
    });
};

module.exports.lookupPasswordResetCode = function(code, next) {
    var connection = mysql.createConnection(config);
    connection.connect();

    connection.query("SELECT * FROM PendingPasswordReset WHERE code = ?", [code], function(err, rows, fields) {
        if (err) {
            next(err);
            return;
        }

        if (rows.length == 0) {
            next(null, null);
            return;
        }

        next(null, rows[0]);
        return;
    });

    connection.end();
};

module.exports.setPassword = function(id, password, next) {
    hashPassword(password, function(err, hash, salt) {
        if (err) {
            return next(err);
        }

        var connection = mysql.createConnection(config);
        connection.connect();

        // Update the password and simultaneously delete the pending password reset row
        connection.beginTransaction(function(err) {
            if (err) {
                connection.end();
                return next(err);
            }

            // First update the password...
            connection.query("UPDATE UserPasswords SET hash = ?, salt = ? WHERE id = ?", [hash, salt, id], function(err, rows, fields) {
                if (err) {
                    connection.end();
                    return next(err);
                }

                // ...then delete the password reset row
                connection.query("DELETE FROM PendingPasswordReset WHERE id = ?", [id], function(err, rows, fields) {
                    if (err) {
                        connection.end();
                        return next(err);
                    }

                    connection.commit(function(err) {
                        connection.end();

                        return next(err);
                    });
                });
            });
        });
    });
};

// next is a function taking three parameters: err, hash, salt
function hashPassword(password, next) {
    // Generate a random salt
    crypto.randomBytes(hashConfig.saltSize, function (err, bytes) {

        if (err) {
            return next(err);
        }

        var salt = bytes.toString('hex');

        // Generate a hash from the password and salt
        crypto.pbkdf2(password, salt, hashConfig.iterations, hashConfig.hashSize, function (err, bytes) {

            if (err) {
                return next(err);
            }

            var hash = bytes.toString('hex');
            return next(null, hash, salt);
        });
    });
}
