<!DOCTYPE HTML>
<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <!-- {{> global_header }} -->
      <?php
      echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js');
      echo $this->Html->css([
        'johndyer-mediaelement-8adf73f/build/mediaelementplayer.css',
        'mediaelement-playlist-plugin-master/_build/mediaelement-playlist-plugin.min.css',
        'video.css',
        'gagiktest1.css'
      ]);
      ?>
      <style>
        //body { display: none;}
        .container-fluid { display: none;}
      </style>
			<title>
				PPP
			</title>
  <?php
      // Videos:
      //  25-2022: Amy
      //  25-2052: Melody
      //  25-1011: Todd
      //  PLACEHOLDERS BELOW
      //  11-1031: Sara
      //  11-2011: Miguel
      // majority of work to be done in static/js/vidmain.js
      // Array/Dict passed from controller: $videos
      // ex.
      //[
      //  (int) 0 => [
      //    'name' => 'Amy',
      //    'videos' => [
      //      (int) 1 => [
      //        'question' => 'What do you do as a teacher',
      //        'fileName' => '11-1011_0_Amy/1 What do you do as a teacher.m4v'
      //      ],
      //      (int) 2 => [
      //        'question' => 'What skills have led you to this job that you are so passionate about',
      //        'fileName' => '11-1011_0_Amy/2 What skills have led you to this job that you are so passionate about.mp4'
      //      ],
      //      (int) 3 => [
      //        'question' => 'What makes you excited to come to work',
      //        'fileName' => '11-1011_0_Amy/3 What makes you excited to come to work.mp4'
      //      ],
      //      (int) 4 => [
      //        'question' => 'Please explain a time when you've experienced passion for your job',
      //        'fileName' => '11-1011_0_Amy/4 Please explain a time when you've experienced passion for your job.mp4'
      //      ],
      //      (int) 5 => [
      //        'question' => 'What are one or two things that you have done that make you most proud of your work',
      //        'fileName' => '11-1011_0_Amy/5 What are one or two things that you have done that make you most proud of your work.mp4'
      //      ],
      //      (int) 6 => [
      //        'question' => 'What are the things you love the most about your career',
      //        'fileName' => '11-1011_0_Amy/6 What are the things you love the most about your career.mp4'
      //      ],
      //      (int) 7 => [
      //        'question' => 'Tell me about a specific instance when you were fully absorbed in your work',
      //        'fileName' => '11-1011_0_Amy/7 Tell me about a specific instance when you were fully absorbed in your work.mp4'
      //      ],
      //      (int) 8 => [
      //        'question' => 'Are there any other ways that your work is meaningful to you',
      //        'fileName' => '11-1011_0_Amy/8 Are there any other ways that your work is meaningful to you.mp4'
      //      ],
      //      (int) 9 => [
      //        'question' => 'What are some of your favorite things about being a teacher',
      //        'fileName' => '11-1011_0_Amy/9 What are some of your favorite things about being a teacher.mp4'
      //      ],
      //      (int) 10 => [
      //        'question' => 'What do you want to be remembered for in your line of work',
      //        'fileName' => '11-1011_0_Amy/10 What do you want to be remembered for in your line of work.mp4'
      //      ]
      //    ]
      //  ]
      //]
      ?>
		</head>
    <body id="activeMain">
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-md-9 col-md-offset-1">
                              <div class="box">
			        <div id="careerTitle">
                <!-- {{occupationTitle}} -->
                <?php echo $occupationTitle; ?>
			        </div>
			        <div id="video">
				  <p id="jobtitle"> </p>
				  <p id="vidtitle"></p>
				  <div onclick="updateTitle()">
				  <div id="video-wrapper" style="width:530px; height: 300px;">
				    <video 
				       autoplay="autoplay"
				       data-showplaylist="true" 
				       class="mep-playlist"
				       width="100%"
				       height="100%"
				       id="vidtag"
               >
              <?php
              foreach ($videos as $person){
                foreach ($person['videos'] as $v){
                  $filePath = '../../vid/' . $v['fileName'];
                  echo '<source type="video/mp4" src="' . $filePath . 
                    '" title="' . $v['question'] . '" data-poster="track2.png">';
                }
              }
              ?>
				    </video>

				</div>
				    <div style="margin-left:720px; margin-top: 10px">

				      <a onclick="showNextCareerButton('up');updateRank('like')" value="Call2Functions" href="#vidup"><span class="upthumb"></span></a>
				      <a onclick="showNextCareerButton('mid');updateRank('neutral')" value="Call2Functions" href="#vidmid"><span class="midthumb"></span></a>
				      <a onclick="showNextCareerButton('down');updateRank('dislike')" value="Call2Functions" href="#viddown"><span class="downthumb"></span></a><br>		      
				      <div id="next-career"><a href="/career/nextcareer"><span class="next-career">Next Career  >></span></a></div>

				    </div>

				   	<div style=" padding-bottom: 0px">
				    <p><font size="3" color = "blue">Filters</font></p>  
				    </div>

				    <select name = "salary" id = "salary" form = "filters">
					  <option value="">Any Salary</option>
					  <option value="1">&lt;$40,000</option>
					  <option value="2">$40,000-$60,000</option>
					  <option value="3">$60,000-$80,000</option>
					  <option value="4">&gt;$100,000</option>
					</select>

					 <select name = "education" id = "education" form = "filters">
					 <option value="">Any Education Level</option>
					  <option value="6">Bachelor's Degree</option>
					  <option value="7">Master's Degree</option>
					  <option value="8">Doctorate</option>
					</select>

					<div style=" padding-top: 20px">
				      <form id = "filters">
				   	</form>
				   	</div>

				   	<button class="filter_button" onclick = "collectFilters()">Update Filters</button>
				   	
				   	<script type="text/javascript">
				   		function collectFilters(){
				   			var form = document.getElementById('filters');
    						var salary = Number(form.elements['salary'].value);
    						var edu = Number(form.elements['education'].value);

    						var pname = window.location.pathname;
        					var socPos = pname.search(/[0-9][0-9]-[0-9][0-9][0-9][0-9]/);
        					//var soc = pname.substring(socPos, socPos+2).concat(pname.substring(socPos+2, socPos+7));
        					var soc = pname.substring(socPos, socPos+7);
    						//console.log("salary and edu");
    						//console.log(salary);
    						//console.log(edu);
    						$.get('career/filters?salary=' + salary + '&education=' + edu + '&soc=' + soc);

				   		}
				   	</script>

            <?php
              echo $this->Html->script([
//                'vidMain.js',
                'johndyer-mediaelement-8adf73f/build/mediaelement-and-player.min.js',
                'mediaelement-playlist-plugin-master/_build/mediaelement-playlist-plugin.min.js'
              ]);
            ?>


				    <script>
				        
						// video playlist
					$('video.mep-playlist').mediaelementplayer({
						"features": ['playlistfeature', 'prevtrack', 'playpause', 'nexttrack', 'current', 'progress', 'duration', 'volume', 'playlist', 'fullscreen', 'autoplay' ],
						"shuffle": false,
						"loop": true,
						"autoplay": true
				
					});
				
					// regular video
					$('video:not(.mep-playlist)').mediaelementplayer({
						"features": ['playpause', 'current', 'progress', 'duration', 'tracks', 'volume', 'fullscreen', 'autoplay'],
						"autoplay": true
				
					});
					
					addFullScreenOverlay();
					updateTitle();
				
				    </script>
                                  </div>
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
<!DOCTYPE HTML>
<html>
<head>
  <?php
    echo $this->Html->script(array(
      "https://code.highcharts.com/highcharts.js",
      "https://code.highcharts.com/modules/data.js",
      "https://code.highcharts.com/modules/exporting.js"
    ));

    echo $this->Html->script('salary.js');
    echo $this->Html->css('salary.css');
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
						Salary
					</div>

					<div id="careerTitle">
            <?php echo $occupationTitle; ?>
					</div>

					<!-- <div> -->
					<!-- TECH DEBT: The container size at this point is hard-coded; might want it to be responsive at some point -->
					<div id="container" style="max-width: 600px; height: 400px; margin: 0 auto">
					</div>

					<p></p>

					<div id="careerInformation">
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

			<!-- Inline formatting, not CSS, seems to only work for centering -->
				<div id="careerInputs" class="col" style="width: 15%; margin: auto;">
						<div class="row inputRow">
							<select id="salaryStateInput" class="col form-control chartOption" style="display: table-cell; margin:auto;">
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
						</div>
						
					</div>
				</div> 
			</div>


		</div>
	</div>

	<!-- <div id="chartContainer" style="height: 300px; width: 100%;"></div> -->

