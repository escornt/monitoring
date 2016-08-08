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
    fclose($handle);
    if (strcmp($line, "d") == 0) {
      return;
    } else if (strcmp($line, "e\n") == 0) {
      echo "conparaison avec E";
      exec("vzctl stop ".$id);
      return;
    } else if (strcmp($line, "x") == 0) {
      exec("vzctl destroy ".$id);
      return;
    } else {
      $cttime = intval(strval($line));
      $curtime = time();
      exec("vzctl status ".$id, $output);
      $state = $output[0];
      $state = substr(substr($state,strrpos(trim($state),' ')), 1);
      echo "Contenue du fichier = ".$line."\n";
      echo "Ct ".$id."\n";
      echo "Ct time = ".$cttime."\n";
      echo "Current time = ".$curtime."\n";
      echo "State = ".$state."\n";
    }
  }
}

?>
