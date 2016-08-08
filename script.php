<?php

$path = "/mnt/pve/NFS/private";

if ($dir = opendir($path))
{
  while (($file = readdir($dir)) != false)
  {
    echo "Fichier :".$file."\n";
  }
  closedir($dir);
}

?>
