<!-- For chart -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
      <?php
        //TODO: Fill in following variables from the NodeJS serverside scripts:
        // any script used by "careerOutlook.php", "skills.php", "salary.php", "education.php"
        $skillsArray = null;
        $wageTypeIsAnnual = true;
        $averageWage = 0;
        $educationRequired = "Not Implemented";
        $careerGrowth = "Not Implemented";
      ?>
<!-- 
{{#if skillsArray}}
<div id="skillsArray">
  {{skillsArray}}
</div>
{{/if}}
-->
<?php
  if ($skillsArray){
    echo '<div id="skillsArray"';
    echo $skillsArray;
    echo '</div>';
  }
?>

<div class="box">

  <div id="iconContainer">

    <div id="videoCategory" class="category">
      <div id="videoSegment" class="iconSegment">
        Video
        <br>
        <a href="video.php">
          <i id="video" class="fa fa-video-camera fa-3x icon" aria-hidden="true"></i>
        </a>
      </div>
    </div>

    <div id="salaryCategory" class="category">
      <div id="salaryDialog" class="dialog">
        <!--
        {{#if wageTypeIsAnnual}}
        Average U.S. Salary
        <br>
        {{averageWage}} per year
        {{else}}
        Average U.S. Wage
        <br>
        {{averageWage}} per hour
        {{/if}}
        -->
        <?php
          echo 'Average U.S. ';
          echo ($wageTypeIsAnnual)?'Salary <br> per year':'Wage <br> per hour';
        ?>
      </div>
      <div id="salarySegment" class="iconSegment" data-dialog-trigger="#salaryDialog">
        Salary
        <br>
        <a href="salary.php">
          <i id="salary" class="fa fa-usd fa-3x icon" aria-hidden="true"></i>
        </a>
      </div>
    </div>

    <div id="educationCategory" class="category">
      <div id="educationDialog" class="dialog">
        <!-- {{educationRequired}} -->
        <?php echo $educationRequired; ?>
      </div>
      <div id="educationSegment" class="iconSegment" data-dialog-trigger="#educationDialog">
        Education
        <br>
        <a href="education.php">
          <i id="education" class="fa fa-graduation-cap fa-3x icon" aria-hidden="true"></i>
        </a>
      </div>
    </div>

    <div id="skillsCategory" class="category">
      <div id="skillsDialog" class="dialog">
        <!--
        {{#if skillsArray}}
        <div id="skillsPieChart" style="width: 300px; height: 300px; margin: 0 auto"></div>
        {{else}}
        Information for this career is not in the database yet.
        {{/if}}
        -->
        <?php
          echo ($skillsArray)?
            '<div id="skillsPieChart" style="width: 300px; height: 300px; margin: 0 auto"></div>':
            'Information for this career is not in the database yet.';
        ?>
      </div>
      <div id="skillsSegment" class="iconSegment" data-dialog-trigger="#skillsDialog">
        Skills
        <br>
        <a href="skills.php">
          <i id="skills" class="fa fa-clipboard fa-3x icon" aria-hidden="true"></i>
        </a>
      </div>
    </div>

    <div id="careerOutlookCategory" class="category">
      <div id="careerOutlookDialog" class="dialog">
        Annual Employment Increase
        <br>
        <!-- {{careerGrowth}} -->
        <?php echo $careerGrowth; ?>
      </div>
      <div id="careerOutlookSegment" class="iconSegment" data-dialog-trigger="#careerOutlookDialog">
        Career Outlook
        <br>
        <a href="outlook.php">
          <i id="careerOutlook" class="fa fa-sun-o fa-3x icon" aria-hidden="true"></i>
        </a>
      </div>
    </div>

    <div id="worldOfWorkCategory" class="category">
      <div id="worldOfWorkDialog" class="dialog">
        World of Work Info
      </div>
      <div id="worldOfWorkSegment" class="iconSegment" data-dialog-trigger="#worldOfWorkDialog">
        World of Work
        <br>
        <a href="world-of-work.php">
          <i id="worldOfWork" class="fa fa-globe fa-3x icon" aria-hidden="true"></i>
        </a>
      </div>
    </div>
  </div>
</div>
