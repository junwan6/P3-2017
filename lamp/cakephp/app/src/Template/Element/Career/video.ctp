			<body onload=checkRating('none')>
			<div id="soc" style="display:none"><?php echo $soc; ?></div>
			<script>
			  var soc = document.getElementById("soc").innerHTML;
			  
			  var checkRating = function(rating){
				  $.ajax({
					url: '/cake/algorithm/checkrating/' + rating + '/' + soc,
					success: function(result){
						if (result == 1) 
							showNextCareerButton('up');
						else if (result == 0)
							showNextCareerButton('mid');
						else if (result == -1)
							showNextCareerButton('down');
					}
				  });
			  };
			</script>
			  <div id="video">
                <p id="jobtitle"> </p>
                <p id="vidtitle"></p>
                <div onclick="updateTitle()">
                  <?php
                    if (empty($videos)) 
                      echo '<h2 class="errorMsg">Sorry, no videos are currently available for this job.</h2>';
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
                    <a onclick="checkRating('up')" value="Call2Functions" href="#vidup"><span class="upthumb" id="upthumb"></span></a>
                    <a onclick="checkRating('mid')" value="Call2Functions" href="#vidmid"><span class="midthumb" id="midthumb"></span></a>
                    <a onclick="checkRating('down')" value="Call2Functions" href="#viddown"><span class="downthumb" id="downthumb"></span></a><br>		      
                    <div id="next-career-up" onclick="nextCareer('up')"><span class="next-career-up">Next Career  >></span></div>
					<div id="next-career-mid" onclick="nextCareer('mid')"><span class="next-career-up">Next Career  >></span></div>
					<div id="next-career-down" onclick="nextCareer('down')"><span class="next-career-up">Next Career  >></span></div>
                  </div>
                  <div style=" padding-bottom: 0px">
                    <p><font size="3" color = "blue">Filters</font></p>
                  </div>
				  <form>
                  <select id="salary">
                    <option value="0">Any Salary</option>
                    <option value="1">&lt;$60,000</option>
                    <option value="2">$60,000-$80,000</option>
                    <option value="3">$80,000-$100,000</option>
					<option value="4">$100,000-$120,000</option>
					<option value="5">&gt;$120,000</option>
                  </select>
                  <select id="education">
                    <option value="0">Any Education Level</option>
                    <option value="1">Bachelor's Degree</option>
                    <option value="2">Master's Degree</option>
                    <option value="3">Doctorate</option>
                  </select>
				  </form>
                </div>
				<script>
					var soc = document.getElementById("soc").innerHTML;
					
					var nextCareer = function(rating) {
						var salary = Number(document.getElementById("salary").value);
						var education = Number(document.getElementById("education").value);
						window.location.href = '/cake/algorithm/nextcareer/' + rating + '/' + soc + '/' + salary + '/' + education;
					};
				</script>
                <script type="text/javascript">
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
			</body>
