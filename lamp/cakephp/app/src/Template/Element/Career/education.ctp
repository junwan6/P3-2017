              <div id="education-contentContainer">
                <div class="educationLine">
                  <div class="educationCategory">
                    Type of School:
                  </div>
                  <div id="typeOfSchool" class="educationValue">
                    <?php echo $typeOfSchool; ?>
                  </div>
                </div>
                <div class="educationLine">
                  <div class="educationCategory">
                    Type of Degree:
                  </div>
                  <div id="typeOfDegree" class="educationValue">
                    <?php echo $typeOfDegree; ?>
                  </div>
                </div>
                <div class="educationLine">
                  <div class="educationCategory">
                    Years in School:
                  </div>
                  <div id="yearsInSchool" class="educationValue">
                    <?php echo $yearsInSchool; ?>
                  </div>
                </div>
              </div>
              <div id="education-careerInformation">
                <div id="yearsInUndergrad">
                  <?php echo $yearsInUndergrad; ?>
                </div>
                <div id="yearsInGrad">
                  <?php echo  $yearsInGrad; ?>
                </div>
                <table id="undergradTable">
                  <tr>
                    <td>9410</td>
                    <td>10138</td>
                    <td>19548</td>
                  </tr>
                  <tr>
                    <td>32405</td>
                    <td>11516</td>
                    <td>43921</td>
                  </tr>
                </table>
                <table id="gradTable">
                  <tr>
                    <td>10725</td>
                  </tr>
                  <tr>
                    <td>22607</td>
                  </tr>
                </table>
              </div>
              <div id="chartContainer" style="width: 450px; height: 450px; margin: 0 auto"></div>
              <div id="input-container" class="row" style="display: table">
                <div id="undergraduateInputs" class="col" style="display: table-cell">
                  Undergraduate
                  <div class="row inputRow" style="display: table">
                    <select id="undergradPublicPrivateInput" class="col form-control chartOption" style="display: table-cell">
                      <option value="public">Public In-State</option>
                      <option value="private">Private Nonprofit</option>
                    </select>
                    <select id="undergradTuitionRoomBoardInput" class="col form-control chartOption" style="display: table-cell">
                      <option value="tuition">Tuition</option>
                      <option value="roomBoard">Room/Board</option>
                      <option value="tuitionRoomBoard">Tuition, Room/Board</option>
                    </select>
                  </div>
                  <div id="undergraduateCostDisplay"></div>
                </div>
                <?php if ($gradSchool){ ?>
                <div id="graduateInputs" class="col" style="display: table-cell">
                  Graduate
                  <div class="row inputRow" style="display: table">
                    <select id="gradPublicPrivateInput" class="col form-control chartOption" style="display: table-cell">
                      <option value="public">Public</option>
                      <option value="private">Private</option>
                    </select>
                    <select class="col form-control chartOption" style="display: table-cell">
                      <option value="tuition">Tuition</option>
                    </select>
                  </div>
                  <div id="graduateCostDisplay"></div>
                </div>
                <?php } ?>
                <div id="education-careerInputs" class="col" style="display: table-cell">
                  Career
                  <div class="row inputRow" style="display: table">
                    <select id="education-salaryStateInput" class="col form-control chartOption" style="display: table-cell">
                    <?php
                      if (!isset($_GET['st'])){
                      $_GET['st'] = 'NAT';
                      }
                      foreach ($states as $st){
                      if (in_array($st, $sts)){
                        echo '<option value ="' . $st . '"' . (($st == $_GET['st'])?' selected="selected"':'')  . '>' . $statename[$st] . '</option>';
                      }
                      }
                      ?>
                    </select>
                    <select id="salaryPositionInput" class="col form-control chartOption" style="display: table-cell">
                      <option value="average">Average Salary</option>
                      <option value="low">Low Salary</option>
                      <option value="median">Median Salary</option>
                      <option value="high">High Salary</option>
                    </select>
                  </div>
                  <div id="careerSalaryDisplay"></div>
                </div>
              </div>