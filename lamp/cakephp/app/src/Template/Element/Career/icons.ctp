<div class="box">

  <div id="iconContainer">

    <div id="videoCategory" class="category" onclick="changeFocus('video')">
      <div id="videoSegment" class="iconSegment">
        <a>
        Video
        <br>
          <i id="video" class="fa fa-video-camera fa-3x icon" aria-hidden="true"></i>
        </a>
      </div>
    </div>

    <div id="salaryCategory" class="category" onclick="changeFocus('salary')">
      <div id="salaryDialog" class="dialog">
        <?php
          echo 'Average U.S. ';
          echo ($wageTypeIsAnnual)?'Salary <br> ' . $averageWage . ' per year'
            :'Wage <br> ' . $averageWage . ' per hour';
        ?>
      </div>
      <div id="salarySegment" class="iconSegment" data-dialog-trigger="#salaryDialog">
        <a>
        Salary
        <br>
          <i id="salary" class="fa fa-usd fa-3x icon" aria-hidden="true"></i>
        </a>
      </div>
    </div>

    <div id="educationCategory" class="category" onclick="changeFocus('education')">
      <div id="educationDialog" class="dialog">
        <!-- {{educationRequired}} -->
        <?php echo $educationRequired; ?>
      </div>
      <div id="educationSegment" class="iconSegment" data-dialog-trigger="#educationDialog">
        <a>
        Education
        <br>
          <i id="education" class="fa fa-graduation-cap fa-3x icon" aria-hidden="true"></i>
        </a>
      </div>
    </div>

    <div id="skillsCategory" class="category" onclick="changeFocus('skills')">
      <div id="skillsDialog" class="dialog">
        <?php
          echo ($skillsArray)?
            '<div id="skillsPieChart" style="width: 300px; height: 300px; margin: 0 auto"></div>':
            'Information for this career is not in the database yet.';
        ?>
      </div>
      <div id="skillsSegment" class="iconSegment" data-dialog-trigger="#skillsDialog">
        <a>
        Skills
        <br>
          <i id="skills" class="fa fa-clipboard fa-3x icon" aria-hidden="true"></i>
        </a>
      </div>
    </div>

    <div id="careerOutlookCategory" class="category" onclick="changeFocus('outlook')">
      <div id="careerOutlookDialog" class="dialog">
        Annual Employment Increase
        <br>
        <!-- {{careerGrowth}} -->
        <?php echo $careerGrowth; ?>
      </div>
      <div id="careerOutlookSegment" class="iconSegment" data-dialog-trigger="#careerOutlookDialog">
        <a>
        Career Outlook
        <br>
          <i id="careerOutlook" class="fa fa-sun-o fa-3x icon" aria-hidden="true"></i>
        </a>
      </div>
    </div>

    <div id="worldOfWorkCategory" class="category" onclick="changeFocus('world-of-work')">
      <div id="worldOfWorkDialog" class="dialog">
        World of Work Info
      </div>
      <div id="worldOfWorkSegment" class="iconSegment" data-dialog-trigger="#worldOfWorkDialog">
        <a>
        World of Work
        <br>
          <i id="worldOfWork" class="fa fa-globe fa-3x icon" aria-hidden="true"></i>
        </a>
      </div>
    </div>
  </div>
</div>
