              <div id="skills-contentContainer">
                <br>
                <?php
                  if (!isset($skillsArray)){
                    echo '<h2 class="errorMsg">';
                    echo '  Information for this career is not in the database yet.';
                    echo '</h2>';
                  } else {
                    echo '<div id="mainSkillsPieChart" style="width: 500px; height: 500px; margin: 0 auto"></div>';
                  }
                  ?>
              </div>
