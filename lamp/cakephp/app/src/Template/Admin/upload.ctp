<!DOCTYPE HTML>
<html>
  <head>
    <!-- {{> global_header }} -->
    <?php echo $this->Html->css('Admin/upload.css'); ?>
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
              Change Overview
            </p>

            <div class="container-fluid">
              <div class="row">
                <div class="col-md-12 col-md-offset-0" style="color:black">
                  <h1>TODO: Changes overview</h1>
                  <?php
                    if ($dryRun){
                      echo '<h1 style="color: red">No action taken, disable ' .
                        '\'dryRun\' in AdminController</h1>';
                    }
                    echo '<input type="button" value="Debug" ' . 
                    'onclick="document.getElementById(\'rawContainer\').style.display = \'initial\';">';
                    echo '<input type="button" value="Hide Debug" ' . 
                    'onclick="document.getElementById(\'rawContainer\').style.display = \'none\';">';
                    echo '<div id="rawContainer" style="display: none;">';
                    debug($statements, true);
                    debug($actions, true);
                    debug($changes, true);
                    debug($request, true);
                    echo '</div>';
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
