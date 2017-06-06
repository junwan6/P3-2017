<?php
require_once('WebDriverTest.php');
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

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
  public function testKnownSearch(){
    // Skip test on searchbar visibility, tested by PagesTest as static element
    $searchCategory = $this->wd->findElement(
      WebDriverBy::partialLinkText('By Search'));
    $this->wd->getMouse()->mouseMove($searchCategory->getCoordinates());
    $searchCategory->click();
    $this->wd->wait()->until(
      WebDriverExpectedCondition::visibilityOfElementLocated(
        WebDriverBy::id('fullSearchBar')
    ));

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
        $this->error("Search result {$srText} does not contain " .
          '"teacher" and "special"');
      }
    }

    if (!is_null($this->screenshotDir)){
      $this->wd->takeScreenShot($this->screenshotDir . 'search.png');
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
        $allowedChars = 'abcdefghijklmnoprstuvwxyz ';
        $searchStr .= $allowedChars[rand(0,strlen($allowedChars)-1)];
      }
      // strpos returns notfound error on searching for '' created by lead/trail ' '
      $searchStr = trim($searchStr);

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
            $this->error("Search result \"{$srText}\" does not match " .
              "entered query \"{$searchStr}\"");
          }
        }
      }

      $this->testPageImgs($this->allowBrokenLinks);
      $this->testPageLinks($this->allowBrokenLinks);
    }
  }

  /* Tests for either content or an error message (class="errorMsg")
   * TODO: standardize css selector references to same levels of parent
   * May require one or the other by argument
   *  Second argument is array of pages requiring a success/fail,
   *  'video', 'skills', 'world-of-work'
   *  'salary', 'education', and 'outlook' are tied to soc, failure means
   *    that the entire page fails to load
   * May navigate within the function or assume caller has already done so
   *   String value as soc, otherwise no navigation
   */
  public function testCareerPage($requiredPages=[], $navigateTo=false){
    if (is_string($navigateTo)){
      $this->wd->get($this->url . 'career/' . $navigateTo);
    }
    $matches = [];
    $urlResult = preg_match('/\/career\/([0-9]{2}-[0-9]{4})\/video/',
      $this->wd->getCurrentURL(), $matches);
    if ($urlResult == 0 || $urlResult === FALSE){
      $this->error('Page not at career video page');
    }
    $soc = $matches[1];

    // Icons
    $videoIcon = $this->wd->findElement(WebDriverBy::id('videoSegment'));
    $salaryIcon = $this->wd->findElement(WebDriverBy::id('salarySegment'));
    $educationIcon = $this->wd->findElement(WebDriverBy::id('educationSegment'));
    $skillsIcon = $this->wd->findElement(WebDriverBy::id('skillsSegment'));
    $careerOutlookIcon = $this->wd->findElement(WebDriverBy::id('careerOutlookSegment'));
    $worldOfWorkIcon = $this->wd->findElement(WebDriverBy::id('worldOfWorkSegment'));

    echo $soc . ': Testing Video Page' . PHP_EOL;
    // Video page
    $videoPageSuccess = null;
    $videoPageFailure = null;
    if (!is_null($this->screenshotDir)){
      $this->wd->takeScreenShot($this->screenshotDir . 'video.png');
    }
    try {
      $videoPageSuccess = $this->wd->findElement(WebDriverBy::id('video-wrapper'));
    } catch (Exception $e){
      if (array_key_exists('video', $requiredPages) && $requiredPages['video']){
        $this->error($soc . ': No videos, specified to have videos.');
      }
    }
    try {
      $videoPageFailure = $this->wd->findElement(WebDriverBy::cssSelector(
        '#video .errorMsg'));
    } catch (Exception $e){
      if (array_key_exists('video', $requiredPages) && !$requiredPages['video']){
        $this->error($soc . ': Videos found, specified to have no videos.');
      }
    }
    // Should never both be null (ungraceful fail) or both non-null (both success, fail)
    if (is_null($videoPageSuccess) == is_null($videoPageFailure)){
      if (is_null($videoPageSuccess)){
        $this->error($soc . ': Video player failed to load or gracefully fail');
      } else {
        $this->error($soc . ': Both video player and error message present');
      }
    }

    echo $soc . ': Testing Salary Page' . PHP_EOL;
    // Salary Page (should always have data)
    // Check hover display
    // TODO: Find way to reset mouse position so no possibility of starting on salary
    $salaryDialog = $this->wd->findElement(WebDriverBy::id('salaryDialog'));
    if ($salaryDialog->isDisplayed()){
      $this->error($soc . ': Salary popup visible without mouse hover');
    }
    $this->wd->getMouse()->mouseMove($salaryIcon->getCoordinates());
    if (!$salaryDialog->isDisplayed()){
      $this->error($soc . ': Salary popup not visible on mouse hover');
    }
    $salaryIcon->click();
    // Now at salary display page
    // Check salary chart (chart hover label, etc. are 3rd party, assume tested)
    $salaryContainer = $this->wd->findElement(WebDriverBy::id('salary-container'));
    // Wait until chart has finished animating (also check for chart)
    // Needs to use descendent selector since php-webdriver doesn't allow $elem->wait()
    /* Commented out, TODO: find attribute that matches finished animation
    $this->wd->wait(5, 500)->until(WebDriverExpectedCondition::presenceOfElementLocated(
      WebDriverBy::cssSelector('#salary-continer g.highcharts-series[transform$="scale(1 1)"]')
    ));
     */
    sleep(3);
    if (!is_null($this->screenshotDir)){
      $this->wd->takeScreenShot($this->screenshotDir . 'salary.png');
    }

    // Find every state in list
    // Check a random state, too time-consuming to check all
    $stateSelect = $this->wd->findElement(WebDriverBy::id('salary-salaryStateInput'));
    $stateOptions = $stateSelect->findElements(WebDriverBy::tagName('option'));

    $stateToCheck = $stateOptions[array_rand($stateOptions)];
    $stateSelect->click();
    $stateToCheck->click();
    // Should be at least 1 bar
    $chartBars = $salaryContainer->findElements(WebDriverBy::cssSelector(
      'g.highcharts-series'));
    if (count($chartBars) == 0){
      $this->error($soc . ': Empty salary chart for '
        . $stateToCheck->getAttribute('value'));
    }

    /* Time consuming to check every state option
     * May not even work, failed load may keep previous data on the chart
    foreach ($stateOptions as $st){
      $stateSelect->click();
      $st->click();
      // Should be 1 for bar, one for legend, 8 total
      $chartPoints = $salaryContainer->findElements(WebDriverBy::cssSelector(
        'rect.highcharts-point'));
      if (count($chartPoints) == 0){
        $this->error($soc . ': Empty salary chart for '
          . $st->getAttribute('value'));
      }
    }
     */

    echo $soc . ': Testing Education Page' . PHP_EOL;
    // Education Page (should always have data)
    // Basic check for chart existence, too many options
    $educationDialog = $this->wd->findElement(WebDriverBy::id('educationDialog'));
    if ($educationDialog->isDisplayed()){
      $this->error($soc . ': Education popup visible without mouse hover');
    }
    $this->wd->getMouse()->mouseMove($educationIcon->getCoordinates());
    if (!$educationDialog->isDisplayed()){
      $this->error($soc . ': Education popup not visible on mouse hover');
    }
    $educationIcon->click();
    // Now at education display page
    sleep(3);
    if (!is_null($this->screenshotDir)){
      $this->wd->takeScreenShot($this->screenshotDir . 'education.png');
    }
    // TODO: Find attr that controls line position, wait for completion
    $this->wd->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(
      WebDriverBy::cssSelector('path.highcharts-tracker')
    ));

    // Skills Page
    echo $soc . ': Testing Skills Page' . PHP_EOL;
    // Skills Page (should always have data)
    // Basic check for chart existence, too many options
    $skillsDialog = $this->wd->findElement(WebDriverBy::id('skillsDialog'));
    if ($skillsDialog->isDisplayed()){
      $this->error($soc . ': Skills popup visible without mouse hover');
    }
    $this->wd->getMouse()->mouseMove($skillsIcon->getCoordinates());
    if (!$skillsDialog->isDisplayed()){
      $this->error($soc . ': Skills popup not visible on mouse hover');
    }
    $skillsIcon->click();

    sleep(3);
    if (!is_null($this->screenshotDir)){
      $this->wd->takeScreenShot($this->screenshotDir . 'skills.png');
    }
    $skillsPageSuccess = null;
    $skillsPageFailure = null;
    try {
      $skillsPageSuccess = $this->wd->findElement(
        WebDriverBy::cssSelector('#skills-contentContainer g.highcharts-pie-series')
      );
    } catch (Exception $e){
      if (array_key_exists('skills', $requiredPages) && $requiredPages['skills']){
        $this->error($soc . ': No skills, specified to have skills.');
      }
    }
    try {
      $skillsPageFailure = $this->wd->findElement(WebDriverBy::cssSelector(
        '#skills-contentContainer .errorMsg'));
    } catch (Exception $e){
      if (array_key_exists('skills', $requiredPages) && !$requiredPages['skills']){
        $this->error($soc . ': Skills found, specified to have no skills.');
      }
    }
    // Should never both be null (ungraceful fail) or both non-null (both success, fail)
    if (is_null($skillsPageSuccess) == is_null($skillsPageFailure)){
      if (is_null($skillsPageSuccess)){
        $this->error($soc . ': Skills chart failed to load or gracefully fail');
      } else {
        $this->error($soc . ': Both skills chart and error message present');
      }
    }
    
    echo $soc . ': Testing Outlook Page' . PHP_EOL;
    // Outlook Page (should always have data)
    // Basic check for chart existence, too many options
    $careerOutlookDialog = $this->wd->findElement(WebDriverBy::id('careerOutlookDialog'));
    if ($careerOutlookDialog->isDisplayed()){
      $this->error($soc . ': Outlook popup visible without mouse hover');
    }
    $this->wd->getMouse()->mouseMove($careerOutlookIcon->getCoordinates());
    if (!$careerOutlookDialog->isDisplayed()){
      $this->error($soc . ': Outlook popup not visible on mouse hover');
    }
    $careerOutlookIcon->click();
    // Now at careerOutlook display page
    if (!is_null($this->screenshotDir)){
      $this->wd->takeScreenShot($this->screenshotDir . 'outlook.png');
    }
    $growthPercent = $this->wd->findElement(WebDriverBy::id('growthPercentText'));
    // TODO: Find better page test, may be "null" or non "" gibberish
    if ($growthPercent->getText() == ''){
      $this->error($soc . ': Blank growth percent text');
    }

    // World-of-Work Page
    echo $soc . ': Testing World-of-Work Page' . PHP_EOL;
    $worldOfWorkDialog = $this->wd->findElement(WebDriverBy::id('worldOfWorkDialog'));
    if ($worldOfWorkDialog->isDisplayed()){
      $this->error($soc . ': World-of-Work popup visible without mouse hover');
    }
    $this->wd->getMouse()->mouseMove($worldOfWorkIcon->getCoordinates());
    if (!$worldOfWorkDialog->isDisplayed()){
      $this->error($soc . ': World-of-Work popup not visible on mouse hover');
    }
    $worldOfWorkIcon->click();
    if (!is_null($this->screenshotDir)){
      $this->wd->takeScreenShot($this->screenshotDir . 'world-of-work.png');
    }
    $worldOfWorkPageSuccess = null;
    $worldOfWorkPageFailure = null;
    try {
      $worldOfWorkPageSuccess = $this->wd->findElement(WebDriverBy::id('d'));
    } catch (Exception $e){
      if (array_key_exists('world-of-work', $requiredPages) && $requiredPages['world-of-work']){
        $this->error($soc . ': No World-of-Work, specified to have World-of-Work.');
      }
    }
    try {
      $worldOfWorkPageFailure = $this->wd->findElement(WebDriverBy::cssSelector(
        '#world-of-work-body .errorMsg'));
    } catch (Exception $e){
      if (array_key_exists('world-of-work', $requiredPages) && !$requiredPages['world-of-work']){
        $this->error($soc . ': World-of-Work found, specified to have no World-of-Work.');
      }
    }
    // Should never both be null (ungraceful fail) or both non-null (both success, fail)
    if (is_null($worldOfWorkPageSuccess) == is_null($worldOfWorkPageFailure)){
      if (is_null($worldOfWorkPageSuccess)){
        $this->error($soc . ': World-of-Work chart failed to load or gracefully fail');
      } else {
        $this->error($soc . ': Both World-of-Work chart and error message present');
      }
    }
  }

  public function testFilteredSearch(){
    $this->enterBrowsePage();
    
    $searchCategory = $this->wd->findElement(
      WebDriverBy::partialLinkText('By Search'));
    $this->wd->getMouse()->mouseMove($searchCategory->getCoordinates());
    $searchCategory->click();
    $this->wd->wait()->until(
      WebDriverExpectedCondition::visibilityOfElementLocated(
        WebDriverBy::id('videoCheckbox')
    ));

    $videoCheckbox = $this->wd->findElement(WebDriverBy::id('videoCheckbox'));
    $videoCheckbox->click();
    $skillsCheckbox = $this->wd->findElement(WebDriverBy::id('skillsCheckbox'));
    $skillsCheckbox->click();

    $searchButton = $this->wd->findElement(WebDriverBy::cssSelector(
      '#fullSearchBar ~ span.input-group-btn > button.btn'));
    $searchButton->click();

    // Now at search results page
    $result = $this->wd->findElement(WebDriverBy::className('video-link'));
    $result->click();

    // Now at fully-specified career page
    $this->testCareerPage(['video'=>true, 'skills'=>true, 'world-of-work'=>true]);
  }
}
?>
