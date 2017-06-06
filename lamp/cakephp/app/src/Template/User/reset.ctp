<!DOCTYPE html>
<html>
<head>
	<?php
         echo $this->Html->script('profile.js');
         echo $this->Html->css('profile.css');
	 function baseLink4(...$args){
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
	 $reset = $session->read('reset');
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
                  Reset Password
		</div>

		<div class="container-fluid">
                  <div class="row">

		    <div class="col-md-6">

		      <?php echo baseLink4($this, 'insert-new-password', '<form action="', '" method="post">'); ?>
		        <div class="insertNewPassword form-group">
			  <font color ="black"> <label for="password">Password</label></font>
          		  <input type="text" class="formTextField form-control" name="password"/>
			</div>
			<div class="insertNewPassword form-group">
                          <font color ="black"> <label for="vpassword">Verify Password</label></font>
                          <input type="text" class="formTextField form-control" name="vpassword"/>
                        </div>
			</a>
			<br>
			<input id="passwordButton" class="btn btn-default formButton" type="submit" value="Set Password"/>
			<font color = "red">
                        <?php if($reset == '2'){?>
                          Invalid Password Change
                        <?php } else if($reset == '1') {?>
                          Password Successfully Changed
                        <?php }else{
                          $session->write('reset','0');
                        }
                        ?>
                      </font>

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
