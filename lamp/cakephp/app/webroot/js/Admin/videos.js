// TODO: Delete and redo this whole thing
//    I had no idea how javascript or jQuery worked when I started
// ideas: Instead of update depending on action taken, iterate through and update
//    use consistent element passing (id vs object vs parent class etc.)
//    avoid setAttribute+string in favor of existing methods
//    use HTML as a storage mechanism, row order etc. instead of storing in ids
//    If only one career will be displayed at one time, more simplifications

// Taken from https://stackoverflow.com/questions/857618/javascript-how-to-extract-filename-from-a-file-input-control
var extractFileName = function(fullPath){
  var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
  var filename = fullPath.substring(startIndex);
  if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
      filename = filename.substring(1);
  }
  return filename;
}

/* Updates filename label on file selection
 * Separate label needed to have initial value set
 *    FileInput value inaccessible due to security reasons
 * Also updates filename change field, which handles rearranging
 */
var setUpload = function(id){
  var fileElement = document.getElementById(id + 'file');
  var fileName = fileElement.value;
  var label = document.getElementById(id + 'label');
  label.innerHTML = extractFileName(fileName);
  label.style.color = "Red";
  var namechange = document.getElementById(id + 'fnamechange');
  namechange.value = extractFileName(fileName);
};

/* Changes fileinput to a text field
 * Allows non-upload file switching, orphan assignment
 */
var swapFileNameType = function(id){
  var row = document.getElementById(id);
  var rowUpload = document.getElementById(id+'file');
  var rowButton = document.getElementById(id+'button');
  var rowLabel = document.getElementById(id+'label');
  var disable = rowButton.disabled;
  rowUpload.disabled = !disable;
  rowButton.disabled = !disable;
  rowLabel.disabled = !disable;
  rowButton.style.display = (!disable?'none':'initial');
  rowLabel.style.display = (!disable?'none':'initial');

  var fnamechange = document.getElementById(id+'fnamechange');
  fnamechange.style.display = (disable?'none':'initial');
  var swapIcon = document.getElementById(id+'fntype');
  var leftCell = row.getElementsByClassName('videoCell')[0];
  var rightCell = row.getElementsByClassName('uploadCell')[0];
  if (disable){
    fnamechange.value = rowLabel.innerHTML;
    swapIcon.classList.add('fa-pencil-square-o');
    swapIcon.classList.remove('fa-upload');
    leftCell.setAttribute('colspan', '1');
    // Setting colspan to 0 still takes space
    rightCell.style.display = 'initial';
  } else {
    swapIcon.classList.remove('fa-pencil-square-o');
    swapIcon.classList.add('fa-upload');
    leftCell.setAttribute('colspan', '2');
    rightCell.style.display = 'none';
  }
}
/* Switches out title for editable field which also updates title
 */
var rename = function(caller, reveal, elemId){
  var nameHead = document.getElementById(elemId+'name');
  var nameChange = document.getElementById(elemId+'pnamechange');
  if (reveal){
    nameHead.style.display = 'none';
    nameChange.style.display = 'initial';
    nameChange.value = nameHead.innerHTML;
    caller.classList.add('fa-pencil-square');
    caller.classList.remove('fa-pencil-square-o');
  } else {
    nameHead.style.display = 'initial';
    nameChange.style.display = 'none';
    caller.classList.remove('fa-pencil-square');
    caller.classList.add('fa-pencil-square-o');
  }
  caller.setAttribute('onclick',
    'rename(this, ' + !reveal + ', \'' + elemId + '\');');
}
var updateNameHead = function(caller, elemId){
  document.getElementById(elemId+'name').innerHTML = caller.value;
}
var rearrangePeople = function(elemId, dir){
  var row = document.getElementById(elemId).parentNode;
  var table = row.parentNode;
  
  var regex = new RegExp('^soc(..-....)p([-\\d]+)$');
  var matches = elemId.match(regex);
  var soc = matches[1];
  var pid = parseInt(matches[2]);
    
  if (dir == 'up' && row.rowIndex > 0){
    var dstRow = table.rows[row.rowIndex-1];
    table.insertBefore(row, dstRow);
    shiftPrefixPid(dstRow, 'soc'+soc, function(tePid){
      return true;
    }, function(tePid){
      return pid;
    });
    shiftPrefixPid(row, 'soc'+soc, function(tePid){
      return true;
    }, function(tePid){
      return pid-1;
    });
  } else if (dir == 'down' && row.rowIndex+1 < table.rows.length){
    var dstRow = table.rows[row.rowIndex+1];
    table.insertBefore(dstRow, row);
    shiftPrefixPid(dstRow, 'soc'+soc, function(tePid){
      return true;
    }, function(tePid){
      return pid;
    });
    shiftPrefixPid(row, 'soc'+soc, function(tePid){
      return true;
    }, function(tePid){
      return pid+1;
    });
  }
}
 
