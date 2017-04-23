/******************************************************************************
  algorithm.js

This module handles the database queries needed to execute the video selection
algorithm.
******************************************************************************/

var mysql = require('mysql');
var fs = require('fs');

// Load the database configuration
var config = JSON.parse(fs.readFileSync(__dirname + '/../config/db-config.json', 'utf8'));

module.exports.getFirstVid = function(successNext, errNext) {
    var connection = mysql.createConnection(config);
    connection.connect();

    connection.query('SELECT soc FROM vidQueue WHERE viewOrder = 1;', function(err, rows, fields) {
        if (err === null && rows.length == 1) {
            successNext(rows[0].soc);
        }
        else {
            errNext(err);
        }
    });

    connection.end();
}

module.exports.getSecondVid = function(successNext, errNext) {
    var connection = mysql.createConnection(config);
    connection.connect();

    connection.query('SELECT soc FROM vidQueue WHERE viewOrder = 2;', function(err, rows, fields) {
        if (err === null && rows.length == 1) {
            successNext(rows[0].soc);
        }
        else {
            errNext(err);
        }
    });

    connection.end();
}

module.exports.getThirdVid = function(successNext, errNext) {
    var connection = mysql.createConnection(config);
    connection.connect();

    connection.query('SELECT soc FROM vidQueue WHERE viewOrder = 3;', function(err, rows, fields) {
        if (err === null && rows.length == 1) {
            successNext(rows[0].soc);
        }
        else {
            errNext(err);
        }
    });

    connection.end();
}
