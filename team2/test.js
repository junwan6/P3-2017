// require the module
var mysql = require('mysql');
var fs = require('fs');
var config = JSON.parse(fs.readFileSync('db-config.json', 'utf8'));

var connection = mysql.createConnection(config);
connection.connect();

var userId = 1;

// Get Ratings from Viewed
connection.query('SELECT soc, title from occupation WHERE id=' + userId, function(err, rows, fields){
	if (err) throw err;
	console.log(rows);
});

connection.end();