/* Moves the row to the delete table, changing css and disabling buttons
 * Updates ids as follows (up/down graphically, not id value):
 *   On delete:
 *     All following rows (upload+delete) shifted up 1
 *     Deleted row moved to end of delete table
 *   On undelete:
 *     All delete rows shifted down by 1
 *     Undeleted row moved to end of upload table
 */
var markRowForDelete = function(caller, disable, elemId){
  var qText = document.getElementById(elemId+'text');
  if (qText.disabled == disable){
    return;
  }
  qText.disabled = disable;
  document.getElementById(elemId+'button').disabled = disable;
  document.getElementById(elemId+'file').disabled = disable;
  document.getElementById(elemId+'fntype').style.visibility
    = (disable?'hidden':'initial');
  document.getElementById(elemId+"drag").style.visibility
    = (disable?'hidden':'initial');
  caller.setAttribute('onclick', 'markRowForDelete(this, ' + !disable +
    ', \'' + elemId + '\');');
  var row = caller.parentNode.parentNode;

  var rowDeleteIcon = document.getElementById(elemId+"dicon");
  if (disable){
    row.classList.add('rowToDelete');
    rowDeleteIcon.classList.remove("fa-trash-o");
    rowDeleteIcon.classList.add("fa-trash");
  } else {
    row.classList.remove('rowToDelete');
    rowDeleteIcon.classList.remove("fa-trash");
    rowDeleteIcon.classList.add("fa-trash-o");
  }
    
  var regex = new RegExp('^soc(..-....)p([-\\d]+)q([-\\d]+)(.*?)' + 
    '(file|label|button|text|delete)?$');
  var matches = elemId.match(regex);
  var qid = parseInt(matches[3]);
  
  var prefix = 'soc' + matches[1] + 'p' + matches[2] + 'q';

  if (disable){
    var personForm = row.parentNode.parentNode.parentNode;
    var maxQid = (-1)+shiftPrefixQid(personForm, prefix,
      function(teQid){
        return teQid >= qid;
      },
      function(teQid){
        return teQid - 1;
      });
      
    shiftPrefixQid(row, prefix,
      function(teQid){
        return true;
      },
      function(teQid){
        return maxQid;
      });
    personForm.getElementsByClassName('deleteTable')
      [0].appendChild(row);
  } else {
    var dtable = row.parentNode;
    var table = dtable.parentNode
      .getElementsByClassName('uploadTable')[0];
    var maxQid = (-1)+shiftPrefixQid(table, prefix, function(q){
      return true;
    }, function(q){
      return q;
    });
    shiftPrefixQid(dtable, prefix, function(teQid){
      return (teQid < qid);
    }, function(teQid){
      return teQid+1;
    });
    shiftPrefixQid(row, prefix, function(teQid){
      return true;
    }, function(teQid){
      return maxQid+1;
    });
    // For some reason uploadTable has a tbody while deleteTable does not
    table.children[0].appendChild(row);
  }
};

