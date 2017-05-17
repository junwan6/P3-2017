<!DOCTYPE HTML>
<html>
		<head>
      <?php include 'partials/global_header.php'; ?>
      <script type="text/javascript" language="javascript" src="static/icons.js"></script>
      <link type="text/css" rel="stylesheet" href="static/icons.css">
      <script type="text/javascript" language="javascript" src="static/skills.js"></script>
      <link type="text/css" rel="stylesheet" href="static/skills.css">

      <title>
        PPP
      </title>
      <?php
        //TODO: Fill in following variables from the NodeJS serverside scripts:
        //  controllers/occupation-controller.js
        //  models/occupation.js
        $occupationTitle = "Not Implemented";
        $skillsArray = null; //array, not implemented in original code
      ?>
		</head>
    <body>
        <!--
        {{#if loggedIn}}
        		{{> navbarlogout }}
    		{{else}}
        		{{> navbar}}
        {{/if}}
        -->
        <?php include 'partials/navbarcombined.php'; ?>
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
                  if (!$skillsArray){
                    echo '<div class="intelligenceTitle">';
                    echo '  Information for this career is not in the database yet.';
                    echo '</div>';
                  }
                ?>
                                  <!--
				      <div class="intelligenceTitle">
					Logical-Mathematical Intelligence
				      </div>
				      <div class="intelligenceDefinition">
					<ul>
					  <li>
					    Logical-mathematical intelligences is the ability to calculate, quantify, consider propositions and hypotheses, and carry out complete mathematical operations.  It enables us to perceive relationships and connections and to use abstract, symbolic thought sequential reasoning skills; and inductive and deductive thinking patterns.
					  </li>
					</ul>
				      </div>
				      <div class="intelligenceTasks">
					<ul>
					  <li>
					    Maintain accurate, complete, and correct student records as required by laws, district policies, and administrative regulations.
					  </li>
					  <li>
					    Prepare, administer, and grade tests and assignments to evaluate students' progress.
					  </li>
					  <li>
					    Assign lessons and correct homework.
					  </li>
					</ul>
				      </div>

				      <div class="intelligenceTitle">
					Interpersonal Intelligence
				      </div>
				      <div class="intelligenceDefinition">
					<ul>
					  <li>
					    Interpersonal intelligence is the ability to understand and interact effect with others. It involves effective verbal and nonverbal communication, the ability to note distinctions among others, sensitivity to the moods and temperaments of others, and certain multiple perspectives. 
					  </li>
					</ul>
				      </div>
				      <div class="intelligenceTasks">
					<ul>
					  <li>
					    Adapt teaching methods and instructional materials to meet students' varying needs and interests.
					  </li>
					  <li>
					    Establish and enforce rules for behavior and procedures for maintaining order among students.
					  </li>
					  <li>
					    Confer with parents or guardians, other teachers, counselors, and administrators to resolve students' behavioral and academic problems.
					  </li>
					  <li>
					    Instruct through lectures, discussions, and demonstrations in one or more subjects, such as English, mathematics, or social studies.
					  </li>
					  <li>
					    Establish clear objectives for all lessons, units, and projects, and communicate these objectives to students.
					  </li>
					  <li>
					    Assist students who need extra help, such as by tutoring and preparing and implementing remedial programs.
					  </li>
					</ul>
				      </div>

				      <div class="intelligenceTitle">
					Spatial Intelligence
				      </div>
				      <div class="intelligenceDefinition">
					<ul>
					  <li>
					    Spatial intelligence is the ability to think in three dimensions.  Core capacities include mental imagery, spatial reasoning, image manipulation, graphic and artistic skills, and an active imagination.  
					  </li>
					</ul>
				      </div>
				      <div class="intelligenceTasks">
					<ul>
					  <li>
					    Prepare materials and classrooms for class activities.
					  </li>
					</ul>
				      </div>
				      -->
			        </div>
                              </div>
                            </div>
                            <div class="col-md-2">
            <!-- {{> icons }} -->
            <?php include 'partials/icons.php'; ?>
                            </div>
                          </div>
                        </div>



			


		</body>
</html>
