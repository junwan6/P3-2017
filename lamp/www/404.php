<!DOCTYPE HTML>
<html>
  <head>
    <!-- {{> global_header }} -->
    <?php include 'partials/global_header.php' ?>
    <?php
      //TODO: Needed variables:
      $loggedIn = false;
    ?>
    <link type="text/css" rel="stylesheet" href="static/404.css">
    <title>
      PPP
    </title>
  </head>
  <body>
    <!-- {{> navbar}} -->
    <?php
      include 'partials/' . (($loggedIn)?'navbarlogout.html':'navbar.html');
    ?>      

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="box">
            <p class="titleText">
              Uh-oh!
            </p>

            <div class="container-fluid">
              <div class="row">
                <div class="col-md-6 col-md-offset-3">
                  <p class="normalText">
                    We couldn't find the page you're looking for.
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
