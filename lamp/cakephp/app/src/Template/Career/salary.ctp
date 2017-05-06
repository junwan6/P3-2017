<!DOCTYPE HTML>
<html>
<head>
  <!-- {{> global_header }} -->
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
            <!-- {{occupationTitle}} -->
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
                  echo '<td>' . ${$st . 'Avg'} . '</td>';
                  echo '<td>' . ${$st . 'Lo'} . '</td>';
                  echo '<td>' . ${$st . 'Med'} . '</td>';
                  echo '<td>' . ${$st . 'Hi'} . '</td>';
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
                  if (is_null($_GET['st'])){
                    $_GET['st'] = 'NAT';
                  }
                  // Converted from "www.50states.com/abbreviations.htm"
                  foreach ($states as $st){
                    if (${$st} == true){
                      echo '<option value ="' . $st . '"' . (($st == $_GET['st'])?' selected="selected"':'')  . '>' . $statename[$st] . '</option>';
                    }
                  }
                ?>
              </select>
						</div>
						
					</div>
				</div> 
			</div>

			<div class="col-md-2">
        <!-- {{> icons }} -->
        <?php echo $this->element('icons'); ?>
			</div>

		</div>
	</div>

	<!-- <div id="chartContainer" style="height: 300px; width: 100%;"></div> -->

</body>
</html>