var markTableForDelete = function(caller, disable, elemId){
  var regex = new RegExp('^soc(..-....)p([-\\d]+)(.*?)$');
  var matches = elemId.match(regex);
  var soc = matches[1];
  var pid = parseInt(matches[2]);
  
  var row = document.getElementById(elemId).parentNode;
  var deleteButtons = row.querySelectorAll(
    '[id^="soc' + soc + 'p' + pid + 'q"][id$=delete]');
  for (var i=0; i < deleteButtons.length; i++){
    var elemId = deleteButtons[i].parentNode.parentNode.id;
    markRowForDelete(deleteButtons[i], !disable, elemId);
    deleteButtons[i].click();
  }
  caller.setAttribute('onclick',
    'markTableForDelete(this, '+!disable+', \'soc'+soc+'p'+pid+'\');');
  if (disable){
    caller.classList.remove("fa-trash-o");
    caller.classList.add("fa-trash");
  } else {
    caller.classList.add("fa-trash-o");
    caller.classList.remove("fa-trash");
  }
  
  var prefix = 'soc' + soc + 'p';

  if (disable){
    var careerDiv = row.parentNode.parentNode.parentNode;
    var maxPid = (-1)+shiftPrefixPid(careerDiv, prefix,
      function(tePid){
        return tePid >= pid;
      },
      function(tePid){
        return tePid - 1;
      });
    shiftPrefixPid(row, prefix,
      function(tePid){
        return true;
      },
      function(tePid){
        return maxPid;
      });
    careerDiv.getElementsByClassName('unpersonTable')
      [0].appendChild(row);
  } else {
  
    var dtable = row.parentNode;
    var table = dtable.parentNode
      .getElementsByClassName('personTable')[0];
    var maxPid = (-1)+shiftPrefixPid(table, prefix, function(q){
      return true;
    }, function(q){
      return q;
    });
    shiftPrefixPid(dtable, prefix, function(tePid){
      return (tePid < pid);
    }, function(tePid){
      return tePid+1;
    });
    shiftPrefixPid(row, prefix, function(tePid){
      return true;
    }, function(tePid){
      return maxPid+1;
    });
    // For some reason uploadTable has a tbody while deleteTable does not
    table.children[0].appendChild(row);
  }
}

/* Inserts a new row to uploadTable (used for initial table population)
 * Shifts all delete table rows down by 1
 * Essentially 1 to 1 port of original html tags to javascript, messy
 */
var addQuestion = function(tableId, person, questionEntry='', fileName=''){
  var table = document.getElementById(tableId);
  
  var id = shiftPrefixQid(table, tableId+'q',
  function(q){
    return true;
  }, function(q){
    return q;
  });
  
  var dtable = document.getElementById(tableId+'dtable');
  if (dtable != null){
    shiftPrefixQid(dtable, tableId+'q',
    function(q){
      return true;
    }, function(q){
      return q+1;
    });
  }
  var nextElemId = tableId + "q" + id + person;
  
  var row = table.insertRow();
  row.className = 'questionRow';
  row.setAttribute('id', nextElemId);

  var question = row.insertCell(-1);
  question.className = 'questionCell';
  var questionText = document.createElement('input');
  questionText.setAttribute('type', 'text');
  questionText.setAttribute('id', nextElemId+'text');
  questionText.setAttribute('class', 'questionText');
  questionText.setAttribute('value', questionEntry);
  questionText.setAttribute('name', nextElemId+'text');
  question.appendChild(questionText);
  
  var fileEdit = row.insertCell(-1);
  fileEdit.className = 'fileNameTypeCell';
  var fileNameTypeIcon = document.createElement('i');
  fileNameTypeIcon.classList.add('fa');
  fileNameTypeIcon.classList.add('fa-pencil-square-o');
  fileNameTypeIcon.setAttribute('aria-hidden', 'true');
  fileNameTypeIcon.setAttribute('id', nextElemId+'fntype');
  fileNameTypeIcon.setAttribute('onclick',
    'swapFileNameType("' + nextElemId + '");');
  fileEdit.appendChild(fileNameTypeIcon);

  var file = row.insertCell(-1);
  file.className = 'videoCell';
  var fileInput = document.createElement('input');
  fileInput.setAttribute('type', 'file');
  fileInput.setAttribute('id', nextElemId+'file');
  // Probably a way to directly set a onchange function
  // Will need to have a constant argument (partial function?)
  fileInput.setAttribute('onchange',
    'setUpload(\'' + nextElemId + '\')');
  fileInput.setAttribute('name', nextElemId+'file');
  fileInput.setAttribute('autocomplete', 'off');
  file.appendChild(fileInput);
  // If constructed by createElement, the label innerHtml is never set
  // TODO: Find out what's going on, low priority
  file.insertAdjacentHTML('beforeend',
    '<label id="' + nextElemId+'label' +
    '" for="' + nextElemId+'file' + '">' +
    fileName + '</label>');
  var fileNameChange = document.createElement('input');
  fileNameChange.setAttribute('type', 'text');
  // Possilble issue with regex matching "file" inside "filename"
  // Changed to "fnamechange" anyways to be safe
  fileNameChange.classList.add('fileNameChange');
  fileNameChange.setAttribute('id', nextElemId+'fnamechange');
  fileNameChange.setAttribute('name', nextElemId+'fnamechange');
  fileNameChange.setAttribute('value', fileName);
  fileNameChange.setAttribute('autocomplete', 'off');
  fileNameChange.style.display = 'none';
  file.appendChild(fileNameChange);

  var upload = row.insertCell(-1);
  upload.className = 'uploadCell';
  var uploadButton = document.createElement('input');
  uploadButton.setAttribute('type', 'button');
  uploadButton.setAttribute('id', nextElemId+'button');
  uploadButton.setAttribute('value', 'Browse...');
  uploadButton.setAttribute('autocomplete', 'off');
  uploadButton.setAttribute('onclick',
    'document.getElementById(\'' +
    nextElemId + 'file\').click();');
  upload.appendChild(uploadButton);
  
  var deleteCell = row.insertCell(-1);
  deleteCell.className = 'deleteCell';
  var deleteIcon = document.createElement('i');
  deleteIcon.classList.add('fa');
  deleteIcon.classList.add('fa-trash-o');
  deleteIcon.setAttribute('id', nextElemId+'dicon');
  deleteIcon.setAttribute('aria-hidden', 'true');
  deleteIcon.setAttribute('onclick',
    'document.getElementById(\'' +
    nextElemId + 'delete\').click();');
  deleteCell.appendChild(deleteIcon);
  
  var deleteBox = document.createElement('input');
  deleteBox.setAttribute('type', 'checkbox');
  deleteBox.setAttribute('id', nextElemId+'delete');
  deleteBox.setAttribute('value', 'Delete');
  deleteBox.setAttribute('name', nextElemId+'delete');
  deleteBox.setAttribute('onclick', 'markRowForDelete(this, true, \'' +
    nextElemId + '\');');
  deleteBox.style.display = 'none';
  deleteBox.setAttribute('autocomplete', 'off');
  deleteCell.appendChild(deleteBox);
  
  var dragCell = row.insertCell(-1);
  var dragIcon = document.createElement('i');
  dragIcon.classList.add('fa');
  dragIcon.classList.add('fa-arrows');
  dragIcon.setAttribute('aria-hidden', 'true');
  dragIcon.setAttribute('id', nextElemId+'drag');
  dragCell.appendChild(dragIcon);
};

