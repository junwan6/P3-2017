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

		// store all soc codes into an array that is sorted in ascending order
		$socList = []; 
		foreach($results as &$r) {
			 $soc = $r['soc'];
		 	 //$socList[] = ['soc' => $soc];
			 $socList[] = $soc;
		 } 

		//$temp = $socList['foo'];
		 // debug($results);

		// if user hits the thumbs up button		
		if ($rating == 'up') {
		   do {
			$soc = $this->handleThumbsUp($socList[0]);

		      	// check to see if generated SOC code exists
		      	$validityQuery = 'SELECT soc FROM Occupation WHERE soc = :soc';
		      	$results = $connection->execute($validityQuery, ['soc'=>$soc])->fetchAll('assoc');
		   } while ($results == NULL); 
		}

		// if user hits the thumbs sideways button
		else if ($rating == 'mid') {
		     do {
				$soc = $this->handleThumbsMid($socList[0]);
                      		// check to see if generated SOC code exists
		        	$validityQuery = 'SELECT soc FROM Occupation WHERE soc = :soc';
		        	$results = $connection->execute($validityQuery, ['soc'=>$soc])->fetchAll('assoc');
                   } while ($results == NULL);
		}

		// if user hits the thumbs down button
		else if ($rating == 'down') {
		     do {
				$soc = $this->handleThumbsDown($socList[0]);
                        	// check to see if generated SOC code exists
			        $validityQuery = 'SELECT soc FROM Occupation WHERE soc = :soc';
		                $results = $connection->execute($validityQuery, ['soc'=>$soc])->fetchAll('assoc');
			} while ($results == NULL);
		}
		
		else {
			$soc = '17-2031';
		}
		
		
		/*
		if ($rating == 'up') 
			$rating_int = 1;
		else if ($rating == 'mid')
			$rating_int = 0; 
		else if ($rating == 'down')
			$rating_int = -1;
		
		$values = array(
                        'id' => $userId,
                        'prevSoc' => $old_soc,
						'nextSoc' => $soc,
                        'rating' => $rating_int,
						'time' => new DateTime('now')
                );
		
		$types = array(
                        'id' => gettype($userId),
                        'prevSoc' => gettype($old_soc),
						'nextSoc' => gettype($soc),
                        'rating' => gettype($rating_int),
						'time' => 'datetime'
                );
				
		$insert = $connection->insert('AlgorithmResults', $values, $types);
		*/
	
		$this->redirect(['controller' => 'career', 'action' => 'displayCareerSingle', $soc, 'video']);
	}

	// implementation of 'Thumbs Up' logic
	private function handleThumbsUp($socCode) {
		$oldSOC = $socCode;
		$newSOC = substr($oldSOC, 0, -2);

		//change the least significant digit of the SOC code 
		$lastDigit1 = rand(0, 9);
		$lastDigit2 = rand(0, 9);
		$newSOC = $newSOC . $lastDigit1 . $lastDigit2;

		// if the generated SOC happens to be the same as the old SOC code,
		// this can indicate that changing the least significant digit does not produce a
		// valid SOC code, no matter the digit. Move on to 3 least sig digs.
		if ($newSOC == $oldSOC) {
		   $newSOC = substr($oldSOC, 0, -3);
		   $lastDigit1 = rand(0, 9);
		   $lastDigit2 = rand(0,9);
		   $lastDigit3 = rand(0,9);
		   $newSOC = $newSOC . $lastDigit1 . $lastDigit2 . $lastDigit3;
		}
		
		return $newSOC;
	}

	// implementation of 'Thumbs Middle' logic
	private function handleThumbsMid($socCode) {
		$oldSOC = $socCode;
		$newSOC = substr($oldSOC, 0, -4);

                //change the 3 least significant digit of the SOC code
		$lastDigit = rand(0, 9);
		$lastDigit2 = rand(0,9);
		$lastDigit3 = rand(0,9);
		$lastDigit4 = rand(0, 9);
		$newSOC = $newSOC . $lastDigit . $lastDigit2 . $lastDigit3 .$lastDigit4;

                return $newSOC;
	 }
	 
        // implementation of 'Thumbs Down' logic
	private function handleThumbsDown($socCode) {
		$oldSOC = $socCode;
		$newSOC = substr($oldSOC, -5);

                //change the 2 most significant digit of the SOC code
		$firstDigit = rand(0, 9);
		$secondDigit = rand(0, 9);
		$newSOC = $firstDigit . $secondDigit . $newSOC;

                return $newSOC;
	 }
	 
	public function addRating($rating = null) {
		$connection = ConnectionManager::get($this->datasource);
		if ($rating == 'up') 
			$rating_int = 1;
		else if ($rating == 'mid')
			$rating_int = 0; 
		else if ($rating == 'down')
			$rating_int = -1;
		
		//$query = "INSERT INTO ViewHistory (id, soc, rating) Values(" . $userId . ",'" . $soc . "'," . $rating_int . ") ON DUPLICATE KEY UPDATE rating=" . $rating_int;
		/*
		$values = array(
                        'id' => $userId,
                        'soc' => $soc,
                        'rating' => $rating_int,
						'time' => new DateTime('now')
                );
		
		$types = array(
                        'id' => gettype($userId),
                        'soc' => gettype($soc),
                        'rating' => gettype($rating_int),
						'time' => 'datetime'
                );
				
		$insert = $connection->insert('ViewHistory', $values, $types);
		*/
		die();
	}
}
