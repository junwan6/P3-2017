<!DOCTYPE HTML>
<html>
		<head>
      <!-- {{> global_header }} -->
      <?php include 'partials/global_header.php'; ?>
			<script type="text/javascript" language="javascript" src="static/icons.js"></script>
                        <link type="text/css" rel="stylesheet" href="static/icons.css">
			<link type="text/css" rel="stylesheet" href="static/careerOutlook.css">

			<title>
				PPP
			</title>
      <?php
        //TODO: Fill in following variables from the NodeJS serverside scripts:
        //  controllers/occupation-controller.js
        //  models/occupation.js
        $occupationTitle = "Not Implemented";
        $careerGrowth = "Not Implemented";
        $currentEmployment = "Not Implemented";
        $futureEmployment = "Not Implemented";
        $jobOpenings = "Not Implemented";
      ?>
		</head>
    <body>
      <!--
			{{#if loggedIn}}
        {{> navbarlogout }}
      {{else}}
        {{> navbar}}
      {{/if}}
      -->
      <?php include 'partials/navbarcombined.php';?>

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
                              <?php include 'partials/icons.php'; ?>
                            </div>
                          </div>
                        </div>

		</body>
</html>
