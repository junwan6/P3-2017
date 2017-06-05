<?php
require_once('WebDriverTest.php');
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class AdminTest extends WebDriverTest{
  /* Variables and methods inherited from parent
    protected $url;
    protected $wd;
    protected $allowBrokenLinks;
    protected $suppressWarnings;
    protected $screenshotDir;
    protected $driverDetached;

    public static function getstatus($url)
    protected function testPageImgs($warnOnly=false)
    protected function testPageLinks($warnOnly=false)
    public function detachDriver()
  */
  /* Default variables for constructor
    $defaultArgs = [
      'binPath' => 'downloads/chrome-linux/chrome',
      'selServPath' => 'http://localhost:4444/wd/hub',
      'timeout' => 5000,
      'pageURL' => 'https://localhost/cake/',
      'warnLinks' => false,
      'suppressWarnings' => false,
      'ssDir' => 'screenshot/',
      'reuseDriver' => null,
    ];
  */

  /* Assumes not logged in
   * Tries Admin button visibility/existence
   * Tries accessing pages via URL
   */
  public function testAccess($denied=true){
    $adminButtonExists = true;
    try{
      $adminButton = $this->wd->findElement(WebDriverBy::id('admin'));
    } catch (Exception $e){
      $adminButtonExists = false;
    }
    if ($adminButtonExists && $denied){
      $this->error('Admin button visible to non-admin/logged-out user');
    }

    $links = [
      ['href'=>'admin',
        'desc'=>'Admin summary page accessible to unprivileged user'],
      ['href'=>'admin/videos',
        'desc'=>'Admin video upload page accessible to unprivileged user'],
      ['href'=>'admin/upload',
        'desc'=>'Admin upload action accessible to unprivileged user'],
      ['href'=>'admin/orphans',
        'desc'=>'Admin filesystem page accessible to unprivileged user'],
      ['href'=>'admin/delete',
        'desc'=>'Admin filesystem delete action accessible to unprivileged user'],
      ['href'=>'admin/user/1',
        'desc'=>'Admin user page accessible to unprivileged user']
    ];
    foreach ($links as $link){
      // getstatus CURL does not use webdriver session, cannot test login
      $this->wd->get($this->url . $link['href']);
      $errorMsg = $this->wd->findElement(WebDriverBy::cssSelector('div.box > p.titleText'));
      if ($errorMsg->getText() !== 'Uh-oh!'){
        throw new Exception($link['desc']);
      }
    }
  }
  
  /* Non-test function, logs into admin account to access admin portal
   * Should be tested by UserTest
   */
  public function login($email='pppp', $pass='pppp', $logout=false){
    if ($logout){
      $logoutButton = $this->wd->findElement(WebDriverBy::id('logout'));
      $this->wd->getMouse()->mouseMove($logoutButton->getCoordinates());
      $logoutButton->click();
    }
    $loginButton = $this->wd->findElement(WebDriverBy::id('login'));
    $this->wd->getMouse()->mouseMove($loginButton->getCoordinates());
    $loginButton->click();

    $emailField = $this->wd->findElement(WebDriverBy::cssSelector(
      'form[action="/cake/login"] > div.loginInfo > input[name=email]'));
    $this->wd->getMouse()->mouseMove($emailField->getCoordinates());
    $emailField->click();
    $emailField->sendKeys($email);

    $passField = $this->wd->findElement(WebDriverBy::cssSelector(
      'form[action="/cake/login"] > div.loginInfo > input[name=password]'));
    $this->wd->getMouse()->mouseMove($passField->getCoordinates());
    $passField->click();
    $passField->sendKeys($pass);

    $loginSubmit = $this->wd->findElement(WebDriverBy::id('loginButton'));
    $this->wd->getMouse()->mouseMove($loginSubmit->getCoordinates());
    $loginSubmit->click();

    if (!is_null($this->screenshotDir)){
      $this->wd->takeScreenShot($this->screenshotDir . 'adminBar.png');
    }
  }

  public function testSummary(){
    $adminButton = $this->wd->findElement(WebDriverBy::id('admin'));
    $this->wd->getMouse()->mouseMove($adminButton->getCoordinates());
    $adminButton->click();
    if (!is_null($this->screenshotDir)){
      $this->wd->takeScreenShot($this->screenshotDir . 'summary.png');
    }
    $summaryTitle = $this->wd->findElement(WebDriverBy::cssSelector('div.box > p.titleText'));
    if ($summaryTitle->getText() !== 'Summary'){
      throw new Exception('Summary page lacks title');
    }

    $videoSOCs = $this->wd->findElements(
      WebDriverBy::cssSelector('.scrollRow:not(.clickable) > td:first-child'));
    $testedSOC = $videoSOCs[array_rand($videoSOCs, 1)];
    if ($testedSOC === NULL){
      throw new Exception('No SOCs have videos');
    } else {
      $this->wd->getMouse()->mouseMove($testedSOC->getCoordinates());
      $testedSOC->click();
      $inputBar = $this->wd->findElement(WebDriverBy::id('inputSOC'));
      if ($inputBar->getAttribute('value') != $testedSOC->getText()){
        throw new Exception('Clicked SOC not added to input bar');
      }
      $testedSOC->click();
      if ($inputBar->getAttribute('value') != ''){
        throw new Exception('Clicked SOC not removed from input bar');
      }
    }
  }
}
?>