/* Wrapper function for addPerson, called by button to take name from input
 * Adds at least one blank row so a blank value will be inserted
 * so subsequent loads will show correct order (manual delete reorders)
 */
var addPersonFromBox = function(soc){
  var inputElem = document.getElementById(soc+'add');
  var name = inputElem.value;
  inputElem.value = '';
  if (name != ''){
    var tableId = addPerson(soc, name, "{}");
    document.getElementById(tableId+'pnamechange').value = name;
    addQuestion(tableId, name);
  }
}

/* Creates person table, used for initial creation
 * 1-to-1 conversion of original html to javascript
 */
var addPerson = function(soc, name, questionsJSON){
  var socTable = document.getElementById(soc);
  
  var pid = shiftPrefixPid(socTable, 'soc'+soc+'p',
  function(q){
    return true;
  }, function(q){
    return q;
  });
  var untable = document.getElementById('soc'+soc+'untable');
  if (untable != null){
    shiftPrefixPid(untable, 'soc'+soc+'p',
    function(q){
      return true;
    }, function(q){
      return q+1;
    });
  }
  
  var questions = JSON.parse(questionsJSON);
  var tableId = 'soc' + soc + 'p' + pid;
  var careerDiv = socTable.insertRow();
  
  var headerTable = document.createElement('table');
  headerTable.className = 'headerTable';
  var headerRow = headerTable.insertRow();
  headerRow.className = 'headerRow';
  var orderCell = headerRow.insertCell(-1);
  orderCell.className = 'orderCell';
  var moveUp = document.createElement('i');
  moveUp.classList.add('fa');
  moveUp.classList.add('fa-arrow-circle-up');
  moveUp.setAttribute('id', tableId+'moveup');
  moveUp.setAttribute('aria-hidden', 'true');
  moveUp.setAttribute('onclick',
    'rearrangePeople(\''+tableId+'\', \'up\');');
  orderCell.appendChild(moveUp);
  var moveDown = document.createElement('i');
  moveDown.classList.add('fa');
  moveDown.classList.add('fa-arrow-circle-down');
  moveDown.setAttribute('id', tableId+'movedown');
  moveDown.setAttribute('aria-hidden', 'true');
  moveDown.setAttribute('onclick',
    'rearrangePeople(\''+tableId+'\', \'down\');');
  orderCell.appendChild(moveDown);
  var headerName = headerRow.insertCell(-1);
  var nameHead = document.createElement('h4');
  nameHead.setAttribute('id', tableId+'name');
  nameHead.innerHTML = name;
  headerName.appendChild(nameHead);
  var nameChange = document.createElement('input');
  nameChange.setAttribute('type', 'text');
  nameChange.setAttribute('id', tableId+'pnamechange');
  nameChange.setAttribute('name', tableId+'pnamechange');
  nameChange.setAttribute('value', 'UNEDITED');
  nameChange.setAttribute('onChange',
    'updateNameHead(this, \'' + tableId + '\');');
  nameChange.style.display = 'none';
  headerName.appendChild(nameChange);
  
  var headerRename = headerRow.insertCell(-1);
  var renameIcon = document.createElement('i');
  renameIcon.classList.add('fa');
  renameIcon.classList.add('fa-pencil-square-o');
  renameIcon.setAttribute('id', tableId+'dicon');
  renameIcon.setAttribute('aria-hidden', 'true');
  renameIcon.setAttribute('onclick',
    'rename(this, true, \''+tableId+'\');');
  headerRename.appendChild(renameIcon);
  var headerDelete = headerRow.insertCell(-1);
  var deleteAllIcon = document.createElement('i');
  deleteAllIcon.classList.add('fa');
  deleteAllIcon.classList.add('fa-trash-o');
  deleteAllIcon.setAttribute('id', tableId+'dicon');
  deleteAllIcon.setAttribute('aria-hidden', 'true');
  deleteAllIcon.setAttribute('onclick',
    'markTableForDelete(this, true, \'soc'+soc+'p'+pid+'\');');
  headerDelete.appendChild(deleteAllIcon);
  careerDiv.appendChild(headerTable);
  
  var uploadTable = document.createElement('table');
  uploadTable.className = 'uploadTable';
  uploadTable.setAttribute('id', tableId);
  careerDiv.appendChild(uploadTable);
  
  // taken from https://stackoverflow.com/questions/7241878/for-in-loops-in-javascript-key-value-pairs
  for (var qid in questions){
    if (typeof questions[qid] !== 'function') {
      var q = questions[qid];
      addQuestion(tableId, name, q[0], q[1]);
    }
  }
  
  var deleteTable = document.createElement('table');
  deleteTable.className = 'deleteTable';
  deleteTable.setAttribute('id', tableId+'dtable');
  careerDiv.appendChild(deleteTable);
  
  var controlsTable = document.createElement('table');
  controlsTable.className = 'controlsTable';
  careerDiv.appendChild(controlsTable);
  
  var controlsRow = controlsTable.insertRow();
  controlsRow.className = 'controlsRow';
  var addQuestionCell = controlsRow.insertCell(-1);
  addQuestionCell.className = 'questionAddCell';
  addQuestionCell.setAttribute('valign', 'top');
  var addQuestionButton = document.createElement('a');
  addQuestionButton.className = 'questionAddButton';
  addQuestionButton.setAttribute('id', tableId+'add');
  addQuestionButton.innerHTML = '+ Add Question';
  addQuestionButton.setAttribute('onclick', 'addQuestion(\''
    + tableId + '\', \'' + name + '\');');
  addQuestionCell.appendChild(addQuestionButton);
  
  return tableId;
};

