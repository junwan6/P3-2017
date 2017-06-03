<!DOCTYPE HTML>
<html>
  <head>
    <!-- {{> global_header }} -->
    <title>
      PPP
    </title>
  </head>
  <body>

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="box">
            <p class="titleText" style="color:black">
              User Overview
            </p>

            <div class="container-fluid">
              <div class="row">
                <div class="col-md-12 col-md-offset-0" style="color:black">
                  <?php
                  echo "<h4>ID: {$user['id']}</h4>";
                  echo "<h4>First Name: {$user['firstName']}</h4>";
                  echo "<h4>Last Name: {$user['lastName']}</h4>";
                  echo "<h4>Email: {$user['email']}</h4>";

                  if (isset($adminuser)){
                    echo "<h4>Administrator</h4>";
                    echo '<form action="' . $user['id'] .
                      '/unadmin" method="post"><input type="submit" value="De-Admin"/></form>';
                  } else {
                    echo "<h4>Regular User</h4>";
                    echo '<form action="' . $user['id'] .
                      '/admin" method="post"><input type="submit" value="Make Admin"/></form>';
                  }

                  if (isset($fbuser)){
                    echo "<h4>Linked to Facebook</h4>";
                  }

                  if (isset($liuser)){
                    echo "<h4>Linked to LinkedIn</h4>";
                  }

                  if (isset($viewhistory)){
                    foreach($viewhistory as $v){
                      echo "{$v['time']}: Rated {$v['soc']} {$v['rating']}";
                    }
                  }
                  ?>
                  <!--
                  <?php print_r($user); ?><br>
                  <?php print_r($fbuser); ?><br>
                  <?php print_r($liuser); ?><br>
                  <?php print_r($adminuser); ?><br>
                  <?php print_r($viewhistory); ?>
                  -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
