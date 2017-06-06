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
    if (!($adminButtonExists xor $denied)){
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
      if (!($errorMsg->getText() !== 'Uh-oh!' xor $denied)){
        $this->error($link['desc']);
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
      $this->error('Summary page lacks title');
    }

    $videoSOCs = $this->wd->findElements(
      WebDriverBy::cssSelector('.scrollRow:not(.clickable) > td:first-child'));
    $testedSOC = $videoSOCs[array_rand($videoSOCs, 1)];
    if ($testedSOC === NULL){
      $this->error('No SOCs have videos');
    } else {
      $this->wd->getMouse()->mouseMove($testedSOC->getCoordinates());
      $testedSOC->click();
      $inputBar = $this->wd->findElement(WebDriverBy::id('inputSOC'));
      if ($inputBar->getAttribute('value') != $testedSOC->getText()){
        $this->error('Clicked SOC not added to input bar');
      }
      $testedSOC->click();
      if ($inputBar->getAttribute('value') != ''){
        $this->error('Clicked SOC not removed from input bar');
      }
    }
  }

  public function testOrphans(){
    $adminButton = $this->wd->findElement(WebDriverBy::id('admin'));
    $this->wd->getMouse()->mouseMove($adminButton->getCoordinates());
    $adminButton->click();
    $orphanButton = $this->wd->findElement(WebDriverBy::partialLinkText(
      'Unassigned Files'));
    $this->wd->getMouse()->mouseMove($orphanButton->getCoordinates());
    $orphanButton->click();
    if (!is_null($this->screenshotDir)){
      $this->wd->takeScreenShot($this->screenshotDir . 'orphans.png');
    }
    $titleText = $this->wd->findElement(WebDriverBy::className('titleText'));
    if ($titleText->getText() != 'Filesystem Cleaning'){
      $this->error('Filesystem Cleaning page failed to load');
    }
    $deleteRows = $this->wd->findElements(WebDriverBy::className('deleteRow'));
    $conflictTables = $this->wd->findElements(WebDriverBy::className('conflictTable'));

    // Choose arbitrary, if one works all should work
    // foreach-break for simpler handling of empty, variable
    foreach ($deleteRows as $row){
      if (strpos($row->getAttribute('class'), 'deleted') !== FALSE){
        $this->error('Row marked for deletion without clicking');
      }
      $this->wd->getMouse()->mouseMove($row->getCoordinates());
      $row->click();
      if (strpos($row->getAttribute('class'), 'deleted') === FALSE){
        $this->error('Row not marked for deletion after clicking');
      }
      break;
    }
    foreach ($conflictTables as $tbl){
      if (strpos($tbl->getAttribute('class'), 'deleted') !== FALSE){
        $this->error('Conflict marked for deletion without clicking');
      }
      $tblTrash = $tbl->findElement(WebDriverBy::tagName('i'));
      $this->wd->getMouse()->mouseMove($tblTrash->getCoordinates());
      $tblTrash->click();
      if (strpos($tbl->getAttribute('class'), 'deleted') === FALSE){
        $this->error('Conflict not marked for deletion after clicking');
      }
      break;
    }
  }

  public function testUser(){
    $adminButton = $this->wd->findElement(WebDriverBy::id('admin'));
    $this->wd->getMouse()->mouseMove($adminButton->getCoordinates());
    $adminButton->click();

    //Pick arbitrary user to check
    $userRow = $this->wd->findElement(WebDriverBy::cssSelector(
     'tr.clickable'));
    $this->wd->getMouse()->mouseMove($userRow->getCoordinates());
    $userRow->click();
    if (!is_null($this->screenshotDir)){
      $this->wd->takeScreenShot($this->screenshotDir . 'user.png');
    }
    $titleText = $this->wd->findElement(WebDriverBy::className('titleText'));
    if ($titleText->getText() != 'User Overview'){
      $this->error('User page failed to load');
    }
  }

  private function checkVideosOrder($personTable){
    $uploadTable = $personTable->findElement(WebDriverBy::className('uploadTable'));
    $deleteTable = $personTable->findElement(WebDriverBy::className('deleteTable'));
    $currentQ = 0;

    $uploadRows = $uploadTable->findElements(WebDriverBy::className('questionRow'));
    foreach ($uploadRows as $row){
      $matches = [];
      preg_match('/soc[0-9]{2}-[0-9]{4}p[0-9]+q([0-9]+)/', $row->getAttribute('id'), $matches);
      $qNum = (int)$matches[1];
      if ($qNum != $currentQ){
        $this->error('Person Table out of order');
      }
      $currentQ = $qNum + 1;
    }
    $deleteRows = $deleteTable->findElements(WebDriverBy::className('questionRow'));
    foreach ($deleteRows as $row){
      $matches = [];
      preg_match('/soc[0-9]{2}-[0-9]{4}p[0-9]+q([0-9]+)/', $row->getAttribute('id'), $matches);
      $qNum = (int)$matches[1];
      if ($qNum != $currentQ){
        $this->error('Person Table out of order');
      }
      $currentQ = $qNum + 1;
    }
  }

  public function testVideos(){
    $adminButton = $this->wd->findElement(WebDriverBy::id('admin'));
    $this->wd->getMouse()->mouseMove($adminButton->getCoordinates());
    $adminButton->click();

    // Go to a blank SOC to test the same thing each time
    $inputSOC = $this->wd->findElement(WebDriverBy::id('inputSOC'));
    $this->wd->getMouse()->mouseMove($inputSOC->getCoordinates());
    $inputSOC->click();
    $inputSOC->sendKeys('99-9999');

    $videoButton = $this->wd->findElement(WebDriverBy::id('gotoButton'));
    $this->wd->getMouse()->mouseMove($videoButton->getCoordinates());
    $videoButton->click();
    if (!is_null($this->screenshotDir)){
      $this->wd->takeScreenShot($this->screenshotDir . 'videos.png');
    }
    $titleText = $this->wd->findElement(WebDriverBy::className('titleText'));
    if ($titleText->getText() != 'Video Upload Panel'){
      $this->error('Video Upload page failed to load');
    }

    $peopleTables = $this->wd->findElements(WebDriverBy::className('personTable'));
    foreach ($peopleTables as $personTable){
      $addQButton = $personTable->findElement(WebDriverBy::className('questionAddButton'));
      for ($i = 0; $i < 10; $i++){
        $this->wd->getMouse()->mouseMove($addQButton->getCoordinates());
        $addQButton->click();
      }
      echo 'Videos: Testing Add Question button' . PHP_EOL;
      $questionRows = $personTable->findElements(WebDriverBy::className('questionText'));
      foreach ($questionRows as $row){
        $this->wd->getMouse()->mouseMove($row->getCoordinates());
        $row->click();
        $row->sendKeys($row->getAttribute('id'));
      }
      $this->checkVideosOrder($personTable);

      // Check deletion of beginning, middle, end
      echo 'Videos: Testing Delete button' . PHP_EOL;
      $uploadTable = $personTable->findElement(WebDriverBy::className('uploadTable'));
      $toDelete = $uploadTable->findElements(WebDriverBy::className('questionRow'));
      foreach ([0,4,10] as $delIndex){
        $del = $toDelete[$delIndex];
        $deleteButton = $del->findElement(WebDriverBy::cssSelector('.deleteCell > i'));
        $this->wd->getMouse()->mouseMove($deleteButton->getCoordinates());
        $deleteButton->click();
      }
      // Only check at end, for time reasons
      $this->checkVideosOrder($personTable);

      // Check undeletion of middle, beginning, end/beginning
      echo 'Videos: Testing Undelete button' . PHP_EOL;
      $deleteTable = $personTable->findElement(WebDriverBy::className('deleteTable'));
      $toUndelete = $deleteTable->findElements(WebDriverBy::className('questionRow'));
      foreach ([1,0,2] as $undelIndex){
        $del = $toUndelete[$undelIndex];
        $deleteButton = $del->findElement(WebDriverBy::cssSelector('.deleteCell > i'));
        $this->wd->getMouse()->mouseMove($deleteButton->getCoordinates());
        $deleteButton->click();
      }
      $this->checkVideosOrder($personTable);

/* Disabled, php-webdriver drag-and-drop has issues
      // Check drag-and-drop (downwards, from top and middle, upwards from bottom and middle)
      echo 'Testing drag-and-drop' . PHP_EOL;
      $dragRows = $uploadTable->findElements(WebDriverBy::cssSelector(
        '.questionRow > td:last-child > i'));
      foreach ([[0, 4],[4,10],[10,4],[4,1]] as $drag){
        $from = $dragRows[$drag[0]];
        $to = $dragRows[$drag[1]];
        // Issues with WebDriver drag-and-drop https://github.com/facebook/php-webdriver/issues/319
        sleep(1);
//        $this->wd->action()->dragAndDrop($from, $to)->perform();
//        $this->wd->action()
//          ->moveToElement($from)
//          ->clickAndHold($from)
//          ->moveToElement($to)
//          ->release($to)
//          ->perform();
        $mouseDown = $this->wd->getMouse()->mouseDown($from->getCoordinates());
        sleep(1);
        $mouseMove = $mouseDown->mouseMove($to->getCoordinates());
        sleep(1);
        $mouseUp = $mouseMove->mouseUp($to->getCoordinates());
        sleep(1);
        // Check order after every operation, just to be sure
        $this->checkVideosOrder($personTable);
        if (!is_null($this->screenshotDir)){
          $this->wd->takeScreenShot($this->screenshotDir . 'videos' . $drag[0] . $drag[1] .'.png');
        }
      }
*/
      // Check rename button
      echo 'Videos: Testing Rename button' . PHP_EOL;
      $headerTable = $personTable->findElement(WebDriverBy::className('headerTable'));
      $nameChangeField = $headerTable->findElement(WebDriverBy::cssSelector('input[type=text]'));
      if ($nameChangeField->isDisplayed()){
        $this->error('Name Change field visible before button pressed');
      }
      $renameButton = $headerTable->findElement(WebDriverBy::className('fa-pencil-square-o'));
      $this->wd->getMouse()->mouseMove($renameButton->getCoordinates());
      $renameButton->click();
      if (!$nameChangeField->isDisplayed()){
        $this->error('Name Change field not visible after button pressed');
      }
      $this->wd->getMouse()->mouseMove($nameChangeField->getCoordinates());
      $nameChangeField->click();
      $nameChangeField->sendKeys($nameChangeField->getAttribute('id'));

      echo 'Videos: Testing Delete person button' . PHP_EOL;
      $personDeleteButton = $headerTable->findElement(WebDriverBy::cssSelector(
        'td:last-child > i'));
      $this->wd->getMouse()->mouseMove($personDeleteButton->getCoordinates());
      $personDeleteButton->click();
      // Only checks personTable being empty, layout of page makes it difficult to get unpersonTable
      if (count($personTable->findElements(WebDriverBy::cssSelector('tbody > *'))) != 0){
        $this->error('Deleting only element of Person Table does not empty it.');
      }
      break;
    }
    if (!is_null($this->screenshotDir)){
      $this->wd->takeScreenShot($this->screenshotDir . 'videosDone.png');
    }
  }
}
?>
