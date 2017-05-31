              <div id="video">
                <p id="jobtitle"> </p>
                <p id="vidtitle"></p>
                <div onclick="updateTitle()">
                  <?php
                    if (empty($videos)) 
                      echo '<h2>Sorry, no videos are currently available for this job.</h2>';
                    else {
                      echo '
                        <div id="video-wrapper" style="width:530px; height: 300px;">
                          <video 
                             autoplay="autoplay"
                             data-showplaylist="true" 
                             class="mep-playlist"
                             width="100%"
                             height="100%"
                             id="vidtag"
                             >';

                      foreach ($videos as $person){
                        foreach ($person['videos'] as $v){
                          $filePath = '../../vid/' . $v['fileName'];
                          echo '<source type="video/mp4" src="' . $filePath . 
                            '" title="' . $v['question'] . '" data-poster="track2.png">';
                        }
                      }
                    
                      echo '</video>

                        </div>';
                    } 
                  ?>
                  <div style="margin-left:720px; margin-top: 10px">
                    <a onclick="showNextCareerButton('up');updateRank('like')" value="Call2Functions" href="#vidup"><span class="upthumb" id="upthumb"></span></a>
                    <a onclick="showNextCareerButton('mid');updateRank('neutral')" value="Call2Functions" href="#vidmid"><span class="midthumb" id="midthumb"></span></a>
                    <a onclick="showNextCareerButton('down');updateRank('dislike')" value="Call2Functions" href="#viddown"><span class="downthumb" id="downthumb"></span></a><br>		      
                    <div id="next-career">
					<?php 
						$url = $this->Url->build(null, true);
						//$url_array = parse_url($url);
						//$rating = $url_array["query"];
						//$new_url = "/algorithm/nextcareer/" . $rating;
						echo $this->Html->link('Next Career  >>', $url, ['class' => 'next-career']);
						?>
					</div>
					<!-- <div id="next-career"><a href="/career/nextcareer"><span class="next-career">Next Career  >></span></a></div> -->
                  </div>
                  <div style=" padding-bottom: 0px">
                    <p><font size="3" color = "blue">Filters</font></p>
                  </div>
                  <select name = "salary" id = "salary" form = "filters">
                    <option value="">Any Salary</option>
                    <option value="1">&lt;$40,000</option>
                    <option value="2">$40,000-$60,000</option>
                    <option value="3">$60,000-$80,000</option>
                    <option value="4">&gt;$100,000</option>
                  </select>
                  <select name = "education" id = "education" form = "filters">
                    <option value="">Any Education Level</option>
                    <option value="6">Bachelor's Degree</option>
                    <option value="7">Master's Degree</option>
                    <option value="8">Doctorate</option>
                  </select>
                  <div style=" padding-top: 20px">
                    <form id = "filters">
                    </form>
                  </div>
                  <button class="filter_button" onclick = "collectFilters()">Update Filters</button>
                </div>
                <script type="text/javascript">
                  function collectFilters(){
                  	var form = document.getElementById('filters');
                  	var salary = Number(form.elements['salary'].value);
                  	var edu = Number(form.elements['education'].value);
                  
                  	var pname = window.location.pathname;
                  	var socPos = pname.search(/[0-9][0-9]-[0-9][0-9][0-9][0-9]/);
                  
                  	var soc = pname.substring(socPos, socPos+7);
                  
                  	$.get('career/filters?salary=' + salary + '&education=' + edu + '&soc=' + soc);
                  }
                  var player = new MediaElementPlayer('video',
                  {
                    "features": ['playlistfeature', 'prevtrack', 'playpause',
                      'nexttrack', 'current', 'progress', 'duration', 'volume',
                      'playlist', 'fullscreen', 'autoplay' ],
                  	"shuffle": false,
                  	"loop": true,
                  	"autoplay": true
                  });
                  
                  addFullScreenOverlay();
                  updateTitle();
                  
                  $("video").click(function (){
                    if (!player.paused){
                      player.pause();
                    }
                  });
                </script>
              </div>