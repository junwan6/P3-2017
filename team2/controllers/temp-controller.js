/******************************************************************************
  temp-controller.js

This module handles web requests for miscellaneous pages. As you might suspect,
this module really shouldn't exist, and its functionalities should be
refactored into more logically cohesive controllers.
******************************************************************************/

var interfaceRatings = require('../models/interfaceRatings.js');

module.exports.handleHomePage = function(req, res) {

    var templateData = new Object();

    if (req.user) {
        templateData.loggedIn = true;
        templateData.firstName = req.user.firstName;
    } else {
        templateData.loggedIn = false;
    }

    if (req.flash('loginAttempt') == 'yes') {

        if (req.flash('error') == 'signUpAttempt') {
            templateData.signUpAttempt = true;
            templateData.loginAttempt = false;
        } else {
            templateData.loginAttempt = true;
            templateData.signUpAttempt = false;
        }

        if (req.flash('success') == 'yes') {

            templateData.success = true;

        } else {

            templateData.success = false;

            if (req.flash('signup') == 'yes') {

                if (req.flash('emptyField') == 'yes') {

                    templateData.reason = 'All fields are required.';

                } else if (req.flash('emailTaken') == 'yes') {

                    templateData.reason = 'That email address is already taken.';

                } else {

                    req.flash('passwordConflict'); //clear it?

                    templateData.reason = 'Passwords do not match.';

                }
                
            } else if (req.flash('login') == 'yes') {

                if (req.flash('incorrectEmail') == 'yes') {

                    templateData.reason = 'Incorrect email.';

                } else {
                    req.flash('incorrectPassword'); //clear it?

                    templateData.reason = 'Incorrect password.';

                }

            } else {
                

                templateData.reason = 'All fields are required.';

            }
        }

    } else {

        templateData.signUpAttempt = false;
        templateData.loginAttempt = false;
        templateData.success = true;

    }

    //clear all flashes
    req.flash('loginAttempt');
    req.flash('error');
    req.flash('success');
    req.flash('signup');
    req.flash('emptyField');
    req.flash('emailTaken');
    req.flash('passwordConflict');
    req.flash('login');
    req.flash('incorrectEmail');
    req.flash('incorrectPassword');
        

    res.render('index.html', templateData);


    
}

module.exports.handleWorldOfWorkPage = function(req, res) {

    var templateData = new Object();

    if (req.user) {
        templateData.loggedIn = true;
    } else {
        templateData.loggedIn = false;
    }
    res.render('worldOfWork.html', templateData);
}

module.exports.handleProfilePage = function(req, res) {

    if (!req.user) {
        res.writeHead(400);
        res.end('Please log in first');
        return;
    }

    interfaceRatings.getViewHistoryForUser(
        req.user.id,
        function (ratings) {
            var templateData = {};
            templateData.loggedIn = true;

            // Sort the ratings into careers they liked, didn't like, and were neutral to
            templateData.likedCareers = [];
            templateData.dislikedCareers = [];
            templateData.neutralCareers = [];

            for (var i = 0; i < ratings.length; i++) {
                // Add the WoW position to the rows
                var pos = getWoWPosition(ratings[i]);
                ratings[i].x = pos.x;
                ratings[i].y = pos.y;

                switch (ratings[i].rating) {
                case -1:
                    templateData.dislikedCareers.push(ratings[i]);
                    break;
                case 0:
                    templateData.neutralCareers.push(ratings[i]);
                    break;
                case 1:
                    templateData.likedCareers.push(ratings[i]);
                    break;
                default:
                    // Do nothing
                }
            }
            
            res.render('profile.html', templateData);
        },
        function (err) {
            console.log(err);
            res.writeHead(500);
            res.end('Server error');
        }
    );
}

module.exports.handleSalaryPage = function(req, res) {

    var templateData = new Object();

    if (req.user) {
        templateData.loggedIn = true;
    } else {
        templateData.loggedIn = false;
    }
    res.render('salary.html', templateData);
}

module.exports.handleDonorPage = function(req, res) {

    var templateData = new Object();

    if (req.user) {
        templateData.loggedIn = true;
    }
    else {
        templateData.loggedIn = false;
    }

    res.render('donors.html', templateData);
}

