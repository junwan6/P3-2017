              <div id="skills-contentContainer">
                <br>
                <?php
                  if (!isset($skillsArray)){
                    echo '<div class="intelligenceTitle">';
                    echo '  Information for this career is not in the database yet.';
                    echo '</div>';
                  } else {
                    echo '<div id="mainSkillsPieChart" style="width: 500px; height: 500px; margin: 0 auto"></div>';
                  }
                  ?>
              </div>