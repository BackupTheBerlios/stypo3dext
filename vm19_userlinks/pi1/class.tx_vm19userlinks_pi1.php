<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2004 Administrateur TYPO3.7.1 (webtech@haras-nationaux.fr)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is 
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
* 
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
* 
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/** 
 * Plugin 'User links edition' for the 'vm19_userlinks' extension.
 *
 * @author      Administrateur TYPO3.7.1 <webtech@haras-nationaux.fr>
 */


require_once(PATH_tslib."class.tslib_pibase.php");
//require_once("typo3conf/ext/vm19_toolbox/functions.php"); // ma boite à outil

class tx_vm19userlinks_pi1 extends tslib_pibase {
        var $prefixId = "tx_vm19userlinks_pi1";         // Same as class name
        var $pics_path="uploads/pics/";
	//var $linkscatndp='1,4'; // id categorie(s) des liens (a separer par des virgules) 
	//A AFFICHER TT LE TEMPS ET A NE PAS AFFICHER à l'utilisateur
	// maintenant passé par une var TS
	// a passer dans un gabarit
	//plugin.tx_vm19userlinks_pi1.fixedLinksCats=1,4
				
        var $pidulinks=0; // pid du dossier virtuel de stockage des liens
        var $scriptRelPath = "pi1/class.tx_vm19userlinks_pi1.php";      // Path to this script relative to the extension dir.
        var $extKey = "vm19_userlinks"; // The extension key.
        
        /**
         * FE Display
         */
        function main($content,$conf)   {
                $this->conf=$conf;
                $this->pi_setPiVarDefaults();
                $this->pi_loadLL();
                $this->pi_USER_INT_obj=1;       // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!

		//debug ($this->conf);
		                        
                $this->pidulinks=$this->cObj->data["pages"]; // le dossier système ou sont stockés les enregistrements
                $uid_util=$GLOBALS["TSFE"]->fe_user->user["uid"];
                if ($uid_util) {// si l'utilisateur est connecté
                        $tb_links=RecupLib("tx_vm19userlinks_ulinks","user_id","link_ids",$uid_util," AND deleted!=1");
                        if($this->piVars["DispCP"]) { // si choix préférénces demandées
                                $content.=$this->DispCP($tb_links);
                        } elseif ($this->piVars["ValidCP"]) { // on vient de la validation des préférences
                                //debug ($this->piVars["u_links"]);
                                $links_ids=(is_array($this->piVars["u_links"]) ? implode(",",$this->piVars["u_links"]) : "");
                                if (RecupLib("tx_vm19userlinks_ulinks","user_id","crdate",$uid_util," AND deleted!=1")) {
                                        $query="UPDATE tx_vm19userlinks_ulinks SET 
                                                link_ids='$links_ids', tstamp=".time()." WHERE user_id=$uid_util";
                                } else {
                                        $query="INSERT INTO tx_vm19userlinks_ulinks 
                                                (pid, tstamp,crdate,cruser_id,user_id,link_ids) VALUES
                                                (".$this->pidulinks.",".time().",".time().",".$uid_util.",".$uid_util.",'".$links_ids."')";
                                }
                                $res=mysql(TYPO3_db,$query) or die("erreur : ".$query);
                                $content.=$this->pi_getLL("reg_prefs","préférences enregistrées");
                                
                                // affichage
                                $tb_links=RecupLib("tx_vm19userlinks_ulinks","user_id","link_ids",$uid_util," AND deleted!=1");
                                $content.=$this->DispAL($tb_links);
                                $content.= '<FORM action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'" name="'.$this->prefixId.'fname" method="POST">
                                <INPUT type="hidden" name="'.$this->prefixId.'[DispCP]" value="OK">';
                                $content.='<INPUT '.$this->pi_classParam("submit-button").' type="submit" name="'.$this->prefixId.'[submit_button]" value="'.$this->pi_getLL("editprefs","Edit preferences").'"></form>';
                                //$content.='<a href="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'&'.$this->prefixId.'[edit_pref]=true">ici</a>';                            
                        } else { // affichage simple sans édition
                                if ($tb_links) { // préférences existent
                                        $content.=$this->pi_getLL("your_prefs","Vos liens actifs :");
                                        $content.=$this->DispAL($tb_links);
                                } else {
                                        $content.=$this->pi_getLL("no_prefs","Aucun lien actif.");
                                }
                                $content.="<br/>".$this->pi_getLL("clickhtap","Cliquez ci dessous pour choisir vos liens")."<br/>";
                                
                                // pour accéder à l'édition des préfére,ces
                                $content.= '<FORM action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'" name="'.$this->prefixId.'fname" method="POST">
                                <INPUT type="hidden" name="'.$this->prefixId.'[DispCP]" value="OK">';
                                $content.='<INPUT '.$this->pi_classParam("submit-button").' type="submit" name="'.$this->prefixId.'[submit_button]" value="'.$this->pi_getLL("editprefs","Editer les liens utilisateur").'"></form>';
                                // marche aussi en plus simple mais flemme de faire un style css qui ressemble à un bouton
                                //$content.='<a href="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'&'.$this->prefixId.'[edit_pref]=true">ici</a>';
                        } 
                } else {
                        $content.=$this->pi_getLL("must_ident","must_ident");

                }
                
                /* exemple fourni par le wizard
                $content.='
        
                        <h3>This is a form:</h3>
                        <form action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'" method="POST">
                                <input type="hidden" name="no_cache" value="1">
                                <input type="text" name="'.$this->prefixId.'[input_field]" value="'.htmlspecialchars($this->piVars["input_field"]).'">
                                <input type="submit" name="'.$this->prefixId.'[submit_button]" value="'.htmlspecialchars($this->pi_getLL("submit_button_label")).'">
                        </form>
                        <BR>
                        <p>You can click here to '.$this->pi_linkToPage("get to this page again",$GLOBALS["TSFE"]->id).'</p>
                '; */
        
                return $this->pi_wrapInBaseClass($content);
        }
        
