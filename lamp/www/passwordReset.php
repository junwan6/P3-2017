<!DOCTYPE HTML>
<html>
  <head>
    <!-- {{> global_header }} -->
    <?php include 'partials/global_header.php'; ?>
    <link type="text/css" rel="stylesheet" href="static/passwordReset.css">
    <title>
      PPP
    </title>
      <?php
        //TODO: Fill in following variables from the NodeJS serverside scripts:
        // controllers/users-controller.js
        // models/users.js
        $email = "Not Implemented";
      ?>
  </head>
  <body>
    <!-- {{> navbar}} -->
    <?php include 'partials/navbarcombined.php'; ?>

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="box">
            <p class="titleText">
              Recover your account
            </p>

            <div class="container-fluid">
              <div class="row">
                <div class="col-md-6 col-md-offset-3">
                  <p class="normalText">
                    We've sent an email to <!-- {{email}} --><?php echo $email; ?>, containing instructions on how to reset your password. Make sure to check your junk mail as well!
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
