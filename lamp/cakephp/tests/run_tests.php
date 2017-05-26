<?php
require_once('classes/PagesTest.php');

$pagesTest = new PagesTest();
$pagesTest->setBrokenLinksAllowed(true);

echo 'Loading Home page:' . PHP_EOL;
$pagesTest->testHomePage();

echo 'Loading Donors page:' . PHP_EOL;
$pagesTest->testDonorsPage(true, true);

echo 'Loading Browse page:' . PHP_EOL;
$pagesTest->testBrowsePage(true, true);


?>
