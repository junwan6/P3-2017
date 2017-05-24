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
            <p class="titleText">
              Delete Overview
            </p>

            <div class="container-fluid">
              <div class="row">
                <div class="col-md-12 col-md-offset-0" style="color:black">
                  <?php
                    foreach($deletes as $d){
                      echo '<p>Deleted "' . htmlspecialchars($d) . '"</p>';
                    }
                    foreach($errors as $e){
                      echo '<p>Invalid delete path "' . htmlspecialchars($d) . '"</p>';
                    }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
