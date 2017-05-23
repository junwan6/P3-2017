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
                      echo '<h1 style="color: red">No action taken, disable \'dryRun\' in AdminController</h1>';
                    } else {
                      // Not as accurate, lists orphans generated during updates
                      // List on videos page scans directory and compares to database
                      echo '<h4>Delete files without questions:</h4>';
                      $uploadForm = $this->Url->build(['controller'=>'Admin', 'action'=>'upload']);
                      echo '<form action="' . $uploadForm . '" method="post" ' .
                        'enctype="multipart/form-data" autocomplete="off">';
                      echo '<table>';
                      foreach ($orphans as $o){
                        echo '<tr class="orphan">';
                        echo '<td>"' . $o['fileName'] . '"</td>';
                        echo '<td>';
                        echo   '<input type="checkbox" name="soc' .
                          $o['soc'] . 'p' . $o['personNum'] .
                          'orphan' . $o['questionNum'] .
                          addslashes($o['person']) . '" ' .
                          'value="' . addslashes($o['fileName']) . '"/>';
                        echo   '';
                        echo '</td>';
                        echo '</tr>';
                      }
                      echo '</table>';
                      echo '<input type="submit" />';
                      echo '</form>';
                    }
                    echo '<input type="button" value="Debug" ' . 
                    'onclick="document.getElementById(\'rawContainer\').style.display = \'initial\';">';
                    echo '<input type="button" value="Hide Debug" ' . 
                    'onclick="document.getElementById(\'rawContainer\').style.display = \'none\';">';
                    echo '<div id="rawContainer" style="display: none;">';
                    debug($orphans, true);
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
