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
  $EXT_TIME = 1296000;
  $DEST_TIME = $EXT_TIME * 2;
  // test si le fichier existe
  if (file_exists($path)) {
    $handle = fopen($path, "r+");
    $line = fgets($handle, 255);
    // d => disabled, ignore la ct
    if (strcmp($line, "d\n") == 0) {
      fclose($handle);
      return;
    // e => sera eteinte au porchain passage
    } else if (strcmp($line, "e\n") == 0) {
      fclose($handle);
      exec("vzctl stop ".$id);
      exec("date +%s > ".$path);
      return;
    // x => sera detruite au prochain passage
    } else if (strcmp($line, "x\n") == 0) {
      fclose($handle);
      exec("vzctl destroy ".$id);
      return;
    // Si pas de commande, comparaisons des timestamps
    } else {
      $cttime = intval(strval($line));
      $curtime = time();
      exec("vzctl status ".$id, $output);
      $state = $output[0];
      $state = substr(substr($state,strrpos(trim($state),' ')), 1);
      $dif = $curtime - $cttime;
      if (strcmp($state, "running") == 0) {
        if ($dif > $EXT_TIME) {
          exec("echo e > ".$path);
        }
      } else if (strcmp($state, "down") == 0) {
        if ($dif > $DEST_TIME) {
          sendmail($id);
          exec("echo x > ".$path);
        }
      }

      /* AFFICHAGE DES VARIABLES*/
      /*
      echo "Contenue du fichier = ".$line;
      echo "Ct ".$id."\n";
      echo "Ct time = ".$cttime."\n";
      echo "Current time = ".$curtime."\n";
      echo "State = ".$state."\n\n";
      */
    }
    fclose($handle);
  }
}

function sendmail($id) {
  $destination = "test@1001pneus.fr";
  $sujet = "Warning, la CT ".$id." sera détruite dans 24h";

	$passage_ligne = "\r\n";

  $header = "From: \"ct-noreply\"<ct-noreply@1001pneus.fr>".$passage_ligne;
  $header .= "Reply-to: \"ct-noreply\" <ct-noreply@1001pneus.fr>".$passage_ligne;
  $header .= "Content-Type: text/plain; charset=\"utf-8\"".$passage_ligne;
  $header .= "MIME-Version: 1.0".$passage_ligne;
  $header .= "Delivered-to: ".$destination.$passage_ligne;

  $message = "La ct ".$id." est inactive et éteinte depuis trop longtemps.".$passage_ligne;
  $message .= "Pour empécher cela allumez la ct et connectez vous dessus puis tapez \"log\".".$passage_ligne;
  $message .= "Afin de ne pas surcharger le serveur, les ct restant inactives trop longtemps seront supprimées.".$passage_ligne;

  mail($destination,$sujet,$message,$header);
}

?>
