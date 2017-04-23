/******************************************************************************
  occupation-controller.js

This module handles requests for web pages that describe occupations.
******************************************************************************/

var occupationModel = require('../models/occupation');
var format = require('../util/format');

// module.exports.handleAlgInput = function(req, res) {
//     occupationModel.filter(req.query['salary'], req.query['education'],
//         function (socs) {
//             console.log(socs);
//         },
//         function (err) {
//             res.writeHead(500);
//             res.end('Server error');
//          });

// }

module.exports.handleVideoPage = function(req, res) {
    occupationModel.find(req.params.occupation,
       function (occupation) {
           var templateData = new Object();
           setupIconTemplateData(templateData, occupation);

           templateData.occupationTitle = occupation.title;

           if (req.user) {
            templateData.loggedIn = true;
        } else {
            templateData.loggedIn = false;
        }

        res.render('video.html', templateData);
    },
    function (err) {
       res.writeHead(500);
       res.end('Server error');
   });
}

module.exports.handleWorldOfWorkPage = function(req, res) {
    occupationModel.find(req.params.occupation,
       function (occupation) {
        occupationModel.getInterests(req.params.occupation,
            function (interests) {
            var templateData = new Object();

            if (req.user) {
                templateData.loggedIn = true;
            } else {
                templateData.loggedIn = false;
            }

            if (interests == null) {
                templateData.noData = true;
                return res.render('worldOfWork.html', templateData);
            }

            // add the new WoW stuff here
            setupIconTemplateData(templateData, occupation);

            templateData.occupationTitle = occupation.title;
            templateData.soc = occupation.soc;
            templateData.realistic = interests.realistic;
            templateData.investigative = interests.investigative;
            templateData.artistic = interests.artistic;
            templateData.social = interests.social;
            templateData.enterprising = interests.enterprising;
            templateData.conventional = interests.conventional;

            res.render('worldOfWork.html', templateData);
        },
        
        function (err) {
            res.writeHead(500);
            res.end('Server error');
        });
    },
    function (err) {
       res.writeHead(500);
       res.end('Server error');
   });
}

module.exports.handleCareerOutlookPage = function(req, res) {
    occupationModel.find(req.params.occupation,
       function (occupation) {
           var templateData = new Object();
           setupIconTemplateData(templateData, occupation);

           templateData.occupationTitle = occupation.title;

           var currentEmployment = parseFloat(occupation.currentEmployment) * 1000;
           templateData.currentEmployment = format.formatWithThousandSeparators(currentEmployment);

           var futureEmployment = parseFloat(occupation.futureEmployment) * 1000;
           templateData.futureEmployment = format.formatWithThousandSeparators(futureEmployment);

           var jobOpenings = parseFloat(occupation.jobOpenings) * 1000;
           templateData.jobOpenings = format.formatWithThousandSeparators(jobOpenings);

           if (req.user) {
            templateData.loggedIn = true;
        } else {
            templateData.loggedIn = false;
        }

        res.render('careerOutlook.html', templateData);
    },
    function (err) {
       res.writeHead(500);
       res.end('Server error');
   });
};

module.exports.handleSalaryPage = function(req, res) {
    occupationModel.find(req.params.occupation,
       function (occupation) {
           occupationModel.getStateData(req.params.occupation,
            function (stateOccupationData) {

               var templateData = new Object();
               setupIconTemplateData(templateData, occupation);

               templateData.occupationTitle = occupation.title;

               templateData.NATAvg = occupation.averageWage;

        // TECH DEBT: Not sure the if statements are explicitly necessary
        if (templateData.lowWageOutOfRange == 1) {
            templateData.NATLo = 187200;
        }
        else {
            templateData.NATLo = occupation.lowWage;
        }

        if (templateData.medianWageOutOfRange == 1) {
            templateData.NATMed = 187200;
        }
        else {
            templateData.NATMed = occupation.medianWage;
        }

        if (templateData.highWageOutOfRange == 1) {
            templateData.NATHi = 187200;
        } 
        else {
            templateData.NATHi = occupation.highWage;
        }

            // State specific code
            for (i = 0; i < stateOccupationData.length; i++) {
                var state = stateOccupationData[i].stateCode;

                if (stateOccupationData[i].averageWage == 0) {
                    templateData[state] = false;
                } else {
                    templateData[state] = true;
                    templateData[state + 'Avg'] = stateOccupationData[i].averageWage;
                    templateData[state + 'Lo'] = stateOccupationData[i].lowWage;
                    templateData[state + 'Med'] = stateOccupationData[i].medianWage;
                    templateData[state + 'Hi'] = stateOccupationData[i].highWage;
                }
            }
            
            if (req.user) {
                templateData.loggedIn = true;
            } else {
                templateData.loggedIn = false;
            }

            res.render('salary.html', templateData);

        },
        function (err) {
            res.writeHead(500);
            res.end('Server error');
        });

},
function (err) {
   res.writeHead(500);
   res.end('Server error');
});
}

