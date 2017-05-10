#!/usr/bin/php
<?php

$filepath = $argv[1];
$file = fopen($filepath, 'r');
$jsonString = fread($file, filesize($filepath));
fclose($file);
$loginInfo = json_decode($jsonString,  true);

$options = ['username' => 'user',
  'password' => 'password',
  'host' => 'host',
  'port' => 'port'
];

$optionsString = '';
foreach ($loginInfo as $k => $v){
  if (is_string($v)){
    if ($k == 'database'){
      $optionsString = ' ' . $v . $optionsString;
    } elseif (in_array($k, array_keys($options))) {
      $optionsString .= ' --' . $options[$k] . '=' . $v;
    }
  }
}
echo 'mysql' . $optionsString;
