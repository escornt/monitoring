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
    $handle = fopen($path, "r+");
    $line = fgets($handle, 255);
    fclose($handle);
    if (strcmp($line, "d\n") == 0) {
      fclose($handle);
      return;
    } else if (strcmp($line, "e\n") == 0) {
      fclose($handle);
      exec("vzctl stop ".$id);
      exec("date +%s > ".$path);
      return;
    } else if (strcmp($line, "x\n") == 0) {
      fclose($handle);
      exec("vzctl destroy ".$id);
      return;
    } else {
      $cttime = intval(strval($line));
      $curtime = time();
      exec("vzctl status ".$id, $output);
      $state = $output[0];
      $state = substr(substr($state,strrpos(trim($state),' ')), 1);
      $dif = $curtime - $cttime;
      if (strcmp($state, "running") == 0) {
        echo "RUNNING CONFIRMED";
        if ($dif > 1296000) {
          exec("echo e > ".$path);
        }
      } else if (strcmp($state, "down") == 0) {
        echo "DOWN CONFIRMED";
        if ($dif > 1296000 * 2) {
          exec("echo e > ".$path);
        }
      }
      echo "Contenue du fichier = ".$line."\n";
      echo "Ct ".$id."\n";
      echo "Ct time = ".$cttime."\n";
      echo "Current time = ".$curtime."\n";
      echo "State = ".$state."\n";
    }
    fclose($handle);
  }
}

?>
