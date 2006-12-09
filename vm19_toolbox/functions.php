<?php
require_once("fonctions.php");
/*!!!!!!!!!!!!!!!!!!!!
 FONCTIONS UTILITAIRES
!!!!!!!!!!!!!!!!!!!! */

// récupération de libellé dans des tables liées
// on ne gère les clés multiples que lorsqu'elles sont en statiques
// renvoie faux si pas trouv�
// EN TRAVAUX

$GbVf['ChemImg']="fileadmin/templates/images/elts_reccurents/";

function txMefMail($admail) {
global $GbVf;
return $admail ? '<a href="mailto:'.$admail.'"><img src="'.$GbVf['ChemImg'].'mailto.gif" class="picto"></a>' : '';
}
// affichage d'une ligne de s�aration consitu� de pointill�..
function DispHRs($width="25") {
global $GbVf;
//  return '<table align ="center" width="'.$width.'%" cellpadding="0" cellspacing="10"><tr><td background="'.$GbVf['ChemImg'].'f_hor_gris.gif" height="3"><img src="'.$GbVf['ChemImg'].'calage.gif"></td></tr></table>';   
  return('<HR align="center" width="'.$width.'%" />');

}

function txRecupLib($LtableN,$LfieldK,$LfieldN,$key,$wheresup="") {
  $valret=false;
  $tkey=explode(",",$key);
  foreach ($tkey as $key) {
    $query="SELECT $LfieldN from $LtableN WHERE $LfieldK='$key' $wheresup";
    $res = mysql(TYPO3_db,$query);
    if (mysql_error())  debug(array(mysql_error(),$query));
    if (mysql_num_rows($res)>0) {
      $tbvr=mysql_fetch_row($res);
      $valret=$tbvr[0].',';
      }
    }
  // vire la derniere virgule �la fin
  if ($valret!=false) $valret=substr($valret,0,strlen($valret)-1);
  return($valret);
  }
// fonctions utiltaires
// Display File Size In List

  function DFSIL ($file) {
    if (file_exists($file)) {
      return ' <small>('.getFilesSize($file).')</small>';
    }
  }


   /* Returns the filesize or false
   */
    function getFilesSize($file) {
        if (!file_exists($file)) { return false; }
        if (!is_file($file)) { return false; }
        $file_size = filesize($file);
        if($file_size >= 1073741824) {
            $file_size = round($file_size / 1073741824 * 100) / 100 . "g";
        } elseif($file_size >= 1048576) {
            $file_size = round($file_size / 1048576 * 100) / 100 . "m";
        } elseif($file_size >= 1024) {
            $file_size = round($file_size / 1024 * 100) / 100 . "k";
        } else {
            $file_size = $file_size . "b";
        }
        return $file_size;

    }

  function getDateF($tstamp) {
    return(date("d/m/Y",$tstamp));
  }

    /**
   * Returns download link
   */
    function getDownloadLink($file_uid) {
//    Désactivation du non-cache (activation)
        return $this->getLinkUrl()."&no_cache=1&file=".$file_uid."&uid=".$this->uid;
//        return $this->getLinkUrl()."&no_cache=0&file=".$file_uid."&uid=".$this->uid;
    }


    /**
   * Returns a url for use in forms and links
   */
  function getLinkUrl($id="",$excludeList="")  {
    $queryString=array();
    $queryString["id"] = "id=".($id ? $id : $GLOBALS["TSFE"]->id);
    $queryString["type"]= $GLOBALS["TSFE"]->type ? 'type='.$GLOBALS["TSFE"]->type : "";
        reset($queryString);
    while(list($key,$val)=each($queryString))  {
      if (!$val || ($excludeList && t3lib_div::inList($excludeList,$key)))  {
        unset($queryString[$key]);
      }
    }
    return $GLOBALS["TSFE"]->absRefPrefix.'index.php?'.implode($queryString,"&");
  }


    /**
   * Returns the requested file
   !! à modifier !!
   */

    function getDownloadFile($uid) {
        $basePath = t3lib_extMgm::siteRelPath($this->extKey).dirname($this->scriptRelPath)."/mimetypes.php";
        if (@is_file($basePath)) { include("./".$basePath); }
        $res = $this->getFilesResult("",$uid);
        $file = mysql_fetch_assoc($res);
        if ((mysql_num_rows($res) != 1) || (!is_file($this->params["path"]."/".$file["file"]))) {
            echo "<B>".$this->pi_getLL("file_not_found")."</B>";
            exit;
        }
        $fI=t3lib_div::split_fileref($file["document"]);
        $sendmime = $mimetypes[$fI["fileext"]];
        if ($sendmime == "") { $sendmime = "application/stream"; }
        header ("HTTP/1.1 200 OK");
        header("Content-type: ".$sendmime);
        header("content-length: ".filesize($this->params["path"]."/".$file["file"]));
        header("Content-Disposition: attachment; filename=\"".$file["file"]."\"");
        $fp=fopen($this->params["path"]."/".$file["file"], "r");
        fpassthru($fp);
        flush();
        exit;
    }

// fonction qui coupe une chaine �la longueur d�ir�, sans couper les mots
function t3tronqstrww ($strac,$long=50,$strsuite=" [...]") {
         if (strlen($strac)<=$long) return $strac;
         return strtok(wordwrap($strac,$long,"\n"),"\n").$strsuite;
}

/*Fonctions deja déclarées dans fonctions.php qui est désormais inclus
// fonction qui vire les x derniers car d'une chaine
function vdc($strap,$nbcar) {
return (substr($strap,0,strlen($strap)-$nbcar));
}

*/
?>
