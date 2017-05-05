<!DOCTYPE HTML>
<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <!-- {{> global_header }} -->
      <?php
      echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js');
      echo $this->Html->css([
        'johndyer-mediaelement-8adf73f/build/mediaelementplayer.css',
        'mediaelement-playlist-plugin-master/_build/mediaelement-playlist-plugin.min.css',
        'video.css',
        'gagiktest1.css'
      ]);
      ?>
			<title>
				PPP
			</title>
      <?php
        //TODO: Fill in following variables from the NodeJS serverside scripts:
        //  controllers/occupation-controller.js
        //  models/occupation.js
        $occupationTitle = "Not Implemented";

        // majority of work to be done in static/js/vidmain.js
        // implement video database
      ?>
		</head>
    <body>
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-md-9 col-md-offset-1">
                              <div class="box">
			        <div id="careerTitle">
                <!-- {{occupationTitle}} -->
                <?php echo $occupationTitle; ?>
			        </div>
			        <div id="video">
				  <p id="jobtitle"> </p>
				  <p id="vidtitle"></p>
				  <div onclick="updateTitle()">
				  <div id="video-wrapper" style="width:530px; height: 300px;">
				    <video 
				       autoplay="autoplay"
				       data-showplaylist="true" 
				       class="mep-playlist"
				       width="100%"
				       height="100%"
				       id="vidtag"
				       >
				    </video>

				</div>
				    <div style="margin-left:720px; margin-top: 10px">

				      <a onclick="showNextCareerButton('up');updateRank('like')" value="Call2Functions" href="#vidup"><span class="upthumb"></span></a>
				      <a onclick="showNextCareerButton('mid');updateRank('neutral')" value="Call2Functions" href="#vidmid"><span class="midthumb"></span></a>
				      <a onclick="showNextCareerButton('down');updateRank('dislike')" value="Call2Functions" href="#viddown"><span class="downthumb"></span></a><br>		      
				      <div id="next-career"><a href="/career/nextcareer"><span class="next-career">Next Career  >></span></a></div>

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
				   	
				   	<script type="text/javascript">
				   		function collectFilters(){
				   			var form = document.getElementById('filters');
    						var salary = Number(form.elements['salary'].value);
    						var edu = Number(form.elements['education'].value);

    						var pname = window.location.pathname;
        					var socPos = pname.search(/[0-9][0-9]-[0-9][0-9][0-9][0-9]/);
        					//var soc = pname.substring(socPos, socPos+2).concat(pname.substring(socPos+2, socPos+7));
        					var soc = pname.substring(socPos, socPos+7);
    						//console.log("salary and edu");
    						//console.log(salary);
    						//console.log(edu);
    						$.get('career/filters?salary=' + salary + '&education=' + edu + '&soc=' + soc);

				   		}
				   	</script>

            <?php
              echo $this->Html->script([
                'vidMain.js',
                'johndyer-mediaelement-8adf73f/build/mediaelement-and-player.min.js',
                'mediaelement-playlist-plugin-master/_build/mediaelement-playlist-plugin.min.js'
              ]);
            ?>


				    <script>
				        
						// video playlist
					$('video.mep-playlist').mediaelementplayer({
						"features": ['playlistfeature', 'prevtrack', 'playpause', 'nexttrack', 'current', 'progress', 'duration', 'volume', 'playlist', 'fullscreen', 'autoplay' ],
						"shuffle": false,
						"loop": true,
						"autoplay": true
				
					});
				
					// regular video
					$('video:not(.mep-playlist)').mediaelementplayer({
						"features": ['playpause', 'current', 'progress', 'duration', 'tracks', 'volume', 'fullscreen', 'autoplay'],
						"autoplay": true
				
					});
					
					addFullScreenOverlay();
					updateTitle();
				
				    </script>
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
