let goToSOCVideos = function (url){
  let soc = document.getElementById('inputSOC').value;
  let socs = soc.match(/\d{2}-\d{4}/g);
  if (socs != null){
    window.open(url + '/' + socs.join('/'), '_blank');
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
}