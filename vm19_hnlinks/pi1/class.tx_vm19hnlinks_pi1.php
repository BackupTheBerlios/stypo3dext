<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004 Vincent (admin, celui �la pioche) (webtech@haras-nationaux.fr)
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
 * Plugin 'hn_links' for the 'vm19_hnlinks' extension.
 *
 * @author	Vincent (admin, celui �la pioche) <webtech@haras-nationaux.fr>
 */


require_once(PATH_tslib."class.tslib_pibase.php");
require_once("dl3tree.inc"); // fichier partagé contenant l'objet permettant de générer des arboresences
require_once("ajaxtools.inc"); // fichier partagé contenant les fonctions (JS notamment) permettant l'utilisation d'Ajax

class tx_vm19hnlinks_pi1 extends tslib_pibase {
	var $prefixId = "tx_vm19hnlinks_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_vm19hnlinks_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "vm19_hnlinks";	// The extension key.
	var $ChemFromRoot="typo3conf/ext/vm19_hnlinks/pi1/";
	var $ChemImg="typo3conf/ext/vm19_hnlinks/pi1/";
	var $DL3tree; // objet Arbre
	var $str3tree; // chaine contenant le code HTML de l'arbre
	
	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		//debug($conf);
		//debug($GLOBALS["TSFE"]);
		
		//debug ($this->cObj->data);

		$this->DL3tree=new dltreeObj;
		$this->DL3tree->imgfopen=$this->ChemImg.'folderOpen.gif'; // nom des fichiers images symoles
		$this->DL3tree->imgfcloseplus =$this->ChemImg.'folderClosedplus.gif';
		$this->DL3tree->imgfclose =$this->ChemImg.'folderClosed.gif';

		//$GLOBALS["TSFE"]->setJS("toto","alert('coucoui')");
		// d?claration des variables JS
		$GLOBALS["TSFE"]->setJS("DL3THNL_1",$this->DL3tree->echDL3TJSVarsInit(false));
		// d?claration des fonctions JS pour l'arbre DL3Tree
		$GLOBALS["TSFE"]->setJS("DL3THNL_2",$this->DL3tree->echDL3TJSFunctions(false));
		// d?claration des fonctions JS pour l'utilisation d'ajax
		$GLOBALS["TSFE"]->setJS("DL3AjxHNL_3",echAjaxJSFunctions(false));
		$GLOBALS["TSFE"]->setJS("DL3AjxHNL_4","var JSChemFromRoot='".$this->ChemFromRoot."ajaxdisplinks.php?pid=';\n");

		// d?claration des styles CSS
		$GLOBALS["TSFE"]->setCSS("DL3THNL_1",$this->DL3tree->echDL3TStyles(false));


		/* la connection est d?j? active
		$mysql_host = "localhost";
		$mysql_user = "root";
		$mysql_pasw = "dlcube19230";
		$mysql_db   = "haras_hnrf";
		
		$link = mysql_connect($mysql_host, $mysql_user, $mysql_pasw);
		
		$db = mysql_select_db ($mysql_db);
		*/
		//$this->cObj->data["pages"] = id du dossier de d?marrage stock? dans l'enregistrement tt_content du plugin 
		$this->getLifromDB($this->cObj->data["pages"]); // calcule l'arbre (MAJ de la propri?t? this->str3tree, et MAL de la propri?t? de l'objet DLTREE->tbChilds)
		// echoise le tableau JS contenant les enfants (tbChilds)
		$GLOBALS["TSFE"]->setJS("DL3THNL_3",$this->DL3tree->echDL3TJStbChilds(false));
		
		$str2ret.='<div id="hnlinks">
		<H2>Base de donn&eacute;es des liens</H2>
		<table><tr><td valign="top">
		<div id="reg3tree">'.
		$this->str3tree.
		'</div></td><td>
		<div id="ajaxdynlinks">Cliquez dans l\'arbre ci-contre pour afficher les liens d\'une rubrique</div>
		</td></tr></table>
		</div>';
		//$str2ret.="<br/>select_key=".$this->cObj->data["select_key"];
		return($str2ret);
	} // fin m?thode main

	// m�hode r�ntrante qui r�up�e les lignes de l'arbre	
	function getLifromDB($parent_id,$clevel=0){
		//global $tbChilds; 
		//global $str3tree;
		//global $DLtree;
		
		$clevel++;
		$sql = "Select uid,pid,title from pages where pid=$parent_id and deleted=0 and hidden=0";
		$res = msq ($sql);
		if($res){
			while($row=mysql_fetch_array($res)){
				$id=$row['uid'];
				$title=str_replace('"',"`",str_replace("'","`",$row['title']));
				//$tbChilds[$parent_id][]=$id;
				// sert uniquement à savoir si le noeud a des enfants
				$resc=msq("Select uid from pages where pid=$id and deleted=0 and hidden=0");
	//ahah('".$this->ChemFromRoot."ajaxdisplinks.php?pid=".$id."','ajaxdynlinks');
// arguments echDL3T1line : ($id,$pid,$label,$cur_depth,$title="",$href="",$onclickJSAction="",$disp=true,$leafimg="",$dispfoldplus=false,$ckbcheked=false,$nodeopen="false",$cdckbox=true)
				//$onclickJSAction="fdisp3t($id); ahah(JSChemFromRoot + '".$id."','ajaxdynlinks');";
				$href="javascript:fdisp3t($id); ahah(JSChemFromRoot + '".$id."','ajaxdynlinks');";
				$this->str3tree.=$this->DL3tree->echDL3T1line ($id,$parent_id,$title,$clevel-1,"",$href,$onclickJSAction,$clevel==1,"",mysql_num_rows($resc)>0,false);
				$this->getLifromDB($id,$clevel);
			}
		}
	}
} // fin def class



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/vm19_hnlinks/pi1/class.tx_vm19hnlinks_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/vm19_hnlinks/pi1/class.tx_vm19hnlinks_pi1.php"]);
}

?>