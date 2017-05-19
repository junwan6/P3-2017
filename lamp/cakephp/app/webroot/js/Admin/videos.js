// TODO: Have javascript/jquery expert go over this
//   No idea on best practices, language-specific bugs, etc.
//   Params could be heavily simplified by caller.id instead of separate var
//   Or rewrite the whole thing, this is a mess
// - Andrew

// Taken from https://stackoverflow.com/questions/857618/javascript-how-to-extract-filename-from-a-file-input-control
var extractFileName = function(fullPath){
  var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
  var filename = fullPath.substring(startIndex);
  if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
      filename = filename.substring(1);
  }
  return filename;
}

var setUpload = function(id){
  var fileElement = document.getElementById(id + 'file');
  var fileName = fileElement.value;
  var label = document.getElementById(id + 'label');
  label.innerHTML = extractFileName(fileName);
  label.style.color = "Red";
};

var addQuestion = function(caller, tableId, id, person,
  questionEntry='', fileName=''){
  var table = document.getElementById(tableId);
  var nextElemId = tableId + "q" + id + person;

  var row = table.insertRow();
  row.className = 'questionRow';
  row.setAttribute('id', nextElemId);
  

  $(row).draggable({
    containment: 'parent',
    cursor: 'move',
    snap: true,
    
    helper: 'clone',
    start: jQueryDrag
  });
  $(row).droppable({
    over: jQueryOver,
    drop: jQueryDrop
  });

/*
          $(ui.draggable).remove();
          $(ui.helper).remove();
*/
  /*
  row.setAttribute('ondragover', 'allowDrop(event)');
  row.setAttribute('ondrop', 'drop(event)');
  
  row.setAttribute('draggable', 'true');
  row.setAttribute('ondragstart', 'drag(event)');
  */
  row.style.backgroundColor = 'rgb(240,240,240)';

  var question = row.insertCell(0);
  question.className = 'questionCell';
  var questionText = document.createElement('input');
  questionText.setAttribute('type', 'text');
  questionText.setAttribute('id', nextElemId+'text');
  questionText.setAttribute('class', 'questionText');
  questionText.setAttribute('value', questionEntry);
  questionText.setAttribute('name', nextElemId+'text');
  question.appendChild(questionText);
  
  var file = row.insertCell(1);
  file.className = 'videoCell';
  var fileInput = document.createElement('input');
  fileInput.setAttribute('type', 'file');
  fileInput.setAttribute('id', nextElemId+'file');
  // Probably a way to directly set a onchange function
  // Will need to have a constant argument (partial function?)
  fileInput.setAttribute('onchange',
    'setUpload(\'' + nextElemId + '\')');
  fileInput.setAttribute('name', nextElemId+'file');
  file.appendChild(fileInput);
  // If constructed by createElement, the label innerHtml is never set
  // TODO: Find out what's going on, low priority
  file.insertAdjacentHTML('beforeend',
    '<label id="' + nextElemId+'label' +
    '" for="' + nextElemId+'file' + '">' +
    fileName + '</label>');

  var upload = row.insertCell(2);
  upload.className = 'uploadCell';
  var uploadButton = document.createElement('input');
  uploadButton.setAttribute('type', 'button');
  uploadButton.setAttribute('id', nextElemId+'button');
  uploadButton.setAttribute('value', 'Browse...');
  uploadButton.setAttribute('onclick',
    'document.getElementById(\'' +
    nextElemId + 'file\').click();');
  upload.appendChild(uploadButton);
  
  var deleteCell = row.insertCell(3);
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
  deleteCell.appendChild(deleteBox);
  
  var dragCell = row.insertCell(4);
  var dragIcon = document.createElement('i');
  dragIcon.classList.add('fa');
  dragIcon.classList.add('fa-arrows');
  dragIcon.setAttribute('aria-hidden', 'true');
  dragCell.appendChild(dragIcon);

  if (caller != null){
    caller.setAttribute('onclick',
      'addQuestion(this, \'' + tableId + '\', ' +
      (parseInt(id)+1) + ', \'' + person + '\');');
  }
};

var makeElemId = function(vars){
  var eId='soc'+vars[1]+'p'+vars[2]+'q'+vars[3]+vars[4];
  if (typeof vars[5] !== "undefined"){
    eId += vars[5];
  }
  return eId;
}

