// TODO: Delete and redo this whole thing
//    I had no idea how javascript or jQuery worked when I started
// ideas: Instead of update depending on action taken, iterate through and update
//    use consistent element passing (id vs object vs parent class etc.)
//    avoid setAttribute+string in favor of existing methods
//    use HTML as a storage mechanism, row order etc. instead of storing in ids
//    If only one career will be displayed at one time, more simplifications

// Taken from https://stackoverflow.com/questions/857618/javascript-how-to-extract-filename-from-a-file-input-control
let extractFileName = function(fullPath){
  let startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
  let filename = fullPath.substring(startIndex);
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
let setUpload = function(id){
  let fileElement = document.getElementById(id + 'file');
  let fileName = fileElement.value;
  let label = document.getElementById(id + 'label');
  label.innerHTML = extractFileName(fileName);
  label.style.color = "Red";
  let namechange = document.getElementById(id + 'fnamechange');
  namechange.value = extractFileName(fileName);
};

/* Changes fileinput to a text field
 * Allows non-upload file switching, orphan assignment
 */
let swapFileNameType = function(id){
  let row = document.getElementById(id);
  let rowUpload = document.getElementById(id+'file');
  let rowButton = document.getElementById(id+'button');
  let rowLabel = document.getElementById(id+'label');
  let disable = rowButton.disabled;
  rowUpload.disabled = !disable;
  rowButton.disabled = !disable;
  rowLabel.disabled = !disable;
  rowButton.style.display = (!disable?'none':'initial');
  rowLabel.style.display = (!disable?'none':'initial');

  let fnamechange = document.getElementById(id+'fnamechange');
  fnamechange.style.display = (disable?'none':'initial');
  let swapIcon = document.getElementById(id+'fntype');
  let leftCell = row.getElementsByClassName('videoCell')[0];
  let rightCell = row.getElementsByClassName('uploadCell')[0];
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
    fnamechange.focus();
  }
}
/* Switches out title for editable field which also updates title
 */
