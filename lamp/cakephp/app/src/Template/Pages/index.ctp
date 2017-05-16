<!DOCTYPE HTML>
<html>
  <head>
    <!-- Replaced by Layouts/default.ctp -->
    <!-- {{> global_header }} -->
    <?php
      echo $this->Html->css('Pages/index.css');
    ?>
    <title>
      PPP
    </title>
    <?php
      // Serverside NodeJS scripts to convert:
      //   controller/temp-controller.js
      //   models/interfaceRatings.js    
      //TODO: Needed variables
      $firstName = "Not Implemented"; //TODO: query database for firstname
      $loggedIn = false;

      //TODO: Login attempts
      $success = false;
      $reason = "Not Implemented";
      $loginAttempt = false;
      $signUpAttempt = false;
    ?>
  </head>
  <body>

    <div id="content">
      <div id="imageContainer">
      </div>
        <!-- {{#if loggedIn}} -->
        <?php if ($loggedIn) { ?>
        <div id="contentContainer" class="container-fluid">
          <div class="row">
	    <p id="welcomeBox">
      Welcome back, <?php echo $firstName; ?><!-- {{ firstName }} -->!
	    </p>
          </div>
          <div class="row row-with-margins">
            <div class="col-md-6">
	      <div id="goToProfileContent" class="landingPageButton">
	        <a class="nostyle" href="profile">
	          View your profile
	        </a>
              </div>
            </div>
            <div class="col-md-6">
	      <div id="browseCareersContent" class="landingPageButton">
	        <a class="nostyle" href="browse">
	          Browse careers now
	        </a>
              </div>
            </div>
	  </div>
	</div>
      <!-- {{else}} -->
      <?php } else { ?>
        <div id="contentContainer" class="container-fluid">
          <div class="row">
	    <p id="welcomeBox">
	      Find your passion, find your career
	    </p>
          </div>
        <div class="row row-with-margins">
          <div class="col-md-6">
	    <div id="freeAccountContent" class="landingPageButton">
	      Create your free account and get started
	    </div>
          </div>
          <div class="col-md-6">
            <div id="browseCareersContent" class="landingPageButton">
	      <a class="nostyle" href="browse">
	        Browse careers now
              </a>
            </div>
          </div>
        </div>
        <div class="row row-with-margins">
          <div class="col-md-4">
            <p class="headerText">
              Hear from passionate professionals
            </p>
            <p class="detailText">
              Watch interviews where you can see and hear what parts of the job makes them tick.
            </p>
          </div>
          <div class="col-md-4">
            <p class="headerText">
              Like the job?
            </p>
            <p class="detailText">
              For each job you like or dislike, we'll keep track of it and so that we can recommend other jobs you might be interested in.
            </p>
          </div>
          <div class="col-md-4">
            <p class="headerText">
              Employment data at your fingertips
            </p>
            <p class="detailText">
              Find salary, education requirements, and employment numbers so you know what your career prospects are.
            </p>
          </div>
        </div>
      <!-- {{/if}} -->
      <?php } ?>
      </div>
  
<!--
      {{#if success}}
      {{else}}
      <div id="errorBox">
        <div id="errorMessage">
          {{reason}}
        </div>
      </div>
      {{/if}}

      {{#if loginAttempt}}
      <div id="loginAttempt">
      </div>
      {{/if}}

      {{#if signUpAttempt}}
      <div id="signUpAttempt">
      </div>
      {{/if}}
-->
    <?php
      if ($success){
        echo '<div id="errorBox">';
        echo '  <div id="errorMessage">';
        echo $reason;
        echo '  </div>';
        echo '</div>';
      }

      if ($loginAttempt){
        echo '<div id="loginAttempt"/>';
      }

      if ($signUpAttempt){
        echo '<div id="signUpAttempt"/>';
      }
    ?>

  </body>
</html>
