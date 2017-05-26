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

  function __construct($binPath='downloads/chrome-linux/chrome',
      $selServPath='http://localhost:4444/wd/hub', $timeout=5000,
      $pageURL='https://localhost/cake/', $warnLinks=false){
    $options = new ChromeOptions();
    // Driver path set by selenium server
    $options->setBinary($binPath);
    $capabilities = DesiredCapabilities::chrome();
    $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

    $this->wd = RemoteWebDriver::create($selServPath, $capabilities, $timeout);
    $this->url = $pageURL;
    $this->allowBrokenLinks = $warnLinks;
  }

  public function setBrokenLinksAllowed($setTo){
    $this->allowBrokenLinks = $setTo;
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
          echo $errStr . PHP_EOL;
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
  protected function testPageLinks($warnOnly=false){
    $linkTags = $this->wd->findElements(WebDriverBy::tagName('a'));
    $linkTags += $this->wd->findElements(WebDriverBy::tagName('area'));
    $linkTags += $this->wd->findElements(WebDriverBy::tagName('link'));
    $linkTags += $this->wd->findElements(WebDriverBy::tagName('script'));
    // Filtering done on 
    $hrefs = array_filter(array_map(function ($t){
      return $t->getAttribute('href');
    },$linkTags), function ($href){
      // Remove javascript links, self links, and nonlinks (<a> w/o href)
      // TODO: Find other disqualifying conditions
      $isToSamePage = ($href == '' || $href == '#');
      $isJavaScript = (substr($href,0,11) === 'javascript:');
      return !($isToSamePage || $isJavaScript);
    });
    foreach ($hrefs as $href){
      $httpsResponseCode = $this->getstatus($href);
      if (substr($httpsResponseCode,0,1) !== '2'){
        $errStr = "URL \"{$href}\" returned response code {$httpsResponseCode}";
        if ($warnOnly){
          echo $errStr . PHP_EOL;
        } else {
          throw new Exception($errStr);
        }
      }
    }
  }

  function __destruct(){
    $this->wd->quit();
  }
}
?>
