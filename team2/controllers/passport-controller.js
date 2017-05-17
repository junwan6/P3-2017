/******************************************************************************
  passport-controller.js

This module handles authentication logic via the Passport interface.
******************************************************************************/

var usersModel = require('../models/users');

module.exports = function(passport, LocalStrategy, FacebookStrategy, LinkedInStrategy, RememberMeStrategy) {

    passport.serializeUser(function(user, done) {
        done(null, user.id);
    });

    passport.deserializeUser(function(id, done) {
        usersModel.findById(id, function(err, user) {
            done(err, user);
        });
    });

    passport.use('local-signup', new LocalStrategy({
        usernameField : 'email',
        passwordField : 'password',
        passReqToCallback: true
    },
    function(req, email, password, done) {
        usersModel.signUp(req, email, password, done);
    }
    ));

    passport.use('local-login', new LocalStrategy({
        usernameField : 'email',
        passwordField : 'password',
        passReqToCallback: true
    },
    function(req, email, password, done) {
        usersModel.login(req, email, password, done);
    }
    ));

    passport.use(new FacebookStrategy({
        clientID: '170986223302968',
        clientSecret: 'c7c6105b74c397899eda935228e6a4e0',
        callbackURL: "http://localhost:8080/auth/facebook/callback",
        profileFields: ['id', 'emails', 'name']
      },
      function(accessToken, refreshToken, profile, done) {
        usersModel.fbAuthenticate(accessToken, refreshToken, profile, done);
      }
    ));

    passport.use(new LinkedInStrategy({
        consumerKey: '75cm081uabz532',
        consumerSecret: 'TsJ7A5aX3JczXzgr',
        callbackURL: "http://localhost:8080/auth/linkedin/callback",
        profileFields: ['id', 'first-name', 'last-name', 'email-address']
      },
      function(token, tokenSecret, profile, done) {
        usersModel.liAuthenticate(token, tokenSecret, profile, done);
      }
    ));

    passport.use(new RememberMeStrategy(
      function(token, done) {
        usersModel.consumeRememberMeToken(token, function(err, uid) {
          if (err) { return done(err); }
          if (!uid) { return done(null, false); }
          
          usersModel.findById(uid, function(err, user) {
            if (err) { return done(err); }
            if (!user) { return done(null, false); }
            return done(null, user);
          });
        });
      },
      usersModel.issueRememberMeToken
    ));

    

};




