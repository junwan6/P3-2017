              <div id="salary-container" style="max-width: 600px; height: 400px; margin: 0 auto">
              </div>
              <p></p>
              <div id="salary-careerInformation">
              </div>
              <div id="salary-careerInputs" class="col" style="width: 15%; margin: auto;">
                <div class="row inputRow">
                  <select id="salary-salaryStateInput" class="col form-control chartOption" style="display: table-cell; margin:auto;">
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
                </div>
              </div>