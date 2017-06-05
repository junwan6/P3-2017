<?php
require_once('classes/PagesTest.php');
require_once('classes/CareerTest.php');
require_once('classes/AdminTest.php');

$testPages = false;
$testCareer = false;
$testAdmin = true;

$pagesTest = new PagesTest(['warnLinks' => true]);

if ($testPages){
  echo 'Loading Home page:' . PHP_EOL;
  echo '  First link test, including navbar in checks' . PHP_EOL;
  $pagesTest->testHomePage();

  echo 'Loading Donors page:' . PHP_EOL;
  $pagesTest->testDonorsPage();

  echo 'Loading Browse page:' . PHP_EOL;
  echo '  Browse link text may be blank if display:none' . PHP_EOL;
  $pagesTest->testBrowsePage();
}

// Reuse driver, currently at homepage
$careerTest = new CareerTest([
  'warnLinks' => true,
  'reuseDriver' => $pagesTest->detachDriver()
]);
if ($testCareer){
  $careerTest->enterBrowsePage();

  echo 'Testing known search "teacher special":' . PHP_EOL;
  $careerTest->testKnownSearch();

  echo 'Testing 10 random searches:' . PHP_EOL;
  $careerTest->testRandomSearch(10);

  // Requires fully-specified career in database
  echo 'Testing filtered search and fully-specified career:' . PHP_EOL;
  $careerTest->testFilteredSearch();

  // Following tests require socs to be as specified
  // If database changes, update SOCs
  echo 'Testing career with no optional fields (25-1199):' . PHP_EOL;
  $careerTest->testCareerPage(['video'=>false, 'skills'=>false,
    'world-of-work'=>false], "25-1199");
  echo 'Testing career with only world of work (25-1021)):' . PHP_EOL;
  $careerTest->testCareerPage(['video'=>false, 'skills'=>false,
    'world-of-work'=>true], "25-1021");
  echo 'Testing career with video, no skills, with world of work (25-1011):' . PHP_EOL;
  $careerTest->testCareerPage(['video'=>true,
    'skills'=>false], "25-1011");
}

$adminTest = new AdminTest([
  'warnLinks' => true,
  'reuseDriver' => $careerTest->detachDriver()
]);
if ($testAdmin){
  // Attempt to access admin pages without logging in
  echo 'Testing Admin Page access restrictions (logged out)' . PHP_EOL;
  $adminTest->testDeniedAccess();

  // Attempt to access admin pages with an unprivileged account
  echo 'Testing Admin Page access restrictions (unprivileged user)' . PHP_EOL;
  $adminTest->login('oooo','oooo');
  $adminTest->testDeniedAccess();

  // Login to admin account
  echo 'Testing Admin summary page' . PHP_EOL;
  $adminTest->login('pppp','pppp', true);
  $adminTest->testSummary();
}

?>
