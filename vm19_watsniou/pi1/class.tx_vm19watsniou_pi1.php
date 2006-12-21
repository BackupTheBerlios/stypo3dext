<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003 Vincent (admin) (vmaury@localhost)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
 * Plugin 'Whats new' for the 'vm19_watsniou' extension.
 *
 * @author  Vincent (admin) <vmaury@localhost>
 */


require_once(PATH_tslib."class.tslib_pibase.php");
require_once("typo3conf/ext/vm19_toolbox/functions.php");

class tx_vm19watsniou_pi1 extends tslib_pibase {
  var $prefixId = "tx_vm19watsniou_pi1";    // Same as class name
  var $scriptRelPath = "pi1/class.tx_vm19watsniou_pi1.php";  // Path to this script relative to the extension dir.
  var $imgPath="typo3conf/ext/vm19_watsniou/";
  var $extKey = "vm19_watsniou";  // The extension key.
  // maintenant c'est pass�en TS Setup avec NiousNumber, d�aut dans ext_typoscript_setup
  //var $NiousNb=20; // nbre de news affich�s
  

  

  function main($content,$conf)  {

    $this->conf=$conf;


    //debug ($conf);
    $this->pi_loadLL();

    //debug($this->piVars);
	$WhereSTH=" WHERE (starttime=0 OR starttime <".time().") AND (endtime=0 OR endtime>".time().") AND deleted=0 AND hidden=0";
    $req="SELECT * from tx_vm19watsniou".$WhereSTH." order by tstamp DESC LIMIT ".$this->conf['NiousNumber'];
    $rep = mysql(TYPO3_db,$req);
    //$content='<H2>'.$this->pi_getLL("WhatsNew").'</H2>'; // le titre est affich�au niveau du plugin
    $content.='<table border="0">';
    while ($rw=mysql_fetch_array($rep)) {
	if ($this->pi_getPageLink($rw[pid] || $rw[typcontent]=="vm19_hnlinks")) { // gestion des droits  ... simple non ?
		// les images sont nomm�s comme les types de contenus  
		$content.='<tr><td><img src="'.$this->imgPath.$rw[typcontent].'.gif"></td><td>';
		// debug $content.= "| getPageLink". $this->pi_getPageLink($rw[pid])."|";
		if ($rw[typcontent]=="vm19_hnlinks") {
			$tabisa=explode("|",$rw[title]);
			$href=strstr($tabisa[1],"http://") ? $tabisa[1] : "http://".$tabisa[1];
			$title=$tabisa[0];
			$content.='<H3><a href="'.$href.'" target="_blank">'.$title.'</a></H3>';
		}
		else $content.='<H3>'.$this->pi_linkToPage($rw[title],$rw[pid]).'</H3>'; // ne fonctionne pas quand l'id ne correspond pas au titre
		//	  $content.='<H2>'.VmlinkToPage($rw[title],$rw[pid]).'</H2>';
			$content.='<DIV style="margin:2px">';
		$content.="<b>".$this->pi_getLL("tc_".$rw[typcontent])."</b>";
		if ($rw[tstamp]<=$rw[crdate]) {$content.=$this->pi_getLL("crdate");} else $content.=$this->pi_getLL("modifdate");
			$femtctrad=$this->pi_getLL("tcf_".$rw[typcontent]); // pour g�er le f�inin de cr�(e) ou modifi�e)
			$content.=$femtctrad.$this->pi_getLL("on");
		$content.=getDateF($rw[tstamp]);
		//      $tabidarbo=unserialize($rw[tabidarbo]);
		$content.="<br/>".$this->pi_getLL("path");
		$tabstrarbo=unserialize(stripslashes($rw[tabstrarbo]));
		foreach ($tabstrarbo as $key=>$value) {
			$content.=$this->pi_linkToPage($value,substr($key,2))." > ";
		}
		$content=vdc($content,3); // enl�e le dernier " > "
		$content.='</DIV></td></tr>';
	} // fin si droit OK
    } // fin boucle sur nouveaut�
    $content.="</table>";


    /*$content='
      <strong>This is a few paragraphs:</strong><BR>
      <P>This is line 1</P>
      <P>This is line 2</P>

      <h3>This is a form:</h3>
      <form action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'" method="POST">
        <input type="hidden" name="no_cache" value="1">
        <input type="text" name="'.$this->prefixId.'[input_field]" value="'.htmlspecialchars($this->piVars["input_field"]).'">
        <input type="submit" name="'.$this->prefixId.'[submit_button]" value="'.htmlspecialchars($this->pi_getLL("submit_button_label")).'">
      </form>
      <BR>
      <P>You can click here to '.$this->pi_linkToPage("get to this page again",$GLOBALS["TSFE"]->id).'</P>
    ';
    */
    return $this->pi_wrapInBaseClass($content);
  }
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/vm19_watsniou/pi1/class.tx_vm19watsniou_pi1.php"])  {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/vm19_watsniou/pi1/class.tx_vm19watsniou_pi1.php"]);
}

function VmlinkToPage($title,$pid) {
  return '<A HREF="?id='.$pid.'">'.$title.'</A>';
}
?>
