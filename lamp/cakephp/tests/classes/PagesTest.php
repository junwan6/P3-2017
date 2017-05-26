<?php
require_once('WebDriverTest.php');
use Facebook\WebDriver\WebDriverBy;

class PagesTest extends WebDriverTest{
  /* Variables and methods inherited from parent
  protected $url;
  protected $wd;
  protected $allowBrokenLinks;

  public static function getstatus($url)
  protected function testPageImgs($warnOnly=false)
  protected function testPageLinks($warnOnly=false)
   */
  /* Default variables for constructor
  function __construct($binPath='downloads/chrome-linux/chrome',
      $selServPath='http://localhost:4444/wd/hub', $timeout=5000,
      $pageURL='https://localhost/cake/', $warnLinks=false){
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
    $this->wd->get($matches[1]);
    $this->wd->get($this->url);
    // Test image sources of all img tags (only the logo on this page)
    $this->testPageImgs($this->allowBrokenLinks);
    $this->testPageLinks($this->allowBrokenLinks);
  }

  /* Test donors page, optionally fom the homepage by clicking the Donors button
   * Not much to test here, static page with minimal content
   * Checks for title, may break if title changed
   */
  public function testDonorsPage($fromHomePage=false, $returnHome=true){
    if ($fromHomePage){
      $donorsButton = $this->wd->findElement(WebDriverBy::partialLinkText('Donors'));
      $this->wd->getMouse()->mouseMove($donorsButton->getCoordinates());
      $donorsButton->click();
    } else {
      $this->wd->get($this->pageURL . 'donors');
    }

    $title = $this->wd->findElement(WebDriverBy::cssSelector('.box > .titleText'));
    if ($title->getText() !== 'Thanks to our donors!'){
      throw new Exception('Donor title not found');
    }

    $this->testPageImgs($this->allowBrokenLinks);
    $this->testPageLinks($this->allowBrokenLinks);

    if ($returnHome){
      $homeButton = $this->wd->findElement(WebDriverBy::id('home'));
      $this->wd->getMouse()->mouseMove($homeButton->getCoordinates());
      $homeButton->click();
    }
  }

  /* Test browse page, optionally fom the homepage by clicking the Browse button
   * cURL checks each link on this page, (major_groups is a static element,
   * inherited from previous group, links may not be accurate)
   * Checks for static content with ids defined in the .ctp
   * Checks imported element major_group.ctp
   */
  public function testBrowsePage($fromHomePage=false, $returnHome=true){
    if ($fromHomePage){
      $browseButton = $this->wd->findElement(WebDriverBy::id('browse'));
      $this->wd->getMouse()->mouseMove($browseButton->getCoordinates());
      $browseButton->click();
    } else {
      $this->wd->get($this->pageURL . 'browse');
    }

    $browseContainer = $this->wd->findElement(WebDriverBy::id('browseContainer'));
    $categories = $this->wd->findElement(WebDriverBy::partialLinktext('By Category'));
    $groupOptions = $this->wd->findElement(WebDriverBy::id('broadCategoryOptions'));
    if ($groupOptions->isDisplayed()){
      throw new Exception('Categories visible before button press');
    }
    $this->wd->getMouse()->mouseMove($categories->getCoordinates());
    $categories->click();
    if (!$groupOptions->isDisplayed()){
      throw new Exception('Categories not visible after buton press');
    }

    $fullSearchBar = $this->wd->findElement(WebDriverBy::id('fullSearchBar'));

    $this->testPageImgs($this->allowBrokenLinks);
    $this->testPageLinks($this->allowBrokenLinks);

    if ($returnHome){
      $homeButton = $this->wd->findElement(WebDriverBy::id('home'));
      $this->wd->getMouse()->mouseMove($homeButton->getCoordinates());
      $homeButton->click();
    }
  }
}
?>
