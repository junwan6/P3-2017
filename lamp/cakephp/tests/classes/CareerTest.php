<?php
require_once('WebDriverTest.php');
use Facebook\WebDriver\WebDriverBy;

class CareerTest extends WebDriverTest{
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
  
  /* Helper function, navigates to Browse page via navbar button
   * Optionally, directly by url
   */
  public function enterBrowsePage($fromURL=false){
    if ($fromURL){
      $this->get($this->url . 'browse');
    } else {
      $browseButton = $this->wd->findElement(WebDriverBy::id('browse'));
      $this->wd->getMouse()->mouseMove($browseButton->getCoordinates());
      $browseButton->click();

      $browseContainer = $this->wd->findElement(WebDriverBy::id('browseContainer'));
      $categories = $this->wd->findElement(WebDriverBy::partialLinktext('By Category'));
      $groupOptions = $this->wd->findElement(WebDriverBy::id('broadCategoryOptions'));
    }
  }

  /* Tests a query with known matches, tests results to make sure all match
   * Tests that all results point to existing careers
   */
  public function testKnownSearch($ssFile='search.png'){
    // Skip test on searchbar visibility, tested by PagesTest as static element
    $searchCategory = $this->wd->findElement(
      WebDriverBy::partialLinkText('By Search'));
    $this->wd->getMouse()->mouseMove($searchCategory->getCoordinates());
    $searchCategory->click();

    $fullSearchBar = $this->wd->findElement(WebDriverBy::id('fullSearchBar'));
    $fullSearchBar->click();
    $fullSearchBar->sendKeys('teacher special');

    $searchButton = $this->wd->findElement(WebDriverBy::cssSelector(
      '#fullSearchBar ~ span.input-group-btn > button.btn'));
    $this->wd->getMouse()->mouseMove($searchButton->getCoordinates());
    $searchButton->click();
    
    // Page navigates to search page with list of results
    $searchResults = $this->wd->findElements(WebDriverBy::className(
      'video-link'));
    foreach($searchResults as $sr){
      $srText = strToLower($sr->getText());
      if (strpos($srText, 'teacher') === false
        || strpos($srText, 'special') === false){
        throw new Exception("Search result {$srText} does not contain " .
          '"teacher" and "special"');
      }
    }

    if (!is_null($ssFile)){
      $this->wd->takeScreenShot($this->screenshotDir . $ssFile);
    }

    $this->testPageImgs($this->allowBrokenLinks);
    $this->testPageLinks($this->allowBrokenLinks);
  }

  /* Generates queries randomly, checks all results for query match
   * Tests all returned links
   */
  public function testRandomSearch($rep){
    for ($n = 0; $n < $rep; $n++){
      $this->enterBrowsePage();
      $searchCategory = $this->wd->findElement(
        WebDriverBy::partialLinkText('By Search'));
      $this->wd->getMouse()->mouseMove($searchCategory->getCoordinates());
      $searchCategory->click();

      $fullSearchBar = $this->wd->findElement(WebDriverBy::id('fullSearchBar'));
      $fullSearchBar->click();

      $searchStr = '';
      for ($i = 0; $i < 3; $i++){
        // q omitted, no careers contain q
        $searchStr .= 'abcdefghijklmnoprstuvwxyz '[rand(0,25)];
      }

      $fullSearchBar->sendKeys($searchStr);

      $searchButton = $this->wd->findElement(WebDriverBy::cssSelector(
        '#fullSearchBar ~ span.input-group-btn > button.btn'));
      $this->wd->getMouse()->mouseMove($searchButton->getCoordinates());
      $searchButton->click();
      
      // Page navigates to search page with list of results
      $searchResults = $this->wd->findElements(WebDriverBy::className(
        'video-link'));
      foreach($searchResults as $sr){
        $srText = strToLower($sr->getText());
        foreach(explode(' ', $searchStr) as $kw){
          if (strpos($srText, $kw) === false){
            throw new Exception("Search result \"{$srText}\" does not match " .
              "entered query \"{$searchStr}\"");
          }
        }
      }

      $this->testPageImgs($this->allowBrokenLinks);
      $this->testPageLinks($this->allowBrokenLinks);
    }
  }

  /* TODO: Implement career page checking
   * 
   */
  public function testFilteredSearch(){

  }
}
?>