var shiftPrefixQid = function(elem, prefix, cond, op){
  var tableElems = elem.querySelectorAll(
    '[id^="' + prefix + '"');
  var maxQid = 0;
  for (var i = -1; i < tableElems.length; i++){
    var elemToUpdate = null;
    if (i == -1){
      elemToUpdate = elem;
    } else {
      elemToUpdate = tableElems[i];
    }
    var teRegex = new RegExp('^soc(..-....)p(\\d+)q(\\d+)(.*?)' + 
      '(file|label|button|text|delete)?$');
    var teMatches = elemToUpdate.id.match(teRegex);
    if (teMatches === null){
      continue;
    }
    var teQid = parseInt(teMatches[3]);
    if (cond(teQid)){
      maxQid = Math.max(maxQid, teQid);
      var newQid = op(teQid).toString();
      teMatches[3] = newQid;
      for (var ii=0; ii < elemToUpdate.attributes.length; ii++){
        var attrRegex = new RegExp('(soc..-....p\\d+q)(\\d+)(.*?' + 
          '(?:file|label|button|text|delete|dicon)?)', 'g');
        elemToUpdate.attributes[ii].value = elemToUpdate.attributes[ii]
          .value.replace(attrRegex, "$1"+newQid+"$3");
      }
    }
  }
  return maxQid;
}

var markRowForDelete = function(caller, disable, elemId){
  document.getElementById(elemId+'text').disabled = disable;
  document.getElementById(elemId+'button').disabled = disable;
  caller.setAttribute('onclick', 'markRowForDelete(this, ' + !disable +
    ', \'' + elemId + '\');');
  var row = caller.parentNode.parentNode;
  row.style.backgroundColor = 
    (disable?'rgb(240,200,200)':'rgb(240,240,240)');
    
  // Weird behavior, tbody automatically created for uploadTable, not deleteTable
  // personForm still gets correct child, but goes one level higher
  var personForm = row.parentNode.parentNode.parentNode;
  if (disable){
    var regex = new RegExp('^soc(..-....)p(\\d+)q(\\d+)(.*?)' + 
      '(file|label|button|text|delete)?$');
    var matches = elemId.match(regex);
    var qId = parseInt(matches[3]);
    
    var prefix = 'soc' + matches[1] + 'p' + matches[2] + 'q';
    var maxQid = shiftPrefixQid(personForm, prefix,
      function(teQid){
        return teQid > qId;
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
    var maxQid = shiftPrefixQid(personForm, prefix, function(q){
      return true;
    }, function(q){
      return q;
    });
    shiftPrefixQid(row, prefix, function(teQid){
      return true;
    }, function(teQid){
      return maxQid;
    });
    // For some reason uploadTable has a tbody while deleteTable does not
    personForm.getElementsByClassName('uploadTable')
      [0].children[0].appendChild(row);
  }
};

// Drag and drop code taken from https://www.w3schools.com/html/html5_draganddrop.asp
// jQuery drag and drop code from https://stackoverflow.com/questions/3591264/can-table-rows-be-made-draggable

function jQueryDrag(event, ui){
  $(this).data('tableId', $(this).closest('table')[0].id);
  $(this).data('rowId', $(this)[0].id);
  $(this).addClass('draggedRow');
}
function jQueryOver(event, ui){
  // TODO: do a makeshift Sortable by swapping row locations,
  console.log(ui);
}
function jQueryDrop(event, ui){
  var remTableId = ui.draggable.data('tableId');
  var dstTableId = $(this).closest('table')[0].id;
  if (remTableId == dstTableId){
    // Assumes tbody automatically added as only element of table
    var table = document.getElementById(remTableId).children[0];
    var rowId = ui.draggable.data('rowId');
    var remRow = document.getElementById(rowId);
    var dstRow = $(this).closest('tr')[0];
    
    var regex = new RegExp('^soc(..-....)p(\\d+)q(\\d+)(.*?)' + 
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

  $(ui.draggable).removeClass('draggedRow');
  $(ui.helper).remove();
}

/* HTML 5 drag and drop implementation, abandoned due to inability to set opacity
// Ported near-identically to jQueryUI above

// Replaced with jQuery closest()
function getClosestParent(elem, type){
  while (elem = elem.parentNode){
    if (elem.tagName.toLowerCase() === type) {
      return elem;
    }
  }
}

function allowDrop(ev){
  ev.preventDefault();
}
function drag(ev) {
  ev.dataTransfer.setData('tableId', getClosestParent(ev.target, 'table').id);
  ev.dataTransfer.setData('rowId', ev.target.id);
}
function drop(ev) {
  ev.preventDefault();
  var remTableId = ev.dataTransfer.getData('tableId');
  var dstTableId = getClosestParent(ev.target, 'table').id;
  if (remTableId == dstTableId){
    // Assumes tbody automatically added as only element of table
    var table = document.getElementById(remTableId).children[0];
    var rowId = ev.dataTransfer.getData('rowId');
    var remRow = document.getElementById(rowId);
    var dstRow = getClosestParent(ev.target, 'tr');
    
    var regex = new RegExp('^soc(..-....)p(\\d+)q(\\d+)(.*?)' + 
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
}
*/