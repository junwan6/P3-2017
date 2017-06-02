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
class AlgorithmController extends PagesController
{
	//This function obtains all SOC codes from the database
	public function nextCareer($rating = null) {
		
		$connection = ConnectionManager::get($this->datasource);
		$query = 'SELECT soc FROM Occupation';
		$results = $connection->execute($query)->fetchAll('assoc');

		//echo 'Total results: ' . $results->num_rows;
		/*
		$found = FALSE;
		foreach ($results as $r) {
			if ($r['soc'] == "25-2022") {
				$found = TRUE;
				break;
			}
		}
		
		if ($found) {
			$soc = '29-1066';
		}
		else {
			$soc = '11-3111';
		}
		*/
		
		if ($rating == 'up') {
			$soc = '25-4012';
		}
		else if ($rating == 'mid') {
			$soc = '19-2012';
		}
		else if ($rating == 'down') {
			$soc = '15-1111';
		}
		else {
			$soc = '17-2031';
		}
	
		$this->redirect(['controller' => 'career', 'action' => 'displayCareerSingle', $soc, 'video']);
	}
}
