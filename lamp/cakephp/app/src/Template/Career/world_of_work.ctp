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
      <?php
        //TODO: Fill in following variables from the NodeJS serverside scripts:
        // controllers/occupation-controller.js
        // models/occupation.js
        $occupationTitle = "Not Implemented";
        $noData = true;
        $soc = "Not Implemented";
        $realistic = "Not Implemented";
        $investigative = "Not Implemented";
        $artistic = "Not Implemented";
        $social = "Not Implemented";
        $enterprising = "Not Implemented";
        $conventional = "Not Implemented";
      ?>
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

			<div class="col-md-2">
        <!-- {{> icons }} -->
        <?php echo $this->element('icons'); ?>
			</div>

		</div>
	</div>

</body>
</html>
