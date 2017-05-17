              <div id="growthPercent">
                <i id="growthPercentIcon" class="fa fa-sun-o fa-5x" aria-hidden="true"></i>
                <p id="growthPercentText">
                  <?php echo $careerGrowth ?>
                </p>
              </div>
              <div id="outlookTable">
                National Trends
                <table class="table">
                  <tr>
                    <th rowspan="2">United States</th>
                    <th colspan="2">Employment</th>
                    <th rowspan="2">Percent Change</th>
                    <th rowspan="2">Projected Annual Job Openings</th>
                  </tr>
                  <tr>
                    <th>2014</th>
                    <th>2024</th>
                  </tr>
                  <tr>
                    <?php
                      echo '<td>' . $occupationTitle . '</td>';
                      echo '<td>' . $currentEmployment . '</td>';
                      echo '<td>' . $futureEmployment . '</td>';
                      echo '<td>' . $careerGrowth . '</td>';
                      echo '<td>' . $jobOpenings . '</td>';
                      ?>
                  </tr>
                </table>
              </div>