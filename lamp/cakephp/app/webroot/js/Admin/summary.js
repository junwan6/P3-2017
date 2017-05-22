let goToSOCVideos = function (url){
  let soc = document.getElementById('inputSOC').value;
  let socs = soc.match(/\d{2}-\d{4}/g);
  if (socs != null){
    window.open(url + '/' + socs.join('/'), '_blank');
  }
}