module.exports.handleEducationPage = function(req, res) {
    occupationModel.find(req.params.occupation,
       function (occupation) {
           occupationModel.getStateData(req.params.occupation,
            function (stateOccupationData) {

               var templateData = new Object();
               setupIconTemplateData(templateData, occupation);

               templateData.occupationTitle = occupation.title;

               templateData['NATAvg'] = occupation.averageWage;
               templateData['NATLo'] = occupation.lowWage;
               templateData['NATMed'] = occupation.medianWage;
               templateData['NATHi'] = occupation.highWage;


               for (i = 0; i < stateOccupationData.length; i++) {
                var state = stateOccupationData[i].stateCode;

                if (stateOccupationData[i].averageWage == 0) {
                    templateData[state] = false;
                } else {
                    templateData[state] = true;
                    templateData[state + 'Avg'] = stateOccupationData[i].averageWage;
                    templateData[state + 'Lo'] = stateOccupationData[i].lowWage;
                    templateData[state + 'Med'] = stateOccupationData[i].medianWage;
                    templateData[state + 'Hi'] = stateOccupationData[i].highWage;
                }
            }
            

            var educationType = occupation.educationRequired;
            switch(educationType) {
                case "associate":
                templateData.typeOfSchool = "Undergraduate";
                templateData.typeOfDegree = "Associate's Degree";
                templateData.yearsInSchool = "2";
                templateData.yearsInUndergrad = 2;
                templateData.yearsInGrad = 0;
                break;
                case "bachelor":
                templateData.typeOfSchool = "Undergraduate";
                templateData.typeOfDegree = "Bachelor's Degree";
                templateData.yearsInSchool = "4";
                templateData.yearsInUndergrad = 4;
                templateData.yearsInGrad = 0;
                break;
                case "master":
                templateData.typeOfSchool = "Graduate";
                templateData.typeOfDegree = "Master's Degree";
                templateData.yearsInSchool = "6";
                templateData.yearsInUndergrad = 4;
                templateData.yearsInGrad = 2;

                templateData.gradSchool = true;
                break;
                case "doctoral or professional":
                templateData.typeOfSchool = "Graduate or Professional";
                templateData.typeOfDegree = "Doctorate or Professional Degree";
                templateData.yearsInSchool = "8";
                templateData.yearsInUndergrad = 4;
                templateData.yearsInGrad = 4;

                templateData.gradSchool = true;
                break;
                default:
                templateData.typeOfSchool = "N/A";
                templateData.typeOfDegree = "N/A";
                templateData.yearsInSchool = "N/A";
                templateData.yearsInUndergrad = 0;
                templateData.yearsInGrad = 0;
            }

            if (req.user) {
                templateData.loggedIn = true;
            } else {
                templateData.loggedIn = false;
            }

            res.render('education.html', templateData);

        },
        function (err) {
            res.writeHead(500);
            res.end('Server error');
        });

},
function (err) {
   res.writeHead(500);
   res.end('Server error');
});
}

module.exports.handleSkillsPage = function(req, res) {
    occupationModel.find(req.params.occupation,
       function (occupation) {


        var templateData = new Object();
        setupIconTemplateData(templateData, occupation);
        templateData.occupationTitle = occupation.title;

        if (req.user) {
            templateData.loggedIn = true;
        } else {
            templateData.loggedIn = false;
        }


        res.render('skills.html', templateData);


    },
    function (err) {
       res.writeHead(500);
       res.end('Server error');
   });
}

module.exports.handleRandomCareer = function (req, res) {
    // If both x and y are specified in the query string, then the request should
    // return a random SOC code in the region specified by the coordinates.
    if ('x' in req.query && 'y' in req.query) {
        // TECH DEBT: Robustness issues
        occupationModel.getRandomSOCInWOWRegion(
            req.query,
            function (soc) {
                res.redirect('/career/' + soc + '/video');
            },
            function (err) {
                res.writeHead(500);
                res.end('Server error');
            });
    }
    else {
        occupationModel.getRandomSOC(
            function (soc) {
                res.redirect('/career/' + soc + '/video');
            },
            function (err) {
                res.writeHead(500);
                res.end('Server error');
            });
    }
}

function setupIconTemplateData(dict, occupation) {
    dict.wageTypeIsAnnual = (occupation.wageType == 'annual');
    var wageString = '$' + format.formatWithThousandSeparators(occupation.averageWage);
    // TECH DEBT: JS doesn't have very good support for named constants but we should find a way around that
    if (occupation.averageWageOutOfRange == 1) {
        wageString = '>=' + wageString;
    }
    dict.averageWage = wageString;

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
    dict.educationRequired = educationString;

    dict.careerGrowth = format.formatPercentage(occupation.careerGrowth);

    if (occupation.skillsText) {
        skillsText = JSON.parse(occupation.skillsText);

        var skillsArray = [];

        if (occupation.naturalistPercent > 0) {
            skillsArray.push([occupation.naturalistPercent, "Naturalistic Intelligence", skillsText.naturalistSkills]);
        }
        if (occupation.musicalPercent > 0) {
            skillsArray.push([occupation.musicalPercent, "Musical Intelligence", skillsText.musicalSkills]);
        }
        if (occupation.logicalPercent > 0) {
            skillsArray.push([occupation.logicalPercent, "Logical-Mathematical Intelligence", skillsText.logicalSkills]);
        }
        if (occupation.existentialPercent > 0) {
            skillsArray.push([occupation.existentialPercent, "Existential Intelligence", skillsText.existentialSkills]);
        }
        if (occupation.interpersonalPercent > 0) {
            skillsArray.push([occupation.interpersonalPercent, "Interpersonal Intelligence", skillsText.interpersonalSkills]);
        }
        if (occupation.bodyPercent > 0) {
            skillsArray.push([occupation.bodyPercent, "Bodily-Kinesthetic Intelligence", skillsText.bodySkills]);
        }
        if (occupation.linguisticPercent > 0) {
            skillsArray.push([occupation.linguisticPercent, "Linguistic Intelligence", skillsText.linguisticSkills]);
        }
        if (occupation.intrapersonalPercent > 0) {
            skillsArray.push([occupation.intrapersonalPercent, "Intra-personal Intelligence", skillsText.intrapersonalSkills]);
        }
        if (occupation.spatialPercent > 0) {
            skillsArray.push([occupation.spatialPercent, "Spatial Intelligence", skillsText.spatialSkills]);
        }

        skillsArray.sort(function(a,b){return b[0]-a[0];});

        dict.skillsArray = skillsArray;
    }
}
