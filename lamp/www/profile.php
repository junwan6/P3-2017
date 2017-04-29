<!DOCTYPE HTML>
<html>
<head>
  <!-- {{> global_header }} -->
  <?php include "partials/global_header.php" ?>
	<script type="text/javascript" language="javascript" src="static/profile.js"></script>
	<link type="text/css" rel="stylesheet" href="static/profile.css">


	<title>
		PPP
	</title>
</head>
<body>
    <!--
	  {{#if loggedIn}}
        {{> navbarlogout }}
    {{else}}
        {{> navbar}}
    {{/if}}
    -->
      <?php
        include 'partials/navbarcombined.php');
      ?>      

        <div class="container-fluid">
	  <div class="row">
	    <div class="col-md-10 col-md-offset-1">
              <div class="box">
                <div id="pageTitle">
                  My Profile
                </div>

                <div class="container-fluid">
                  <div class="row">

                    <div class="col-md-6">
	              <p class="heading"> Careers I Like</p>
                      <div class="scrollable">
                        <!--
                        {{#each likedCareers}}
                        <a class="careerLink" href="/career/{{this.soc}}/video">{{this.title}}</a><br>
                        {{else}}
                        <p class="noneText">None</p><br>
                        {{/each}}
                        -->
                        <?php
                          foreach ($likedCareers as $lcareer){
                            echo '<a class="careerLink" href="career/' .
                              $lcareer['soc'] . '/video">' . $lcareer['title'] . '</a><br>';
                          }
                          if is_null($likedCareers){
                            echo '<p class="noneText">None</p><br>';
                          }
                        ?>
                      </div>

	              <p class="heading"> Careers I Don't Like:</p>
                      <div class="scrollable">
                        <!--
                        {{#each dislikedCareers}}
                        <a class="careerLink" href="/career/{{this.soc}}/video">{{this.title}}</a><br>
                        {{else}}
                        <p class="noneText">None</p><br>
                        {{/each}}
                        -->
                        <?php
                          foreach ($dislikedCareers as $dlcareer){
                            echo '<a class="careerLink" href="career/' .
                              $dlcareer['soc'] . '/video">' . $dlcareer['title'] . '</a><br>';
                          }
                          if is_null($dislikedCareers){
                            echo '<p class="noneText">None</p><br>';
                          }
                        ?>
                      </div>

                <p class="heading">Careers I'm Unsure About:</p>
                      <div class="scrollable">
                        <!--
                        {{#each neutralCareers}}
                        <a class="careerLink" href="/career/{{this.soc}}/video">{{this.title}}</a><br>
                        {{else}}
                        <p class="noneText">None</p><br>
                        {{/each}}
                        -->
                        <?php
                          foreach ($neutralCareers as $ncareer){
                            echo '<a class="careerLink" href="career/' .
                              $ncareer['soc'] . '/video">' . $ncareer['title'] . '</a><br>';
                          }
                          if is_null($neutralCareers){
                            echo '<p class="noneText">None</p><br>';
                          }
                        ?>
                      </div>
	            </div>

	            <div class="col-md-6 centered">
	              <p class="heading">My World of Work Map</p>
                      <div>
                        <canvas id="occupationPlotter"></canvas>
	                <img id="wowImage" src="images/wow.jpg" alt="World of Work" align="center" width="100%" height="auto">
                      </div>
	            </div>

                  </div>
                </div>
              </div>
            </div>
	  </div>
        </div>

        <p id="hoverOccupationTitle"></p>

        <div class="hidden">
          <!--
          {{#each likedCareers}}
          <career type="like" title="{{title}}" soc="{{soc}}" x="{{x}}" y="{{y}}"></career>
          {{/each}}

          {{#each dislikedCareers}}
          <career type="dislike" title="{{title}}" soc="{{soc}}" x="{{x}}" y="{{y}}"></career>
          {{/each}}

          {{#each neutralCareers}}
          <career type="neutral" title="{{title}}" soc="{{soc}}" x="{{x}}" y="{{y}}"></career>
          {{/each}}
          -->
          <?php
            foreach ($likedCareers as $lcareer){
              echo '<career type="like" title="' . $lcareer['title'] . '" soc="' .
                $lcareer['soc'] . '" x="' . $lcareer['x'] . '" y="' . $lcareer['y'] . '"></career>';
            }
            foreach ($dislikedCareers as $dlcareer){
              echo '<career type="like" title="' . $dlcareer['title'] . '" soc="' .
                $dlcareer['soc'] . '" x="' . $dlcareer['x'] . '" y="' . $dlcareer['y'] . '"></career>';
            }
            foreach ($neutralCareers as $ncareer){
              echo '<career type="like" title="' . $ncareer['title'] . '" soc="' .
                $ncareer['soc'] . '" x="' . $ncareer['x'] . '" y="' . $ncareer['y'] . '"></career>';
            }
          ?>
        </div>
</body>
</html>
