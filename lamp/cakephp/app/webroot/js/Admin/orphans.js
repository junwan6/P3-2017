let markFileForDelete = function(caller){
  let chkbox = caller.querySelector('input');
  let icon = caller.querySelector('i');
  if (icon.classList.contains('fa-trash-o')){
    caller.classList.add("deleted");
    icon.classList.remove("fa-trash-o");
    icon.classList.add("fa-trash");
    chkbox.checked = true;
  } else {
    caller.classList.remove("deleted");
    icon.classList.add("fa-trash-o");
    icon.classList.remove("fa-trash");
    chkbox.checked = false;
  }
};

let markConflictForDelete = function(caller){
  let conflictBox = caller.parentNode;
  let chkbox = caller.querySelector('input');
  let icon = caller.querySelector('i');
  if (icon.classList.contains('fa-trash-o')){
    conflictBox.classList.add("deleted");
    icon.classList.remove("fa-trash-o");
    icon.classList.add("fa-trash");
    chkbox.checked = true;
  } else {
    conflictBox.classList.remove("deleted");
    icon.classList.add("fa-trash-o");
    icon.classList.remove("fa-trash");
    chkbox.checked = false;
  }
}

let markFolderForDelete = function(caller){
  let folderBox = caller.parentNode.parentNode;
  let chkbox = caller.parentNode.querySelector('input');
  let icon = caller;
  if (icon.classList.contains('fa-trash-o')){
    folderBox.classList.add("deleted");
    icon.classList.remove("fa-trash-o");
    icon.classList.add("fa-trash");
    chkbox.checked = true;
  } else {
    folderBox.classList.remove("deleted");
    icon.classList.add("fa-trash-o");
    icon.classList.remove("fa-trash");
    chkbox.checked = false;
  }
}