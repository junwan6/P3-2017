# Installing Dependencies:
* `facebook/php-webdriver`: Installed via composer
  * `composer require facebook/webdriver`
  * Requires `php-curl` and `php-zip`
* Chromium: `wget https://download-chromium.appspot.com/dl/Linux_x64?type=snapshots --content-disposition`
  * ChromeDriver: `wget https://chromedriver.storage.googleapis.com/2.29/chromedriver_linux64.zip`
  * `sudo apt-get install libxi6 libgconf-2-4 libxss1 libgtk-3-0`
  * Firefox considered but unused due to inability to set binary path
* Following http://www.alittlemadness.com/2008/03/05/running-selenium-headless/:
  * Running selenium/browser in headless (no GUI) mode
  * `sudo apt-get install xvfb`
  * `Xvfb :99 -ac`
  * `export DISPLAY=:99`
* Selenium server: `wget https://goo.gl/s4o9Vx --content-disposition`
  * Run with command `nohup java -Dwebdriver.chrome.driver="<path>" -jar <selenium server jar>`
    * Requires Java (`sudo apt-get install default-jre`)
  * Added `start_selenium.sh` script (no checks for already running, etc.)

#Directory
* `run_tests.php`: Single page to start running of tests defined in `classes\`
* `start_selenium.sh`: Basic startup script for selenium server, on machine with no display
* `selenium-server-standalong-*.jar`: Standalong Selenium server used to emulate user interaction with a browser
* `server.log`: Redirected output of selenium server
* `classes/`: PHP classes, containing tests grouped together by Controller, etc.
* `downloads/`: Files downloaded to set up testing environment, such as chromium binary, chrome webdriver binary
* `composer.*`, `vendor/`: Composer files created on installing php-webdriver