let rename = function(caller, reveal, elemId){
  let nameHead = document.getElementById(elemId+'name');
  let nameChange = document.getElementById(elemId+'pnamechange');
  if (reveal){
    nameHead.style.display = 'none';
    nameChange.style.display = 'initial';
    nameChange.value = nameHead.innerHTML;
    caller.classList.add('fa-pencil-square');
    caller.classList.remove('fa-pencil-square-o');
    nameChange.focus();
  } else {
    nameHead.style.display = 'initial';
    nameChange.style.display = 'none';
    caller.classList.remove('fa-pencil-square');
    caller.classList.add('fa-pencil-square-o');
  }
  caller.setAttribute('onclick',
    'rename(this, ' + !reveal + ', \'' + elemId + '\');');
}
let updateNameHead = function(caller, elemId){
  document.getElementById(elemId+'name').innerHTML = caller.value;
}
let rearrangePeople = function(elemId, dir){
  let row = document.getElementById(elemId).parentNode;
  let table = row.parentNode;
  
  let regex = new RegExp('^soc(..-....)p([-\\d]+)$');
  let matches = elemId.match(regex);
  let soc = matches[1];
  let pid = parseInt(matches[2]);
    
  if (dir == 'up' && row.rowIndex > 0){
    let dstRow = table.rows[row.rowIndex-1];
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
    let dstRow = table.rows[row.rowIndex+1];
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
let markRowForDelete = function(caller, disable, elemId){
  let qText = document.getElementById(elemId+'text');
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
  let row = caller.parentNode.parentNode;

  let rowDeleteIcon = document.getElementById(elemId+"dicon");
  if (disable){
    row.classList.add('rowToDelete');
    rowDeleteIcon.classList.remove("fa-trash-o");
    rowDeleteIcon.classList.add("fa-trash");
  } else {
    row.classList.remove('rowToDelete');
    rowDeleteIcon.classList.remove("fa-trash");
    rowDeleteIcon.classList.add("fa-trash-o");
  }
    
  let regex = new RegExp('^soc(..-....)p([-\\d]+)q([-\\d]+)(.*?)' + 
    '(file|label|button|text|delete)?$');
  let matches = elemId.match(regex);
  let qid = parseInt(matches[3]);
  
  let prefix = 'soc' + matches[1] + 'p' + matches[2] + 'q';

  if (disable){
    let personForm = row.parentNode.parentNode.parentNode;
    let maxQid = (-1)+shiftPrefixQid(personForm, prefix,
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
    let dtable = row.parentNode;
    let table = dtable.parentNode
      .getElementsByClassName('uploadTable')[0];
    let maxQid = (-1)+shiftPrefixQid(table, prefix, function(q){
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

let markTableForDelete = function(caller, disable, elemId){
  let regex = new RegExp('^soc(..-....)p([-\\d]+)(.*?)$');
  let matches = elemId.match(regex);
  let soc = matches[1];
  let pid = parseInt(matches[2]);

  let row = document.getElementById(elemId).parentNode;
  let deleteButtons = row.querySelectorAll(
    '[id^="soc' + soc + 'p' + pid + 'q"][id$=delete]');
  for (let i=0; i < deleteButtons.length; i++){
    let elemId = deleteButtons[i].parentNode.parentNode.id;
    markRowForDelete(deleteButtons[i], disable, elemId);
    let dicon = document.getElementById(elemId+'dicon');
    if (disable){
      dicon.setAttribute('onclick', 'null');
      deleteButtons[i].setAttribute('checked', true);
    } else {
      dicon.setAttribute('onclick',
        'document.getElementById(\'' + elemId + 'delete\').click();');
      deleteButtons[i].removeAttribute('checked');
    }
  }
  
  caller.setAttribute('onclick',
    'markTableForDelete(this, '+!disable+', \'soc'+soc+'p'+pid+'\');');
  if (disable){
    row.classList.add('tableToDelete');
    caller.classList.remove("fa-trash-o");
    caller.classList.add("fa-trash");
  } else {
    row.classList.remove('tableToDelete');
    caller.classList.add("fa-trash-o");
    caller.classList.remove("fa-trash");
  }
  
  document.getElementById(elemId+'moveup').style.visibility
    = (disable?'hidden':'initial');
  document.getElementById(elemId+'movedown').style.visibility
    = (disable?'hidden':'initial');
  document.getElementById(elemId+'add').style.visibility
    = (disable?'hidden':'initial');
  document.getElementById(elemId+'pnicon').style.visibility
    = (disable?'hidden':'initial');

  
  let prefix = 'soc' + soc + 'p';

  if (disable){
    let careerDiv = row.parentNode.parentNode.parentNode;
    let maxPid = (-1)+shiftPrefixPid(careerDiv, prefix,
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
  
    let dtable = row.parentNode;
    let table = dtable.parentNode
      .getElementsByClassName('personTable')[0];
    let maxPid = (-1)+shiftPrefixPid(table, prefix, function(q){
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
let addQuestion = function(tableId, person, questionEntry='', fileName=''){
  let table = document.getElementById(tableId);
  
  let id = shiftPrefixQid(table, tableId+'q',
  function(q){
    return true;
  }, function(q){
    return q;
  });
  
  let dtable = document.getElementById(tableId+'dtable');
  if (dtable != null){
    shiftPrefixQid(dtable, tableId+'q',
    function(q){
      return true;
    }, function(q){
      return q+1;
    });
  }
  let nextElemId = tableId + "q" + id + person;
  
  let row = table.insertRow();
  row.className = 'questionRow';
  row.setAttribute('id', nextElemId);

  let question = row.insertCell(-1);
  question.className = 'questionCell';
  let questionText = document.createElement('input');
  questionText.setAttribute('type', 'text');
  questionText.setAttribute('id', nextElemId+'text');
  questionText.setAttribute('class', 'questionText');
  questionText.setAttribute('value', questionEntry);
  questionText.setAttribute('name', nextElemId+'text');
  question.appendChild(questionText);
  
  let fileEdit = row.insertCell(-1);
  fileEdit.className = 'fileNameTypeCell';
  let fileNameTypeIcon = document.createElement('i');
  fileNameTypeIcon.classList.add('fa');
  fileNameTypeIcon.classList.add('fa-pencil-square-o');
  fileNameTypeIcon.setAttribute('aria-hidden', 'true');
  fileNameTypeIcon.setAttribute('id', nextElemId+'fntype');
  fileNameTypeIcon.setAttribute('onclick',
    'swapFileNameType("' + nextElemId + '");');
  fileEdit.appendChild(fileNameTypeIcon);

  let file = row.insertCell(-1);
  file.className = 'videoCell';
  let fileInput = document.createElement('input');
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
  let fileNameChange = document.createElement('input');
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

  let upload = row.insertCell(-1);
  upload.className = 'uploadCell';
  let uploadButton = document.createElement('input');
  uploadButton.setAttribute('type', 'button');
  uploadButton.setAttribute('id', nextElemId+'button');
  uploadButton.setAttribute('value', 'Browse...');
  uploadButton.setAttribute('autocomplete', 'off');
  uploadButton.setAttribute('onclick',
    'document.getElementById(\'' +
    nextElemId + 'file\').click();');
  upload.appendChild(uploadButton);
  
  let deleteCell = row.insertCell(-1);
  deleteCell.className = 'deleteCell';
  let deleteIcon = document.createElement('i');
  deleteIcon.classList.add('fa');
  deleteIcon.classList.add('fa-trash-o');
  deleteIcon.setAttribute('id', nextElemId+'dicon');
  deleteIcon.setAttribute('aria-hidden', 'true');
  deleteIcon.setAttribute('onclick',
    'document.getElementById(\'' +
    nextElemId + 'delete\').click();');
  deleteCell.appendChild(deleteIcon);
  
  let deleteBox = document.createElement('input');
  deleteBox.setAttribute('type', 'checkbox');
  deleteBox.setAttribute('id', nextElemId+'delete');
  deleteBox.setAttribute('value', 'Delete');
  deleteBox.setAttribute('name', nextElemId+'delete');
  deleteBox.setAttribute('onclick',
    'markRowForDelete(this, true, \'' + nextElemId + '\');');
  deleteBox.style.display = 'none';
  deleteBox.setAttribute('autocomplete', 'off');
  deleteCell.appendChild(deleteBox);
  
  let dragCell = row.insertCell(-1);
  let dragIcon = document.createElement('i');
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
let addPersonFromBox = function(soc){
  let inputElem = document.getElementById(soc+'add');
  let name = inputElem.value;
  inputElem.value = '';
  if (name != ''){
    let tableId = addPerson(soc, name, "{}");
    document.getElementById(tableId+'pnamechange').value = name;
    addQuestion(tableId, name);
  }
}

/* Creates person table, used for initial creation
 * 1-to-1 conversion of original html to javascript
 */
let addPerson = function(soc, name, questionsJSON){
  let socTable = document.getElementById(soc);
  
  let pid = shiftPrefixPid(socTable, 'soc'+soc+'p',
  function(q){
    return true;
  }, function(q){
    return q;
  });
  let untable = document.getElementById('soc'+soc+'untable');
  if (untable != null){
    shiftPrefixPid(untable, 'soc'+soc+'p',
    function(q){
      return true;
    }, function(q){
      return q+1;
    });
  }
  
  let questions = JSON.parse(questionsJSON);
  let tableId = 'soc' + soc + 'p' + pid;
  let careerDiv = socTable.insertRow();
  
  let headerTable = document.createElement('table');
  headerTable.className = 'headerTable';
  let headerRow = headerTable.insertRow();
  headerRow.className = 'headerRow';
  let orderCell = headerRow.insertCell(-1);
  orderCell.className = 'orderCell';
  let moveUp = document.createElement('i');
  moveUp.classList.add('fa');
  moveUp.classList.add('fa-arrow-circle-up');
  moveUp.setAttribute('id', tableId+'moveup');
  moveUp.setAttribute('aria-hidden', 'true');
  moveUp.setAttribute('onclick',
    'rearrangePeople(\''+tableId+'\', \'up\');');
  orderCell.appendChild(moveUp);
  let moveDown = document.createElement('i');
  moveDown.classList.add('fa');
  moveDown.classList.add('fa-arrow-circle-down');
  moveDown.setAttribute('id', tableId+'movedown');
  moveDown.setAttribute('aria-hidden', 'true');
  moveDown.setAttribute('onclick',
    'rearrangePeople(\''+tableId+'\', \'down\');');
  orderCell.appendChild(moveDown);
  let headerName = headerRow.insertCell(-1);
  let nameHead = document.createElement('h4');
  nameHead.setAttribute('id', tableId+'name');
  nameHead.innerHTML = name;
  headerName.appendChild(nameHead);
  let nameChange = document.createElement('input');
  nameChange.setAttribute('type', 'text');
  nameChange.setAttribute('id', tableId+'pnamechange');
  nameChange.setAttribute('name', tableId+'pnamechange');
  nameChange.setAttribute('value', 'UNEDITED');
  nameChange.setAttribute('onChange',
    'updateNameHead(this, \'' + tableId + '\');');
  nameChange.style.display = 'none';
  headerName.appendChild(nameChange);
  
  let headerRename = headerRow.insertCell(-1);
  let renameIcon = document.createElement('i');
  renameIcon.classList.add('fa');
  renameIcon.classList.add('fa-pencil-square-o');
  renameIcon.setAttribute('id', tableId+'pnicon');
  renameIcon.setAttribute('aria-hidden', 'true');
  renameIcon.setAttribute('onclick',
    'rename(this, true, \''+tableId+'\');');
  headerRename.appendChild(renameIcon);
  let headerDelete = headerRow.insertCell(-1);
  let deleteAllIcon = document.createElement('i');
  deleteAllIcon.classList.add('fa');
  deleteAllIcon.classList.add('fa-trash-o');
  deleteAllIcon.setAttribute('id', tableId+'dallicon');
  deleteAllIcon.setAttribute('aria-hidden', 'true');
  deleteAllIcon.setAttribute('onclick',
    'markTableForDelete(this, true, \'soc'+soc+'p'+pid+'\');');
  headerDelete.appendChild(deleteAllIcon);
  careerDiv.appendChild(headerTable);
  
  let uploadTable = document.createElement('table');
  uploadTable.className = 'uploadTable';
  uploadTable.setAttribute('id', tableId);
  careerDiv.appendChild(uploadTable);
  
  // taken from https://stackoverflow.com/questions/7241878/for-in-loops-in-javascript-key-value-pairs
  for (let qid in questions){
    if (typeof questions[qid] !== 'function') {
      let q = questions[qid];
      addQuestion(tableId, name, q[0], q[1]);
    }
  }
  
  let deleteTable = document.createElement('table');
  deleteTable.className = 'deleteTable';
  deleteTable.setAttribute('id', tableId+'dtable');
  careerDiv.appendChild(deleteTable);
  
  let controlsTable = document.createElement('table');
  controlsTable.className = 'controlsTable';
  careerDiv.appendChild(controlsTable);
  
  let controlsRow = controlsTable.insertRow();
  controlsRow.className = 'controlsRow';
  let addQuestionCell = controlsRow.insertCell(-1);
  addQuestionCell.className = 'questionAddCell';
  addQuestionCell.setAttribute('valign', 'top');
  let addQuestionButton = document.createElement('a');
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
let shiftPrefixQid = function(elem, prefix, cond, op){
  let tableElems = elem.querySelectorAll(
    '[id^="' + prefix + '"]');
  let nextQid = 0;
  for (let i = -1; i < tableElems.length; i++){
    let elemToUpdate = null;
    if (i == -1){
      elemToUpdate = elem;
    } else {
      elemToUpdate = tableElems[i];
    }
    let teRegex = new RegExp('^soc(..-....)p([-\\d]+)q([-\\d]+)(.*?)' + 
      '(file|label|button|text|delete)?$');
    let teMatches = elemToUpdate.id.match(teRegex);
    if (teMatches === null){
      continue;
    }
    let teQid = parseInt(teMatches[3]);
    if (cond(teQid)){
      nextQid = Math.max(nextQid-1, teQid)+1;
      let newQid = op(teQid).toString();
      teMatches[3] = newQid;
      for (let ii=0; ii < elemToUpdate.attributes.length; ii++){
        let attrRegex = new RegExp('(soc..-....p[-\\d]+q)([-\\d]+)(.*?' + 
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
let shiftPrefixPid = function(elem, prefix, cond, op){
  let tableElems = elem.querySelectorAll(
    '[id^="' + prefix + '"]');
  let nextPid = 0;
  for (let i = -1; i < tableElems.length; i++){
    let elemToUpdate = null;
    if (i == -1){
      elemToUpdate = elem;
    } else {
      elemToUpdate = tableElems[i];
    }
    let teRegex = new RegExp('^soc(..-....)p([-\\d]+)(.*)$');
    let teMatches = elemToUpdate.id.match(teRegex);
    if (teMatches === null){
      continue;
    }
    let tePid = parseInt(teMatches[2]);
    if (cond(tePid)){
      nextPid = Math.max(nextPid-1, tePid)+1;
      let newPid = op(tePid).toString();
      teMatches[2] = newPid;
      for (let ii=0; ii < elemToUpdate.attributes.length; ii++){
        let attrRegex = new RegExp('(soc..-....p)([-\\d]+)(.*)', 'g');
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

let jQueryDrag = function (event, ui){
  ui.item.data('rowId', ui.item[0].id);
}
let jQueryDrop = function (event, ui){
  // Assumes tbody automatically added as only element of table
  let table = $(this)[0];
  let rowId = ui.item.data('rowId');
  let remRow = document.getElementById(rowId);
  let dstRow = ui.item.prev()[0];
  
  let regex = new RegExp('^soc(..-....)p([-\\d]+)q([-\\d]+)(.*?)' + 
    '(file|label|button|text|delete)?$');
  let matches = rowId.match(regex);
  let remQid = parseInt(matches[3]);
  matches = dstRow.id.match(regex);
  let dstQid = parseInt(matches[3]);
  let prefix = 'soc' + matches[1] + 'p' + matches[2] + 'q';
  
  let moveForwards = (remQid < dstQid);
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