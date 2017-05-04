<?php
  if ($_GET['random']){  
    // TODO: pick from list of existing soc codes
    $soc = '';
    for ($i = 0; $i < 6; $i++){
      if ($i == 2){
        $soc .= '-';
      }
      if ($_GET['x'] && $i == 0){
        $soc .= $_GET['x'];
      } elseif ($_GET['y'] && $i == 1){
        $soc .= $_GET['y'];
      } else {
        $soc .= (string)rand(0,9);
      }
    }
    $_GET['soc'] = $soc;
    $_GET['focus'] = 'video';
    $_GET['random'] = null;
  }
echo '<!-- ';
foreach ($_GET as $g){
  echo $g . "\n";
}
echo ' -->';
  switch ($_GET['focus']){
  case "video":
    include 'video.php';
    break;
  case "salary":
    include 'salary.php';
    break;
  case "education":
    include 'education.php';
    break;
  case "skills":
    include 'skills.php';
    break;
  case "outlook":
    include 'outlook.php';
    break;
  case "world-of-work":
    include 'world-of-work.php';
    break;
  default:
    include 'video.php';
  }
?>
