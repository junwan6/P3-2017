<!DOCTYPE HTML>
<html>
  <head>
    <!-- {{> global_header }} -->
    <?php echo $this->Html->css('Admin/summary.css'); ?>
    <?php echo $this->Html->script('Admin/summary.js'); ?>
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
              Summary
            </p>

            <div class="container-fluid">
              <div class="row">
                <div class="col-md-10 col-md-offset-1">
                  <div class="bodyContents">
                    <h4>Videos</h4>
                      <table class="videosTable">
                        <thead>
                        <tr><td colspan="4">
                          <span style="width:100%" class="cellspan">
                            <input type="text" id="inputSOC"
                              placeholder="enter a SOC or list of SOCs">
                              <!-- placeholder="or navigate by search bar"> -->
                          </span>
                          <?php
                            echo '<span class="cellspan">' .
                              '<input type="button" value="Go To" onclick="' .
                              'goToSOCVideos(\'' . $this->Url->build(['controller'=>'Admin',
                              'action'=>'videos']) . '\');' .
                              '"><span>';
                          ?>
                        </td></tr>
                          <tr><td>SOC</td><td>Occupation</td><td>People</td><td>Videos</td></tr>
                        </thead>
                        <?php
                          foreach ($videoList as $soc => $career){
                            $peopleString = implode(', ', array_map(function($p){
                              return $p['name'];
                            }, $career['people']));
                            $videoSum = array_sum(array_map(function($p){
                              return count($p['questions']);
                            }, $career['people']));
                            echo '<tr class="videosRow" onclick="addSOC(\'' . $soc . '\');">';
                            echo "<td>{$soc}</td><td>{$career['title']}</td>" . 
                              "<td>{$peopleString}</td><td>{$videoSum}</td>";
                            echo '</tr>';
                          }
                        ?>
                      </table>
                    <h4>Users</h4>
                    <h4>View Trends</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
