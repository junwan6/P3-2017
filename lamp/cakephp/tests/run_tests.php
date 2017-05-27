<?php
require_once('classes/PagesTest.php');
require_once('classes/CareerTest.php');

$pagesTest = new PagesTest(['warnLinks' => true]);


echo 'Loading Home page:' . PHP_EOL;
echo '  First link test, including navbar in checks' . PHP_EOL;
$pagesTest->testHomePage('home.png');

echo 'Loading Donors page:' . PHP_EOL;
$pagesTest->testDonorsPage('donors.png');

echo 'Loading Browse page:' . PHP_EOL;
echo '  Browse link text may be blank if display:none' . PHP_EOL;
$pagesTest->testBrowsePage('browse.png');

// Reuse driver, currently at homepage
$careerTest = new CareerTest([
  'warnLinks' => true,
  'reuseDriver' => $pagesTest->detachDriver()
]);
$careerTest->enterBrowsePage();

echo 'Testing known search "teacher special":' . PHP_EOL;
$careerTest->testKnownSearch();

echo 'Testing 10 random searches:' . PHP_EOL;
$careerTest->testRandomSearch(10);

echo 'Testing filtered search and fully-specified career:' . PHP_EOL;
$careerTest->testFilteredSearch();

?>
