/******************************************************************************
  occupation.js

This module handles queries for any data related to occupations.
******************************************************************************/

var mysql = require('mysql');
var fs = require('fs');

// Load the database configuration
var config = JSON.parse(fs.readFileSync(__dirname + '/../config/db-config.json', 'utf8'));

// Finds the national occupation data for the occupation with the given SOC code.
// successNext takes the argument "occupation" of type Occupation
// errNext takes the argument "err" containing a description of the error
module.exports.find = function(soc, successNext, errNext) {
    var connection = mysql.createConnection(config);
    connection.connect();

    var targetFields = ['Occupation.soc', 'title', 'wageType', 'averageWage', 'averageWageOutOfRange', 'lowWage', 'lowWageOutOfRange', 'medianWage', 'medianWageOutOfRange', 'highWage', 'highWageOutOfRange', 'educationRequired', 'currentEmployment', 'futureEmployment', 'careerGrowth', 'jobOpenings', 'naturalistPercent', 'musicalPercent', 'logicalPercent', 'existentialPercent', 'interpersonalPercent', 'bodyPercent', 'linguisticPercent', 'intrapersonalPercent', 'spatialPercent', 'skillsText'];
    var queryString = "SELECT ";
    for (i = 0; i < targetFields.length; i++) {
        queryString += targetFields[i];
        if ((i+1) < targetFields.length) {
            queryString += ", ";
        } else {
            queryString += " ";
        }
    }

    queryString += "FROM Skills, Occupation WHERE Occupation.soc = ? && Skills.soc = ?;";

    connection.query(queryString, [soc, soc], function(err, rows, fields) {
        if (err === null && rows.length == 1) {
            successNext(rows[0]);
            connection.end();
        } else {
            connection.query('SELECT * FROM Occupation WHERE soc = ?;', [soc], function(err2, rows2, fields2) {
                if (err2 === null && rows2.length == 1) {
                    successNext(rows2[0]);
                }
                else {
                    errNext(err2);
                };
            });
            connection.end();
        }
    });

/*
    connection.query('SELECT * FROM Occupation WHERE soc = ?;', [soc], function(err, rows, fields) {
        if (err === null && rows.length == 1) {
            successNext(rows[0]);
        }
        else {
            errNext(err);
        };
    });
*/
}

module.exports.getStateData = function(soc, successNext, errNext) {
    var connection = mysql.createConnection(config);
    connection.connect();

    connection.query('SELECT * FROM StateOccupation WHERE soc = ?;', [soc], function(err, rows, fields) {
        if (err === null && rows.length > 0) {
            successNext(rows);
        }
        else {
            errNext(err);
        };
    });

    connection.end();
}

module.exports.getSkills = function(soc, successNext, errNext) {

    var connection = mysql.createConnection(config);
    connection.connect();

    connection.query("SELECT * FROM Skills WHERE soc = ?", [soc], function(err, rows, fields) {
        if (err === null && rows.length == 1) {
            successNext(rows[0]);
        } else {
            successNext(null);
        }
    });

    connection.end();
}

module.exports.getInterests = function(soc, successNext, errNext) {

    var connection = mysql.createConnection(config);
    connection.connect();

    connection.query("SELECT * FROM OccupationInterests WHERE soc = ?", [soc], function(err, rows, fields) {
        if (err === null && rows.length == 1) {
            successNext(rows[0]);
        } else {
            successNext(null);
        }
    });

    connection.end();

}

// successNext takes an argument as a string representing the random SOC code
// errNext takes an argument as an error object
module.exports.getRandomSOC = function(successNext, errNext) {
    var connection = mysql.createConnection(config);
    connection.connect();

    connection.query('SELECT soc FROM Occupation ORDER BY RAND() LIMIT 1;', function(err, rows, fields) {
        if (err === null && rows.length == 1) {
            successNext(rows[0].soc);
        }
        else {
            errNext(err);
        }
    });

    connection.end();
}

// coordinate is an object with attributes x and y. This coordinate specifies the
// region in which to get the random SOC code. Both x and y should only be in the
// range [-1, 1].
// successNext takes an argument as a string representing the random SOC code
// errNext takes an argument as an error object
module.exports.getRandomSOCInWOWRegion = function(coordinate, successNext, errNext) {
    // Since the WoW image is split into 12 sections, we take the region to be
    // one of the 12 sections. Determine which section the coordinate falls in:
    // Note that we number the sections starting from 0, and we start from the
    // positive x-axis and rotate counter-clockwise.
    angleInRadians = Math.atan2(coordinate.y, coordinate.x);
    // Convert the range of angleInRadians from [-pi, pi] to [0, 2pi]
    angleInRadians = (angleInRadians >= 0 ? angleInRadians : angleInRadians + 2 * Math.PI);
    // Convert it to degrees for computational simplicity
    angleInDegrees = angleInRadians * 180 / Math.PI;
    // Convert angle into a sector, bounding it to [0, 11]
    section = Math.floor(Math.min(Math.max(0, angleInDegrees / 30), 11));

    var connection = mysql.createConnection(config);
    connection.connect();

    /// TECH DEBT: OccupationInterests doesn't filter out jobs with education
    /// requirements below a Bachelor's, so we need to do this join with
    /// Occupation to make sure we don't include these jobs
    connection.query("SELECT OI.soc FROM OccupationInterests OI, Occupation O WHERE OI.wowRegion = ? AND OI.soc = O.soc ORDER BY RAND() LIMIT 1", [section], function(err, rows, fields) {
        if (err) {
            return errNext(err);
        }

        if (rows.length == 0) {
            return errNext(new Error("No occupation in region"));
        }

        return successNext(rows[0].soc);
    });
}

module.exports.searchOccupationNames = function(query, successNext, errNext) {
    // Split query string into words
    var keywords = query.split(/[,\s]+/);

    if (keywords.length == 0) {
        errNext('No keywords');
        return;
    }

    // Add % to the beginning and end of each keyword
    for (var i = 0; i < keywords.length; i++) {
        keywords[i] = '%' + keywords[i] + '%';
    }

    // Build a series of nested queries to get all job titles that match each keyword
    var sqlQuery = 'SELECT * FROM Occupation WHERE title LIKE ?';
    for (var i = 1; i < keywords.length; i++) {
        sqlQuery += ' AND soc IN (SELECT soc FROM Occupation WHERE title LIKE ?';
    }
    for (var i = 1; i < keywords.length; i++) {
        sqlQuery += ')';
    }
    sqlQuery += ';';

    var connection = mysql.createConnection(config);
    connection.connect();

    connection.query(sqlQuery, keywords, function(err, rows, fields) {
        if (err === null) {
            successNext(rows);
        }
        else {
            errNext(err);
        };
    });

    connection.end();
}
