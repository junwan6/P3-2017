/******************************************************************************
  main.js

This is the entry-point to the program and is the file that should be executed
using Node.js. The purpose of this module is to set up the application and
dispatch requests to other controller modules.
******************************************************************************/

var fs = require('fs');
var express = require('express');
var app = express();
var hbs = require ('hbs');
var passport = require('passport')
var LocalStrategy = require('passport-local').Strategy;
var FacebookStrategy = require('passport-facebook').Strategy;
var LinkedInStrategy = require('passport-linkedin').Strategy;
var session = require('express-session');
var bodyParser = require('body-parser');
var cookieParser = require('cookie-parser');
var flash = require('connect-flash');
var RememberMeStrategy = require('passport-remember-me').Strategy;



// Use Handlebars as the templating engine, and make it the default engine for
// html and hbs files
app.set('view engine', 'hbs');
app.engine('html', require('hbs').__express);
app.engine('hbs', require('hbs').__express);
// Register partials
hbs.registerPartials(__dirname + '/views/partials');

// Serve static files
app.use(express.static('public'));

// Cookie Parser
app.use(cookieParser());

// Body Parser
app.use(bodyParser.urlencoded({extended: true}));

// Sessions
app.use(session({secret: "replacethis",saveUninitialized: true,resave: true}));

// Passport
app.use(passport.initialize());
app.use(passport.session());
app.use(passport.authenticate('remember-me'));

// Connect Flash
app.use(flash());

// Load the app server configuration
var appConfig = JSON.parse(fs.readFileSync(__dirname + '/config/app-config.json', 'utf8'));


// Set up routing
app.get('/', function(req, res) {
    require('./controllers/temp-controller').handleHomePage(req, res);
});

app.get('/loginAttempt', function(req, res) {
    req.flash('loginAttempt', 'yes');
    res.redirect('/');
});

app.get('/worldOfWork', function(req, res) {
    require('./controllers/temp-controller').handleWorldOfWorkPage(req, res);
});

app.get('/profile', function(req, res) {
    require('./controllers/temp-controller').handleProfilePage(req, res);
});

app.get('/salary', function(req, res) {
    require('./controllers/occupation-controller').handleSalaryPage(req, res);
});

app.post('/signup', passport.authenticate('local-signup', { successRedirect: '/profile', failureRedirect: '/loginAttempt', failureFlash: 'signUpAttempt' }));

app.post('/login', passport.authenticate('local-login', { failureRedirect: '/loginAttempt', failureFlash: 'loginAttempt' }),
    function(req, res, next) {
        require('./controllers/user-controller').handleLogin(req, res, next);
    },
    function(req, res) {
        res.redirect('/profile');
    });

app.post('/logout', function(req, res){
  	require('./controllers/user-controller').handleLogout(req, res);
});

app.get('/auth/facebook', passport.authenticate('facebook', { scope : 'email' }));

app.get('/auth/facebook/callback',
        passport.authenticate('facebook', {
            successRedirect : '/profile',
            failureRedirect : '/'
        }));

app.get('/auth/linkedin', passport.authenticate('linkedin', { scope: ['r_basicprofile', 'r_emailaddress'] }));

app.get('/auth/linkedin/callback',
        passport.authenticate('linkedin', {
            successRedirect : '/profile',
            failureRedirect : '/'
        }));
app.get('/career/filters*', function(req, res) {
    var salary = req.query['salary'];
    var edu = req.query['education'];
    var soc =  req.query['soc'];
    //if (salary && edu)
     //   console.log('soc: '+soc +'salary: '+salary + ' education: ' + edu);
    require('./controllers/algorithm-controller').handleFilters(req, res, salary, edu);
});

app.get('/career/:occupation/video', function(req, res) {
    // var salary = req.query['salary'];
    // var edu = req.query['education']; 
    // if (salary && edu)
    //     console.log('salary: '+salary + ' education: ' + edu);

    //require('./controllers/occupation-controller').handleAlgInput(req, res);
    //require('./models/videoLogic_integ').handleAlgInput(req, res);
    require('./controllers/occupation-controller').handleVideoPage(req, res);
});

app.get('/career/:occupation/salary', function(req, res) {
    require('./controllers/occupation-controller').handleSalaryPage(req, res);
});

app.get('/career/:occupation/education', function(req, res) {
    require('./controllers/occupation-controller').handleEducationPage(req, res);
});

app.get('/career/:occupation/skills', function(req, res) {
    require('./controllers/occupation-controller').handleSkillsPage(req, res);
});

app.get('/career/:occupation/outlook', function(req, res) {
    require('./controllers/occupation-controller').handleCareerOutlookPage(req, res);
});

app.get('/career/:occupation/world-of-work', function(req, res) {
    require('./controllers/occupation-controller').handleWorldOfWorkPage(req, res);
});

app.get('/career/random', function(req, res) {
    require('./controllers/occupation-controller').handleRandomCareer(req, res);
});

app.get('/career/:occupation/vidup', function(req, res) {
	console.log("it's up");
	require('./controllers/algorithm-controller').handleVideoLike(req, res);
});

app.get('/career/:occupation/vidmid', function(req, res) {
	console.log("it's mid");
	require('./controllers/algorithm-controller').handleVideoNeutral(req, res);
});

app.get('/career/:occupation/viddown', function(req, res) {
	console.log("it's down");
	require('./controllers/algorithm-controller').handleVideoDislike(req, res);
});

app.get('/career/nextcareer', function(req, res) {
	require('./controllers/algorithm-controller').handleNextCareer(req, res);
});

app.get('/browse', function(req, res) {
    require('./controllers/browse-controller').handleBrowsePage(req, res);
});

// search requests should use a REST API; query string is in parameter q
app.get('/search', function(req, res) {
    require('./controllers/browse-controller').handleSearchRequest(req, res);
});

app.get('/help', function(req, res) {
    res.writeHead(501);
    res.end('501 - Not implemented');
});

app.get('/recover-account', function(req, res) {
    require('./controllers/user-controller').handleRecoverAccount(req, res);
});

app.post('/password-reset', function(req, res) {
    require('./controllers/user-controller').handlePasswordReset(req, res);
});

app.get('/new-password', function(req, res) {
    require('./controllers/user-controller').handleNewPassword(req, res);
});

app.post('/set-password', function(req, res) {
    require('./controllers/user-controller').handleSetPassword(req, res);
});

app.get('/donors', function(req, res) {
    require('./controllers/temp-controller').handleDonorPage(req, res);
});

// 404 routing; always keep this as the last route
app.use(function(req, res) {
    require('./controllers/temp-controller').handleUnknownRoute(req, res);
});

require('./controllers/passport-controller.js')(passport, LocalStrategy, FacebookStrategy, LinkedInStrategy, RememberMeStrategy);




// Run server
app.listen(appConfig.port);
