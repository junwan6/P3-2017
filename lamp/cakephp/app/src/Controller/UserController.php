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
    	//print("hello world");  
    	//$db = mysqli_connect("localhost", "root", "root", "p3_test"); 
   
	$db = ConnectionManager::get($this->datasource);
  
	if($_SERVER["REQUEST_METHOD"] == "POST")
    	{
		$email = ($_POST['email']);
		$password = ($_POST['password']);

		$userFields = ['email', 'id'];

		//$sql = sprintf("SELECT email FROM Users WHERE email = '%s'", mysqli_real_escape_string($db, $email));  //i changed id to email
		
		$sql = 'SELECT ' . implode(',', $userFields) . ' FROM Users WHERE email = :email';
		$result = $db->execute($sql, ['email' => $email])->fetchAll('assoc');


		if (count($result) > 0) //if you found a match of emails
    		{
			$email = $result['0']['email']; //email of user
			$id = $result['0']['id']; 	//id of user
			$passwordFields = ['hash', 'salt'];
			
			//query and execution to get the hash and salt for the user with the given id
			$queryPassword = 'SELECT ' . implode(',', $passwordFields) . ' FROM UserPasswords WHERE id = :id';
			$resultPassword = $db->execute($queryPassword, ['id' => $id])->fetchAll('assoc');
			
			//check if user id has hash and salt (it always should)
			if(count($resultPassword) > 0){
				$hashDB = $resultPassword['0']['hash']; //hash from database
				$saltDB = $resultPassword['0']['salt']; //salt from database
			}

			//TODO: Display correct profile
			$this->display("profile");
		}
		
		else
		{
			echo 'Username and Password NOT found: ';
			printf("%s is not in the Database. \n", $email);
		}

	}

    
    }

    public function signup()
    {
	$db = mysqli_connect("localhost", "root", "root", "p3_test");
	
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$firstName = ($_POST['firstName']);
		$lastName = ($_POST['lastName']);
		$email = ($_POST['email']);
		//insert the record into the database upon signup
		//TODO: insert into password table
		//$sqlPassword = "INSERT INTO UserPasswords (hash, salt) VALUES ($hash, $salt);
		$sql = "INSERT INTO Users (firstName, lastName, email) VALUES ('$firstName', '$lastName', '$email')";

		if(mysqli_query($db, $sql))
		{
			echo "new record created successfully";
		//	$this->display("profile");
		}
	}

	$this->display("profile");  //change this to display the homepage again bc user has not successfully logged in
	 
    }
}

