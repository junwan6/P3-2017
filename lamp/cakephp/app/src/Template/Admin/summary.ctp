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
                    <?php
                      
                      $orphansLink = $this->Url->build(['controller'=>'Admin',
                        'action'=>'displayOrphans']);
                      echo '<h4>Videos (';
                      echo "<a href=\"{$orphansLink}\">Unassigned Files</a>";
                      echo ')</h4>';
                    ?>
                      <table class="scrollTable">
                        <thead>
                        <tr><td colspan="4">
                          <?php
                            $videosLink = $this->Url->build(['controller'=>'Admin',
                              'action'=>'displayVideos']);
                            echo '<span style="width:100%" class="cellspan">' .
                              '<input type="text" id="inputSOC" ' .
                                'placeholder="enter a SOC, list of SOCs, or blank for all" '. 
                                'onchange="updateSOCLink(\'' . $videosLink . '\');"' . '>';
                            // placeholder="or navigate by search bar">
                            echo '</span>';
                            echo '<span class="cellspan">' .
                              '<a id="gotoButton" href="' . $videosLink . '">' .
                              '<input type="button" value="Go To"></a>' .
                              '<span>';
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
                            echo '<tr class="scrollRow" onclick="addSOC(\'' . $soc . '\');">';
                            echo "<td>{$soc}</td><td>{$career['title']}</td>" . 
                              "<td>{$peopleString}</td><td>{$videoSum}</td>";
                            echo '</tr>';
                          }
                        ?>
                      </table>
                    <h4>Users</h4>
                      <table class="scrollTable">
                        <thead><tr><td>ID</td><td>Name</td><td>Email</td><td>Admin</td><tr></thead>
                        <?php
                          foreach ($userList as $u){
                            $name = $u['firstName'] . ' ' . $u['lastName'];
                            $email = $u['email'];
                            $uid = $u['id'];
                            $isAdmin = $u['isAdmin'];
                            echo '<tr class="scrollRow clickable" onclick="toUserPage(' .
                              $uid . ')">';
                            $userLink = $this->Url->build('/admin/user/' . $uid);
                            echo '<td>' . $uid . '<a href="' . $userLink . '" id="user' .
                              $uid . '"/> </td>';
                            echo '<td>' . $name . '</td>';
                            echo '<td>' . $email . '</td>';
                            $notAdmin = '<i class="fa fa-times" aria-hidden="true"></i>';
                            $adminIcon = '<i class="fa fa-check" aria-hidden="true"></i>';
                            echo '<td>' . ($isAdmin?$adminIcon:$notAdmin) . '</td>';
                            echo '</tr>';
                          }
                        ?>
                      </table>
                    <!-- <h4>View Trends</h4> -->
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
