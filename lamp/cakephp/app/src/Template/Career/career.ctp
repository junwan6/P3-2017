<!DOCTYPE HTML>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php
      echo $this->Html->script([
        'career.js',
        'johndyer-mediaelement-8adf73f/build/mediaelement-and-player.min.js',
        'mediaelement-playlist-plugin-master/_build/mediaelement-playlist-plugin.min.js',
        'https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js',
        'https://code.highcharts.com/highcharts.js',
        'https://code.highcharts.com/modules/data.js',
        'https://code.highcharts.com/modules/exporting.js'
      ]);
      echo $this->Html->css([
        'career.css',
        'johndyer-mediaelement-8adf73f/build/mediaelementplayer.css',
        'mediaelement-playlist-plugin-master/_build/mediaelement-playlist-plugin.min.css'
      ]);
      
      $states = array('NAT', 'AK', 'AL', 'AR', 'AZ', 'CA', 'CO', 'CT', 'DC', 'DE', 'FL', 'GA', 'HI', 'IA', 'ID', 'IL', 'IN', 'KS', 'KY', 'LA', 'MA', 'MD', 'ME', 'MI', 'MN', 'MO', 'MS', 'MT', 'NC', 'ND', 'NE', 'NH', 'NJ', 'NM', 'NV', 'NY', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VA', 'VT', 'WA', 'WI', 'WV', 'WY');
      $statename = array("NAT"=>"National Average","AL"=>"Alabama","AK"=>"Alaska","AZ"=>"Arizona","AR"=>"Arkansas","CA"=>"California","CO"=>"Colorado","CT"=>"Connecticut","DE"=>"Delaware","FL"=>"Florida","GA"=>"Georgia","HI"=>"Hawaii","ID"=>"Idaho","IL"=>"Illinois","IN"=>"Indiana","IA"=>"Iowa","KS"=>"Kansas","KY"=>"Kentucky","LA"=>"Louisiana","ME"=>"Maine","MD"=>"Maryland","MA"=>"Massachusetts","MI"=>"Michigan","MN"=>"Minnesota","MS"=>"Mississippi","MO"=>"Missouri","MT"=>"Montana","NE"=>"Nebraska","NV"=>"Nevada","NH"=>"New Hampshire","NJ"=>"New Jersey","NM"=>"New Mexico","NY"=>"New York","NC"=>"North Carolina","ND"=>"North Dakota","OH"=>"Ohio","OK"=>"Oklahoma","OR"=>"Oregon","PA"=>"Pennsylvania","RI"=>"Rhode Island","SC"=>"South Carolina","SD"=>"South Dakota","TN"=>"Tennessee","TX"=>"Texas","UT"=>"Utah","VT"=>"Vermont","VA"=>"Virginia","WA"=>"Washington","WV"=>"West Virginia","WI"=>"Wisconsin","WY"=>"Wyoming","AS"=>"American Samoa","DC"=>"District of Columbia","FM"=>"Federated States of Micronesia","GU"=>"Guam","MH"=>"Marshall Islands","MP"=>"Northern Mariana Islands","PW"=>"Palau","PR"=>"Puerto Rico","VI"=>"Virgin Islands","AA"=>"Armed Forces Americas","AE"=>"Armed Forces (Other)","AP"=>"Armed Forces Pacific");
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
            <?php
              echo '<div class="' .
                ($focus!='video'?'in':'') .
                'active-body" id="video-body">';
            ?>
              <div id="pageTitle">
                Video
              </div>
              <div id="careerTitle">
                <?php echo $occupationTitle; ?>
              </div>
              <div id="video">
                <p id="jobtitle"> </p>
                <p id="vidtitle"></p>
                <div onclick="updateTitle()">
                  <?php
                    if (empty($videos)) 
                      echo '<h2>Sorry, no videos are currently available for this job.</h2>';
                    else {
                      echo '
                        <div id="video-wrapper" style="width:530px; height: 300px;">
                          <video 
                             autoplay="autoplay"
                             data-showplaylist="true" 
                             class="mep-playlist"
                             width="100%"
                             height="100%"
                             id="vidtag"
                             >';

                      foreach ($videos as $person){
                        foreach ($person['videos'] as $v){
                          $filePath = '../../vid/' . $v['fileName'];
                          echo '<source type="video/mp4" src="' . $filePath . 
                            '" title="' . $v['question'] . '" data-poster="track2.png">';
                        }
                      }
                    
                      echo '</video>

                        </div>';
                    } 
                  ?>
                  <div style="margin-left:720px; margin-top: 10px">
                    <a onclick="showNextCareerButton('up');updateRank('like')" value="Call2Functions" href="#vidup"><span class="upthumb" id="upthumb"></span></a>
                    <a onclick="showNextCareerButton('mid');updateRank('neutral')" value="Call2Functions" href="#vidmid"><span class="midthumb" id="midthumb"></span></a>
                    <a onclick="showNextCareerButton('down');updateRank('dislike')" value="Call2Functions" href="#viddown"><span class="downthumb" id="downthumb"></span></a><br>		      
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
                </div>
                <script type="text/javascript">
                  function collectFilters(){
                  	var form = document.getElementById('filters');
                  	var salary = Number(form.elements['salary'].value);
                  	var edu = Number(form.elements['education'].value);
                  
                  	var pname = window.location.pathname;
                  	var socPos = pname.search(/[0-9][0-9]-[0-9][0-9][0-9][0-9]/);
                  
                  	var soc = pname.substring(socPos, socPos+7);
                  
                  	$.get('career/filters?salary=' + salary + '&education=' + edu + '&soc=' + soc);
                  }
                  var player = new MediaElementPlayer('video',
                  {
                    "features": ['playlistfeature', 'prevtrack', 'playpause',
                      'nexttrack', 'current', 'progress', 'duration', 'volume',
                      'playlist', 'fullscreen', 'autoplay' ],
                  	"shuffle": false,
                  	"loop": true,
                  	"autoplay": true
                  });
                  addFullScreenOverlay();
                  updateTitle();
                </script>
              </div>
            </div>
            <?php
              echo '<div class="' .
                ($focus!='salary'?'in':'') .
                'active-body" id="salary-body">';
            ?>
              <div id="pageTitle">
                Salary
              </div>
              <div id="careerTitle">
                <?php echo $occupationTitle; ?>
              </div>
              <div id="salary-container" style="max-width: 600px; height: 400px; margin: 0 auto">
              </div>
              <p></p>
              <div id="salary-careerInformation">
                <table id="salary-careerTable">
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
              <div id="salary-careerInputs" class="col" style="width: 15%; margin: auto;">
                <div class="row inputRow">
                  <select id="salary-salaryStateInput" class="col form-control chartOption" style="display: table-cell; margin:auto;">
                  <?php
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
            <?php
              echo '<div class="' .
                ($focus!='education'?'in':'') .
                'active-body" id="education-body">';
            ?>
              <div id="pageTitle">
                Education
              </div>
              <div id="careerTitle">
                <?php echo $occupationTitle; ?>
              </div>
              <div id="education-contentContainer">
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
              </div>
              <div id="education-careerInformation">
                <div id="yearsInUndergrad">
                  <?php echo $yearsInUndergrad; ?>
                </div>
                <div id="yearsInGrad">
                  <?php echo  $yearsInGrad; ?>
                </div>
                <table id="undergradTable">
                  <tr>
                    <td>9410</td>
                    <td>10138</td>
                    <td>19548</td>
                  </tr>
                  <tr>
                    <td>32405</td>
                    <td>11516</td>
                    <td>43921</td>
                  </tr>
                </table>
                <table id="gradTable">
                  <tr>
                    <td>10725</td>
                  </tr>
                  <tr>
                    <td>22607</td>
                  </tr>
                </table>
                <table id="education-careerTable">
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
                <div id="education-careerInputs" class="col" style="display: table-cell">
                  Career
                  <div class="row inputRow" style="display: table">
                    <select id="education-salaryStateInput" class="col form-control chartOption" style="display: table-cell">
                    <?php
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
            <?php
              echo '<div class="' .
                ($focus!='outlook'?'in':'') .
                'active-body" id="outlook-body">';
            ?>
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
            <?php
              echo '<div class="' .
                ($focus!='skills'?'in':'') .
                'active-body" id="skills-body">';
            ?>
              <div id="pageTitle">
                Skills
              </div>
              <div id="careerTitle">
                <?php echo $occupationTitle; ?>
              </div>
              <div id="skills-contentContainer">
                <br>
                <?php
                  if (!isset($skillsArray)){
                    echo '<div class="intelligenceTitle">';
                    echo '  Information for this career is not in the database yet.';
                    echo '</div>';
                  } else {
                    echo '<div id="mainSkillsPieChart" style="width: 500px; height: 500px; margin: 0 auto"></div>';
                  }
                  ?>
              </div>
            </div>
            <?php
              echo '<div class="' .
                ($focus!='world-of-work'?'in':'') .
                'active-body" id="world_of_work-body">';
            ?>
              <div id="pageTitle">
                <br>
                World of Work
              </div>
              <div id="careerTitle">
                <?php echo $occupationTitle; ?>
                <div>
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
        <div class="col-md-2">
          <?php
            echo $this->element('career_icons', [
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
