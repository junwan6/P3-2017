// TODO: Delete and redo this whole thing
//    I had no idea how javascript or jQuery worked when I started
// ideas: Instead of update depending on action taken, iterate through and update
//    use consistent element passing (id vs object vs parent class etc.)
//    avoid setAttribute+string in favor of existing methods
//    use HTML as a storage mechanism, row order etc. instead of storing in ids

// TODO: For current form, add rearrange for filename to be sent in POST

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
  var namechange = document.getElementById(id + 'fnamechange');
  namechange.value = extractFileName(fileName);
};

var addQuestion = function(caller, tableId, person,
  questionEntry='', fileName=''){
  var table = document.getElementById(tableId);
  
  var id = 1+shiftPrefixQid(table, tableId+'q',
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
  fileNameChange.setAttribute('id', nextElemId+'fnamechange');
  fileNameChange.setAttribute('name', nextElemId+'fnamechange');
  fileNameChange.setAttribute('value', fileName);
  fileNameChange.setAttribute('autocomplete', 'off');
  fileNameChange.style.display = 'none';
  file.appendChild(fileNameChange);

  var upload = row.insertCell(2);
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
  deleteBox.setAttribute('autocomplete', 'off');
  deleteCell.appendChild(deleteBox);
  
  var dragCell = row.insertCell(4);
  var dragIcon = document.createElement('i');
  dragIcon.classList.add('fa');
  dragIcon.classList.add('fa-arrows');
  dragIcon.setAttribute('aria-hidden', 'true');
  dragCell.appendChild(dragIcon);
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

  if (disable){
    row.classList.add('rowToDelete');
  } else {
    row.classList.remove('rowToDelete');
  }
    
  var regex = new RegExp('^soc(..-....)p(\\d+)q(\\d+)(.*?)' + 
    '(file|label|button|text|delete)?$');
  var matches = elemId.match(regex);
  var qId = parseInt(matches[3]);
  
  var prefix = 'soc' + matches[1] + 'p' + matches[2] + 'q';

  if (disable){
    var personForm = row.parentNode.parentNode.parentNode;
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
    var dtable = row.parentNode;
    var table = dtable.parentNode
      .getElementsByClassName('uploadTable')[0];
    var maxQid = shiftPrefixQid(table, prefix, function(q){
      return true;
    }, function(q){
      return q;
    });
    console.log(maxQid);
    shiftPrefixQid(dtable, prefix, function(teQid){
      return (teQid < qId);
    }, function(teQid){
      return teQid+1;
    });
// TODO: FIX THIS, delete 3, undelete middle, index off by 1
    shiftPrefixQid(row, prefix, function(teQid){
      return true;
    }, function(teQid){
      return maxQid+1;
    });
    // For some reason uploadTable has a tbody while deleteTable does not
    table.children[0].appendChild(row);
  }
};

// Drag and drop code taken from https://www.w3schools.com/html/html5_draganddrop.asp
// jQuery drag and drop code from https://stackoverflow.com/questions/3591264/can-table-rows-be-made-draggable
$(function(){
  $('.uploadTable').children('tbody').sortable({
    start: jQueryDrag,
    stop: jQueryDrop
  });
});

function jQueryDrag(event, ui){
  ui.item.data('rowId', ui.item[0].id);
}
function jQueryDragStop(event, ui){
  $(this).removeClass('draggedRow');
}
function jQueryDrop(event, ui){
  // Assumes tbody automatically added as only element of table
  var table = $(this)[0];
  var rowId = ui.item.data('rowId');
  var remRow = document.getElementById(rowId);
  var dstRow = ui.item.prev()[0];
  
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