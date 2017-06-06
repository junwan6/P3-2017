<!DOCTYPE HTML>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php
      echo $this->Html->script([
        'Career/career.js',
        'Career/icons.js',
        'johndyer-mediaelement-8adf73f/build/mediaelement-and-player.min.js',
        'mediaelement-playlist-plugin-master/_build/mediaelement-playlist-plugin.min.js',
        'https://code.highcharts.com/highcharts.js',
        'https://code.highcharts.com/modules/data.js',
        'https://code.highcharts.com/modules/exporting.js'
      ]);
      echo $this->Html->css([
        'Career/career.css',
        'Career/icons.css',
        '../js/johndyer-mediaelement-8adf73f/build/mediaelementplayer.css',
        '../js/mediaelement-playlist-plugin-master/_build/mediaelement-playlist-plugin.min.css'
      ]);
      
      $states = array('NAT', 'AK', 'AL', 'AR', 'AZ', 'CA', 'CO', 'CT', 'DC', 'DE', 'FL', 'GA', 'HI', 'IA', 'ID', 'IL', 'IN', 'KS', 'KY', 'LA', 'MA', 'MD', 'ME', 'MI', 'MN', 'MO', 'MS', 'MT', 'NC', 'ND', 'NE', 'NH', 'NJ', 'NM', 'NV', 'NY', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VA', 'VT', 'WA', 'WI', 'WV', 'WY');
      $statename = array("NAT"=>"National Average","AL"=>"Alabama","AK"=>"Alaska","AZ"=>"Arizona","AR"=>"Arkansas","CA"=>"California","CO"=>"Colorado","CT"=>"Connecticut","DE"=>"Delaware","FL"=>"Florida","GA"=>"Georgia","HI"=>"Hawaii","ID"=>"Idaho","IL"=>"Illinois","IN"=>"Indiana","IA"=>"Iowa","KS"=>"Kansas","KY"=>"Kentucky","LA"=>"Louisiana","ME"=>"Maine","MD"=>"Maryland","MA"=>"Massachusetts","MI"=>"Michigan","MN"=>"Minnesota","MS"=>"Mississippi","MO"=>"Missouri","MT"=>"Montana","NE"=>"Nebraska","NV"=>"Nevada","NH"=>"New Hampshire","NJ"=>"New Jersey","NM"=>"New Mexico","NY"=>"New York","NC"=>"North Carolina","ND"=>"North Dakota","OH"=>"Ohio","OK"=>"Oklahoma","OR"=>"Oregon","PA"=>"Pennsylvania","RI"=>"Rhode Island","SC"=>"South Carolina","SD"=>"South Dakota","TN"=>"Tennessee","TX"=>"Texas","UT"=>"Utah","VT"=>"Vermont","VA"=>"Virginia","WA"=>"Washington","WV"=>"West Virginia","WI"=>"Wisconsin","WY"=>"Wyoming","AS"=>"American Samoa","DC"=>"District of Columbia","FM"=>"Federated States of Micronesia","GU"=>"Guam","MH"=>"Marshall Islands","MP"=>"Northern Mariana Islands","PW"=>"Palau","PR"=>"Puerto Rico","VI"=>"Virgin Islands","AA"=>"Armed Forces Americas","AE"=>"Armed Forces (Other)","AP"=>"Armed Forces Pacific");
      ?>
    <title>
      PPP
    </title>
      <table id="careerTable">
        <?php
          if (isset($jobOpenings)){
            foreach ($states as $st){
              echo '<!-- ' . $st . ' --><tr>';
              if (in_array($st, $sts)){
                echo '<td>' . $avg[$st] . '</td>';
                echo '<td>' . $lo[$st] . '</td>';
                echo '<td>' . $med[$st] . '</td>';
                echo '<td>' . $hi[$st] . '</td>';
              } else {
                echo '<td><td><td><td></td></td></td></td>';
              }
              echo '</tr>';
            }
          }
          ?>
      </table>
      <?php
        if (isset($skillsArray)){
          echo '<div id="skillsArray">';
          echo $skillsArray;
          echo '</div>';
        }
      ?>

  </head>
  <body>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-9 col-md-offset-1">
          <div class="box" id="whiteBox">
            <?php
              $pages = ['video' => ['videos'],
                'salary' => ['states', 'statename', 'sts'],
                'education' => ['states', 'statename',
                  'typeOfSchool', 'typeOfDegree', 'yearsInSchool',
                  'yearsInUndergrad', 'yearsInGrad', 'gradSchool', 'sts'],
                'outlook' => ['careerGrowth', 'occupationTitle',
                  'currentEmployment', 'futureEmployment', 'jobOpenings'],
                'skills' => ['skillsArray'],
                'world-of-work' => ['soc', 'realistic', 'investigative',
                      'artistic', 'social', 'enterprising', 'conventional']
              ];
              
              foreach ($pages as $page => $vars){
                echo '<div class="' . ($focus==$page?'':'in') .
                  'active-body" id="' . $page . '-body">';
                  
                echo '<div id="pageTitle">' .
                  ucwords(str_replace(['-','_'], ' ', $page)) . '</div>';
                echo '<div id="careerTitle">' . $occupationTitle . '</div>';
                
                $localVars = get_defined_vars();
                $varsToPass = array_combine($vars, array_map(
                  function($e) use ($localVars){
                    return $localVars[$e];
                  }
                , $vars));
                echo $this->element('Career/' . $page, $varsToPass);
                  
                echo '</div>';
              }
            ?>
          </div>
        </div>
        <div class="col-md-2">
          <?php
            echo $this->element('Career/icons', [
              'occupationTitle' => $occupationTitle,
              'wageTypeIsAnnual' => $wageTypeIsAnnual,
              'averageWage' => $averageWage,
              'careerGrowth' => $careerGrowth,
              'educationRequired' => $educationRequired
            ]);
            ?>
        </div>
      </div>
    </div>
  </body>
</html>
