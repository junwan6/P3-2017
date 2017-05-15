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
/*
  public function displayCareer(...$path){
    $soc = '15-1142'; // soc must be set, due to routing. Default anyways
		if (!empty($path[0])) {
			$soc = $path[0];
    }
    $focus = 'none'; // routing forces focus to be a valid value. Defaults to default: case anyways
    if (!empty($path[1])) {
      $focus = $path[1];
    }
    $this->set('soc', $soc);
    $this->set('focus', $focus);

    // Takes the place of the Model, CakePHP requires one-to-one model-to-table
    // which is not compatible with the current database
    $connection = ConnectionManager::get($this->datasource);
    // Fields used by all pages:
    //  Occupation: title => occupationTitle
    //  Occupation: wageType => wageTypeIsAnnual
    //  Occupation: averageWage
    //  Occupation: careerGrowth
    //  Occupation: educationRequired 
    //  Skills: * => skillsArray
    $occupationFields = ['title', 'averageWage', 'wagetype', 'careerGrowth', 'educationRequired'];
    switch($focus){
    case 'salary':
      // in addition to ['title', 'averageWage', 'wagetype', 'careerGrowth', 'educationRequired']
      array_push($occupationFields, 'averageWageOutOfRange', 'lowWage', 'lowWageOutOfRange',
        'medianWage', 'medianWageOutOfRange', 'highWage', 'highWageOutOfRange');
      $query = 'SELECT ' . implode(',', $occupationFields) . ' FROM Occupation WHERE soc = :soc';
      $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');

      //If there is more than one, something's gone wrong, but get last one anyways
      foreach ($results as $r){
        // set averageWage, wageTypeIsAnnual, educationRequired, careerGrowth for icons
        $this->set($this->setupIconTemplateData($r));
        
        // Hardcoded values set in original code
        // No behavior given on averageWageOutOfRange, no rows are set to 1
        // TODO: Add to some global configuration file
        $this->set('NATAvg', $r['averageWage']);
        $this->set('NATHi', (($r['highWageOutOfRange'] == 0)?$r['highWage']:187200) );
        $this->set('NATMed', (($r['medianWageOutOfRange'] == 0)?$r['medianWage']:187200) );
        $this->set('NATLo', (($r['lowWageOutOfRange'] == 0)?$r['lowWage']:187200) );
        $this->set('NAT', true);
      }

      $query = 'SELECT statecode, averageWage, averageWageOutOfRange, lowWage,' .
        'lowWageOutOfRange, medianWage, medianWageOutOfRange, highWage,' .
        ' highWageOutOfRange FROM StateOccupation WHERE soc = :soc';
      $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');

      foreach ($results as $r){
        if ($r['averageWage'] != 0){
          // No behavior given on averageWageOutOfRange, no rows are set to 1
          $this->set($r['statecode'] . 'Avg', $r['averageWage']);
          $this->set($r['statecode'] . 'Hi', $r['highWage']);
          $this->set($r['statecode'] . 'Med', $r['medianWage']);
          $this->set($r['statecode'] . 'Lo', $r['lowWage']);
          $this->set($r['statecode'], true);
        }
      }
      $query = 'SELECT * FROM Skills WHERE soc = :soc';
      $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');
      foreach ($results as $r){
        $this->set('skillsArray', $this->setupSkillIconTemplateData($r));
      }
    break;

    case 'education':
      // Hardcoded into occupation-controller.js
      // TODO: Add to some hardcoded configuration file
      $schoolData = [
        'associate' => ['Undergraduate', 'Associate\'s Degree', '2', 2, 0, false],
        'bachelor' => ['Undergraduate', 'Bachelor\'s Degree', '4', 4, 0, false],
        'master' => ['Graduate', 'Master\'s Degree', '6', 4, 2, true],
        'doctoral or professional' => ['Graduate or Professional',
          'Doctorage or Professional Degree', '8', 4, 4, true]
        ];
      $schoolFields = ['typeOfSchool', 'typeOfDegree' ,'yearsInSchool'
        ,'yearsInUndergrad' ,'yearsInGrad', 'gradSchool'];
      // in addition to ['title', 'averageWage', 'wagetype', 'careerGrowth', 'educationRequired']
      array_push($occupationFields, 'lowWage', 'medianWage', 'highWage');
      $query = 'SELECT ' . implode(',', $occupationFields) . ' FROM Occupation WHERE soc = :soc';
      $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');

      //If there is more than one, something's gone wrong, but get last one anyways
      foreach ($results as $r){
        // set averageWage, wageTypeIsAnnual, educationRequired, careerGrowth for icons
        $this->set($this->setupIconTemplateData($r));
        
        // Does not use 'OutOfRange' values like Salary page
        // TODO: Figure out which to use, make consistent
        $this->set('NATAvg', $r['averageWage']);
        $this->set('NATHi', $r['highWage']);
        $this->set('NATMed', $r['medianWage']);
        $this->set('NATLo', $r['lowWage']);
        $this->set('NAT', true);

        $this->set(array_combine($schoolFields, $schoolData[$r['educationRequired']]));
      }

      $query = 'SELECT statecode, averageWage, averageWageOutOfRange, lowWage,' .
        'lowWageOutOfRange, medianWage, medianWageOutOfRange, highWage,' .
        ' highWageOutOfRange FROM StateOccupation WHERE soc = :soc';
      $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');

      foreach ($results as $r){
        if ($r['averageWage'] != 0){
          // No behavior given on averageWageOutOfRange, no rows are set to 1
          $this->set($r['statecode'] . 'Avg', $r['averageWage']);
          $this->set($r['statecode'] . 'Hi', $r['highWage']);
          $this->set($r['statecode'] . 'Med', $r['medianWage']);
          $this->set($r['statecode'] . 'Lo', $r['lowWage']);
          $this->set($r['statecode'], true);
        }
      }
      $query = 'SELECT * FROM Skills WHERE soc = :soc';
      $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');
      foreach ($results as $r){
        $this->set('skillsArray', $this->setupSkillIconTemplateData($r));
      }
    break;

    case 'skills':
      $query = 'SELECT ' . implode(',', $occupationFields) . ' FROM Occupation WHERE soc = :soc';
      $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');

      //If there is more than one, something's gone wrong, but get last one anyways
      foreach ($results as $r){
        // set averageWage, wageTypeIsAnnual, educationRequired, careerGrowth for icons
        $this->set($this->setupIconTemplateData($r));
        $this->set('occupationTitle', $r['title']);
      }
      $query = 'SELECT * FROM Skills WHERE soc = :soc';
      $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');
      foreach ($results as $r){
        $this->set('skillsArray', $this->setupSkillIconTemplateData($r));
      }
    break;

    case 'outlook':
      array_push($occupationFields, 'currentEmployment', 'futureEmployment', 'jobOpenings');
      $query = 'SELECT ' . implode(',', $occupationFields) . ' FROM Occupation WHERE soc = :soc';
      $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');

      //If there is more than one, something's gone wrong, but get last one anyways
      foreach ($results as $r){
        // set averageWage, wageTypeIsAnnual, educationRequired, careerGrowth for icons
        $this->set($this->setupIconTemplateData($r));

        $this->set('occupationTitle', $r['title']);
        $this->set('currentEmployment', number_format(floatval($r['currentEmployment']) * 1000));
        $this->set('futureEmployment', number_format(floatval($r['futureEmployment']) * 1000));
        $this->set('jobOpenings', number_format(floatval($r['jobOpenings']) * 1000));
      }
      $query = 'SELECT * FROM Skills WHERE soc = :soc';
      $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');
      foreach ($results as $r){
        $this->set('skillsArray', $this->setupSkillIconTemplateData($r));
      }
    break;

    case 'world-of-work':
      $query = 'SELECT ' . implode(',', $occupationFields) . ' FROM Occupation WHERE soc = :soc';
      $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');

      //If there is more than one, something's gone wrong, but get last one anyways
      foreach ($results as $r){
        // set averageWage, wageTypeIsAnnual, educationRequired, careerGrowth for icons
        $this->set($this->setupIconTemplateData($r));
        $this->set('occupationTitle', $r['title']);
      }

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
      $query = 'SELECT * FROM Skills WHERE soc = :soc';
      $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');
      foreach ($results as $r){
        $this->set('skillsArray', $this->setupSkillIconTemplateData($r));
      }
    break;

    // Temporary minimal change to database
    //   Videos table added columns 'person', 'fileName'
    //   CREATE TABLE Videos (
    //     soc CHAR(7),
    //     personNum TINYINT,
    //     person TEXT,
    //     questionNum TINYINT,
    //     question TEXT,
    //     fileName TEXT,
    //     PRIMARY KEY(soc, personNum, questionNum)
    //   );
    // TODO: Make database have proper form instead of above
    // Required following changes to database:
    //   Create Interview table (Replaced Videos, more accurate name) 
    //   Interview Table add column 'fileName'
    //   Interview Table repurpose column 'personNum' for order of showing if multiple
    //   Create People table (proper form, avoids redundant names with personNum)
    //     soc, personNum, name
    //     primary key (soc, personNum)
    case 'video':
      $query = 'SELECT ' . implode(',', $occupationFields) . ' FROM Occupation WHERE soc = :soc';
      $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');

      //If there is more than one, something's gone wrong, but get last one anyways
      foreach ($results as $r){
        // set averageWage, wageTypeIsAnnual, educationRequired, careerGrowth for icons
        $this->set($this->setupIconTemplateData($r));
        $this->set('occupationTitle', $r['title']);
      }
      $query = 'SELECT * FROM Skills WHERE soc = :soc';
      $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');
      foreach ($results as $r){
        $this->set('skillsArray', $this->setupSkillIconTemplateData($r));
      }

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
            $r['person'] . '/' . $r['fileName']];
      }
      $this->set('videos', $videos);
    break;
    
    default:
      $this->redirect(['controller' => 'career', 'action' => 'displayCareer', $soc, 'video']);
      return;
    }

    // TODO: Figure out implementation of switcher (set JS variable?)
    // Currently switches webpages
    $this->display($focus);
  }
*/
  // TODO: Implement better search
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

  public function redirectRandom(){
    $soc = '15-1142'; // TODO: Error handling for empty database?
    $connection = ConnectionManager::get($this->datasource);
    $results = [];

    $wowX = $this->request->getQuery('x');
    $wowY = $this->request->getQuery('y');
    if (!is_null($wowX) && !is_null($wowY)){
      // Implementation taken from 'occupation.js:getRandomSOCInWOWRegion()'
      $rad = atan2($wowY, $wowX);
      $rad = ($rad >= 0)?($rad):($rad + 2.0*pi());
      $deg = $rad * (180.0/pi());
      $region = floor(min(max(0,$deg/30.0), 11));
      // Additional check added as Interests may be defined for occupation without other data
      $query = 'SELECT soc FROM OccupationInterests WHERE wowRegion = :region AND soc IN (SELECT soc FROM Occupation) ORDER BY RAND() LIMIT 1';
      $results = $connection->execute($query, ['region' => $region])->fetchAll('assoc');
    } else {
      $query = 'SELECT soc FROM Occupation ORDER BY RAND() LIMIT 1';
      $results = $connection->execute($query)->fetchAll('assoc');
    }

    // Should always have one result as per query
    foreach ($results as $r){
      $soc = $r['soc'];
    }
    // TODO: Handle 0 results, from full or constrained random

    $this->redirect(['controller' => 'career', 'action' => 'displayCareerSingle', $soc, 'video']);
  }

  public function displayCareerSingle(...$path){
    $soc = '15-1142'; // soc must be set, due to routing. Default anyways
		if (isset($path[0])) {
			$soc = $path[0];
    }

    $focus = $path[1];
    if (!isset($path[1]) || !in_array($path[1], ['video', 'salary', 'education',
      'outlook', 'skills', 'world-of-work'])){
      $focus = 'video';
    }

    $this->set('soc', $soc);
    $this->set('focus', $focus);

    $connection = ConnectionManager::get($this->datasource);

    $occupationFields = ['title', 'averageWage', 'wagetype', 'careerGrowth',
      'educationRequired', 'averageWageOutOfRange', 'lowWage', 'lowWageOutOfRange',
      'medianWage', 'medianWageOutOfRange', 'highWage', 'highWageOutOfRange',
      'currentEmployment', 'futureEmployment', 'jobOpenings'];
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

    $query = 'SELECT statecode, averageWage, averageWageOutOfRange, lowWage,' .
      'lowWageOutOfRange, medianWage, medianWageOutOfRange, highWage,' .
      ' highWageOutOfRange FROM StateOccupation WHERE soc = :soc';
    $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');

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
          $r['person'] . '/' . $r['fileName']];
    }
    $this->set('videos', $videos);

    $this->display('career');
  }
}