        // fonction qui affiche simplement les liens actifs
        function DispAL($tb_links) {
                if ($tb_links=="") {
                        $whtbl="0";
                } else $whtbl="uid IN (".$tb_links.")";
                $query = "SELECT * from tt_links where $whtbl AND deleted!=1 AND hidden!=1 AND pid=".$this->pidulinks." order by category";
                $res = mysql(TYPO3_db,$query) or die("req invalide : $query");
                if (mysql_num_rows($res)>0) {
                        $ret.='<table border="0">';
                        while ($row = mysql_fetch_array($res)) {
                                $ret.='<tr><td align="center">';        
                                $ret.='<a href="'.$row['url'].'" target="_blank" title="'.$row['note'].'">';
                                $ret.=$this->srcimg($row['image'],$row['uid'],$row['title'])."</a></td>";
                                $ret.='<td><a href="'.$row['url'].'" target="'.(strstr($row['url'],"http://") ? "_blank" : "_self").'" title="'.$row['note'].'">'.$row['title'].'</a></td>';
                                $ret.='</tr>';  
                        }
                        $ret.='</table>';
                } else { //pas de liens actifs
                        $ret.="<br/>".$this->pi_getLL("no_act_links","Aucun lien spécifique actif")."<br/>";
                }
                return($ret);
        }
        
        // fonction qui affiche un formulaire permettant d'editer les liens actifs
        function DispCP($tb_links) {
		$sqlIN=$this->conf['fixedLinksCats'] ? "category NOT IN (".$this->conf['fixedLinksCats'].")" : "1";
                $query = "SELECT * from tt_links where $sqlIN AND deleted!=1 AND hidden!=1 AND pid=".$this->pidulinks." order by category";
                $res = mysql(TYPO3_db,$query);
                if (mysql_num_rows($res)>0) {
                        $ret.= '<FORM action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'" name="'.$this->prefixId.'fname" method="POST">
                                <INPUT type="hidden" name="'.$this->prefixId.'[ValidCP]" value="OK">';
                        $ret.='<table border="0">';
                        $vtb_links=array();
                        if ($tb_links) $vtb_links=explode(",",$tb_links);
                        while ($row = mysql_fetch_array($res)) {
				$ncat=RecupLib("tt_links_cat","uid","title",$row['category']);
				if ($catc!=$ncat) {
					$catc=$ncat;
					$ret.='<tr><td colspan="2"><b>'.$catc.'</b></td></tr>'; 
				}
                                $ret.='<tr><td>';       
                                $ret.='<input type="checkbox" name="'.$this->prefixId.'[u_links][]" value="'.$row[uid].'" '.(in_array($row[uid],$vtb_links) ? "checked" : "").'>';
                                $ret.='</td><td align="center">';       
                                $ret.='<a href="'.$row['url'].'" target="_blank" title="'.$row['title']." ".$row['note'].'">';
                                $ret.=$this->srcimg($row['image'],$row['uid'],$row['title'])."</a></td>";
                                $ret.='<td><a href="'.$row['url'].'" target="'.(strstr($row['url'],"http://") ? "_blank" : "_self").'" title="'.$row['note'].'">'.$row['title'].'</a></td>';
                                $ret.='</tr>';  
                        }
                        $ret.='</table>';
                        $ret.='<INPUT '.$this->pi_classParam("submit-button").' type="submit" name="'.$this->prefixId.'[submit_button]" value="'.$this->pi_getLL("validprefs","Valid preferences").'"></form>';
                } else { //pas de liens actifs
                        $ret.="<br/>".$this->pi_getLL("no_act_links","Aucun lien spécifique disponible")."<br/>";
                }
                return($ret);
        }
        
        function srcimg($fimg,$uid,$title="->click-<") {
        if (!$fimg) {
                return($title);
        } elseif( strstr($fimg,",")) { // il y a au moins 2 images
                $tbimgs=explode(",",$fimg);
                return('<img border="0" src="'.$this->pics_path.$tbimgs[0].'" title="'.$title.'" name="img'.$uid.'" 
                onmouseover="img'.$uid.'.src=\''.$this->pics_path.$tbimgs[1].'\';"
                onmouseout="img'.$uid.'.src=\''.$this->pics_path.$tbimgs[0].'\';"
                onload="imago'.$uid.' = new Image;imago'.$uid.'.src=\''.$this->pics_path.$tbimgs[1].'\';"
                 >');
                
        } else { // une seule image
                return('<img border="0" src="'.$this->pics_path.$fimg.'" title="'.$title.'">');
        }
  }

        
}               


if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/vm19_userlinks/pi1/class.tx_vm19userlinks_pi1.php"])   {
        include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/vm19_userlinks/pi1/class.tx_vm19userlinks_pi1.php"]);
}

// fonction utilitaire
function RecupLib($LtableN,$LfieldK,$LfieldN,$key,$wheresup="") {
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
  // vire la derniere virgule à la fin
  if ($valret!=false) $valret=substr($valret,0,strlen($valret)-1);
  return($valret);
  }

?>
