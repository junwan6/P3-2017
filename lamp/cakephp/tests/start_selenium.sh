#!/bin/bash

Xvfb :99 -ac &
export DISPLAY=:99

nohup java -Dwebdriver.chrome.driver="downloads/chromedriver" -jar selenium-server-standalone-3.4.0.jar &> server.log &
