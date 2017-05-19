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
                <div class="col-md-6 col-md-offset-3">
                  <?php
                  // Helper function to avoid long strings
                  // Tried to use HEREDOC, variable interpolation
                  // Failed due to HEREDOC requiring EOT without indent
                  // Failed due to {$var} syntax requiring " while HTML uses "
                  function tag($type = 'span', $attr = [], $content = null){
                    $tag = '<' . $type;
                    foreach($attr as $k => $v){
                      $tag .= ' ' . $k . '="' . $v . '"';
                    }
                    if (is_null($content)){
                      return $tag . ' />' . "\n";
                    } elseif (is_string($content)) {
                      return $tag . '>' . $content . '</' . $type . '>' . "\n";
                    } elseif ($content === true){
                      if (!isset($unclosed[$type])){
                        $unclosed[$type] = 0;
                      }
                      $unclosed[$type] += 1;
                      return $tag . '>' . "\n";
                    }
                  }
                  
                  foreach ($videoList as $soc => $career){
                    echo '<div class="careerVideos" id="' . $soc . '">';
                    echo $this->Html->link(
                      $career['title'] . '<p class="socCode">' . $soc . '</p>',
                      ['controller' => 'career',
                        'action' => 'displayCareerSingle', $soc, 'video'],
                      ['escape' => false, 'target' => '_blank']);
                    foreach($career['people'] as $pid => $p){
                      echo "<h4>{$p['name']}</h4>";
                      echo '<form action="upload" method="post"
                        enctype="multipart/form-data" autocomplete="off">';
                      // Every element has full data to allow multiple forms on one page
                      // TODO: Use relative JS ids/classes to allow reuse of ids
                      //   WILL REQUIRE REWRITE OF AdminController:videoUpload()
                      $tableId = 'soc' . $soc . 'p' . $pid;
                      echo '<table class="uploadTable" id="' . $tableId . '">';
                      // Rows populated by javascript
                      echo '<script>';
                      foreach ($p['questions'] as $qid => $q){
                        echo "addQuestion(null, '" . $tableId . "', " .
                          $qid . ", '" . addslashes($p['name']) . "', '" .
                          addslashes($q[0]) . "', '" . addslashes($q[1]) . "');";
                      }
                      echo '</script>';
                      echo '</table>';
                      echo '<table class="deleteTable" id="' . $tableId . 'dtable">';
                      echo '</table>';
                      
                      echo tag('input', [
                        'type'=>'button', 'id'=>$tableId . 'add',
                        'onclick'=>'addQuestion(this, \'' . $tableId . '\', ' .
                          (max(array_keys($p['questions']))+1) .
                          ', \'' . $p['name'] . '\');',
                        'value'=>'Add Question'
                      ]);
                      echo '<input type="submit">';
                      echo '</form>';
                    }
                    echo '</div><br><br>';
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
