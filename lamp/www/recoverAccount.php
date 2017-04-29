<!DOCTYPE HTML>
<html>
  <head>
    <!-- {{> global_header }} -->
    <?php include 'partials/global_header.php'; ?>
    <link type="text/css" rel="stylesheet" href="static/recoverAccount.css">
    <title>
      PPP
    </title>
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
                  <form id="passwordResetForm" action="password-reset" method="post">
                    <input id="passwordResetTextField" type="text" class="formTextField form-control" name="email" placeholder="Email address"/>
                    <input id="passwordResetButton" type="submit" class="btn btn-default formButton" value="Reset my password"/>
                  </form>

                  <p class="normalText">
                    Enter your email address so we can send you an email containing instructions on how to reset your password.
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
