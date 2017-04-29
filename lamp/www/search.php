<!DOCTYPE HTML>
<html>
		<head>
      <!-- {{> global_header }} -->
      <?php include 'partials/global_header.php'; ?>
			<link type="text/css" rel="stylesheet" href="/static/search.css">

			<title>
				PPP
			</title>
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
                            <div class="col-md-8 col-md-offset-2">
                              <div class="box">
                                <!--
                                {{#if resultsEmpty}}
                                <div id="pageTitle">
                                  No results for "{{query}}"
                                </div>
                                {{else}}
                                <div id="pageTitle">
                                  Search results for "{{query}}"
                                </div>

                                <div class="container content">
                                  {{#each results}}
                                  <div class="row search-result">
                                    <a class="video-link" href="/career/{{soc}}/video">
                                      {{title}}
                                    </a>
                                    <br>
                                    <div class="search-result-details">
                                      {{#if wageTypeIsAnnual}}
                                      <span title="Average annual salary">
                                        <i class="fa fa-usd icon detail-icon" aria-hidden="true"></i>
                                        <span class="detail-text">{{averageWage}}</span>
                                      </span>
                                      {{else}}
                                      <span title="Average hourly wage">
                                        <i class="fa fa-usd icon detail-icon" aria-hidden="true"></i>
                                        <span class="detail-text">{{averageWage}}/hr</span>
                                      </span>
                                      {{/if}}

                                      <span title="Education required">
                                        <i class="fa fa-graduation-cap icon detail-icon" aria-hidden="true"></i>
                                        <span class="detail-text">{{educationRequired}}</span>
                                      </span>
                                      <span title="Career growth">
                                        <i class="fa fa-sun-o icon detail-icon" aria-hidden="true"></i>
                                        <span class="detail-text">{{careerGrowth}}</span>
                                      </span>
                                    </div>
                                  </div>
                                  {{/each}}
                                </div>
                                {{/if}}
                                -->
                                <div id="pageTitle">
                                  <?php echo (($resultsEmpty)?('No results for "'):('Search Results for "')) . $query . '"'; ?>
                                </div>
                                <?php
                                  if ($resultsEmpty){
                                    echo '<div class="container content">';
                                    foreach ($results as $r){
                                      echo '<div class="row search-result">';
                                      echo '<a class="video-link" href="career/' . $soc . '/video">';
                                      echo $title;
                                      echo '</a><br><div class="search-result-details">';
                                      if ($wageTypeIsAnnual){
                                        echo '<span title="Average annual salary"><i class="fa fa-usd icon detail-icon" aria-hidden="true"></i>';
                                        echo '<span class="detail-text">' . $averageWage . '</span>';
                                        echo '</span>';
                                      } else {
                                        echo '<span title="Average hourly wage"><i class="fa fa-usd icon detail-icon" aria-hidden="true"></i>';
                                        echo '<span class="detail-text">' . $averageWage . '/hr</span>';
                                        echo '</span>';
                                      }
                                      echo '<span title="Education required"><i class="fa fa-graduation-cap icon detail-icon" aria-hidden="true"></i>';
                                      echo '<span class="detail-text">' . $educationRequired . '</span>';
                                      echo '</span>';
                                      echo '<span title="Career growth"><i class="fa fa-sun-o icon detail-icon" aria-hidden="true"></i>';
                                      echo '<span class="detail-text">' . $careerGrowth . '</span>';
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
