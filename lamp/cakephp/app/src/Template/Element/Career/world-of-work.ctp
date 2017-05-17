              <div style="display:table; margin: 0 auto;">
                <?php
                  if ($noData){
                    echo 'Sorry, we don\'t have World of Work data for this occupation.';
                  } else {
                    echo '<canvas id="d" style="z-index: 1;"></canvas>';
                    echo '<div id="interestsTableDiv">';
                    echo '  <table id="interestsTable">';
                    $interests = array('soc', 'realistic', 'investigative',
                      'artistic', 'social', 'enterprising', 'conventional');
                    foreach ($interests as $int){
                      echo '<tr><td>' . ${$int} . '</td></tr>';
                    }
                    echo '  </table>';
                    echo '</div>';
                  }
                  ?>
              </div>