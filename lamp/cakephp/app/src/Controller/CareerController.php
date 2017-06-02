<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Datasource\ConnectionManager;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class CareerController extends PagesController
{
  /* Helper function for summarizing data for sidebar icon display
   * Also used by Search page for results summary
  */
  public function setupIconTemplateData($r){
    $iconData = [];
    $iconData['occupationTitle'] = $r['title'];
    $iconData['averageWage'] = '$' . number_format($r['averageWage']);
    $iconData['wageTypeIsAnnual'] = ($r['wagetype'] == 'annual');
    $percentGrowth = round(floatval($r['careerGrowth']));
    $percentGrowth = ($percentGrowth > 0)?('+' . $percentGrowth):$percentGrowth;
    $iconData['careerGrowth'] = $percentGrowth . '%';
    $eduMapping = [
      'none' => 'No education required',
      'high school' => 'High school education',
      'some college' => 'Some college',
      'postsecondary degree' => 'Postsecondary nondegree award',
      'associate' => 'Associate\'s degree',
      'bachelor' => 'Bachelor\'s degree',
      'master' => 'Master\'s degree',
      'doctoral or professional' => 'Doctoral or Professional degree'
    ];
    $iconData['educationRequired'] = $eduMapping[$r['educationRequired']];

    return $iconData;
  }

  /* Helper function for parsing skills data into text parsed by Javascript
   * Also used by Skills page for verbose display
  */
  public function setupSkillIconTemplateData($r){
    $skillsArray = [];
    $skillsText = json_decode($r['skillsText'],true);
    $intelligences = [
      'naturalist' => 'Naturalistic',
      'musical' => 'Musical',
      'logical' => 'Logical-Mathematical',
      'existential' => 'Existential',
      'interpersonal' => 'Interpersonal',
      'intrapersonal' => 'Intra-personal',
      'body' => 'Bodily-Kinesthetic',
      'linguistic' => 'Linguistic',
      'spatial' => 'Sptial'
    ];
    foreach ($intelligences as $intel => $intelName){
      if ($r[$intel . 'Percent'] > 0){
			$skillsArray[] = [$r[$intel . 'Percent'], $intelName . " Intelligence",
				implode(',', $skillsText[$intel . 'Skills'])];
      }
    }
    usort($skillsArray, function($a,$b){
      $n = $b[0]-$a[0];
      // Sign extraction taken from
      // https://stackoverflow.com/questions/7556574/how-to-get-sign-of-a-number
      // Necessary as usort converts to int, this ensures correct sorting
      return ($n > 0) - ($n < 0);
    });
    return implode(',', array_map(function($intel){
      return implode(',', $intel);
    }, $skillsArray));
  }

  /* Single display function for all career pages (on single page)
   * Consolidated queries into one per table
  */
  public function displayCareerSingle(...$path){
    $soc = '15-1142'; // soc must be set, due to routing. Default anyways
		if (isset($path[0])) {
			$soc = $path[0];
    }

    // Redirects to video page on invalid/missing focus, to update history
    $focus = $path[1];
    if (!isset($path[1]) || !in_array($path[1], ['video', 'salary', 'education',
      'outlook', 'skills', 'world-of-work'])){
      $this->redirect(['controller' => 'career',
        'action' => 'displayCareerSingle', $soc, 'video']);
    }

    // No additional processing done, make available to View
    $this->set('soc', $soc);
    $this->set('focus', $focus);

    $connection = ConnectionManager::get($this->datasource);

    // Get data from Occupation
    
    // Fields consolidated from those needed by views
    // TODO: replace with * if these are all the columns
    $occupationFields = ['title', 'averageWage', 'wagetype', 'careerGrowth',
      'educationRequired', 'averageWageOutOfRange', 'lowWage', 'lowWageOutOfRange',
      'medianWage', 'medianWageOutOfRange', 'highWage', 'highWageOutOfRange',
      'currentEmployment', 'futureEmployment', 'jobOpenings'];
    // Hardcoded values for lengths of undergrad, grad, etc.
    // TODO: Create programatically for possible additions (?)
    //    column value => [(string)classification, (string)full name,
    //    (string)total length, (int)ugrad, (int)grad, (bool)has_grad]
    $schoolData = [
      'associate' => ['Undergraduate', 'Associate\'s Degree', '2', 2, 0, false],
      'bachelor' => ['Undergraduate', 'Bachelor\'s Degree', '4', 4, 0, false],
      'master' => ['Graduate', 'Master\'s Degree', '6', 4, 2, true],
      'doctoral or professional' => ['Graduate or Professional',
        'Doctorage or Professional Degree', '8', 4, 4, true]
      ];
    $schoolFields = ['typeOfSchool', 'typeOfDegree' ,'yearsInSchool'
      ,'yearsInUndergrad' ,'yearsInGrad', 'gradSchool'];
    $query = 'SELECT ' . implode(',', $occupationFields) . ' FROM Occupation WHERE soc = :soc';
    $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');

    // State data initialized here for national averages
    $avg = [];
    $hi = [];
    $med = [];
    $lo = [];
    $sts = [];
    
    //If there is more than one, something's gone wrong, but get last one anyways
    foreach ($results as $r){
      // set averageWage, wageTypeIsAnnual, educationRequired, careerGrowth for icons
      $this->set($this->setupIconTemplateData($r));
      
      // Hardcoded values set in original code
      // No behavior given on averageWageOutOfRange, no rows are set to 1
      // TODO: Add to some global configuration file
      $avg['NAT'] = $r['averageWage'];
      $hi['NAT'] = (($r['highWageOutOfRange'] == 0)?$r['highWage']:187200);
      $med['NAT'] = (($r['medianWageOutOfRange'] == 0)?$r['medianWage']:187200);
      $lo['NAT'] = (($r['lowWageOutOfRange'] == 0)?$r['lowWage']:187200);
      $sts[] = 'NAT';

      $this->set(array_combine($schoolFields, $schoolData[$r['educationRequired']]));

      $this->set('currentEmployment', number_format(floatval($r['currentEmployment']) * 1000));
      $this->set('futureEmployment', number_format(floatval($r['futureEmployment']) * 1000));
      $this->set('jobOpenings', number_format(floatval($r['jobOpenings']) * 1000));
    }
    if (count($results) == 0){
      throw new NotFoundException;
    }

    // Get data from StateOccupation
    
    // TODO: turn list of columns into * or imploded array
    $query = 'SELECT statecode, averageWage, averageWageOutOfRange, lowWage,' .
      'lowWageOutOfRange, medianWage, medianWageOutOfRange, highWage,' .
      ' highWageOutOfRange FROM StateOccupation WHERE soc = :soc';
    $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');

    // Change from original implementation, array rather than separate for each state
    foreach ($results as $r){
      if ($r['averageWage'] != 0){
        $avg[$r['statecode']] = $r['averageWage'];
        $hi[$r['statecode']] = $r['highWage'];
        $med[$r['statecode']] = $r['medianWage'];
        $lo[$r['statecode']] = $r['lowWage'];
        $sts[] = $r['statecode'];
      }
    }
    $this->set('avg', $avg);
    $this->set('hi', $hi);
    $this->set('med', $med);
    $this->set('lo', $lo);
    $this->set('sts', $sts);
    
    $query = 'SELECT * FROM Skills WHERE soc = :soc';
    $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');
    foreach ($results as $r){
      $this->set('skillsArray', $this->setupSkillIconTemplateData($r));
    }
    
    // Get data from OccupationInterests

    $interestsFields = ['realistic', 'investigative', 'artistic', 'social', 'enterprising',
      'conventional'];
    $query = 'SELECT ' . implode(',', $interestsFields) . ' FROM OccupationInterests WHERE soc = :soc';
    $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');
    if (count($results) == 0){
      $this->set('noData', true);
    } else {
      // Template variables named same as table columns, and no processing needed
      foreach ($results as $r){
        $this->set($r);
      }
      $this->set('noData', false);
    }
    
    // Get data from Videos

    $videos = [];
    $query = 'SELECT * FROM Videos WHERE soc = :soc';
    $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');
    foreach ($results as $r){
      if (!isset($videos[$r['personNum']])){
        $videos[$r['personNum']] = [];
      }
      $videos[$r['personNum']]['name'] = $r['person'];
      if (!isset($videos[$r['personNum']]['videos'])){
        $videos[$r['personNum']]['videos'] = [];
      }
      $videos[$r['personNum']]['videos'][$r['questionNum']] =
        ['question' => $r['question'],
        'fileName' => $r['soc'] . '_' . $r['personNum'] . '_' .
          $r['person'] . '/' . rawurlencode($r['fileName'])];
    }
    $this->set('videos', $videos);

    $this->display('career');
  }

  /* Search function, finds all careers with title matching all keywords
   * Separated by spaces or commas
   * Support for additional filtering if desired
   * TODO: Implement better search algorithm besides AND ... AND ... AND
   */
  public function search(){
    $connection = ConnectionManager::get($this->datasource);
    $keywords = preg_split('/[,\s]+/', $this->request->getQuery('q'));
    if (count($this->request->query) == 1 && !is_null($this->request->getQuery('q'))
      && $this->request->getQuery('q') == ''){
      $this->set('query', '');
      $this->set('resultsEmpty', true);
      $this->display('search');
      return;
    }

    // Variable substitution must avoid string operations due to SQL injection
    $keySubs = [];
    $query = 'SELECT soc,title,wagetype,averageWage,educationRequired,careerGrowth FROM Occupation WHERE ';
    $query .= implode(' AND ', array_map(function($s) use (&$keySubs){
      $sub = 'keyword' . count($keySubs);
      $keySubs[$sub] = '%' . $s . '%';
      return 'title LIKE :' . $sub;
    }, $keywords));
    // TODO: add better search filters
    if (!is_null($this->request->getQuery('video'))){
      $query .= ' AND soc IN (SELECT soc FROM Videos)';
    }
    if (!is_null($this->request->getQuery('skills'))){
      $query .= ' AND soc IN (SELECT soc FROM Skills)';
    }
    if (!is_null($this->request->getQuery('growth'))){
      $query .= ' AND careerGrowth >= 0.0';
    }
    $results = $connection->execute($query, $keySubs)->fetchAll('assoc');
    $searchResults = [];
    foreach ($results as $r){
      $soc = $r['soc'];
      $summary = $this->setupIconTemplateData($r);
      $summary['soc'] = $soc;
      $searchResults[] = $summary;
    }
    $this->set('query', $this->request->getQuery('q'));
    $this->set('resultsEmpty', count($searchResults) == 0);
    $this->set('results', $searchResults);
    $this->display('search');
  }
  
  /* Picks an SOC from the database and redirects to the page
   * Optionally takes x,y coordinates for 'random' by World of Work
   * TODO: Add filters as in search
   */
  public function redirectRandom(){
    // TODO: Explicit error handling for empty database?
    $soc = '15-1142';
    $connection = ConnectionManager::get($this->datasource);
    $results = [];

    $wowX = $this->request->getQuery('x');
    $wowY = $this->request->getQuery('y');
    if (is_null($wowX) || is_null($wowY)){
      $query = 'SELECT soc FROM Occupation ORDER BY RAND() LIMIT 1';
      $results = $connection->execute($query)->fetchAll('assoc');
    } else {
      // Implementation taken from 'occupation.js:getRandomSOCInWOWRegion()'
      $rad = atan2($wowY, $wowX);
      $rad = ($rad >= 0)?($rad):($rad + 2.0*pi());
      $deg = $rad * (180.0/pi());
      $region = floor(min(max(0,$deg/30.0), 11));
      // Additional check added as Interests may be defined for occupation without other data
      $query = 'SELECT soc FROM OccupationInterests WHERE wowRegion = :region AND soc IN (SELECT soc FROM Occupation) ORDER BY RAND() LIMIT 1';
      $results = $connection->execute($query, ['region' => $region])->fetchAll('assoc');
    }

    // Should always have one result as per query
    // If no results, $soc never set from default
    foreach ($results as $r){
      $soc = $r['soc'];
    }

    $this->redirect(['controller' => 'career', 'action' => 'displayCareerSingle', $soc, 'video']);
  }

  /* AJAX return with list of occupation titles, currently as one chunk
   * Called on focus on search element, only once
   */
  // TODO: Expand for partial queries, to reduce load on server
  // IN SCRIPT: Delay inverse relation to partial entry length?
  //   ie. type one letter, wait 5 seconds, type 3 letters, wait 1 second, etc.
  public function getAutoComplete(){
    $query = $this->request->getQuery('q');
    $connection = ConnectionManager::get($this->datasource);
    $keywords = preg_split('/[,\s]+/', $this->request->getQuery('q'));
    
    $keySubs = [];
    // title field must be named 'label' to be displayed under search bar
    // 'soc' used as getter by script to create link
    $query = 'SELECT soc,title AS label FROM Occupation WHERE ';
    $query .= implode(' AND ', array_map(function($s) use (&$keySubs){
      $sub = 'keyword' . count($keySubs);
      $keySubs[$sub] = '%' . $s . '%';
      return 'title LIKE :' . $sub;
    }, $keywords));
    $results = $connection->execute($query, $keySubs)->fetchAll('assoc');
    echo json_encode($results);
    // attempt to prevent additional text from being added
    // TODO: ensure only JSON or blank is sent, by empty layout, header/enclosement, etc.
    die();
  }
}
