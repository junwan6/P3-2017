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
class UserController extends PagesController
{
    public function login()
    {
	$db = ConnectionManager::get($this->datasource);
  
	if($_SERVER["REQUEST_METHOD"] == "POST")
    	{
		$email = ($_POST['email']);
		$password = ($_POST['password']);

		$userFields = ['email', 'id'];
		
		$sql = 'SELECT ' . implode(',', $userFields) . ' FROM Users WHERE email = :email';
		$result = $db->execute($sql, ['email' => $email])->fetchAll('assoc');

		//if email is found check for a password match
		if (count($result) > 0) //if you found a match of emails
    		{
			$email = $result['0']['email']; //email of user
			$id = $result['0']['id']; 	//id of user
			$SaltField = ['salt'];
			$HashField = ['hash'];
			
			//query and execution to get the hash and salt for the user with the given id
			$querySalt = 'SELECT ' . implode(',', $SaltField) . ' FROM UserPasswords WHERE id = :id';
			$resultSalt = $db->execute($querySalt, ['id' => $id])->fetchAll('assoc');


			//resultSalt gets the row containing salt of the matching ID
			//index the row to grab the salt
			
			//check if user id has a salt (it always should)
			if(count($resultSalt) > 0)
			{
				$saltDB = $resultSalt['0']['salt']; //salt from database

				$passwordAndSalt = $password . $saltDB;
				$hash = md5($passwordAndSalt);

				$queryHashedPwd = 'SELECT ' . implode(',', $HashField) . ' FROM UserPasswords WHERE hash = :hash AND salt = :salt AND id = :id';
				$resultHash = $db->execute($queryHashedPwd, ['hash' => $hash, 'salt' => $saltDB, 'id' => $id])->fetchAll('assoc');

			
				if(count($resultHash) > 0)
				{
					$this->display("profile");
				}
				else
				{
					echo "Incorrect Password";
					$this->display("default");
				}
			}
			else
			{
				echo "User ID not present in password table"; //this should never happen
			}

			//TODO: Display correct profile
//			$this->display("profile");
		}		
		else
		{
			echo 'Email NOT found: ';
			printf("%s is not in the Database. \n", $email);
		}

	}

    
    }


    public function signup()
    {
	$db = ConnectionManager::get($this->datasource);
	
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$firstName = ($_POST['firstName']);
		$lastName = ($_POST['lastName']);
		$email = ($_POST['email']);
		$password = ($_POST['password']);
		$length = 10;

		$salt = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
		$hash = md5($password . $salt);

		//insert the record into the database upon signup
		$values = array(
                        'firstName'=> gettype($firstName),
                        'lastName'=> gettype($lastName),
                        'email'=> gettype($email)
                );
		
		$resultInsert = $db->insert("Users", ['firstName' => $firstName, 'lastName' => $lastName, 'email' => $email], $values);
		
		
		//grab the id
		$idField = ['id'];
		$queryID = 'SELECT id FROM Users WHERE firstName = :firstName AND lastName= :lastName AND email = :email';
                $resultID = $db->execute($queryID, ['firstName' => $firstName, 'lastName' => $lastName, 'email' => $email])->fetchAll('assoc');

		if($resultID){
			$id = $resultID['0']['id'];
			$intID = (int)$id;
			
			$values2 = array(
				'hash'=> gettype($hash),
				'salt'=> gettype($salt),
				'id'=> gettype($id)
			);
			
			$resultPass = $db->insert("UserPasswords", ['hash' => $hash, 'salt' => $salt, 'id' => $id], $values2);
			
			if(!($resultPass))
			{
				//this happens if firstname, lastname, and email are all
				//already in the database (should change this)
				echo "password insert failed";
			}
			else if ($resultPass) //means password was inserted correctly
			{
				$this->display("profile");
			}
		}
		
		
	}
    }
}

