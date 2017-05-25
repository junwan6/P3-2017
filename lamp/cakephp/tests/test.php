<?php
  require_once('vendor/autoload.php');
  use Facebook\WebDriver\WebDriverBy;
  use Facebook\WebDriver\Remote\RemoteWebDriver;
  use Facebook\WebDriver\Remote\DesiredCapabilities;
  use Facebook\WebDriver\Chrome\ChromeOptions;

  $options = new ChromeOptions();
  // Driver path set by selenium server
  $options->setBinary('downloads/chrome-linux/chrome');
  $capabilities = DesiredCapabilities::chrome();
  $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

  $web_driver = RemoteWebDriver::create('http://localhost:4444/wd/hub',
    $capabilities, 5000);

  $web_driver->get('https://localhost/cake/');
  echo $web_driver->getTitle();

  $elem = $web_driver->findElement(WebDriverBy::id('contentContainer'));
  echo $elem->getAttribute('class');

  $web_driver->quit();

?>
