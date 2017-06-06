<?php
require_once('WebDriverTest.php');
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class AlgorithmTest extends WebDriverTest{
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

	public function clickRating($rep) {
		$this->enterBrowsePage();
		$searchCategory = $this->wd->findElement(
			WebDriverBy::linkText('Random Occupation'));
		$this->wd->getMouse()->mouseMove($searchCategory->getCoordinates());
		$searchCategory->click();

		for ($i = 0; $i < $rep; $i++) {
			$rating_int = rand(0, 2);
			
			$this->wd->findElement(WebDriverBy::className('midthumb-selected'));
			
			if ($rating_int == 0 && $this->wd->findElement(WebDriverBy::className('midthumb-selected')) != null) 
				$rating_int = rand(1, 2);
			else if ($rating_int == 1 && $this->wd->findElement(WebDriverBy::className('upthumb-selected')) != null) {
				$rating_int = rand(0, 1);
				if ($rating_int == 1) $rating_int = 2;
			}
			else if ($rating_int == 2 && $this->wd->findElement(WebDriverBy::className('downthumb-selected')) != null) 
				$rating_int = rand(0, 1);
			
			
			// thumbs mid
			if ($rating_int == 0) {
				$thumb_id = 'midthumb';
				$nextcareer_id = 'next-career-mid';
			}
			// thumbs up
			else if ($rating_int == 1) {
				$thumb_id = 'upthumb';
				$nextcareer_id = 'next-career-up';
			}
			// thumbs down
			else {
				$thumb_id = 'downthumb';
				$nextcareer_id = 'next-career-down';
			}
			
			$thumb = $this->wd->findElement(WebDriverBy::id($thumb_id));
			$this->wd->getMouse()->mouseMove($thumb->getCoordinates());
			$thumb->click();
			
			$nextcareer = $this->wd->findElement(WebDriverBy::id($nextcareer_id));
			$this->wd->getMouse()->mouseMove($nextcareer->getCoordinates());
			$nextcareer->click();
		}
	}
}
?>
