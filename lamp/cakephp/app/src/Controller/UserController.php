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
        $db = mysqli_connect("localhost", "root", "root", "p3_test");

        //$connection = ConnectionManager::get($this->datasource);

        if($_SERVER["REQUEST_METHOD"] == "POST")
        {
                $email = ($_POST['email']);

                $sql = sprintf("SELECT email FROM Users WHERE email = '%s'", mysqli_real_escape_string($db, $email));  //i change\
		d id to email
                $result = mysqli_query($db, $sql);

                $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
                //$active = $row['active'];
                $count = mysqli_num_rows($result);
                //$count = count($result);
                $mypassword = mysqli_real_escape_string($db, $_POST['password']);

                //for debugging purposes
		/*
                $query="SELECT * FROM Users";
                $results = mysqli_query($db, $query);
                while ($row = mysqli_fetch_array($results)){
		      echo '<tr>';
                      foreach($row as $field)
																							{																			           echo '<td>' . htmlspecialchars($field) . '</td>';
				 echo "\n";															                        }
		      echo "\n";
                }
		*/
	        //end of debugging code

                if ($count)
		{
			echo 'Username and Password Found';
		        $this->display("profile");
                }
	        else
                {																				 echo 'Username and Password NOT found: ';														      printf("%s is not in the Database. \n", $email);
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
                $sql = "INSERT INTO Users (firstName, lastName, email) VALUES ('$firstName', '$lastName', '$email')";

                if(mysqli_query($db, $sql))
	        {
			echo "new record created successfully";
			//$this->display("profile");
		}
	}

        $this->display("profile");  //change this to display the homepage again bc user has not successfully logged in

    }
}


