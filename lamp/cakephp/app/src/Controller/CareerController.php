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
  public function displayCareer(...$path){
    $soc = '15-1142'; // TODO: Determine behavior on accessing career/ with no path
		if (!empty($path[0])) {
			$soc = $path[0];
    }
    $focus = 'video'; // Default view if accessing career/12-4125/ with no focus
    if (!empty($path[1])) {
      $focus = $path[1];
    }
    $this->set('soc', $soc);
    $this->set('focus', $focus);

    // Takes the place of the Model, CakePHP requires one-to-one model-to-table
    // which is not compatible with the current database
    $connection = ConnectionManager::get('test');
    switch($focus){
      case 'salary':
        $query = 'SELECT title, averageWage, averageWageOutOfRange, lowWage,' .
          'lowWageOutOfRange, medianWage, medianWageOutOfRange, highWage,' .
          ' highWageOutOfRange FROM Occupation WHERE soc = :soc';
        $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');

        //If there is more than one, something's gone wrong, but get last one anyways
        foreach ($results as $r){
          $this->set('occupationTitle', $r['title']);
          // Hardcoded values set in original code
          // No behavior given on averageWageOutOfRange, no rows are set to 1
          // TODO: Add to some global configuration file
          $this->set('NATAvg', $r['averageWage']);
          $this->set('NATHi', (($r['highWageOutOfRange'] == 0)?$r['highWage']:187200) );
          $this->set('NATMed', (($r['medianWageOutOfRange'] == 0)?$r['medianWage']:187200) );
          $this->set('NATLo', (($r['lowWageOutOfRange'] == 0)?$r['lowWage']:187200) );
          $this->set('NAT', true);
        }
        debug($results);

        $query = 'SELECT statecode, averageWage, averageWageOutOfRange, lowWage,' .
          'lowWageOutOfRange, medianWage, medianWageOutOfRange, highWage,' .
          ' highWageOutOfRange FROM StateOccupation WHERE soc = :soc';
        $results = $connection->execute($query, ['soc' => $soc])->fetchAll('assoc');

        foreach ($results as $r){
          // No behavior given on averageWageOutOfRange, no rows are set to 1
          $this->set($r['statecode'] . 'Avg', $r['averageWage']);
          $this->set($r['statecode'] . 'Hi', $r['highWage']);
          $this->set($r['statecode'] . 'Med', $r['medianWage']);
          $this->set($r['statecode'] . 'Lo', $r['lowWage']);
          $this->set($r['statecode'], true);
        }

        break;
      case 'education':
        break;
      case 'skills':
        break;
      case 'outlook':
        break;
      case 'world_of_work':
        break;
      case 'video':
      default:
        $focus = 'video';
    }

    // TODO: Figure out implementation of switcher (set JS variable?)
    $this->display($focus);
  }
}
