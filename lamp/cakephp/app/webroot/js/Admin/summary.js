// Does not use redirect, to allow ctrl-click to open in new tab
let updateSOCLink = function (url){
  let input = document.getElementById('inputSOC').value;
  let socs = input.match(/\d{2}-\d{4}/g);
  if (socs == null){
    document.getElementById('gotoButton').setAttribute('href', url);
  } else {
    document.getElementById('gotoButton').setAttribute('href',
      url + '/' + socs.join('/'));
  }
}

let addSOC = function(soc){
  let input = document.getElementById('inputSOC');
  let existing = input.value.match(/\d{2}-\d{4}/g);
  let uniqueSOC = {};
  // doing {soc: true} makes {"soc" : true}
  for (let i = 0; existing != null && i < existing.length; i++){
    uniqueSOC[existing[i]] = true;
  }
  if (soc in uniqueSOC){
    delete uniqueSOC[soc];
  } else {
    uniqueSOC[soc] = true;
  }
  input.value = Object.keys(uniqueSOC).join(', ');
  input.onchange();
}

let toUserPage = function(uid){
  document.getElementById('user' + uid).click();
}
