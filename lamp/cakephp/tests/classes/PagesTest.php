<?php
require_once('WebDriverTest.php');
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class PagesTest extends WebDriverTest{
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

  /* Starting point for all stepthrough tests
   * Checks for static elements, dynamic checks will be part of respective
   * tests for each controller (i.e. UserController tests "Welcome back, etc.")
   * Ends with webdriver at index page
   */
  public function testHomePage(){
    // Loads the homepage, assumes this is run on same 
    $this->wd->get($this->url);

    // Test existence of static elements, navbar from Layout, content from page
    // Will throw exception on failure to find element, uncaught as test case
    $page = $this->wd->findElement(WebDriverBy::id('content'));
    $navbar = $this->wd->findElement(WebDriverBy::id('navibar'));
    // Ignore login+signup/logout button, will be part of UserController test

    // Test background image by loading URL given in CSS of background
    $background = $this->wd->findElement(WebDriverBy::id('imageContainer'));
    $bgCssURL = $background->getCSSValue('background-image');
    $matches = [];
    preg_match('/^url\("(.*)"\)$/', $bgCssURL, $matches);
    $httpsReturn = $this->getStatus($matches[1]);
    if (substr($httpsReturn, 0, 1) !== '2'){
      throw new Exception('Background image does not exist');
    }

    if (!is_null($this->screenshotDir)){
      $this->wd->takeScreenShot($this->screenshotDir . 'home.png');
    }

    // Test image sources of all img tags (only the logo on this page)
    // First run, test navbar links as well
    $this->testPageImgs($this->allowBrokenLinks);
    $this->testPageLinks($this->allowBrokenLinks, false);
  }

  /* Test donors page, optionally fom the homepage by clicking the Donors button
   * Not much to test here, static page with minimal content
   * Checks for title, may break if title changed
   */
  public function testDonorsPage(){
    $donorsButton = $this->wd->findElement(WebDriverBy::partialLinkText('Donors'));
    $this->wd->getMouse()->mouseMove($donorsButton->getCoordinates());
    $donorsButton->click();

    $title = $this->wd->findElement(WebDriverBy::cssSelector('.box > .titleText'));
    if ($title->getText() !== 'Thanks to our donors!'){
      throw new Exception('Donor title not found');
    }
    
    if (!is_null($this->screenshotDir)){
      $this->wd->takeScreenShot($this->screenshotDir . 'donors.png');
    }

    $this->testPageImgs($this->allowBrokenLinks);
    $this->testPageLinks($this->allowBrokenLinks);

    $homeButton = $this->wd->findElement(WebDriverBy::id('home'));
    $this->wd->getMouse()->mouseMove($homeButton->getCoordinates());
    $homeButton->click();
  }

  /* Test browse page, optionally fom the homepage by clicking the Browse button
   * cURL checks each link on this page, (major_groups is a static element,
   * inherited from previous group, links may not be accurate)
   * Checks for static content with ids defined in the .ctp
   * Checks imported element major_group.ctp
   */
  public function testBrowsePage(){
    $browseButton = $this->wd->findElement(WebDriverBy::id('browse'));
    $this->wd->getMouse()->mouseMove($browseButton->getCoordinates());
    $browseButton->click();

    $browseContainer = $this->wd->findElement(WebDriverBy::id('browseContainer'));
    $categories = $this->wd->findElement(WebDriverBy::partialLinktext('By Category'));
    $groupOptions = $this->wd->findElement(WebDriverBy::id('broadCategoryOptions'));
    if ($groupOptions->isDisplayed()){
      throw new Exception('Categories visible before button press');
    }
    $this->wd->getMouse()->mouseMove($categories->getCoordinates());
    $categories->click();
    $this->wd->wait()->until(
      WebDriverExpectedCondition::visibilityOfElementLocated(
        WebDriverBy::id('broadCategoryOptions')
    ));
    if (!$groupOptions->isDisplayed()){
      throw new Exception('Categories not visible after button press');
    }

    $fullSearchBar = $this->wd->findElement(WebDriverBy::id('fullSearchBar'));

    if (!is_null($this->screenshotDir)){
      $this->wd->takeScreenShot($this->screenshotDir . 'browse.png');
    }

    $this->testPageImgs($this->allowBrokenLinks);
    // Link text appears as "" as only visible text printed
    // Cannot expand all categories as expanding one collapses others
    $this->testPageLinks($this->allowBrokenLinks);

    $homeButton = $this->wd->findElement(WebDriverBy::id('home'));
    $this->wd->getMouse()->mouseMove($homeButton->getCoordinates());
    $homeButton->click();
  }
}
?>