/* Helper function to perform operation on all question IDs
 * for all children of 'elem' matching 'prefix',
 *   if it satisfies 'cond', perform 'op'
 * where 'cond' and 'op' are functions taking in a numeric qid
 */
var shiftPrefixQid = function(elem, prefix, cond, op){
  var tableElems = elem.querySelectorAll(
    '[id^="' + prefix + '"]');
  var nextQid = 0;
  for (var i = -1; i < tableElems.length; i++){
    var elemToUpdate = null;
    if (i == -1){
      elemToUpdate = elem;
    } else {
      elemToUpdate = tableElems[i];
    }
    var teRegex = new RegExp('^soc(..-....)p([-\\d]+)q([-\\d]+)(.*?)' + 
      '(file|label|button|text|delete)?$');
    var teMatches = elemToUpdate.id.match(teRegex);
    if (teMatches === null){
      continue;
    }
    var teQid = parseInt(teMatches[3]);
    if (cond(teQid)){
      nextQid = Math.max(nextQid-1, teQid)+1;
      var newQid = op(teQid).toString();
      teMatches[3] = newQid;
      for (var ii=0; ii < elemToUpdate.attributes.length; ii++){
        var attrRegex = new RegExp('(soc..-....p[-\\d]+q)([-\\d]+)(.*?' + 
          '(?:file|label|button|text|delete|dicon)?)', 'g');
        elemToUpdate.attributes[ii].value = elemToUpdate.attributes[ii]
          .value.replace(attrRegex, "$1"+newQid+"$3");
      }
    }
  }
  return nextQid;
}

