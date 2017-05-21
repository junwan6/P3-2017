<!DOCTYPE HTML>
<html>
  <head>
    <!-- {{> global_header }} -->
    <?php echo $this->Html->css('Admin/videos.css'); ?>
    <?php echo $this->Html->script('Admin/videos.js'); ?>
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
              Video Upload Panel
            </p>

            <div class="container-fluid">
              <div class="row">
                <div class="col-md-12 col-md-offset-0">
                  <?php
                  foreach ($videoList as $soc => $career){
                    $uploadForm = $this->Url->build(['controller'=>'Admin', 'action'=>'upload']);
                    echo '<form action="' . $uploadForm . '" method="post" ' .
                      'enctype="multipart/form-data" autocomplete="off">';
                    
                    echo '<div class="careerVideos">';
                    echo $this->Html->link(
                      $career['title'] . '<p class="socCode">' . $soc . '</p>',
                      ['controller' => 'career',
                        'action' => 'displayCareerSingle', $soc, 'video'],
                      ['escape' => false, 'target' => '_blank']);
                    // Person table folded into javascript addPerson
                    // Question row folded into javascript addQuestion
                    // For creation of new row and new table
                    echo '<table class="personTable" id="' . $soc . '">';
                    echo '</table>';
                    foreach($career['people'] as $pid => $p){
                      echo '<script>';
                        echo "addPerson('" . $soc . "', '" . addslashes($p['name']) .
                        "', '" . addslashes(json_encode($p['questions'])) . "');";
                      echo '</script>';
                    }
                    
                    echo '<table class="unpersonTable" id="soc'
                      . $soc . 'untable"></table>';
                      
                    echo '<table class="headerTable">';
                    echo '<tr>';
                    echo '<td class="addPersonCell">' . 
                      '<span style="width:100%" class="cellspan">' . 
                      '<input type="text" class="addPersonText" ' .
                      'id="' . $soc . 'add" />' . 
                      '</span>';
                      
                    echo '<span class="cellspan"><input type="button" ' .
                      'value="Add Person" id="addPersonButton" onclick="' .
                      'addPersonFromBox(\'' . $soc . '\');"' .
                      '"/></span></td>';
                    echo '<td id="submitAllCell"><input type="submit"/></td>';
                    echo '</tr>';
                    echo '</table>';
                    echo '</div>';
                    echo '</form>';
                    echo '<br><br>';
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
