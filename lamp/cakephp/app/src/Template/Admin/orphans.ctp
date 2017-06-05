<!DOCTYPE HTML>
<html>
  <head>
    <!-- {{> global_header }} -->
    <?php echo $this->Html->css('Admin/orphans.css'); ?>
    <?php echo $this->Html->script('Admin/orphans.js'); ?>
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
              Filesystem Cleaning
            </p>

            <div class="container-fluid">
              <div class="row">
                <div class="col-md-12 col-md-offset-0" style="color:black">
                  <?php
                    if (count($orphans) > 0){
                      $uploadForm = $this->Url->build(['controller'=>'Admin',
                        'action'=>'delete']);
                      echo '<form action="' . $uploadForm . '" method="post" ' .
                        'enctype="multipart/form-data" autocomplete="off">';
                      if (array_key_exists('root', $orphans)){
                        $career = $orphans['root'];
                        echo '<h3>Invalid files in the "vid" directory:</h3>';
                        echo '<table class="deleteTable">';
                        foreach ($career as $file){
                          echo '<tr class="deleteRow" onclick="markFileForDelete(this)">';
                          $fileName = basename($file['path']). '/';
                          $info = $file['info'];
                          echo '<td>' . $fileName . '</td>';
                          echo '<td>' . ($info['dir']?'Folder':$info['size']) . '</td>';
                          echo '<td>' . $info['ctime'];
                          echo '<td>';
                          echo '<input type="checkbox" name="' .
                            urlencode($fileName) . '" style="display:none"/>';
                          echo '<i class="fa fa-trash-o" aria-hidden="true"></i>';
                          echo '</td>';
                          echo '</tr>';
                        }
                        echo '</table>';
                        
                      }
                      foreach ($orphans as $soc => $career){
                        if ($soc == 'root'){
                          continue;
                        }
                        foreach ($career['people'] as $pNum => $person){
                          $folderName = "{$soc}_{$pNum}_{$person['name']}/";
                          echo '<div>';
                          echo '<h3>' . htmlspecialchars($folderName) . '</h3>';
                          if (count($person['files']) != 0){
                            echo '<h4>Unassigned files:</h4>';
                            echo '<table class="orphanTable">';
                            echo '<thead>';
                            echo '<th>Filename</th><th>Filesize</th><th>Date Created:</th><th>Delete</th>';
                            echo '</thead>';
                            foreach ($person['files'] as $file){
                              echo '<tr class="deleteRow" onclick="markFileForDelete(this);">';
                              $fileName = basename($file['path']);
                              $info = $file['info'];
                              echo '<td>' . $fileName . '</td>';
                              echo '<td>' . ($info['dir']?'Folder':$info['size']) . '</td>';
                              echo '<td>' . $info['ctime'];
                              echo '<td>';
                              echo '<input type="checkbox" name="' . 
                                urlencode($folderName . $fileName) . '" style="display:none"/>';
                              echo '<i class="fa fa-trash-o" aria-hidden="true"></i>';
                              echo '</td>';
                              echo '</tr>';
                            }
                            echo '</table>';
                          } else if (!array_key_exists('deadLinks', $person)){
                            echo "<h4>Number not used by any person";
                            echo '<i class="fa fa-trash-o" aria-hidden="true" ' .
                              'onclick="markFolderForDelete(this);"></i>';
                            echo '<input type="checkbox" name="' .
                              urlencode("{$folderName}/") .
                              '" style="display:none"/>';
                            echo '</h4>';
                          }
                          echo '</div>';
                          
                          if (array_key_exists('deadLinks', $person)){
                            echo '<h4>Files referred to but not found:</h4>';
                            echo '<table class="deadLinkTable">';
                            foreach ($person['deadLinks'] as $qNum => $dl){
                              echo "<tr><td>Question {$qNum}</td><td>{$dl}</td></tr>";
                            }
                            echo '</table>';
                          }
                          
                          if (array_key_exists('conflict', $person)){
                            echo 'Person-number conflicts:';
                            foreach ($person['conflict'] as $cname => $cfiles){
                              $folderName = "{$soc}_{$pNum}_{$cname}/";
                              echo '<table class="conflictTable">';
                              echo '<thead onclick="markConflictForDelete(this);">';
                              echo "<th colspan=3>Conflicting folder {$folderName}:</th>";
                              echo '<th>';
                              echo '<input type="checkbox" name="' .
                                urlencode($folderName) .
                                '" style="display:none"/>';
                              echo '<i class="fa fa-trash-o" aria-hidden="true"></i></th>';
                              echo '</thead>';
                              echo '<thead>';
                              echo '<th>Filename</th><th>Filesize</th><th>Date Created:</th><th>Delete</th>';
                              echo '</thead>';
                              foreach ($cfiles as $cfile){
                                echo '<tr class="deleteRow" onclick="markFileForDelete(this);">';
                                $fileName = basename($cfile['path']);
                                $info = $cfile['info'];
                                echo '<td>' . $fileName . '</td>';
                                echo '<td>' . ($info['dir']?'Folder':$info['size']) . '</td>';
                                echo '<td>' . $info['ctime'];
                                echo '<td>';
                                echo '<input type="checkbox" name="' .
                                  urlencode($folderName . $fileName) .
                                  '" style="display:none"/>';
                                echo '<i class="fa fa-trash-o" aria-hidden="true"></i>';
                                echo '</td>';
                                echo '</tr>';
                              }
                              echo '</table>';
                            }
                          }
                          echo '<hr>';
                        }
                      }
                      echo '<input type="submit" />';
                      echo '</form>';
                    } else {
                      echo '<h2>No files to delete</h2>';
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
