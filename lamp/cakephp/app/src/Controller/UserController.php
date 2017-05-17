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
	public function signup(){	
   	//print("hello world");  
    $db = mysqli_connect("localhost", "root", "root", "p3_test"); 
   
    //$connection = ConnectionManager::get($this->datasource);
   
    if($_SERVER["REQUEST_METHOD"] == "POST")
      {
	      $myusername = mysqli_real_escape_string($db,$_POST['email']);
		    $mypassword = mysqli_real_escape_string($db,$_POST['password']);
			  $sql = "SELECT id FROM admin WHERE username = '$myusername' and passcode = '$mypassword'";
				$result = mysqli_query($db,$sql);
        if($result == false)
          print("fuck");
        $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
			  $active = $row['active'];
			  $count = mysqli_num_rows($result);
			}

    if($count == 1)
      {
	      session_register("myusername");
	      $_SESSION['login_user'] = $myusername;
		    header("location: welcome.php");
		  }
	  else
		  {
				$error = "Your Login Name or Password is invalid";
      }

     $this->display("profile");
    }					    
}
