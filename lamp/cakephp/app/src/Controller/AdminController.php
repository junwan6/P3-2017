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
    // TODO: Delete all references to debugging tool
    $dryRun = false;
    
    $connection = ConnectionManager::get($this->datasource);
    $updates = [];
    // Combine changes for question text and video file
    // Ensure multiple changes (ex. delete) do not conflict
    foreach ($this->request->data as $k => $v){
      $matches = [];
      // Extracts data from both video and text fields
      preg_match('/^soc(..-....)p(\d+)q(\d+)(.*?)' . 
        '(file|text|delete|fnamechange)?$/', $k, $matches);
      $soc = $matches[1];
      $pNum = $matches[2];
      $person = $matches[4];
      $qNum = $matches[3];
      $inputType = $matches[5];

      // Create per-soc, per-person, questions
      if (!array_key_exists($soc, $updates)){
        $updates[$soc] = [];
      }
      if (!array_key_exists($pNum, $updates[$soc])){
        $updates[$soc][$pNum] = ['name'=>$person, 'questions'=>[]];
      }
      if (!array_key_exists($qNum, $updates[$soc][$pNum]['questions'])){
        $updates[$soc][$pNum]['questions'][$qNum] = [];
      }
      
      // If file upload occurred and succeeded:
      if ($inputType == 'file' && $v['size'] != 0 && $v['error'] == 0){
        // Add all upload information
        $updates[$soc][$pNum]['questions'][$qNum] += $v;
      // If text field (change check done later)
      } else if ($inputType == 'fnamechange'){
        $updates[$soc][$pNum]['questions'][$qNum]['fileName'] = $v;
      } else if ($inputType == 'text'){
        // Add question text
        $updates[$soc][$pNum]['questions'][$qNum]['text'] = $v;
      // If delete (question, not file)
      } else if ($inputType == 'delete'){
        // Set delete field
        $updates[$soc][$pNum]['questions'][$qNum]['delete'] = true;
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
          $query = 'SELECT * FROM Videos WHERE soc = :soc AND ' . 
            'personNum = :pNum AND person = :person AND questionNum = :qNum';
          $results = $connection->execute($query, ['soc'=>$soc, 'pNum'=>$pNum,
            'person'=>$name, 'qNum'=>$qNum])->fetchAll('assoc');
          
          $setFields = [];
          $rowExists = (count($results) != 0);
          // If the row is to be deleted:
          if (in_array('delete', array_keys($update)) && $rowExists){
            $queuedUpdates['database'][] = [
              'set'=>[],
              // Only checks NOT NULL fields, issues when converting
              'check'=>['soc'=>$soc, 'personNum'=>$pNum, 'questionNum'=>$qNum],
              'action'=>'delete'];
            if ($results[0]['fileName'] != null){
              $queuedUpdates['orphans'][] = $results[0]['fileName'];
            }
            // If file was uploaded, PHP handles deletion of tmp
            continue;
          }
          // If a file has been uploaded:
          // Validity of file upload checked beforehand
          $newFile = (in_array('name', array_keys($update)));
          if ($newFile){
            $dest = WWW_ROOT . 'vid/' . $soc . '_' . $pNum . '_' . $name;
            $queuedUpdates['filesystem'][] = [
              'src' => $update['tmp_name'],
              'dir' => $dest,
              'name' => $update['name']
            ];
            $setFields['fileName'] = $update['name'];
            // Orphaned files handled on new file copy
          } else if (in_array('fileName', array_keys($update))){
            $setFields['fileName'] = $update['fileName'];
          }
          
          // Conditional mess, but necessary
          // TODO: Check/redo/clean up (string? enum?)
          $isBlank = ($update['text'] == '');
          $changedQuestion = false;
          if (($rowExists && $update['text'] != $results[0]['question'])
            || (!$rowExists && !$isBlank)){
            $setFields['question'] = $update['text'];
            $changedQuestion = true;
          }
          if ((!$rowExists && !$isBlank) || $changedQuestion|| $newFile){
            $checkedFields = ['soc'=>$soc, 'personNum'=>$pNum,
            'person'=>$name, 'questionNum'=>$qNum];
            $queuedUpdates['database'][] = [
              'set'=>$setFields,
              'check'=>$checkedFields,
              'action'=>($rowExists?'update':'insert')
            ];
          }
        }
      }
    }
    foreach ($queuedUpdates['database'] as $stmt){
      // Should be no duplicates, SELECT would have found
      // New socs and ordering handled by client
      // Single table, adding new soc same as adding new question
      if ($stmt['action'] == 'insert'){
        $fields = $stmt['set'] + $stmt['check'];
        $fieldNames = implode(', ', array_keys($fields));
        $fieldSubs = implode(', ', array_map(function ($s){
            return ':' . $s;
        }, array_keys($fields)));
        $fieldTypes = array_map(function ($s){return gettype($s);}, $fields);
        $insert = 'INSERT INTO Videos ' .
          "({$fieldNames}) VALUES ({$fieldSubs})";
        if ($dryRun){
          debug([$insert, $fields, $fieldTypes]);
        } else {
          $connection->execute($insert, $fields, $fieldTypes);
        }
      } else if ($stmt['action'] == 'update'){
        $setFields = implode(', ', array_map(function ($s){
          return $s . ' = :' . $s;}, array_keys($stmt['set'])));
        $checkFields = implode(' AND ', array_map(function ($s){
          return $s . ' = :' . $s;}, array_keys($stmt['check'])));
        $fields = $stmt['set'] + $stmt['check'];
        $fieldTypes = array_map(function ($s){return gettype($s);}, $fields);
        $update = "UPDATE Videos SET {$setFields} WHERE {$checkFields}";
        if ($dryRun){
          debug([$update, $fields, $fieldTypes]);
        } else {
          $connection->execute($update, $fields, $fieldTypes);
        }
      } else if ($stmt['action'] == 'delete'){
        $conditions = implode(' AND ', array_map(function ($s){
          return $s . ' = :' . $s;}, array_keys($stmt['check'])));
        $fieldTypes = array_map(function ($s){return gettype($s);}, $stmt['check']);
        $delete = 'DELETE FROM Videos WHERE ' . $conditions;
        if ($dryRun){
          debug([$delete, $stmt['check'], $fieldTypes]);
        } else {
          $connection->execute($delete, $stmt['check'], $fieldTypes);
        }
      }
    }

    foreach ($queuedUpdates['filesystem'] as $move){
      // Create folder if it does not exist
      $folder = new Folder($move['dir'], true);
      $dest = $folder->path . '/' . $move['name'];
      if (file_exists($dest)){
        if ($dryRun){
          debug([$dest, $dest . '#']);
        } else {
          rename($dest, $dest . '#');
          $queuedUpdates['orphans'][] = basename($dest) . '#';
        }
      }
      if ($dryRun){
        debug([$move['src'], $dest]);
      } else {
        move_uploaded_file($move['src'], $dest);
      }
    }
    if ($dryRun){
      debug($this->request->data);
      debug($updates);
      debug($queuedUpdates);
    }

    $this->set('changes', $queuedUpdates);
    // TODO: Implement own file preservation + garbage collection
    // Create multistage upload/confirm (add uploads to orphan directory?)
    $this->display('upload');
  }
}
