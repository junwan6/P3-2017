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
    if (!$this->request->is('post')){
      // TODO: Error page, not uploading anything
      die();
    }
    $connection = ConnectionManager::get($this->datasource);
    $updates = [];
    // Combine changes for question text and video file
    foreach ($this->request->data as $k => $v){
      $matches = [];
      // Extracts data from both video and text fields
      preg_match('/^soc(..-....)p(\d+)q(\d+)(.*?)(?:text)?$/', $k, $matches);
      $soc = $matches[1];
      $pNum = $matches[2];
      $person = $matches[4];
      $qNum = $matches[3];

      // TODO: Only populate actual updates (identify unchanged questions)
      // No check added for filesize here due to above
      if (!array_key_exists($soc, $updates)){
        $updates[$soc] = [];
      }
      if (!array_key_exists($pNum, $updates[$soc])){
        $updates[$soc][$pNum] = ['name'=>$person, 'questions'=>[]];
      }
      if (!array_key_exists($qNum, $updates[$soc][$pNum]['questions'])){
        $updates[$soc][$pNum]['questions'][$qNum] = [];
      }
      // If the input is a file and has been uploaded
      // TODO: type checking, disallow non-video upload (codecs?)
      //   $v['type'] is a MIME type (?), get list and check
      if (is_array($v) && $v['size'] != 0 && $v['error'] == 0){
        $updates[$soc][$pNum]['questions'][$qNum]
          = array_replace($updates[$soc][$pNum]['questions'][$qNum], $v);
      } elseif(is_string($v)) {
        $updates[$soc][$pNum]['questions'][$qNum]
          = array_replace($updates[$soc][$pNum]['questions'][$qNum], ['text'=>$v]);
      }
    }
    // Validate and construct changes
    // Allow confirmation of update, option to delete orphans
    $queuedUpdates = ['database'=>[], 'filesystem'=>[], 'orphans'=>[]];
    foreach ($updates as $soc => $people){
      foreach ($people as $pNum => $person){
        $name = $person['name'];
        foreach ($person['questions'] as $qNum => $update){
          // Find existing video file, get current question to see if update necessary
          $query = 'SELECT fileName, question FROM Videos WHERE soc = :soc AND ' . 
            'personNum = :pNum AND person = :person AND questionNum = :qNum';
          $results = $connection->execute($query, ['soc'=>$soc, 'pNum'=>$pNum,
            'person'=>$name, 'qNum'=>$qNum])->fetchAll('assoc');
          
          $setFields = [];
          // If a file has been uploaded:
          if (count($update) != 1){
            $dest = WWW_ROOT . 'vid/' . $soc . '_' . $pNum . '_' . $name;
            $queuedUpdates['filesystem'][] = [
              $update['tmp_name'], $dest, $update['name']
            ];
            $setFields['fileName'] = $update['name'];
          }
          // If a new or changed entry:
          if (count($results) == 0){
            $setFields['question'] = $update['text'];
            $queuedUpdates['database'][] = [$setFields];
          } else if ($update['text'] != $results[0]['question']){
            $setFields['question'] = $update['text'];
            $checkedFields = ['soc'=>$soc, 'personNum'=>$pNum,
            'person'=>$name, 'questionNum'=>$qNum];
            $queuedUpdates['database'][] = [$setFields, $checkedFields];
            // Will only be one row due to primary key
            $queuedUpdates['orphans'][] = $results[0];
          }
        }
      }
    }
  debug($queuedUpdates);
  // TODO: DECIDE WHAT TO DO WITH PAGE (AJAX/'upload complete')
  }
}
