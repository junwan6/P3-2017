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
                    echo '<table class="unpersonTable" id="soc'
                      . $soc . 'untable"></table>';
                    foreach($career['people'] as $pNum => $p){
                      echo '<script>';
                        echo "addPerson('" . $soc . "', '" . addslashes($p['name']) .
                        "', '" . addslashes(json_encode($p['questions'])) . "');";
                      echo '</script>';
                    }
                    
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
                        
                    if (array_key_exists($soc, $orphans) || array_key_exists($soc, $deadLinks)){
                      echo '<a style="text-align:left" ' . 
                        'onclick="toggleWarnings(\'' . $soc . '\');">' .
                        '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>' . 
                        'Warnings for ' . $soc . '</a>';
                      echo '<div id="' . $soc . 'warnings" style="color:black; display:none">';
                    }
                    foreach($career['people'] as $pNum => $p){
                      $orphansExist = array_key_exists($soc, $orphans)
                        && array_key_exists($pNum, $orphans[$soc]['people']);
                      $deadLinksExist = array_key_exists($soc, $deadLinks)
                        && array_key_exists($pNum, $deadLinks[$soc]['people']);
                      if ($orphansExist || $deadLinksExist){
                        echo '<h4>' . $p['name'] . '</h4>';
                        echo '<table class="fileErrors">';
                        echo '<thead><th>Unlinked Files</th><th>Nonexistant Files</th></thead>';
                        echo '<td class="orphanCell"><table>';
                        if ($orphansExist){
                          foreach ($orphans[$soc]['people'][$pNum]['files'] as $oNum => $o){
                            echo '<tr class="orphan">';
                            echo '<td>"' . $o . '"</td>';
                            echo '<td>';
                            echo   '<input type="checkbox" name="soc' .
                              $soc . 'p' . $pNum . 'orphan' . $oNum . addslashes($p['name']) . '" ' .
                              'value="' . addslashes($o) . '"/>';
                            echo   '';
                            echo '</td>';
                            echo '</tr>';
                          }
                        }
                        echo '</table></td><td class="deadLinkCell"><table>';
                        if ($deadLinksExist){
                          foreach ($deadLinks[$soc]['people'][$pNum]['files'] as $qNum => $dl){
                            echo '<tr class="deadLink"><td>Question&nbsp;' . $qNum . ':</td>';
                            echo '<td>"' . $dl . '"</td></tr>';
                          }
                        }
                        echo '</table></td>';
                        echo '</table>';
                      }
                    }
                    if (array_key_exists($soc, $orphans) || array_key_exists($soc, $deadLinks)){
                      echo '</div>';
                    }
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
