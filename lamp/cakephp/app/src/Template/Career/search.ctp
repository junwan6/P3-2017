<!DOCTYPE HTML>
<html>
		<head>
      <?php
        echo $this->Html->css('Career/search.css');
      ?>

			<title>
				PPP
			</title>
		</head>
    <body>
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                              <div class="box">
                                <div id="pageTitle">
                                  <?php
                                    if ($resultsEmpty){
                                      if ($query == ''){
                                        echo 'No keywords entered';
                                      } else {
                                        echo 'No results for "' . $query . '"';
                                      }
                                    } else {
                                      echo 'Search results for "' . $query . '"';
                                    }
                                  ?>
                                </div>
                                <?php
                                  if (!$resultsEmpty){
                                    echo '<div class="container content">';
                                    foreach ($results as $r){
                                      echo '<div class="row search-result">';
                                      echo '<a class="video-link" href="' . $r['soc'] . '/video">';
                                      echo $r['occupationTitle'];
                                      echo '</a><br><div class="search-result-details">';
                                      if ($r['wageTypeIsAnnual']){
                                        echo '<span title="Average annual salary"><i class="fa fa-usd icon detail-icon" aria-hidden="true"></i>';
                                        echo '<span class="detail-text">' . $r['averageWage'] . '</span>';
                                        echo '</span>';
                                      } else {
                                        echo '<span title="Average hourly wage"><i class="fa fa-usd icon detail-icon" aria-hidden="true"></i>';
                                        echo '<span class="detail-text">' . $r['averageWage'] . '/hr</span>';
                                        echo '</span>';
                                      }
                                      echo '<span title="Education required"><i class="fa fa-graduation-cap icon detail-icon" aria-hidden="true"></i>';
                                      echo '<span class="detail-text">' . $r['educationRequired'] . '</span>';
                                      echo '</span>';
                                      echo '<span title="Career growth"><i class="fa fa-sun-o icon detail-icon" aria-hidden="true"></i>';
                                      echo '<span class="detail-text">' . $r['careerGrowth'] . '</span>';
                                      echo '</span></div></div>';
                                }
                                echo '</div>';
                              }
                            ?>
                          </div>
                        </div>
                      </div>
                    </div>
</html>
