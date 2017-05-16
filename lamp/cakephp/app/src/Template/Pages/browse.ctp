<!DOCTYPE HTML>
<html>
		<head>
      <!-- {{> global_header }} -->
      <?php
        echo $this->Html->script('Pages/browse.js');
        echo $this->Html->css('Pages/browse.css');
      ?>

			<title>
				PPP
			</title>
		</head>
    <style>
    .ui-autocomplete {
      height: 200px;
      overflow-y: scroll;
      overflow-x: hidden;
    }
    </style>
    <script>
      var careerList = [];
      var getAutoCompleteFull = function(){
        if (careerList.length == 0){
          $.ajax({
            url: 'career/autocomplete',
            success: function(result){
              careerList = JSON.parse(result);
              $('#fullSearchBar').autocomplete({
                source: careerList,
                select: function(event, ui){
                  window.location.href = "career/" + ui.item.soc;
                }
              });
            }
          });
        }
      };
    </script>
    <body>

      <div id="browseContainer" class="container-fluid">
        <div class="row">
          <div class="col-md-8 col-md-offset-2">
            <div class="box">
              <div id="pageTitle">
                Browse
              </div>
              <div class="panel-group" id="browseAccordion" role="tablist" aria-multiselectable="true">
                <!-- {{> major_group }} -->
                <?php echo $this->element('major_group'); ?>

                <div class="panel panel-default majorSection">
                  <div class="panel heading" role="tab">
                    <a class="collapsed accordionHeading" role="button" data-toggle="collapse" data-parent="#browseAccordion" href="#specificOccupationOptions" aria-expanded="false" aria-controls="specificOccupationOptions">By Search</a>
                  </div>
                  <div id="specificOccupationOptions" class="panel-collapse collapse" role="tab-panel">
                    <form class="form-inline" action="career/search" method="get" role="form">
                      <div class="input-group" id="browse-search">
                        <input id="fullSearchBar" class="form-control" type="search" name="q"
                          placeholder="Search careers..." autocomplete="off" onfocus="getAutoCompleteFull()">
                        <span class="input-group-btn">
                          <button class="btn btn-secondary" type="submit">
                            <i class="fa fa-search" aria-hidden="true"></i>
                          </button>
                        </span>
                      </div>
                      <div id="search-filters">
                        Only show occupations with:&nbsp;
                        <input type="checkbox" name="video">videos&nbsp;
                        <input type="checkbox" name="skills">skills&nbsp;
                        <input type="checkbox" name="growth">positive career growth&nbsp;
                      </div>
                    </form>
                  </div>
                </div>

                <div class="panel panel-default majorSection">
                  <a class="accordionHeading" href="career/random">Random Occupation</a>
                </div>

                <div class="panel panel-default majorSection">
                  <div class="panel heading" role="tab">
                    <a class="collapsed accordionHeading" role="button" data-toggle="collapse" data-parent="#browseAccordion" href="#worldOfWorkOptions" aria-expanded="false" aria-controls="worldOfWorkOptions">Random Occupation by World of Work region</a>
                  </div>
                  <div id="worldOfWorkOptions" class="panel-collapse collapse" role="tab-panel">
                    <?php
                      echo $this->Html->image('wow.png', array(
                        'id' => 'world-of-work', 'alt' => 'World of Work',
                        'align' => 'center', 'width' => '540px', 'height' => 'auto'
                      ));
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
		</body>
</html>
