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
  /* Helper function to initialize an array of form soc->pNum->qNum/file
   * Takes title and person name for labels
   * Returns whether the field existed, for additional specific initialization
   */
  private function initializeSOCPerson(&$arr, $soc, $title, $pNum, $person, $field){
    if (!array_key_exists($soc, $arr)){
      $arr[$soc] = ['title' => $title, 'people' => []];
    }
    if (!array_key_exists($pNum, $arr[$soc]['people'])){
      $arr[$soc]['people'][$pNum] = ['name' => $person, $field => []];
      return true;
    }
    return false;
  }

  /* Shared function to throw error on non-admin access attempt
   * Uses session isAdmin, assume database checking done
   * TODO: As greater authentication overhaul, recheck (for revoked access mid-session)
   */
  public function requireAdmin(){
    if (!$this->request->session()->read('isAdmin')){
      // Same principle as "don't tell if user or pw failed", prevent scanners
      throw new NotFoundException;
      //throw new ForbiddenException;
    }
  }
  
  /* Gets list of to populate SOC scrollable
   * More features to be added (User view history, etc.)
   */
  public function displaySummary(){
    $this->requireAdmin();
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
      $this->initializeSOCPerson($videoList,
        $soc, $title, $pNum, $person, 'questions');
      $videoList[$soc]['people'][$pNum]['questions'][$qNum] = [$question, $fileName];
    }
    
    $this->set('videoList', $videoList);

    $query = 'SELECT firstName,lastName,email,Users.id, IF (AdminUsers.id IS NULL, FALSE, TRUE) as isAdmin FROM Users LEFT JOIN AdminUsers ON (Users.id = AdminUsers.id)';
    $results = $connection->execute($query)->fetchAll('assoc');
    $this->set('userList', $results);
    $this->display('summary');
  }
  
  /* Gets list of videos to initialize videos editing page
   * Takes list of SOCs from URL, to show list or initialize new SOCs
   */
  public function displayVideos(...$careers){
    $this->requireAdmin();
    $connection = ConnectionManager::get($this->datasource);
    $videoList = [];
    // Orphan and dead-link checking handled by separate page
    // TODO: add dynamic checker, alert user when change will cause issue
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
        $this->initializeSOCPerson($videoList,
          $soc, $title, $pNum, $person, 'questions');
        $videoList[$soc]['people'][$pNum]['questions'][$qNum] = [$question, $fileName];
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
    $this->set('videoList', $videoList);
    $this->display('videos');
  }
  
  /* Applies changes made on videos page to the database/filesystem
   * Parses request parameters into updates
   * Updates checked for necessity into queuedUpdates
   * Executes queuedUpdates for files,
   */
  public function uploadVideos(){
    $this->requireAdmin();
    if (!$this->request->is('post')){
      // TODO: Error page, not uploading anything
      $this->display('upload');
      return;
    }
    // TODO: Delete all references to debugging tool
    $dryRun = false;
    
    $connection = ConnectionManager::get($this->datasource);
    $folderPath = WWW_ROOT . 'vid/';
    $updates = [];
    // TODO: Integrate into existing data structure
    //   Request parsing -> validity checking -> execution
    //   request->data      updates              queuedUpdates
    $nameUpdates = [];
    // Combine changes for question text and video file
    // Ensure multiple changes (ex. delete) do not conflict
    foreach ($this->request->data as $k => $v){
      $matches = [];
      // Extracts data from both video and text fields
      preg_match('/^soc(..-....)p(\d+)(q\d+|pnamechange|pnameswap)(.*?)' . 
        '(file|text|delete|fnamechange)?$/', $k, $matches);
      $soc = $matches[1];
      $pNum = $matches[2];
      $person = $matches[4];
      $qNum = null;
      $inputType = null;
      if ($matches[3] == 'pnamechange'){
        if ($v != 'UNEDITED'){
          // All other updates are name-independent, change should not affect
          $nameUpdates[] = [
            'set'=>['person'=>$v],
            'check'=>['soc'=>$soc, 'personNum'=>$pNum],
            'action'=>'update',
            
            'swapOnly'=>(array_key_exists("soc{$soc}p{$pNum}pnameswap{$person}",
              $this->request->data)),
            
            'src' => $folderPath . "{$soc}_{$pNum}_{$person}",
            'dir' => rtrim($folderPath, '/') ,
            'name' => "{$soc}_{$pNum}_{$v}"
          ];
        }
        continue;
      } else if ($matches[3] == 'pnameswap') {
        continue;
      } else {
        $qNum = substr($matches[3],1);
        $inputType = $matches[5];
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
            $dest = $folderPath . $soc . '_' . $pNum . '_' . $name;
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
      $query = '';
      $fields = [];
      $fieldTypes = [];
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
      } else if ($stmt['action'] == 'delete'){
        $checkFields = implode(' AND ', array_map(function ($s){
          return $s . ' = :' . $s;}, array_keys($stmt['check'])));
        $fields = $stmt['check'];
        $fieldTypes = array_map(function ($s){return gettype($s);}, $stmt['check']); 
        $query = 'DELETE FROM Videos WHERE ' . $checkFields;
      }
      if (!$dryRun){
        $connection->execute($query, $fields, $fieldTypes);
      }
      $actionsTaken[] = ([$query, $fields, $fieldTypes]);
    }

    // Appends folder move to end
    $queuedUpdates['filesystem'] = array_merge($queuedUpdates['filesystem'], $nameUpdates);
    foreach ($queuedUpdates['filesystem'] as $move){
      // Create folder if it does not exist
      $folder = new Folder($move['dir'], true);
      $dest = $folder->path . '/' . $move['name'];
      
      $nameChange = array_key_exists('swapOnly', $move);
      if ($nameChange && $move['swapOnly']){
        continue;
      }
      if (file_exists($dest)){
        if (!$dryRun){
          rename($dest, $dest . '#');
        }
        $actionsTaken[] = ([$dest, $dest . '#']);
      }
      if ($nameChange){
        if (!$dryRun){
          rename($move['src'], $dest);
        }
      } else {
        if (!$dryRun){
          move_uploaded_file($move['src'], $dest);
        }
      }
      $actionsTaken[] = ([$move['src'], $dest]);
    }
    
    $this->set('dryRun', $dryRun);
    $this->set('request', $this->request->data);
    $this->set('changes', $updates);
    $this->set('actions', $queuedUpdates);
    $this->set('statements', $actionsTaken);
    // TODO: Implement own file preservation + garbage collection
    // Create multistage upload/confirm (add uploads to orphan directory?)
    $this->display('upload');
  }
  
  // From http://jeffreysambells.com/2012/10/25/human-readable-filesize-php
  private function human_filesize($bytes, $decimals = 2) {
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
  }

  /* Extracts relevant information from stat() of path
   * Directory, creation time, size (converted from bytes)
   */
  private function getPathInfo($path){
    $stat = stat($path);
    if (!$stat){
      return [];
    }
    $humanInfo = [];
    $humanInfo['dir'] = is_dir($path);
    // Date format from https://secure.php.net/manual/en/function.filemtime.php
    $humanInfo['ctime'] = date ("F d Y H:i:s", $stat['ctime']);
    $humanInfo['size'] = $this->human_filesize($stat['size']);
    return $humanInfo;
  }

  /* Displays information on user (very basic)
   */
  public function displayUser($uid){
    $this->requireAdmin();
    $connection = ConnectionManager::get($this->datasource);
    $idRep = ['id'=>$uid];
    $query = 'SELECT * FROM Users WHERE id = :id';
    $results = $connection->execute($query,$idRep)->fetchall('assoc');
    foreach($results as $r){
      $this->set('user', $r);
    }

    $query = 'SELECT * FROM FBUsers WHERE userId = :id';
    $results = $connection->execute($query,$idRep)->fetchall('assoc');
    foreach($results as $r){
      $this->set('fbuser', $r);
    }

    $query = 'SELECT * FROM LIUsers WHERE userId = :id';
    $results = $connection->execute($query,$idRep)->fetchall('assoc');
    foreach($results as $r){
      $this->set('liuser', $r);
    }

    $query = 'SELECT * FROM AdminUsers WHERE id = :id';
    $results = $connection->execute($query,$idRep)->fetchall('assoc');
    foreach($results as $r){
      $this->set('adminuser', $r);
    }
    
    $fields = implode(', ',
      ['ViewHistory.soc', 'realistic', 'investigative', 'artistic', 'social',
      'enterprising', 'conventional', 'rating', 'time', 'title']
    );
    $query = 'SELECT ' . $fields . ' FROM ViewHistory ViewHistory INNER JOIN Occupation ON ' .
      'ViewHistory.soc = Occupation.soc INNER JOIN OccupationInterests ' .
      'ON ViewHistory.soc = OccupationInterests.soc WHERE ViewHistory.id = :id';
    $results = $connection->execute($query,$idRep)->fetchall('assoc');
    $this->set('viewhistory', $results);

    $likedCareers = [];
    $neutralCareers = [];
    $dislikedCareers = [];

    foreach($results as $r){
      $soc = $r['soc'];
      $interests = array_slice($r, 1, 6, true);
      $careerTitle = $r['title'];
      $angles = [
        'realistic' => deg2rad(270), 'investigative' => deg2rad(300),
        'artistic' => deg2rad(240), 'social' => deg2rad(180),
        'enterprising' => deg2rad(120), 'conventional' => deg2rad(60)];
      $maxInterest = array_keys($interests, max($interests))[0];
      $xyWeights = [[
        ($interests[$maxInterest] * 1 * cos($angles[$maxInterest])),
        ($interests[$maxInterest] * 1 * sin($angles[$maxInterest]))
      ]];
      foreach ($interests as $intType => $intVal){
        if ($intType != $maxInterest && $intVal > 0.5){
          $xyWeights[] = [
            ($intVal * 1 * cos($angles[$intType])),
            ($intVal * 1 * sin($angles[$intType]))
          ];
        }
      }
      $wowX = array_column($xyWeights, 0);
      $wowY = array_column($xyWeights, 1);
      $careerArr = ['soc' => $soc, 'title'=>$careerTitle,
        'x' => array_sum($wowX)/count($wowX),
        'y' => array_sum($wowY)/count($wowY)
      ];
      if($r['rating'] == -1){
        $dislikedCareers[] = $careerArr;
      } else if($r['rating'] == 0){
        $neutralCareers[] = $careerArr;
      } else if($r['rating'] == 1){
        $likedCareers[] = $careerArr;
      }
    }
    $this->set('likedCareers', $likedCareers);
    $this->set('neutralCareers', $neutralCareers);
    $this->set('dislikedCareers', $dislikedCareers);

    $this->display('user');
  }

  public function setAdmin($uid, $to){
    $this->requireAdmin();
    $connection = ConnectionManager::get($this->datasource);

    if ($to == 'admin'){
      $connection->execute('INSERT INTO AdminUsers VALUES (:id)', ['id'=> $uid]);
    } else if ($to == 'unadmin'){
      $connection->execute('DELETE FROM AdminUsers WHERE id = :id', ['id'=> $uid]);
    }

    $this->redirect(['controller' => 'admin',
      'action' => 'displayUser', $uid]);
  }
  
  /* Gets list of files from the video folder, and removes all that are used by a row
   * Also detects invalid folders in the base directory, and reused person numbers
   * In finding unused files, also finds references to nonexistant files
   * In proper usage, most unnecessary, but allows fixing of malformed folders
   */
  public function displayOrphans(){
    $this->requireAdmin();
    $connection = ConnectionManager::get($this->datasource);
    // TODO: Refactor 'orphans' to 'errors', update comments
    // Combined with deadlinks to handle 'empty' arrays
    $orphans = [];
    $folderPath = WWW_ROOT . 'vid/';
    $folders = array_diff(scandir($folderPath), ['.', '..']);
    foreach ($folders as $f) {
      $path = $folderPath . $f;
      $matches = [];
      $regex = '/^(\d{2}-\d{4})_(\d+)_(.*)$/';
      // Nonmatch returns 0, error returns false, type-independent check
      // as both mean the folder has issues
      $regexFound = (preg_match($regex, $f, $matches) == 1);
      if (!$regexFound || !is_dir($path)){
        $orphans['root'][] = ['path' => $path,
          'info' => $this->getPathInfo($path)];
      } else {
        $soc = $matches[1];
        $pNum = $matches[2];
        $person = $matches[3];
        $files = array_diff(scandir($path), ['.', '..']);
        $newPerson = $this->initializeSOCPerson($orphans,
          $soc, '', $pNum, $person, 'files');
        if ($newPerson){
          $orphans[$soc]['people'][$pNum]['files'] = $files;
        // Two people for the same pNum, manual error
        } else {
          // Uses name as key as no two folders can have same name
          if (!array_key_exists('conflict', $orphans[$soc]['people'][$pNum])){
            $orphans[$soc]['people'][$pNum]['conflict'] = [];
          }
          $orphans[$soc]['people'][$pNum]['conflict'][$person] = $files;
        }
      }
    }
    
    $query = 'SELECT * FROM Videos';
    $results = $connection->execute($query)->fetchAll('assoc');
    foreach ($results as $r){
      $soc = $r['soc'];
      $pNum = $r['personNum'];
      $person = $r['person'];
      $qNum = $r['questionNum'];
      $fileName = $r['fileName'];
      // Opposite of orphan exists, rows have no matching file/folder
      // Possibly caused by manual database update w/o upload/copy
      // TODO: Clean up conditional mess, avoiding index access on null
      $nameInConflict = (array_key_exists('conflict', $orphans[$soc]['people'][$pNum])
          && array_key_exists($person, $orphans[$soc]['people'][$pNum]['conflict']));;
      $nameMismatch = ($person != $orphans[$soc]['people'][$pNum]['name']
        && !$nameInConflict);
      if (!array_key_exists($soc, $orphans)
        || !array_key_exists($pNum, $orphans[$soc]['people'])
        || $nameMismatch){
        $this->initializeSOCPerson($orphans,
          $soc, '', $pNum, $person, 'deadLinks');
        $orphans[$soc]['people'][$pNum]['deadLinks'][$qNum] = $fileName;
        continue;
      }
      // Above conditions ensure:
      //   soc in orphans
      //   pNum in orphans[soc]
      $personRef = &$orphans[$soc]['people'][$pNum];
      // Multiple folders exist for same pNum
      // Possibly caused by copying error, error in upload code
      if ($nameInConflict){
        // Swap with main entry in orphans (mark others as probably invalid)
        // If table enforces pNum-person equivalence, 100% correct behavior
        // Otherwise, last row to reference conflict will set that as main
        $tmpName = $personRef['name'];
        $tmpFiles = $personRef['files'];
        $personRef['name'] = $person;
        $personRef['files'] = $personRef['conflict'][$person];
        $personRef['conflict'][$tmpName] = $tmpFiles;
        unset($personRef['conflict'][$person]);
      }
      // Remove non-orphans from orphans list
      // Above conditions ensure:
      //   soc in orphans
      //   pNum in orphans[soc]
      //   name equals orphans[soc][..][pnum][name]
      // if filename does not match, dead link
      $fileIndex = array_search($fileName, $personRef['files']);
      // If not found, returns boolean false, != index 0
      if ($fileIndex === false){
        $personRef['deadLinks'][$qNum] = $fileName;
      } else {
        unset($personRef['files'][$fileIndex]);
        // Trim all arrays whose files are all accounted for
        // Conflicts not trimmed as they should not exist
        if (count($personRef['files']) == 0
          && !array_key_exists('conflict', $personRef)){
          unset($orphans[$soc]['people'][$pNum]);
        }
        if (count($orphans[$soc]['people']) == 0){
          unset($orphans[$soc]);
        }
      }
    }
    
    foreach ($orphans as $soc => $career){
      if ($soc == 'root'){
        continue;
      }
      // Trimming done during query, remaining empty started empty
      foreach ($career['people'] as $pNum => $person){
        foreach ($person['files'] as $fNum => $f){
          $filePath = $folderPath . implode('_',
            [$soc, $pNum, $person['name']]) . '/' . $f;
          $fileInfo = $this->getPathInfo($filePath);
          $orphans[$soc]['people'][$pNum]['files'][$fNum]
            = ['path' => $filePath, 'info' => $fileInfo];
        }
        if (array_key_exists('conflict', $person)){
          foreach($person['conflict'] as $cname => $cfiles){
            foreach($cfiles as $cfNum => $cf){
            $filePath = $folderPath . implode('_',
              [$soc, $pNum, $cname]) . '/' . $cf;
            $fileInfo = $this->getPathInfo($filePath);
            $orphans[$soc]['people'][$pNum]['conflict'][$cname][$cfNum]
              = ['path' => $filePath, 'info' => $fileInfo];
            }
          }
        }
      }
    }
    
    $this->set('orphans', $orphans);
    $this->display('orphans');
  }
  
  // taken from https://stackoverflow.com/questions/3349753/delete-directory-with-files-in-it
  // Recursive delete of a folder with files or folders in it
  private function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
//      throw new InvalidArgumentException("$dirPath must be a directory");
      unlink($dirPath);
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
      $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
      if (is_dir($file)) {
        self::deleteDir($file);
      } else {
        unlink($file);
      }
    }
    rmdir($dirPath);
  }
  
  /* Executes deletes created on the orphans page
   * Filepathes encoded and decoded by urlencode due to issues with escapes
   * Checks made for '' and '..' to prevent deformed POST from deleting more
   */
  public function cleanFilesystem(){
    $this->requireAdmin();
    if (!$this->request->is('post')){
      // TODO: Error page, not uploading anything
      $this->display('delete');
      return;
    }
    $path = WWW_ROOT . 'vid/';
    $deletes = [];
    $errors = [];
    foreach($this->request->data as $deleteEncoded => $exists){
      $delete = urldecode($deleteEncoded);
      if (preg_match('/\.\.\/|\/\.\.|^\.\.$|^$/', $delete) !== false){
        $this->deleteDir($path . $delete);
        $deletes[] = $path . $delete;
      } else {
        $errors[] = $path . $delete;
      }
    }
    $this->set('deletes', $deletes);
    $this->set('errors', $errors);
    $this->display('delete');
  }
}
