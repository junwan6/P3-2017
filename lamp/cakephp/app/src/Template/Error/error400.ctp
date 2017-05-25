<!DOCTYPE HTML>
<html>
  <head>
    <!-- {{> global_header }} -->
    <?php echo $this->Html->css('404.css'); ?>
    <title>
      PPP
    </title>
  </head>
  <body>
    <!-- {{> navbar}} -->
    <?php echo $this->element('navbar'); ?>

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="box">
            <p class="titleText">
              Uh-oh!
            </p>

            <div class="container-fluid">
              <div class="row">
                <div class="col-md-6 col-md-offset-3">
                  <p class="normalText">
                    We couldn't find the page you're looking for.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
