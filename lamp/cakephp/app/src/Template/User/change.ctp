<!DOCTYPE html>
<html>
<head>
	<?php
         echo $this->Html->script('profile.js');
         echo $this->Html->css('profile.css');
	 function baseLink3(...$args){
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
	$session = $this->request->session();
	$changed = $session->read('changed');
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
                  Change Password
		</div>

		<div class="container-fluid">
                  <div class="row">

		    <div class="col-md-6">

		      <?php echo baseLink3($this, 'change', '<form action="', '" method="post">'); ?>
		        <div class="changePassword form-group">
			  <font color ="black"> <label for="password">Password</label></font>
          		  <input type="text" class="formTextField form-control" name="password"/>
			</div>
			<div class="changePassword form-group">
                          <font color ="black"> <label for="vpassword">Verify Password</label></font>
                          <input type="text" class="formTextField form-control" name="vpassword"/>
                        </div>
		        </a>
		        <br>
			<input id="changePasswordButton" class="btn btn-default formButton" type="submit" value="Change Password"/>
		      </form>
		      <font color = "red">
		      	<?php if($changed == '2'){?>
			  Invalid Password Change	
		      	<?php } else if($changed == '1') {?>
		          Password Successfully Changed
			<?php }else{ 
			  $session->write('changed','0');
		       	}
			?>
		      </font>
		    </div>
		  </div>
                </div>
	      </div>
	    </div>
	  </div>
	</div>
</body>
</html>
