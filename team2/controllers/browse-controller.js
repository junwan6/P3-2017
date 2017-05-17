/******************************************************************************
  browse-controller.js

This module handles requests related to browsing through careers.
******************************************************************************/

var occupationModel = require('../models/occupation');
var format = require('../util/format');

module.exports.handleBrowsePage = function (req, res) {

    var templateData = new Object();

    if (req.user) {
        templateData.loggedIn = true;
    } else {
        templateData.loggedIn = false;
    }
    res.render('browse.html', templateData);
}

module.exports.handleSearchRequest = function (req, res) {

    var templateData = new Object();

    if (req.user) {
        templateData.loggedIn = true;
    } else {
        templateData.loggedIn = false;
    }

    if ('q' in req.query) {
        templateData.query = req.query.q;

        occupationModel.searchOccupationNames(
            req.query.q,

            function (rows) {
                templateData.resultsEmpty = (rows.length == 0);

                templateData.results = new Array(rows.length);
                for (var i = 0; i < rows.length; i++) {
                    var occupation = rows[i];
                    var result = new Object();

                    result.soc = occupation.soc;
                    result.title = occupation.title;

                    result.wageTypeIsAnnual = (occupation.wageType == 'annual');
                    var wageString = '$' + format.formatWithThousandSeparators(occupation.medianWage);
                    // TECH DEBT: JS doesn't have very good support for named constants but we should find a way around that
                    if (occupation.medianAnnualWageOutOfRange == 1) {
                        wageString = '>=' + wageString;
                    }
                    result.averageWage = wageString;

                    var educationDecoder = { 'none' : 'No education required',
                                             'high school' : 'High school education',
                                             'some college' : 'Some college',
                                             'postsecondary nondegree' : 'Postsecondary nondegree award',
                                             'associate' : "Associate's degree",
                                             'bachelor' : "Bachelor's degree",
                                             'master' : "Master's degree",
                                             'doctoral or professional' : "Doctoral or Professional degree" };
                    // TECH DEBT: Robustness issues
                    var educationString = educationDecoder[occupation.educationRequired];
                    result.educationRequired = educationString;

                    result.careerGrowth = format.formatPercentage(occupation.careerGrowth);

                    templateData.results.push(result);
                }
                
                res.render('search.html', templateData);
            },

            function (err) {
                res.writeHead(500);
                res.end('500 - Server error');
            });
    }
    else {
        res.writeHead(400);
        res.end('400 - Client error (no query string)');
    }
}
