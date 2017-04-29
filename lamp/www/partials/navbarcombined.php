			<div id="fader"></div>

      <?php if (!$loggedIn) { ?>
			<div id="signUpBox">
				<div id="signUpContent">
					<br>
					<p id="signUpTitle" class="centered">Sign Up</p>
					<form action="signup" method="post">
						<div class="signUpInfo form-group">
							<label for="firstName">First Name</label>
							<input type="text" class="formTextField form-control" name="firstName"/>
						</div>
						<div class="signUpInfo form-group">
							<label for="lastName">Last Name</label>
							<input type="text" class="formTextField form-control" name="lastName"/>
						</div>
						<div class="signUpInfo form-group">
							<label for="email">Email</label>
							<input type="text" class="formTextField form-control" name="email"/>
						</div>
						<div class="signUpInfo form-group">
							<label for="password">Password</label>
							<input type="password" class="formTextField form-control" name="password"/>
						</div>
						<div class="signUpInfo form-group">
							<label for="password">Verify Password</label>
							<input type="password" class="formTextField form-control" name="verifypassword"/>
						</div>

						<input id="signUpButton" class="btn btn-default formButton" type="submit" value="Sign Up"/>

						<a href="auth/linkedin.php">
						<div id="linkedinBox">
							<div id="linkedinButton">
							</div>
						</div>
						</a>
                                                
                                                <a id="switchToLogin" class="signUpOption">
							Already a member? Log in now
                                                </a>

						<i id="signUpCloseButton" class="fa fa-times fa-2x" aria-hidden="true"></i>
					</form>

				</div>
			</div>

			<div id="loginBox">
				<div id="loginContent">
					<br>
					<p id="loginTitle" class="centered">Log In</p>
					<form action="login" method="post">
						<div class="loginInfo form-group">
							<label for="email">Email</label>
							<input type="text" class="formTextField form-control" name="email"/>
						</div>
						<div class="loginInfo form-group">
							<label for="password">Password</label>
							<input type="password" class="formTextField form-control" name="password"/>
						</div>
                                                <div class="checkbox">
                                                  <label>
						    <input name="remember_me" type="checkbox">Remember me
                                                  </label>
                                                </div>
						<input id="loginButton" type="submit" class="btn btn-default formButton" value="Log In"/>
                                                <br>
						<a href="auth/facebook.php" class="btn btn-default"><div id="fbTestBox"><div id="fbTest">Log in with Facebook</div></div></a>
                                                <br>
						<a href="recover-account.php" class="loginOption">Forgot password?</a>
                                                <br>
						<a id="switchToSignUp" class="loginOption">Create your free account</a>

						<i id="loginCloseButton" class="fa fa-times fa-2x" aria-hidden="true"></i>
					</form>
				</div>
      </div>
      <?php } ?>
			<div id="navibar">
                          <div class="container-fluid">
                            <div class="row row-eq-height">
                              <div class="col-md-3">
			        	<a href=""><img id="logo" src="static/images/p3logo.png"> <span class="logo">Passionate People Project </span> </a>
                              </div>
                              <div id="mainButtons" class="col-md-6">
					<a href="">
					<div id="home" class="navibutton">
						Home
					</div>
					</a>
					<a href="profile.php">
					<div id="myProfile" class="navibutton">
						My Profile
					</div>
					</a>
                                        <a href="browse.php">
						<div id="browse" class="navibutton">
							Browse
						</div>
					</a>

                                        <form id="searchBarForm" class="form-inline" action="search" method="get" role="form">
                                          <div class="input-group">
                                            <input id="searchBar" class="form-control" type="text" name="q" placeholder="Search careers...">
                                            <span class="input-group-btn">
                                              <button id="searchButton" class="btn btn-secondary" type="submit">
                                                <i class="fa fa-search" aria-hidden="true"></i>
                                              </button>
                                            </span>
                                          </div>
                                        </form>
                                        
                                        <div class="navibutton">
                                          <a href="donors.php">
                                            Donors
                                          </a>
                                        </div>
                              </div>
                              <?php if (!$loggedIn) { ?>
                              <div id="signUpLoginColumn" class="col-md-3">
                                <div id="signUp" class="navibutton">
                                  Sign up
                                </div>
                              <div id="login" class="navibutton">
                                Login
                              </div>
                              <?php } else { ?>
                              <form action="logout" method="post">
                                <input id="logout" class="navibutton" type="submit" value="Logout"/>
                              </form>
                              <?php } ?>
                              </div>
                            </div>
                          </div>
                         <div id="PASSION"> <a href="#"  id="passionWord">PASSION (n.) </a><span id="passionDef">: the energizing love you have for your work, in which you are thoroughly absorbed and find deeply meaningful</span> </div>
			</div>
