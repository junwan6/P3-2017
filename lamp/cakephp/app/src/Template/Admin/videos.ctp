<!DOCTYPE HTML>
<html>
  <head>
    <!-- {{> global_header }} -->
    <?php echo $this->Html->css('Admin/videos.css'); ?>
    <title>
      PPP
    </title>
    <script>
    // TODO: Have javascript/jquery expert go over this
    //   No idea on best practices, language-specific bugs, etc. - Andrew
    
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
    
    var addQuestion = function(caller, tableId, id, person){
      var table = document.getElementById(tableId);
      var nextElemId = tableId + "q" + id + person;

      var row = table.insertRow();
      row.className = 'questionRow';
      var question = row.insertCell(0);
      var questionText = document.createElement('input');
      questionText.setAttribute('type', 'text');
      questionText.setAttribute('id', nextElemId+'text');
      questionText.setAttribute('class', 'questionText');
      questionText.setAttribute('value', '');
      questionText.setAttribute('name', nextElemId+'text');
      question.appendChild(questionText);
      
      var file = row.insertCell(1);
      file.className = 'videoFile';
      var fileInput = document.createElement('input');
      fileInput.setAttribute('type', 'file');
      fileInput.setAttribute('id', nextElemId+'file');
      // Probably a way to directly set a onchange function
      // Will need to have a constant argument (partial function?)
      fileInput.setAttribute('onchange',
        'setUpload(\'' + nextElemId + '\')');
      fileInput.setAttribute('name', nextElemId+'file');
      file.appendChild(fileInput);
      var fileLabel = document.createElement('label');
      fileLabel.setAttribute('for', nextElemId+'file');
      fileLabel.setAttribute('id', nextElemId+'label');
      file.appendChild(fileLabel);
      fileLabel.innerHtml = '';

      var upload = row.insertCell(2);
      var uploadButton = document.createElement('input');
      uploadButton.setAttribute('type', 'button');
      uploadButton.setAttribute('id', nextElemId+'button');
      uploadButton.setAttribute('value', 'Browse...');
      uploadButton.setAttribute('onclick',
        'document.getElementById(\'' +
        nextElemId + 'file\').click();');
      upload.appendChild(uploadButton);
      
      var deleteRow = row.insertCell(3);
      var deleteBox = document.createElement('input');
      deleteBox.setAttribute('type', 'checkbox');
      deleteBox.setAttribute('id', nextElemId+'delete');
      deleteBox.setAttribute('value', 'Delete');
      deleteBox.setAttribute('name', nextElemId+'delete');
      deleteBox.setAttribute('onclick', 'setRow(this, true, \'' +
        nextElemId + '\');');
      deleteRow.appendChild(deleteBox);
      // If constructed by createElement, the label innerHtml is never set
      // Still issues, in narrow window original labels are below, new are beside
      // TODO: Find out what's going on, low priority
      deleteRow.insertAdjacentHTML('beforeend',
        '<label for="' + nextElemId+'delete' + '">Delete</label>');

      caller.setAttribute('onclick',
        'addQuestion(this, \'' + tableId + '\', ' +
        (parseInt(id)+1) + ', \'' + person + '\');');
    };
    
    var setRow = function(caller, setTo, elemId){
      document.getElementById(elemId+'text').disabled = setTo;
      document.getElementById(elemId+'button').disabled = setTo;
      caller.setAttribute('onclick', 'setRow(this, ' + !setTo +
        ', \'' + elemId + '\');');
      caller.parentNode.parentNode.style.backgroundColor = 
        (setTo?'red':'');
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
                        enctype="multipart/form-data">';
                      // Every element has full data to allow multiple forms on one page
                      // TODO: Use relative JS ids/classes to allow reuse of ids
                      //   WILL REQUIRE REWRITE OF AdminController:videoUpload()
                      $tableId = 'soc' . $soc . 'p' . $pid;
                      echo '<table class="uploadTable" id="' . $tableId . '">';
                      $nextQid = 0;
                      foreach ($p['questions'] as $qid => $q){
                        $elemId = 'soc' . $soc . 'p' . $pid . 'q' . $qid . $p['name'];
                        if ($qid >= $nextQid){
                          $nextQid = $qid+1;
                        }
                        echo '<tr class="questionRow">';
                        echo tag('td', [],
                          tag('input', [
                            'id'=>$elemId . 'text',
                            'class'=>'questionText', 'value'=>$q[0],
                            'type'=>'text', 'name'=>$elemId . 'text'])
                        );
                        echo '<td class="videoFile">';
                        echo tag('input', [
                          'type'=>'file', 'id'=>$elemId . 'file',
                          'onchange'=>'setUpload(\'' . $elemId . '\');',
                          'name'=>$elemId . 'file']);
                        echo tag('label', [
                          'for'=>$elemId . 'file', 'id'=>$elemId . 'label'
                          ], $q[1]);
                        echo '</td>';
                        echo tag('td', [] , tag('input', [
                          'type'=>'button', 'value'=>'Browse...',
                          'id'=>$elemId . 'button',
                          'onclick'=>'document.getElementById(\'' .
                            $elemId . 'file\').click();']
                        ));
                        echo tag('td', [] , tag('input', [
                          'type'=>'checkbox', 'id'=>$elemId . 'delete',
                          'value'=>'Delete', 'name'=>$elemId . 'delete',
                          'onclick'=>'setRow(this, true, \'' . $elemId . '\');']
                        ) . tag('label',['for'=>$elemId . 'delete'], 'Delete'));
                        echo '</tr>';
                      }
                      echo '</table>';
                      echo tag('input', [
                        'type'=>'button', 'id'=>$elemId . 'button',
                        'onclick'=>'addQuestion(this, \'' . $tableId . '\', ' .
                          $nextQid . ', \'' . $p['name'] . '\');',
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
