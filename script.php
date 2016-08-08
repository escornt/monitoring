<?php

$path = "/mnt/pve/NFS/private";

if ($dir = opendir($path))
{
  while (($file = readdir($dir)) != false)
  {
    if ($file != "." && $file != "..") {
      handle_file($path."/".$file."/cttime", $file);
    }
  }
  closedir($dir);
}

function handle_file($path, $id) {
  if (file_exists($path)) {
    $handle = fopen($path, "r");
    $line = fgets($handle, 255);
    //echo "*".$line."\n";
    if ($line == "d") {
      return;
    } else if ($line == "e") {
      exec("vzctl stop ".$id);
    } else if ($line == "x") {
      exec("vzctl destroy ".$id);
    } else {
      $cttime = intval(strval($line));
      $curtime = time();
      exec("vzctl status ".$id, $output);
      $state = $output[0];
      $state = substr(substr($state,strrpos(trim($state),' ')), 1);
      /*echo "Ct ".$id."\n";
      echo "Ct time = ".$cttime."\n";
      echo "Current time = ".$curtime."\n";
      echo "State = ".$state."\n";*/
    }
    fclose($handle);
  }
}

?>