module.exports.handleUnknownRoute = function(req, res) {
    
    var templateData = new Object();

    if (req.user) {
        templateData.loggedIn = true;
    }
    else {
        templateData.loggedIn = false;
    }

    res.render('404.html', templateData);
}

function getWoWPosition(interests) {
    if (interests.realistic === null) {
        // If there is no interests data, these values may be null
        return {x: 0, y: 0};
    }
    var realistic = interests.realistic;
    var investigative = interests.investigative;
    var artistic = interests.artistic;
    var social = interests.social;
    var enterprising = interests.enterprising;
    var conventional = interests.conventional;
    var interestArray = [realistic,
                         investigative,
                         artistic,
                         social,
                         enterprising,
                         conventional]; 
    var coordArray = [];

    Math.radians = function(degrees) {
	return degrees * Math.PI / 180;
    };

    for (var i = 0; i < interestArray.length; i++) {
	if (interestArray[i] > 0.5) {
	    if (i == 0) {	// realistic
	    	var x = realistic*Math.cos(Math.radians(-90));
    		var y = realistic*Math.sin(Math.radians(90));
    		coordArray.push([x,y]);
	    }
	    if (i == 1) {	// investigative
	    	var x = investigative*Math.cos(Math.radians(-60));
    		var y = investigative*Math.sin(Math.radians(60));
    		coordArray.push([x,y]);
	    }
	    if (i == 2) {	// artistic
	    	var x = artistic*Math.cos(Math.radians(240));
    		var y = artistic*Math.sin(Math.radians(-240));
    		coordArray.push([x,y]);    		
	    }
	    if (i == 3) {	// social
	    	var x = social*Math.cos(Math.radians(180));
    		var y = social*Math.sin(Math.radians(-180));
    		coordArray.push([x,y]);			
	    }
	    if (i == 4) {	// enterprising 
	    	var x = enterprising*Math.cos(Math.radians(120));
    		var y = enterprising*Math.sin(Math.radians(-120));
    		coordArray.push([x,y]);				
	    }
	    if (i == 5) {	// conventional 
	    	var x = conventional*Math.cos(Math.radians(60));
    		var y = conventional*Math.sin(Math.radians(-60));
    		coordArray.push([x,y]);	
	    }
	}
    }

    var averageX = 0;
    var averageY = 0;

    if (coordArray.length == 0) {
	var specificInterest = indexOfMax(interestArray);
    	if (specificInterest == 0) {	// realistic
    	    averageX = realistic*Math.cos(Math.radians(-90));
	    averageY = realistic*Math.sin(Math.radians(90));
    	}
    	if (specificInterest == 1) {	// investigative
    	    averageX = investigative*Math.cos(Math.radians(-60));
    	    averageY = investigative*Math.sin(Math.radians(60));
    	}
    	if (specificInterest == 2) {	// artistic
    	    averageX = artistic*Math.cos(Math.radians(240));
    	    averageY = artistic*Math.sin(Math.radians(-240));
    	}
    	if (specificInterest == 3) {	// social
    	    averageX = social*Math.cos(Math.radians(180));
    	    averageY = social*Math.sin(Math.radians(-180));
    	}
    	if (specificInterest == 4) {	// enterprising 
    	    averageX = enterprising*Math.cos(Math.radians(120));
    	    averageY = enterprising*Math.sin(Math.radians(-120));
    	}
    	if (specificInterest == 5) {	// conventional 
    	    averageX = conventional*Math.cos(Math.radians(60));
    	    averageY = conventional*Math.sin(Math.radians(-240));
    	}
    }
    else {
        for (var j = 0; j < coordArray.length; j++) {
	    averageX += coordArray[j][0];
	    averageY += coordArray[j][1];
        }

        averageX /= coordArray.length;
        averageY /= coordArray.length;
    }

    return {x: averageX, y: averageY}
}

function indexOfMax(arr) {
    if (arr.length === 0) {
	return -1;
    }

    var max = arr[0];
    var maxIndex = 0;

    for (var i = 1; i < arr.length; i++) {
	if (arr[i] > max) {
	    maxIndex = i;
	    max = arr[i];
	}
    }

    return maxIndex;
}
