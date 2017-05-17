<!DOCTYPE HTML>
<html>
  <head>
    <!-- {{> global_header }} -->
    <?php echo $this->Html->css('Admin/videos.css'); ?>
    <title>
      PPP
    </title>
    <script>
    // Taken from https://stackoverflow.com/questions/857618/javascript-how-to-extract-filename-from-a-file-input-control
    var extractFileName = function(fullPath){
      var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
      var filename = fullPath.substring(startIndex);
      if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
          filename = filename.substring(1);
      }
      return filename;
    }
    
    var setUpload = function(id){
      var fileElement = document.getElementById(id + 'file');
      var fileName = fileElement.value;
      var label = document.getElementById(id + 'label');
      label.innerHTML = extractFileName(fileName);
      label.style.color = "Red";
    };
    </script>
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
                      return $tag . ' />';
                    } elseif (is_string($content)) {
                      return $tag . '>' . $content . '</' . $type . '>';
                    } elseif ($content === true){
                      if (!isset($unclosed[$type])){
                        $unclosed[$type] = 0;
                      }
                      $unclosed[$type] += 1;
                      return $tag . '>';
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
                      //echo tag('h4', [], $p['name']);
                      echo "<h4>{$p['name']}</h4>";
                      //echo tag('form', ['action'=>'upload', 'method'=>'post',
                      //  'enctype'=>'multipart/form-data'], true);
                      echo '<form action="upload" method="post"
                        enctype="multipart/form-data">';
                      echo '<table class="uploadTable">';
                      foreach ($p['questions'] as $qid => $q){
                        $elemId = 'soc' . $soc . 'p' . $pid . 'q' . $qid . $p['name'];
                        echo '<tr>';
                        echo tag('td', [],
                          tag('input', ['class'=>'questionText', 'value'=>$q[0],
                            'type'=>'text', 'name'=>$elemId . 'text'])
                        );
                        echo '<td class="videoFile">';
                        echo tag('input', ['type'=>'file', 'id'=>$elemId . 'file',
                          'onchange'=>'setUpload(\'' . $elemId . '\');', 'name'=>$elemId]);
                        echo tag('label', ['for'=>$elemId, 'id'=>$elemId . 'label'], $q[1]);
                        echo '</td>';
                        echo tag('td', [] , tag('input', [
                          'type'=>'button', 'value'=>'Browse...',
                          'onclick'=>'document.getElementById(\'' . $elemId . 'file\').click();']
                        ));
                        echo '</tr>';
                      }
                      echo '</table>';
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
