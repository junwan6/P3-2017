<!DOCTYPE HTML>
<html>
		<head>
      <!-- {{> global_header }} -->
      <?php
        echo $this->Html->css('outlook.css');
      ?>

			<title>
				PPP
			</title>
		</head>
    <body>
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-9 col-md-offset-1">
            <div class="box">
              <div id="pageTitle">
			          Career Outlook
			        </div>
			        <div id="careerTitle">
                <!-- {{occupationTitle}} -->
                <?php echo $occupationTitle; ?>
			        </div>
                                <div id="growthPercent">
                                  <i id="growthPercentIcon" class="fa fa-sun-o fa-5x" aria-hidden="true"></i>
                                  <p id="growthPercentText">
                                    <!-- {{careerGrowth}} -->
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
                                      <!-- 
                                      <td>{{occupationTitle}}</td>
                                      <td>{{currentEmployment}}</td>
                                      <td>{{futureEmployment}}</td>
                                      <td>{{careerGrowth}}</td>
                                      <td>{{jobOpenings}}</td>
                                      -->
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
                              </div>
                            </div>
                            <div class="col-md-2">
                              <!-- {{> icons }} -->
                              <?php
                                echo $this->element('icons', [
                                  'occupationTitle' => $occupationTitle,
                                  'wageTypeIsAnnual' => $wageTypeIsAnnual,
                                  'averageWage' => $averageWage,
                                  'careerGrowth' => $careerGrowth,
                                  'educationRequired' => $educationRequired,
                                  'skillsArray' => $skillsArray,
                                ]);
                              ?>
                            </div>
                          </div>
                        </div>

		</body>
</html>
