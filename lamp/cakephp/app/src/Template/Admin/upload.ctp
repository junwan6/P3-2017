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
                <div class="col-md-12 col-md-offset-0">
                  <h1 style="color:black">TODO: Changes overview</h1>
                  <?php
                    if ($dryRun){
                      echo '<h1 style="color: red">No action taken, disable \'dryRun\' in AdminController</h1>';
                    }
                    debug($request, true);
                    debug($changes, true);
                    debug($actions, true);
                    debug($statements, true);
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
