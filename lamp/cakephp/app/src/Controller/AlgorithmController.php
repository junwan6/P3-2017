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
use DateTime;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class AlgorithmController extends PagesController
{
	// checkRating handles rating history for the user if he/she is logged in
	public function checkRating($rating = null, $soc = null) {
		$userId = $this->request->session()->read('id');
		// result_rating is echoed back to ajax call in video.ctp 
		// the value 2 indicates that the user is not signed in, and thus the video has no ratings
		$result_rating = 2;
		
		if ($rating == 'up') 
			$rating_int = 1;
		else if ($rating == 'mid')
			$rating_int = 0; 
		else if ($rating == 'down')
			$rating_int = -1;
		
		// user is logged in
		if ($userId != null) {
			$connection = ConnectionManager::get($this->datasource);
			$query = 'SELECT rating FROM ViewHistory WHERE id= ? AND soc = ?';
			$results = $connection->execute($query, [$userId, $soc], ['integer', 'string'])->fetchAll('assoc');
			
			// the user has rated the career before
			if ($results != NULL) {
				$result_rating = $results[0]['rating'];
				
				if ($rating != 'none') {
				// user selected same rating as before, undoing their rating and deleting from the database
					if ($result_rating == $rating_int) 
						$this->deleteRating($connection, $userId, $soc);
		
					// user selected different rating, updating the database
					else {
						$result_rating = $rating_int;
						$this->updateRating($connection, $rating_int, $userId, $soc);
					}
				}
			}
			// the user never rated the career before and must insert into the database
			else {
				$result_rating = $rating_int;
				$this->addRating($connection, $rating_int, $userId, $soc);
			}
		}
		// user is not logged in but rated the career
		else if ($rating != 'none') 
			$result_rating = $rating_int;
		
		echo $result_rating;
		
		die();
	}

	// addRating records the user's rating of the current job into the database
	private function addRating($connection, $rating_int, $userId, $soc) {
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
	}

	// updateRating changes the user's rating of the current job in the db if he or she rates the same job differently 
	private function updateRating($connection, $rating_int, $userId, $soc) {
		$update_query = 'UPDATE ViewHistory SET rating = ?, time = ? WHERE id = ? AND soc = ?'; 
		$connection->execute($update_query, [$rating_int, new DateTime('now'), $userId, $soc], ['integer', 'datetime', 'integer', 'string']);
	}

	// deleteRating deletes the user's rating of the current job from the db if they 'unpress' a rating button.
	// (When a button is pressed, the button is highlighted)
	private function deleteRating($connection, $userId, $soc) {
		$delete_query = 'DELETE FROM ViewHistory WHERE id = ? AND soc = ?';
		$connection->execute($delete_query, [$userId, $soc], ['integer', 'string']);
	}
	
	//This function obtains all SOC codes from the database
	public function nextCareer($rating = null, $old_soc = null) {
		$userId = $this->request->session()->read('id');
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

		 $numLoops = 0;

		// if user hits the thumbs up button		
		if ($rating == 'up') {
		   do {
			$soc = $this->handleThumbsUp($old_soc, $numLoops);

		      	// keep polling until the generated SOC code exists in database
		      	$validityQuery = 'SELECT soc FROM Occupation WHERE soc = :soc';
		      	$results = $connection->execute($validityQuery, ['soc'=>$soc])->fetchAll('assoc');
			++$numLoops;
		   } while ($results == NULL); 
		}

		// if user hits the thumbs sideways button
		else if ($rating == 'mid') {
		     do {
				$soc = $this->handleThumbsMid($old_soc);

				// keep polling until the generated SOC code exists in database
		        	$validityQuery = 'SELECT soc FROM Occupation WHERE soc = :soc';
		        	$results = $connection->execute($validityQuery, ['soc'=>$soc])->fetchAll('assoc');
                   } while ($results == NULL);
		}

		// if user hits the thumbs down button
		else if ($rating == 'down') {
		     do {
				$soc = $this->handleThumbsDown($old_soc);

				// keep polling until the generated SOC code exists in database
			        $validityQuery = 'SELECT soc FROM Occupation WHERE soc = :soc';
		                $results = $connection->execute($validityQuery, ['soc'=>$soc])->fetchAll('assoc');
			} while ($results == NULL);
		}
		
		else {
			// should not ever happen, set SOC to 00-0000 for error.
			$soc = $rating;
		}
		
		// add algorithm's results to AlgorithmResults table
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
			
		if ($userId != null) 
			$insert = $connection->insert('AlgorithmResults', $values, $types);
	
		$this->redirect(['controller' => 'career', 'action' => 'displayCareerSingle', $soc, 'video']);
	}

	// implementation of 'Thumbs Up' logic
	private function handleThumbsUp($socCode, $numLoops) {

		// retain the same SOC code except the 2 least significant digits 
		$oldSOC = $socCode;
		$newSOC = substr($oldSOC, 0, -2);

		// change the 2 least significant digits of the SOC code 
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

		   //if the new SOC is still the same as the old one, act as if the user pressed Thumbs Middle.
		   if ($numLoops == 1000) {
		      $newSOC = $this->handleThumbsMid($newSOC);
		   }
		}
		
		return $newSOC;
	}

	// implementation of 'Thumbs Middle' logic
	private function handleThumbsMid($socCode) {
		// retain the same SOC code except the 4 least significant digits
		$oldSOC = $socCode;
		$newSOC = substr($oldSOC, 0, -4);

                // change the 3 least significant digit of the SOC code
		$lastDigit = rand(0, 9);
		$lastDigit2 = rand(0,9);
		$lastDigit3 = rand(0,9);
		$lastDigit4 = rand(0, 9);
		$newSOC = $newSOC . $lastDigit . $lastDigit2 . $lastDigit3 .$lastDigit4;

		// if the newly generated SOC code is the same as the old one, replace the 5 least significant digits with random digits  
		if ($newSOC == $oldSOC) {
		   $lastDigit = rand(0, 9);
		   $lastDigit2 = rand(0,9);
                   $lastDigit3 = rand(0,9);
		   $lastDigit4 = rand(0, 9);
		   $lastDigit5 = rand(0, 0);

		   $newSOC = substr($oldSOC, 0, -6);
		   $newSOC = $newSOC . $lastDigit . '-' .
		   $lastDigit2 . $lastDigit3. $lastDigit4. $lastDigit5;
		}

                return $newSOC;
	 }
	 
        // implementation of 'Thumbs Down' logic
	private function handleThumbsDown($socCode) {
		$oldSOC = $socCode;
	
                // change first two digits of the SOC code
		$digit1 = rand(1, 5);
		$digit2 = rand(0, 9);

		// check if new first two digits are the same as the old SOC's. If so, change them randomly.
		if ($digit1 == $this->castToNumber(substr($oldSOC, 0, -6))) {
		   $digit1 = $digit1 + rand(0,4);
		}
		if ($digit2 == $this->castToNumber(substr($oldSOC, 1, -5))) {
                   $digit2 = $digit2 + 1;
		}

		// change the rest of the digits of the SOC code
		$digit3 = rand(0, 9);
		$digit4 = rand(0, 9);
		$digit5 = rand(0, 9);
		$digit6 = rand(0, 9);
		$newSOC = $digit1 . $digit2 .'-'. $digit3 . $digit4 . $digit5 . $digit6;

                return $newSOC;
	}

	// helper function for handleThumbsDown. This function captures the first two digits of the SOC code argument
	// and casts them to an integer.
	private function castToNumber($SOCdigit) {
		if ($SOCdigit) {
		   return ord(strtolower($SOCdigit)) - 96;
		}
		else {
		     return 0;
		}
	}
}
