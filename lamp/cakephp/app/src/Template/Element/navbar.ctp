  <div id="fader"></div>
  <?php
    //TODO: Fill in following variables from the NodeJS serverside scripts:
    //  controllers/browse-controller.js
    //  controllers/temp-controller.js
    $loggedIn = false;

    /**
     * Wrapper function for Url->build
     * Parameters: [link] [prepend] [append]
     *  link: Optional, defaults to '/'. Link to be made from base url
     *  prepend: Optional. String to be prepended to path
     *  append: Optional. String to be appended to path
     */
    function baseLink(...$args){
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

  <?php if (!$loggedIn) { ?>
  <div id="signUpBox">
    <div id="signUpContent">
      <br>
      <p id="signUpTitle" class="centered">Sign Up</p>
      <?php echo baseLink($this, 'signup', '<form action="', '" method="post">'); ?>
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
        
        <?php echo baseLink($this, 'auth/linkedin', '<a href"', '">'); ?>
          <div id="linkedinBox">
            <div id="linkedinButton"></div>
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
      <?php echo baseLink($this, 'login', '<form action="', '" method="post">'); ?>
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
        <?php echo baseLink($this, 'auth/facebook', '<a href="', '" class="btn btn-default">'); ?>
          <div id="fbTestBox"><div id="fbTest">Log in with Facebook</div></div>
        </a>
        <br>
        <?php echo baseLink($this, 'recover-account', '<a href="', '" class="loginOption">Forgot password?</a>'); ?>
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
          <?php echo baseLink($this, '', '<a href="', '">'); ?>
            <?php echo $this->Html->image('p3logo.png', array('id'=>'logo')); ?>
            <span class="logo">Passionate People Project </span>
          </a>
        </div>
        <div id="mainButtons" class="col-md-6">
          <?php echo baseLink($this, '', '<a href="', '">'); ?>
            <div id="home" class="navibutton">
              Home
            </div>
          </a>
        <?php echo baseLink($this, 'profile', '<a href="', '">'); ?>
          <div id="myProfile" class="navibutton">
            My Profile
          </div>
        </a>
        <?php echo baseLink($this, 'browse', '<a href="', '">'); ?>
          <div id="browse" class="navibutton">
            Browse
          </div>
        </a>

        <?php echo baseLink($this, 'career/search', '<form id="searchBarForm" class="form-inline" action="', '" method="get" role="form">'); ?>
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
          <?php echo baseLink($this, 'donors', '<a href="', '">'); ?>
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
      </div>
      <?php } else { ?>
      <?php echo baseLink($this, 'logout', '<form action="', '" method="post">'); ?>
        <input id="logout" class="navibutton" type="submit" value="Logout"/>
      </form>
      <?php } ?>
    </div>
  </div>
  <div id="PASSION"> <a href="javascript:"  id="passionWord">PASSION (n.) </a><span id="passionDef">: the energizing love you have for your work, in which you are thoroughly absorbed and find deeply meaningful</span> </div>
  </div>
