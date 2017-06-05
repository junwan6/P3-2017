<!DOCTYPE HTML>
<html>
  <head>
    <!-- {{> global_header }} -->
    <title>
      PPP
    </title>
    <?php
      echo $this->Html->css('Admin/user.css');
      echo $this->Html->script('Admin/user.js');
    ?>
  </head>
    <div style="display:none">
    <?php
      if ($likedCareers === NULL || count($likedCareers) == 0){
      } else {
        foreach ($likedCareers as $lcareer){
          echo '<career type="like" title="' . $lcareer['title'] . '" soc="' .
            $lcareer['soc'] . '" x="' . $lcareer['x'] . '" y="' . $lcareer['y'] . '"></career>';
        }
      }
      if ($dislikedCareers === NULL || count($dislikedCareers) == 0){
      } else {
        foreach ($dislikedCareers as $dlcareer){
          echo '<career type="dislike" title="' . $dlcareer['title'] . '" soc="' .
            $dlcareer['soc'] .'" x="'. $dlcareer['x'] .'" y="'. $dlcareer['y'] . '"></career>';
        }
      }

      if ($neutralCareers === NULL || count($neutralCareers) == 0){
      } else {
        foreach ($neutralCareers as $ncareer){
          echo '<career type="neutral" title="' . $ncareer['title'] . '" soc="' .
            $ncareer['soc'] . '" x="' . $ncareer['x'] . '" y="' . $ncareer['y'] . '"></career>';
        }
      }
    ?>
    </div>
  <body>

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="box">
            <p class="titleText" style="color:black">
              User Overview
            </p>

            <div class="container-fluid">
              <div class="row">
                <div class="col-md-12 col-md-offset-0" style="color:black">
                  <?php
                  echo "<h4>ID: {$user['id']}</h4>";
                  echo "<h4>First Name: {$user['firstName']}</h4>";
                  echo "<h4>Last Name: {$user['lastName']}</h4>";
                  echo "<h4>Email: {$user['email']}</h4>";

                  if (isset($adminuser)){
                    echo "<h4>Administrator</h4>";
                    echo '<form action="' . $user['id'] .
                      '/unadmin" method="post"><input type="submit" value="De-Admin"/></form>';
                  } else {
                    echo "<h4>Regular User</h4>";
                    echo '<form action="' . $user['id'] .
                      '/admin" method="post"><input type="submit" value="Make Admin"/></form>';
                  }

                  if (isset($fbuser)){
                    echo "<h4>Linked to Facebook</h4>";
                  }

                  if (isset($liuser)){
                    echo "<h4>Linked to LinkedIn</h4>";
                  }

                  ?>
	              <h4 class="heading">My World of Work Map</h4>
                  <div>
                    <canvas id="occupationPlotter"></canvas>
                    <?php
                      echo $this->Html->image('wow.jpg', array(
                        'id'=>'wowImage', 'alt'=>'World of Work', 'align'=>'center',
                        'width'=>'100%', 'height'=>'auto'));
                    ?>
                  </div>
              <p id="hoverOccupationTitle"></p>

                  <?php

                  if (isset($viewhistory)){
                    echo '<table class="ratingsTable">';
                    echo '<thead><tr><th>Time</th><th>Career</th><th>Rating</th></tr></thead>';
                    foreach($viewhistory as $v){
                      echo '<tr id="' . $v['soc'] . '" class="careerRow">';
                      echo "<td>{$v['time']}</td>";
                      $careerLink = $this->Url->build(['controller'=>'Career',
                        'action'=>'', $v['soc'], 'video']);
                      echo '<td><a href="' . $careerLink . '">';
                      echo "{$v['soc']} : {$v['title']}";
                      echo '</a></td>';
                      //echo "<td>{$v['rating']}</td>";
                      $thumbIcon = [
                        1=>'<span class="upthumb" id="upthumb"></span>',
                        0=>'<span class="midthumb" id="midthumb"></span>',
                        -1=>'<span class="downthumb" id="downthumb"></span>'];
                      echo "<td>{$thumbIcon[$v['rating']]}</td>";
                      echo '</tr>';
                    }
                    echo '</table>';
                  }
                  ?>
                  <!--
                  <?php print_r($user); ?><br>
                  <?php print_r($fbuser); ?><br>
                  <?php print_r($liuser); ?><br>
                  <?php print_r($adminuser); ?><br>
                  <?php print_r($viewhistory); ?>
                  -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
