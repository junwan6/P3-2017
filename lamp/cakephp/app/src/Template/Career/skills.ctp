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
