# Installed:
* `facebook/php-webdriver`: Installed via composer
  * `composer require facebook/php-webdriver`
  * Requires `php-curl` and `php-zip`
* Chromium: `wget https://download-chromium.appspot.com/dl/Linux_x64?type=snapshots --content-disposition`
  * ChromeDriver: `wget https://chromedriver.storage.googleapis.com/2.29/chromedriver_linux64.zip`
  * Firefox considered but unused due to inability to set binary path
* Following http://www.alittlemadness.com/2008/03/05/running-selenium-headless/:
  * Running selenium/browser in headless (no GUI) mode
  * `sudo apt-get install xvfb`
  * `Xvfb :99 -ac`
  * `export DISPLAY=:99`
* Selenium server: `wget https://goo.gl/s4o9Vx --content-disposition`
  * Run with command `nohup java -Dwebdriver.chrome.driver="<path>" -jar <selenium server jar>`
    * Requires Java (`sudo apt-get install default-jre`)
