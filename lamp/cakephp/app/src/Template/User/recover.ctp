<!DOCTYPE html>
<html>
<head>
	<?php
         echo $this->Html->script('profile.js');
         echo $this->Html->css('profile.css');
	 function baseLink2(...$args){
      	   $view = $args[0];
      	   $str = $view->Url->build('/');
      	   if (count($args) >= 2){
             $str .= $args[1];
           }
           if (count($args) == 4){
             $str = $args[2] . $str . $args[3];
           }
           return $str;
         }

	?>
</head>
<style>
<label>
	color: black;
</label>
</style>

<body>
	<div class="container-fluid">
          <div class="row">
            <div class="col-md-10 col-md-offset-1">
              <div class="box">
                <div id="pageTitle">
                  Recover Account
		</div>

		<div class="container-fluid">
                  <div class="row">

		    <div class="col-md-6">

		      <?php echo baseLink2($this, 'tempURL', '<form action="', '" method="post">'); ?>
		        <div class="recover form-group">
			  <font color ="black"> <label for="email">Email</label></font>
          		  <input type="text" class="formTextField form-control" name="email"/>
			</div>
			</a>
			<br>
			<input id="recoverButton" class="btn btn-default formButton" type="submit" value="Send Reset Email"/>
		      </form>
		    </div>
		  </div>
                </div>
	      </div>
	    </div>
	  </div>
	</div>
</body>
</html>