</body>
</html>
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
			</div>
		</body>
</html>
<!DOCTYPE HTML>
<html>
		<head>
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
                <?php echo $occupationTitle; ?>
			        </div>
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
                              </div>
                            </div>
                          </div>
                        </div>

		</body>
</html>
<!DOCTYPE HTML>
<html>
		<head>
      <?php
      echo $this->Html->script('skills.js');
      echo $this->Html->css('skills.css');
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
				  Skills
			        </div>
                                
			        <div id="careerTitle">
          <!-- {{occupationTitle}} -->
          <?php echo $occupationTitle; ?>
			        </div>

                                
			        <div id="contentContainer">
                <br>
                <!--
			          {{#if skillsArray}}
			          {{else}}
			          <div class="intelligenceTitle">
			            Information for this career is not in the database yet.
			          </div>
                {{/if}}
                -->
                <?php
                  if (!isset($skillsArray)){
                    echo '<div class="intelligenceTitle">';
                    echo '  Information for this career is not in the database yet.';
                    echo '</div>';
                  } else {
                    echo '<div id="mainSkillsPieChart" style="width: 500px; height: 500px; margin: 0 auto"></div>';
                  }
                ?>
              <!-- Populated by skills.js -->
			        </div>
              </div>
            </div>
          </div>
        </div>
		</body>
</html>
<!DOCTYPE HTML>
<html>
<head>
  <!-- {{> global_header }} -->
  <!--
	<script type="text/javascript" language="javascript" src="static/icons.js"></script>
	<link type="text/css" rel="stylesheet" href="static/icons.css">

	<script type="text/javascript" language="javascript" src="static/worldOfWork.js"></script>
	<link type="text/css" rel="stylesheet" href="static/worldOfWork.css">
  -->
  <?php
    echo $this->Html->script('world_of_work.js');
    echo $this->Html->css('world_of_work.css');
  ?>

	<title>
		PPP
	</title>
</head>

<body>

	<!-- PAGE CODE HERE -->
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-9 col-md-offset-1">
				<div class="box">

					<div id="pageTitle">
						<br>
						World of Work
						<p></p>
					</div>

					<div id="careerTitle">
            <!-- {{occupationTitle}} -->
            <?php echo $occupationTitle; ?>
						<p></p>

					<!-- http://stackoverflow.com/questions/15639726/how-to-set-canvas-on-top-of-image-in-html -->
<!-- 					<div id="container">
						<a href="#" data-toggle="tooltip" title="Middle School Teacher">

							<canvas id="c"  style="z-index: 1;"></canvas>
							<canvas id="cover" style="z-index: 2;"></canvas>
							
							<img class='img' src="/images/wow.png" alt="World of Work" align="center" width="auto" height="auto">
							
							<canvas id="canvas" width="0px" height="0px"></canvas>
								
						</a>
					</div> -->					
        <div>
          <!--
                                  {{#if noData}}
                                  Sorry, we don't have World of Work data for this occupation.
                                  {{else}}
					<canvas id="d"  style="z-index: 1;"></canvas>
					<!- - <canvas id="cover" style="z-index: 2;"></canvas> - ->
					<div id="interestsTableDiv">
						<table id="interestsTable">
							<tr><td>{{soc}}</td></tr>
							<tr><td>{{realistic}}</td></tr>
							<tr><td>{{investigative}}</td></tr>
							<tr><td>{{artistic}}</td></tr>
							<tr><td>{{social}}</td></tr>
							<tr><td>{{enterprising}}</td></tr>
							<tr><td>{{conventional}}</td></tr>
						</table>
					</div>
                                  {{/if}}
          -->
          <?php
            if ($noData){
              echo 'Sorry, we don\'t have World of Work data for this occupation.';
            } else {
              echo '<canvas id="d" style="z-index: 1;"></canvas>';
              echo '<div id="interestsTableDiv">';
              echo '  <table id="interestsTable">';
              $interests = array('soc', 'realistic', 'investigative', 'artistic', 'social', 'enterprising', 'conventional');
              foreach ($interests as $int){
                echo '<tr><td>' . ${$int} . '</td></tr>';
              }
              echo '  </table>';
              echo '</div>';
            }
          ?>
				</div>

					</div>
				</div>
			</div>


		</div>
	</div>

</body>
</html>