/* Near-identical to shiftPrefixQid, but for Pid
 * Enough changes necessary to do new function instead of modifying each call
 */
var shiftPrefixPid = function(elem, prefix, cond, op){
  var tableElems = elem.querySelectorAll(
    '[id^="' + prefix + '"]');
  var nextPid = 0;
  for (var i = -1; i < tableElems.length; i++){
    var elemToUpdate = null;
    if (i == -1){
      elemToUpdate = elem;
    } else {
      elemToUpdate = tableElems[i];
    }
    var teRegex = new RegExp('^soc(..-....)p([-\\d]+)(.*)$');
    var teMatches = elemToUpdate.id.match(teRegex);
    if (teMatches === null){
      continue;
    }
    var tePid = parseInt(teMatches[2]);
    if (cond(tePid)){
      nextPid = Math.max(nextPid-1, tePid)+1;
      var newPid = op(tePid).toString();
      teMatches[2] = newPid;
      for (var ii=0; ii < elemToUpdate.attributes.length; ii++){
        var attrRegex = new RegExp('(soc..-....p)([-\\d]+)(.*)', 'g');
        elemToUpdate.attributes[ii].value = elemToUpdate.attributes[ii]
          .value.replace(attrRegex, "$1"+newPid+"$3");
      }
    }
  }
  return nextPid;
}

// Drag and drop code taken from https://www.w3schools.com/html/html5_draganddrop.asp
// jQuery drag and drop code from https://stackoverflow.com/questions/3591264/can-table-rows-be-made-draggable
$(function(){
  $('.uploadTable').children('tbody').sortable({
    start: jQueryDrag,
    stop: jQueryDrop
  });
});

var jQueryDrag = function (event, ui){
  ui.item.data('rowId', ui.item[0].id);
}
var jQueryDrop = function (event, ui){
  // Assumes tbody automatically added as only element of table
  var table = $(this)[0];
  var rowId = ui.item.data('rowId');
  var remRow = document.getElementById(rowId);
  var dstRow = ui.item.prev()[0];
  
  var regex = new RegExp('^soc(..-....)p([-\\d]+)q([-\\d]+)(.*?)' + 
    '(file|label|button|text|delete)?$');
  var matches = rowId.match(regex);
  var remQid = parseInt(matches[3]);
  matches = dstRow.id.match(regex);
  var dstQid = parseInt(matches[3]);
  var prefix = 'soc' + matches[1] + 'p' + matches[2] + 'q';
  
  var moveForwards = (remQid < dstQid);
  shiftPrefixQid(table, prefix, function(teQid){
    return (moveForwards && (teQid > remQid && teQid <= dstQid))
      || (!moveForwards && (teQid < remQid && teQid >= dstQid));
  }, function(teQid){
    return teQid + (moveForwards?-1:1);
  });
  shiftPrefixQid(remRow, prefix, function(teQid){
    return true;
  }, function(teQid){
    return dstQid;
  });
  table.insertBefore(remRow, dstRow);
  if (moveForwards){
    table.insertBefore(dstRow, remRow);
  }
}