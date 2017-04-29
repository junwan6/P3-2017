<!DOCTYPE HTML>
<html>
  <head>
    <!-- {{> global_header }} -->
    <?php include 'partials/global_header.php'; ?>
    <link type="text/css" rel="stylesheet" href="static/newPassword.css">
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
              <!-- 
              {{#if badCode}}
              Oops!
              {{else}}
                {{#if expiredCode}}
                Oops!
                {{else}}
                Enter a new password
                {{/if}}
              {{/if}}
              -->
              <?php
                if ($badCode){
                  echo 'Oops!';
                } else {
                  echo 'Enter a new password';
                }
              ?>
            </p>

            <div class="container-fluid">
              <div class="row">
                <div class="col-md-6 col-md-offset-3">
                  <!--
                  {{#if badCode}}
                  <p class="normalText">
                    The URL you've provided is invalid, please double check the URL to make sure it's correct.
                  </p>
                  {{else}}
                    {{#if expiredCode}}
                  <p class="normalText">
                    This link has already expired, please <a href="/recover-account">request another password reset</a>.
                  </p>
                    {{else}}
                  <form id="newPasswordForm" action="/set-password" method="post">
                    <input type="hidden" name="code" value="{{code}}"/>
                    <input type="password" class="form-control formTextField newPasswordTextField" name="password" placeholder="Password"/>
                    <input type="password" class="form-control formTextField newPasswordTextField" name="verifypassword" placeholder="Verify password"/>
                    <input id="newPasswordButton" type="submit" class="btn btn-default formButton" value="Set new password"/>
                  </form>
                    {{/if}}
                  {{/if}}
                  -->
                  <?php
                    if ($badCode){
                      echo '<p class="normalText">';
                      echo 'The URL you\'ve provided is invalid, please double check the URL to make sure it\'s correct.';
                      echo '</p>';
                    } elseif ($expiredCode){
                      echo '<p class="normalText">';
                      echo 'This link has already expired, please <a href="recover-account">request another password reset</a>.';
                      echo '</p>';
                    } else {
                  ?>
                    <form id="newPasswordForm" action="set-password" method="post">
                      <!-- <input type="hidden" name="code" value="{{code}}"/> -->
                      <?php echo '<input type="hidden" name="code" value="' . $code . '"/>'; ?>
                      <input type="password" class="form-control formTextField newPasswordTextField" name="password" placeholder="Password"/>
                      <input type="password" class="form-control formTextField newPasswordTextField" name="verifypassword" placeholder="Verify password"/>
                      <input id="newPasswordButton" type="submit" class="btn btn-default formButton" value="Set new password"/>
                    </form>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
