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
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

/**
 * Controller for admin control and overview
 *   Example functions: user list, video upload, spreadsheet upload/update
 */
// TODO: VERIFY USER ON ALL ACTIONS
class AdminController extends PagesController
{
  public function displayVideos(){
    $connection = ConnectionManager::get($this->datasource);
    $query = 'SELECT Occupation.title, Videos.* FROM ' . 
      'Videos INNER JOIN Occupation ON Videos.soc = Occupation.soc';
    
    $results = $connection->execute($query)->fetchAll('assoc');
    
    $videoList = [];
    foreach($results as $r){
      $soc = $r['soc'];
      $title = $r['title'];
      $pNum = $r['personNum'];
      $person = $r['person'];
      $qNum = $r['questionNum'];
      $question = $r['question'];
      $fileName = $r['fileName'];
      // TODO: Simplify if each career will only ever have one person
      if (!array_key_exists($soc, $videoList)){
        $videoList[$soc] = ['title' => $title, 'people' => []];
      }
      if (!array_key_exists($pNum, $videoList[$soc]['people'])){
        $videoList[$soc]['people'][$pNum] = ['name' => $person, 'questions' => []];
      }
      $videoList[$soc]['people'][$pNum]['questions'][$qNum] = [$question, $fileName];
    }
    $this->set('videoList', $videoList);
    $this->display('videos');
  }
  
  public function uploadVideos(){
    if ($this->request->is('post')){
      debug($this->request);
      foreach ($this->request->data as $k => $v){
        $matches = [];
        preg_match('/soc(..-....)p(\d+)q(\d+)(.+)/', $k, $matches);
        $soc = $matches[1];
        $pNum = $matches[2];
        $person = $matches[4];
        $qNum = $matches[3];
        // If the input is a file and has been changed
        // TODO: type checking, disallow non-video upload (codecs?)
        if (is_array($v) && $v['size'] != 0){
          $dest = new Folder(WWW_ROOT . 'vid/' . $soc . '_' . $pNum . '_' . $person);
        } else {
          
        }
      }
    }
    die();
  }
}
