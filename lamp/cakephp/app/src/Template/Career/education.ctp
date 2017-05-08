<!DOCTYPE HTML>
<html>
		<head>

      <?php
        echo $this->Html->script([
          'education.js',
          'https://code.highcharts.com/highcharts.js',
          'https://code.highcharts.com/modules/data.js',
          'https://code.highcharts.com/modules/exporting.js',
        ]);
        echo $this->Html->css([
          'education.css'
        ]);
      ?>

			<title>
				PPP
      </title>
      <?php
        $states = array('NAT', 'AK', 'AL', 'AR', 'AZ', 'CA', 'CO', 'CT', 'DC', 'DE', 'FL', 'GA', 'HI', 'IA', 'ID', 'IL', 'IN', 'KS', 'KY', 'LA', 'MA', 'MD', 'ME', 'MI', 'MN', 'MO', 'MS', 'MT', 'NC', 'ND', 'NE', 'NH', 'NJ', 'NM', 'NV', 'NY', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VA', 'VT', 'WA', 'WI', 'WV', 'WY');
      ?>
		</head>
    <body>
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-9 col-md-offset-1">
            <div class="box">
			        <div id="pageTitle">
				  Education
			        </div>
			        <div id="careerTitle">
          <?php echo $occupationTitle; ?>
			        </div>

              <div id="contentContainer">
          <!-- WARNING: JAVASCRIPT TAKES CONTENTS OF <div> TAG INCLUDING COMMENTS-->
				  <div class="educationLine">
				    <div class="educationCategory">
				      Type of School:
				    </div>
				    <div id="typeOfSchool" class="educationValue">
            <?php echo $typeOfSchool; ?>
				    </div>
				  </div>
                                  
				  <div class="educationLine">
				    <div class="educationCategory">
				      Type of Degree:
				    </div>
            <div id="typeOfDegree" class="educationValue">
            <?php echo $typeOfDegree; ?>
				    </div>
				  </div>
                                  
				  <div class="educationLine">
				    <div class="educationCategory">
				      Years in School:
				    </div>
				    <div id="yearsInSchool" class="educationValue">
              <?php echo $yearsInSchool; ?>
				    </div>
				  </div>

			          <!--
				      <div class="educationLine">
				        <div class="educationCategory">
				          Other Information:
				        </div>
				        <div class="educationValue">
				          Teaching Credential
				        </div>
				      </div>
				      -->
                    </div>

                    <div id="careerInformation">
                    <div id="yearsInUndergrad">
                      <?php echo $yearsInUndergrad; ?>
                    </div>
                    <div id="yearsInGrad">
                      <?php echo  $yearsInGrad; ?>
                    </div>

                    	<table id="undergradTable">
                    		<tr> <!--public-->
                    			<td>9410</td>
                    			<td>10138</td>
                    			<td>19548</td>
                    		</tr>
                    		<tr> <!--private-->
                    			<td>32405</td>
                    			<td>11516</td>
                    			<td>43921</td>
                    		</tr>
                    	</table>

                    	<table id="gradTable">
                    		<tr> <!--public-->
                    			<td>10725</td>
                    		</tr>
                    		<tr> <!--private-->
                    			<td>22607</td>
                    		</tr>
                    	</table>

                      <table id="careerTable">
                        <?php
                          foreach ($states as $st){
                            echo '<tr>';
                            if (isset(${$st})){
                              echo '<td>' . ${$st . 'Avg'} . '</td>';
                              echo '<td>' . ${$st . 'Lo'} . '</td>';
                              echo '<td>' . ${$st . 'Med'} . '</td>';
                              echo '<td>' . ${$st . 'Hi'} . '</td>';
                            } else {
                              echo '<td><td><td><td></td></td></td></td>';
                            }
                            echo '</tr>';
                          }
                        ?>
                      </table>

                    </div>

		            <div id="chartContainer" style="width: 450px; height: 450px; margin: 0 auto"></div>

		            <div id="input-container" class="row" style="display: table">
		                <div id="undergraduateInputs" class="col" style="display: table-cell">
		                	Undergraduate
		                	<div class="row inputRow" style="display: table">
		                		<select id="undergradPublicPrivateInput" class="col form-control chartOption" style="display: table-cell">
		                			<option value="public">Public In-State</option>
		                			<option value="private">Private Nonprofit</option>
		                		</select>
		                		<select id="undergradTuitionRoomBoardInput" class="col form-control chartOption" style="display: table-cell">
		                			<option value="tuition">Tuition</option>
									<option value="roomBoard">Room/Board</option>
									<option value="tuitionRoomBoard">Tuition, Room/Board</option>
		                		</select>
		                	</div>
		                	<div id="undergraduateCostDisplay"></div>
		                </div>

                    <?php if ($gradSchool){ ?>
		                <div id="graduateInputs" class="col" style="display: table-cell">
		                	Graduate
		                	<div class="row inputRow" style="display: table">
		                		<select id="gradPublicPrivateInput" class="col form-control chartOption" style="display: table-cell">
		                			<option value="public">Public</option>
		                			<option value="private">Private</option>
		                		</select>
		                		<select class="col form-control chartOption" style="display: table-cell">
		                			<option value="tuition">Tuition</option>
		                		</select>
		                	</div>
		                	<div id="graduateCostDisplay"></div>
		                </div>
                    <?php } ?>

		                <div id="careerInputs" class="col" style="display: table-cell">
		                	Career
		                	<div class="row inputRow" style="display: table">
		                		<select id="salaryStateInput" class="col form-control chartOption" style="display: table-cell">
                    <?php
                      // Converted from "www.50states.com/abbreviations.htm"
                        $statename = array(
                        "NAT"=>"National Average",
                        "AL"=>"Alabama",
                        "AK"=>"Alaska",
                        "AZ"=>"Arizona",
                        "AR"=>"Arkansas",
                        "CA"=>"California",
                        "CO"=>"Colorado",
                        "CT"=>"Connecticut",
                        "DE"=>"Delaware",
                        "FL"=>"Florida",
                        "GA"=>"Georgia",
                        "HI"=>"Hawaii",
                        "ID"=>"Idaho",
                        "IL"=>"Illinois",
                        "IN"=>"Indiana",
                        "IA"=>"Iowa",
                        "KS"=>"Kansas",
                        "KY"=>"Kentucky",
                        "LA"=>"Louisiana",
                        "ME"=>"Maine",
                        "MD"=>"Maryland",
                        "MA"=>"Massachusetts",
                        "MI"=>"Michigan",
                        "MN"=>"Minnesota",
                        "MS"=>"Mississippi",
                        "MO"=>"Missouri",
                        "MT"=>"Montana",
                        "NE"=>"Nebraska",
                        "NV"=>"Nevada",
                        "NH"=>"New Hampshire",
                        "NJ"=>"New Jersey",
                        "NM"=>"New Mexico",
                        "NY"=>"New York",
                        "NC"=>"North Carolina",
                        "ND"=>"North Dakota",
                        "OH"=>"Ohio",
                        "OK"=>"Oklahoma",
                        "OR"=>"Oregon",
                        "PA"=>"Pennsylvania",
                        "RI"=>"Rhode Island",
                        "SC"=>"South Carolina",
                        "SD"=>"South Dakota",
                        "TN"=>"Tennessee",
                        "TX"=>"Texas",
                        "UT"=>"Utah",
                        "VT"=>"Vermont",
                        "VA"=>"Virginia",
                        "WA"=>"Washington",
                        "WV"=>"West Virginia",
                        "WI"=>"Wisconsin",
                        "WY"=>"Wyoming",
                        "AS"=>"American Samoa",
                        "DC"=>"District of Columbia",
                        "FM"=>"Federated States of Micronesia",
                        "GU"=>"Guam",
                        "MH"=>"Marshall Islands",
                        "MP"=>"Northern Mariana Islands",
                        "PW"=>"Palau",
                        "PR"=>"Puerto Rico",
                        "VI"=>"Virgin Islands",
                        //"AE"=>"Armed Forces Africa",
                        "AA"=>"Armed Forces Americas",
                        //"AE"=>"Armed Forces Canada",
                        //"AE"=>"Armed Forces Europe",
                        //"AE"=>"Armed Forces Middle East",
                        "AE"=>"Armed Forces (Other)",
                        "AP"=>"Armed Forces Pacific"
                      );
                      if (!isset($_GET['st'])){
                        $_GET['st'] = 'NAT';
                      }
                      foreach ($states as $st){
                        if (isset(${$st})){
                          echo '<option value ="' . $st . '"' . (($st == $_GET['st'])?' selected="selected"':'')  . '>' . $statename[$st] . '</option>';
                        }
                      }
                    ?>
								</select>
		                		<select id="salaryPositionInput" class="col form-control chartOption" style="display: table-cell">
		                			<option value="average">Average Salary</option>
		                			<option value="low">Low Salary</option>
		                			<option value="median">Median Salary</option>
		                			<option value="high">High Salary</option>
		                		</select>
		                	</div>
		                	<div id="careerSalaryDisplay"></div>
		                </div>
		             </div> 

              </div>
            </div>
            <div class="col-md-2">
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
		</body>
</html>
