<?php
  include 'db_config.php';
/*
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
*//*
  // var connection = mysql.createConnection(config);
  // connection.connect();
  $conn = new mysqli($servername, $username, $password, $dbname);
  
  // Look up the user associated with the email address
  // connection.query("SELECT * FROM Users WHERE email = ?", [email], function(err, userRows) {
  $userQuery = $conn->prepare('SELECT * FROM Users WHERE email = ?');
  $userQuery->bind_param('s', $_POST['email']);
  $userQueryResult = $userQuery->execute();
  
  if ($userQueryResult->num_rows == 1){
    $user = $userQueryResult->fetch_assoc();
    $userid = $user['id'];
    // connection.query("SELECT * FROM UserPasswords WHERE id = ?",
    //   [userRows[0].id], function (err, userPasswordRows) {
    $passQuery = $conn->prepare('SELECT * FROM UserPasswords WHERE id = ?');
    $passQuery->bind_param('i', $userid);
    $passQueryResult = $passQuery->execute();
    if ($passQueryResult->numRows == 1){
      // Change from previous implementation: hash before sending
      //   using AJAX in javascript, ex. /p3/get-salt.php?email=abc
      //   get-salt.php runs mysql query, returns salt
      $pass = $passQueryResult->fetch_assoc();
      if ($pass['hash'] == $_POST['hash']){
        $loggedIn = true;
        // TODO: CREATE LOGGED-IN COOKIE
      } else {
        // TODO: show error message
      }
    }
  }

  $conn->close();

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
    });*/
?>
