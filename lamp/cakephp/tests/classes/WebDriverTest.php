<?php
require_once('vendor/autoload.php');
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Exception\UnknownServerException
  as UnknownServerException;

class WebDriverTest {
  protected $url;
  protected $wd;
  protected $allowBrokenLinks;
  protected $suppressWarnings;
  protected $screenshotDir;
  protected $driverDetached;

  function __construct($opts){
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
    $args = array_replace($defaultArgs, $opts);
    
    $this->url = $args['pageURL'];
    $this->allowBrokenLinks = $args['warnLinks'];
    $this->screenshotDir = $args['ssDir'];
    $this->driverDetached = false;
    
    if (is_null($args['reuseDriver'])){
      $options = new ChromeOptions();
      // Driver path set by selenium server
      $options->setBinary($args['binPath']);
      $capabilities = DesiredCapabilities::chrome();
      $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
      $this->wd = RemoteWebDriver::create($args['selServPath'],
        $capabilities, $args['timeout']);
      $this->wd->get($this->url);
    } else {
      $this->wd = $args['reuseDriver'];
    }
  }

  /* Taken from https://stackoverflow.com/questions/17832181/how-to-check-if-a-https-site-exists-in-php
   * Gets the HTTP response code for a URL, needed as php-webdriver reads
   *   the CakePHP error page as a valid load and throws no errors
   * While specifics of the error page may be checked, disabling debug will
   *   change the error page, breaking all tests
   * Requres cURL, but needed for php-webdriver anyways
   */
  public static function getstatus($url) {
    $c = curl_init();
    curl_setopt($c, CURLOPT_HEADER, true);
    curl_setopt($c, CURLOPT_NOBODY, true);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($c, CURLOPT_URL, $url);
    // Added options, to suppress cURL stdout output, follow redirects
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);    
    curl_exec($c);
    $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
    curl_close($c);
    return $status;
  }

  /* Helper function to test all img tags on the current page
   * Does not work for images specified through CSS
   * Does not handle existing non-images as "src" of a tag
   */
  protected function testPageImgs($warnOnly=false){
    $imgTags = $this->wd->findElements(WebDriverBy::tagName('img'));
    // Page remains loaded, "src" attribute may be loaded whenever
    // webdriver get() not used due to issues with CakePHP error page
    // TODO: get MIME type from cURL, check against recognized image types
    foreach ($imgTags as $img){
      $src = $img->getAttribute('src');
      // No processing done of "src" url, must be a loadable URL
      // Valid being a respose of "2xx", redirects being followed
      // assert() not used as PHP 7.0 disables assert by default
      //   attempts to enable assert did not work, going with Exceptions
      $httpsResponseCode = $this->getstatus($src);
      if (substr($httpsResponseCode,0,1) !== '2'){
        $errStr = "Image \"{$src}\" returned code {$httpsResponseCode}";
        if ($warnOnly){
          if (!$this->suppressWarnings){
            echo $errStr . PHP_EOL;
          }
        } else {
          throw new Exception($errStr);
        }
      }
    }
  }

  /* Helper function to test all link-like tags on a page
   * Also tests stylesheets, existence of js files
   * As with testPageImgs, does not work with javascript redirects, etc.
   * Intentionally omits form and input tags
   * Currently only warns, due to incomplete modules
   */
  protected function testPageLinks($warnOnly=false, $ignoreNavbar=true){
    $searchedElem = null;
    if ($ignoreNavbar){
      $searchedElem = $this->wd->findElement(WebDriverBy::cssSelector(
        'body > div.cakephp-container'));
    } else {
      $searchedElem = $this->wd;
    }
    $linkTags = $searchedElem->findElements(WebDriverBy::tagName('a'));
    $linkTags += $searchedElem->findElements(WebDriverBy::tagName('area'));
    $linkTags += $searchedElem->findElements(WebDriverBy::tagName('link'));
    $linkTags += $searchedElem->findElements(WebDriverBy::tagName('script'));
    // Filtering done on 
    $links = array_filter(array_map(function ($t){
      return [
        'text' => $t->getText(),
        'href' => $t->getAttribute('href'),
        'isDisplayed' => $t->isDisplayed()
      ];
    },$linkTags), function ($link){
      $href = $link['href'];
      // Remove javascript links, self links, and nonlinks (<a> w/o href)
      // TODO: Find other disqualifying conditions
      $isToSamePage = ($href == '' || $href == '#');
      $isJavaScript = (substr($href,0,11) === 'javascript:');
      return !($isToSamePage || $isJavaScript);
    });
    foreach ($links as $link){
      $httpsResponseCode = $this->getstatus($link['href']);
      if (substr($httpsResponseCode,0,1) !== '2'){
        $errStr = ($link['isDisplayed']?
          "Link \"{$link['text']}\" -> ":"Undisplayed link ");
        $errStr .= "\"{$link['href']}\" returned {$httpsResponseCode}";
        if ($warnOnly){
          if (!$this->suppressWarnings){
            echo $errStr . PHP_EOL;
          }
        } else {
          throw new Exception($errStr);
        }
      }
    }
  }
  
  public function detachDriver(){
    $this->driverDetached = true;
    return $this->wd;
  }

  public static error($str="Unspcified Error", $level = 'error'){
    if ($level == 'warn'){
      echo $str;
    } else if ($level == 'error'){
      throw new Exception($str);
    }
  }

  function __destruct(){
    if (!$this->driverDetached){
      $this->wd->quit();
    }
  }
}
?>
