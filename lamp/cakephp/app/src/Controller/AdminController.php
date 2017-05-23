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

/**
 * Controller for admin control and overview
 *   Example functions: user list, video upload, spreadsheet upload/update
 */
// TODO: VERIFY USER ON ALL ACTIONS
class AdminController extends PagesController
{
  public function displaySummary(){
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
      if (!array_key_exists($soc, $videoList)){
        $videoList[$soc] = ['title' => $title, 'people' => []];
      }
      if (!array_key_exists($pNum, $videoList[$soc]['people'])){
        $videoList[$soc]['people'][$pNum] = ['name' => $person, 'questions' => []];
      }
      $videoList[$soc]['people'][$pNum]['questions'][$qNum] = [$question, $fileName];
    }
    
    $this->set('videoList', $videoList);
    $this->display('summary');
  }
  public function displayVideos(...$careers){
    $connection = ConnectionManager::get($this->datasource);
    $videoList = [];
    $orphans = [];
    $deadLinks = [];
    // Routing enforces at least 1 argument
    if (count($careers) == 1 && $careers[0] == 'all'){
      $query = 'SELECT Occupation.title, Videos.* FROM ' . 
        'Videos INNER JOIN Occupation ON Videos.soc = Occupation.soc';
      
      $results = $connection->execute($query)->fetchAll('assoc');
      
      foreach($results as $r){
        $soc = $r['soc'];
        $title = $r['title'];
        $pNum = $r['personNum'];
        $person = $r['person'];
        $qNum = $r['questionNum'];
        $question = $r['question'];
        $fileName = $r['fileName'];
        if (!array_key_exists($soc, $videoList)){
          $videoList[$soc] = ['title' => $title, 'people' => []];
        }
        if (!array_key_exists($pNum, $videoList[$soc]['people'])){
          $videoList[$soc]['people'][$pNum] = ['name' => $person, 'questions' => []];
        }
        $videoList[$soc]['people'][$pNum]['questions'][$qNum] = [$question, $fileName];
        
        // Check to see if file exists
        $path = WWW_ROOT . 'vid/' . $soc . '_' . $pNum . '_' . $person;
        $folder = new Folder($path, true);
        $dest = $folder->path . '/' . $fileName;
        if (!file_exists($dest)){
          if (!array_key_exists($soc, $deadLinks)){
            $deadLinks[$soc] = ['title' => $title, 'people' => []];
          }
          if (!array_key_exists($pNum, $deadLinks[$soc]['people'])){
            $deadLinks[$soc]['people'][$pNum] = ['name' => $person, 'files' => []];
          }
          $deadLinks[$soc]['people'][$pNum]['files'][$qNum] = $fileName;
        }
        // Populate files in directory if nonexistant, remove all matching files
        
        if (!array_key_exists($soc, $orphans)){
          $orphans[$soc] = ['title' => $title, 'people' => []];
        }
        if (!array_key_exists($pNum, $orphans[$soc]['people'])){
          $orphans[$soc]['people'][$pNum] = ['name' => $person, 'files' => []];
          $orphans[$soc]['people'][$pNum]['files'] = scandir($folder->path);
          $orphans[$soc]['people'][$pNum]['files'] = 
            array_diff($orphans[$soc]['people'][$pNum]['files'], ['.', '..']);
        }
        $orphans[$soc]['people'][$pNum]['files'] = 
          array_diff($orphans[$soc]['people'][$pNum]['files'], [$fileName]);
        if (count($orphans[$soc]['people'][$pNum]['files']) == 0){
          unset($orphans[$soc]['people'][$pNum]);
        }
        if (count($orphans[$soc]['people']) == 0){
          unset($orphans[$soc]);
        }
      }
    // Following allows multiple socs to be displayed on page access
    // If soc with no videos is requested, initialize blank info for UI fill-in
    } else {
      foreach($careers as $soc){
        if (preg_match('/^[0-9]{2}-[0-9]{4}$/', $soc) == 1){
          $query = 'SELECT * FROM Videos WHERE soc = :soc';
          $results = $connection->execute($query, ['soc'=>$soc])->fetchAll('assoc');
          $noVideos = true;
          
          $query = 'SELECT title FROM Occupation WHERE soc = :soc';
          $occupation = $connection->execute($query, ['soc'=>$soc])->fetchAll('assoc');
          $title = (count($occupation) == 0?'No Career Data':$occupation[0]['title']);
          foreach($results as $r){
            $pNum = $r['personNum'];
            $person = $r['person'];
            $qNum = $r['questionNum'];
            $question = $r['question'];
            $fileName = $r['fileName'];
            if (!array_key_exists($soc, $videoList)){
              $videoList[$soc] = ['title' => $title, 'people' => []];
            }
            if (!array_key_exists($pNum, $videoList[$soc]['people'])){
              $videoList[$soc]['people'][$pNum] = ['name' => $person, 'questions' => []];
            }
            $videoList[$soc]['people'][$pNum]['questions'][$qNum] = [$question, $fileName];
        
            // Check to see if file exists
            // TODO: Rearrange file to remove repeated code below
            $path = WWW_ROOT . 'vid/' . $soc . '_' . $pNum . '_' . $person;
            $folder = new Folder($path, true);
            $dest = $folder->path . '/' . $fileName;
            if (!file_exists($dest)){
              if (!array_key_exists($soc, $deadLinks)){
                $deadLinks[$soc] = ['title' => $title, 'people' => []];
              }
              if (!array_key_exists($pNum, $deadLinks[$soc]['people'])){
                $deadLinks[$soc]['people'][$pNum] = ['name' => $person, 'files' => []];
              }
              $deadLinks[$soc]['people'][$pNum]['files'][$qNum] = $fileName;
            }
            // Populate files in directory if nonexistant, remove all matching files
            
            if (!array_key_exists($soc, $orphans)){
              $orphans[$soc] = ['title' => $title, 'people' => []];
            }
            if (!array_key_exists($pNum, $orphans[$soc]['people'])){
              $orphans[$soc]['people'][$pNum] = ['name' => $person, 'files' => []];
              $orphans[$soc]['people'][$pNum]['files'] = scandir($folder->path);
              $orphans[$soc]['people'][$pNum]['files'] = 
                array_diff($orphans[$soc]['people'][$pNum]['files'], ['.', '..']);
            }
            $orphans[$soc]['people'][$pNum]['files'] = 
              array_diff($orphans[$soc]['people'][$pNum]['files'], [$fileName]);
            if (count($orphans[$soc]['people'][$pNum]['files']) == 0){
              unset($orphans[$soc]['people'][$pNum]);
            }
            if (count($orphans[$soc]['people']) == 0){
              unset($orphans[$soc]);
            }
          }
          if (count($results) == 0){
            $videoList[$soc] = ['title' => $title, 'people' => [
              ['name' => '', 'questions' => [
                ['', '']
              ]]
            ]];
          }
        }
      }
    }
    $this->set('orphans', $orphans);
    $this->set('deadLinks', $deadLinks);
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
    $nameUpdates = [];
    $deleteFiles = [];
    $orphans = [];
    // Combine changes for question text and video file
    // Ensure multiple changes (ex. delete) do not conflict
    foreach ($this->request->data as $k => $v){
      $matches = [];
      // Extracts data from both video and text fields
      preg_match('/^soc(..-....)p(\d+)(?:q(\d+)(.*?)|(pnamechange)|(orphan)\d+(.*?))' . 
        '(file|text|delete|fnamechange)?$/', $k, $matches);
      $soc = $matches[1];
      $pNum = $matches[2];
      $person = null;
      $qNum = null;
      $inputType = null;
      if ($matches[5] == 'pnamechange'){
        if ($v != 'UNEDITED'){
          // All other updates are name-independent, change should not affect
          $nameUpdates[] = [
            'set'=>['person'=>$v],
            'check'=>['soc'=>$soc, 'personNum'=>$pNum],
            'action'=>'update',
          ];
        }
        continue;
      } else if ($matches[6] == 'orphan'){
        $person = $matches[7];
        $deleteFiles[] = implode('_', [$soc, $pNum, $person]) .
          '/' . $v;
        continue;
      } else {
        $person = $matches[4];
        $qNum = $matches[3];
        $inputType = $matches[8];
      }

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
    $queuedUpdates = ['database'=>[], 'filesystem'=>[]];
    foreach ($updates as $soc => $people){
      foreach ($people as $pNum => $person){
        $name = $person['name'];
        // In case of blanks inbetween updates, assume intentional and add empty
        $interstitialBlanks = [];
        foreach ($person['questions'] as $qNum => $update){
          // Find existing video file, get current question to see if update necessary
		  // If pNum not in table, return 2 rows
		  // Primary key enforces all others return 1 or 0
          $query = 'SELECT * FROM Videos WHERE (soc = :soc AND ' . 
            'personNum = :pNum AND questionNum = :qNum) ' .
            'OR :pNum NOT IN (SELECT personNum FROM ' .
            'Videos WHERE soc = :soc) LIMIT 2';
          $results = $connection->execute($query, ['soc'=>$soc,
            'pNum'=>$pNum, 'qNum'=>$qNum])->fetchAll('assoc');
          $setFields = [];
          $rowExists = (count($results) == 1);
          $newPerson = (count($results) == 2);
          // If the row is to be deleted:
          if (in_array('delete', array_keys($update))){
            if (!$rowExists){
              continue;
            }
            $queuedUpdates['database'][] = [
              'set'=>[],
              // Only checks NOT NULL fields, issues when converting
              'check'=>['soc'=>$soc, 'personNum'=>$pNum, 'questionNum'=>$qNum],
              'action'=>'delete'];
            // If file was uploaded, PHP handles deletion of tmp
            // Orphaned files checked against other video usage
            continue;
          }
          if (!$rowExists && $update['text'] == ''
            && $update['fileName'] == '' && !($newPerson && $qNum == 0)){
            $interstitialBlanks[] = $qNum;
            continue;
          }
          // Conditional mess, but necessary
          // TODO: Check/redo/clean up (string? enum?)
          
          // If a file has been uploaded:
          // Validity of file upload checked beforehand
          $newFile = (in_array('name', array_keys($update)));
          // If the filename has been changed
          $changedFile = (in_array('fileName', array_keys($update))
            && !($rowExists && $update['fileName'] == $results[0]['fileName']));
          if ($newFile){
            $dest = WWW_ROOT . 'vid/' . $soc . '_' . $pNum . '_' . $name;
            $queuedUpdates['filesystem'][] = [
              'src' => $update['tmp_name'],
              'dir' => $dest,
              'name' => $update['name']
            ];
            $setFields['fileName'] = $update['name'];
            // Orphaned files handled on new file copy
          } else if ($changedFile || $newPerson){
            $setFields['fileName'] = $update['fileName'];
          }
          
          $isBlank = ($update['text'] == '');
          $changedQuestion = false;
          if (($rowExists && $update['text'] != $results[0]['question'])
            || (!$rowExists && !$isBlank)){
            $setFields['question'] = $update['text'];
            $changedQuestion = true;
          } else if (!$rowExists || $newPerson){
            $setFields['question'] = $update['text'];
          }
          if ((!$rowExists && !$isBlank) || $changedQuestion 
            || $newFile || $changedFile || $newPerson){
            $checkedFields = ['soc'=>$soc, 'personNum'=>$pNum, 'questionNum'=>$qNum];
            $queuedUpdates['database'][] = [
              'set'=>$setFields,
              'check'=>$checkedFields,
              'action'=>($rowExists?'update':'insert')
            ];
            foreach ($interstitialBlanks as $ib){
              $queuedUpdates['database'][] = [
                'set'=>['fileName'=>'','question'=>'',
                  'questionNum'=>$ib],
                'check'=>array_diff_key($checkedFields, ['questionNum'=>0]),
                'action'=>'insert'
              ];
            }
            $interstitialBlanks = [];
          }
        }
      }
    }
    $actionsTaken = [];
    // Appends name updates to end
    $queuedUpdates['database'] = array_merge($queuedUpdates['database'], $nameUpdates);
    foreach ($queuedUpdates['database'] as $stmt){
      // checkFields reused after update and delete, to check for orphans
      $orphanCheck = null;
      $fieldType = null;
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
        $query = 'INSERT INTO Videos ' .
          "({$fieldNames}) VALUES ({$fieldSubs})";
      } else if ($stmt['action'] == 'update'){
        $setFields = implode(', ', array_map(function ($s){
          return $s . ' = :' . $s;}, array_keys($stmt['set'])));
        $checkFields = implode(' AND ', array_map(function ($s){
          return $s . ' = :' . $s;}, array_keys($stmt['check'])));
        $fields = $stmt['set'] + $stmt['check'];
        $fieldTypes = array_map(function ($s){return gettype($s);}, $fields);
        $query = "UPDATE Videos SET {$setFields} WHERE {$checkFields}";
        $orphanCheck = $fields;
      } else if ($stmt['action'] == 'delete'){
        $checkFields = implode(' AND ', array_map(function ($s){
          return $s . ' = :' . $s;}, array_keys($stmt['check'])));
        $fields = $stmt['check'];
        $fieldTypes = array_map(function ($s){return gettype($s);}, $stmt['check']); 
        $query = 'DELETE FROM Videos WHERE ' . $checkFields;
        $orphanCheck = $stmt['check'];
      }
      if (!is_null($orphanCheck)){
        // TODO: Rearrange this+above so no repetition of code
        // Check to see if an update will orphan a file
        if (array_key_exists('fileName', $stmt['set'])
          || $stmt['action'] == 'delete'){
          $select = 'SELECT fileName, person FROM Videos WHERE ' .
            'soc = :soc AND personNum = :personNum AND ' .
            'fileName IN (SELECT fileName FROM Videos WHERE ' . 
            'soc = :soc AND personNum = :personNum ' .
            'AND questionNum = :questionNum)';
          $results = $connection->execute($select,
            $orphanCheck, $fieldTypes)->fetchAll('assoc');
          if (count($results) == 1){
            $orphans[] = $orphanCheck
              + ['fileName' => $results[0]['fileName'],
              'person' => $results[0]['person']];
          }
        }
      }
      if (!$dryRun){
        $connection->execute($query, $fields, $fieldTypes);
      }
      $actionsTaken[] = ([$query, $fields, $fieldTypes]);
    }

    foreach ($queuedUpdates['filesystem'] as $move){
      // Create folder if it does not exist
      $folder = new Folder($move['dir'], true);
      $dest = $folder->path . '/' . $move['name'];
      if (file_exists($dest)){
        if (!$dryRun){
          rename($dest, $dest . '#');
          // Orphans handled by database update, required for upload
        }
        $actionsTaken[] = ([$dest, $dest . '#']);
      }
      if (!$dryRun){
        move_uploaded_file($move['src'], $dest);
      }
      $actionsTaken[] = ([$move['src'], $dest]);
    }
    
    foreach ($deleteFiles as $path){
      $vidPath = WWW_ROOT . 'vid/' ;
      $fileToDelete = $vidPath . $path;
      if (file_exists($fileToDelete)){
        if (!$dryRun){
          unlink($fileToDelete);
        }
        $actionsTaken[] = (['delete', $fileToDelete]);
      }
    }

    $this->set('dryRun', $dryRun);
    $this->set('orphans', $orphans);
    $this->set('request', $this->request->data);
    $this->set('changes', $updates);
    $this->set('actions', $queuedUpdates);
    $this->set('statements', $actionsTaken);
    // TODO: Implement own file preservation + garbage collection
    // Create multistage upload/confirm (add uploads to orphan directory?)
    $this->display('upload');
  }
}
