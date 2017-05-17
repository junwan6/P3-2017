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
                  function htmlTag($type = 'span', $attr = [], $content = null){
                    $tag = '<' . $type;
                    foreach($attr as $k => $v){
                      $tag .= ' ' . $k . '="' . $v . '"';
                    }
                    if (is_null($content)){
                      return $tag . ' />';
                    } else {
                      return $tag . '>' . $content . '</' . $type . '>';
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
                      echo '<h4>' . $p['name'] . '</h4>';
                      echo '<form action="upload" method="post" ' . 
                        'enctype="multipart/form-data">';
                      echo '<table class="uploadTable">';
                      foreach ($p['questions'] as $qid => $q){
                        $elemId = 'soc' . $soc . 'p' . $pid . 'q' . $qid . $p['name'];
                        echo '<tr>';
                        echo '<td><input class="questionText" value="' .
                          $q[0] . '" type="text" name="' . $elemId . 'text"/></td>';
                        echo '<td class="videoFile">';
                        echo '<input type="file" name="' .
                          $elemId . '" id="' . $elemId . 'file" ' . 
                          'onchange="setUpload(\'' . $elemId . '\');"/>';
                        echo '<label for="' . $elemId . '" id="' .
                          $elemId . 'label">' . $q[1] . '</label>';
                        echo '</td>';
                        echo '<td><input type="button" value="Browse..." ' . 
                          'onclick="document.getElementById(\'' . $elemId . 'file\').click();" /></td>';
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
