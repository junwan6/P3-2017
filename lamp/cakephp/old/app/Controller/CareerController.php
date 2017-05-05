<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class CareerController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
  public $uses = array();

  public function displayCareer(){
    $path = func_get_args();
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
    // TODO: Figure out implementation of switcher (set JS variable?)
    $page = ((in_array($focus,
      array('video', 'salary', 'education', 'skills', 'outlook', 'world-of-work')))?
      $focus:'video');
    $this->display($page);
  }
}
