<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2004 Vincent (admin, celui Ю la pioche) (webtech@haras-nationaux.fr)
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
 * Plugin 'reglementation' for the 'vm19_hn_reglementation' extension.
 *
 * @author	Vincent (admin, celui Ю la pioche) <webtech@haras-nationaux.fr>
 */


require_once(PATH_tslib."class.tslib_pibase.php");
require_once("dl3tree.inc"); // fichier partagц╘ contenant l'objet permettant de gц╘nц╘rer des arboresences
require_once("typo3conf/ext/vm19_toolbox/functions.php");
//require_once("fonctions.php"); // fichier partagц╘ contenant l'objet permettant de gц╘nц╘rer des arboresences INCLUS INDIRECTEMENT CI-DESSUS


class tx_vm19hnreglementation_pi1 extends tslib_pibase {
	var $prefixId = "tx_vm19hnreglementation_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_vm19hnreglementation_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "vm19_hn_reglementation";	// The extension key.
	var $ChemImg="typo3conf/ext/vm19_hn_reglementation/pi1/";
	var $ChemUpLoads="uploads/tx_vm19hnreglementation/";

	var $DL3tree; // objet Arbre
	var $str3tree; // chaine contenant le code HTML de l'arbre
	var $tabTLId; // chaine contenant les typolink
	var $text_indent=15;// identation quand textes attachés
	var $pi_checkCHash=true;
	/**
	 Cette version fonctionne parfaitement avec le magnifique arbre DLCube mais ne peut etre indexé à cause de l'impossibilité
	 de génrérer un Chash valide en Javascript...
	 */
	function main($content,$conf)	{
		
		//debug ($this->cObj->data);
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();		// Loading the LOCAL_LANG values
		//debug ($_REQUEST);
		
//		$this->migrate();
//		$this->update();
//		die("coucou");
		
		
		
		$this->DL3tree=new dltreeObj;
		$this->DL3tree->imgfopen=$this->ChemImg.'folderOpen.gif'; // nom des fichiers images symoles
		$this->DL3tree->imgfcloseplus =$this->ChemImg.'folderClosedplus.gif';
		$this->DL3tree->imgfclose =$this->ChemImg.'folderClosed.gif';
		
		//$this->cHash_array =t3lib_div::cHashParams(t3lib_div::implodeArrayForUrl('',$_GET));
		//$GLOBALS['TSFE']->reqCHash();
		//$GLOBALS['TSFE']->makeCacheHash();
		
		//$GLOBALS["TSFE"]->setJS("toto","alert('coucoui')");
		// d?claration des variables JS
		$GLOBALS["TSFE"]->setJS("DL3THNR_1",$this->DL3tree->echDL3TJSVarsInit(false));
		// d?claration des fonctions JS pour l'arbre DL3Tree
		$GLOBALS["TSFE"]->setJS("DL3THNR_2",$this->DL3tree->echDL3TJSFunctions(false));
		// d?claration des fonctions JS spécifiques
		$this->tabTLId="tabTLId=new Array();\n";
		$this->getLifromDB($this->cObj->data["pages"]); // calcule l'arbre (MAJ de la propri?t? this->str3tree, et MAL de la propri?t? de l'objet DLTREE->tbChilds)
		$GLOBALS["TSFE"]->setJS("DL3THNR_5",$this->tabTLId); // echoise tableau JS des typolink
		$GLOBALS["TSFE"]->setJS("DL3SHNR_4","
			function setirubval(val) {
				document.reg3tform.id.value=val;
				fdisp3t(val); // force deploiement sous arbre
				document.reg3tform.action=tabTLId[val];
				document.reg3tform.submit();
			}
			
			");
	
		// d?claration des styles CSS
		//$GLOBALS["TSFE"]->setCSS("DL3THNR_1",$this->DL3tree->echDL3TStyles(false));
		//$this->cObj->data["pages"] = id du dossier de d?marrage stock? dans l'enregistrement tt_content du plugin 
		// echoise le tableau JS contenant les enfants (tbChilds)
		$GLOBALS["TSFE"]->setJS("DL3THNR_3",$this->DL3tree->echDL3TJStbChilds(false));
		
		//$str2ret.="Test ".htmlspecialchars(this->typolink($this->pi_getPageLink($GLOBALS["TSFE"]->id)."&irub=3"));
		//t3lib_div::$this->makeCacheHash();
		$str2ret.='
		<form action="'.t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $this->pi_getPageLink($GLOBALS['TSFE']->id).'" name="reg3tform" method="GET">
				<input type="hidden" name="id" value="">';
//				<input type="hidden" name="cHash" value="'.md5($_REQUEST['irub']).'">';
				
		//$str2ret.='<input type="hidden" name="no_cache" value="1">';
		
		$str2ret.='
		<H2>Base de donn&eacute;es r&#232;glementation</H2>
		<div id="reg3tree">'.
		$this->str3tree.
		'</div>
		<div id="reg3rubcont">'.
		$this->DispReg($GLOBALS["TSFE"]->id)
		.'</div>
		';	
		
		/*
		
		//$str2ret.="<br/>select_key=".$this->cObj->data["select_key"];
		$Margt['ttc_uid']=19230;
		$Margt['pointer']=$this->piVars["pointer"];

		la fonction pi_list_linkSingle possХde u dernier argument aoptionel qui permet de passer un tableau de hachage contenant autant d'arguments supplИmentaires que l'on veut !
		ici on y passe l'uid du tt_content contenant le plugin de faГon Ю les diffИrencier
		!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-
		
		$str2ret.=$this->pi_list_linkSingle("TEST",$this->internal["currentRow"]["uid"],1,$Margt);
		*/

		$str2ret.="</form>";
		return($str2ret);
	} // fin m?thode main
		
	function DispReg($irub) {
	//coucou
		
		if ($irub=="") {
			return ("Veuillez cliquer dans une cat&#233;gorie ci-contre pour afficher les articles de loi correspondant...");
		} else {
/*
tx_vm19hnreglementation_textes
Champ 	Type 	Null 	Défaut
uid  	int(11) 	Non  	 
pid  	int(11) 	Non  	0 
tstamp  	int(11) 	Non  	0 
crdate  	int(11) 	Non  	0 
cruser_id  	int(11) 	Non  	0 
sorting  	int(10) 	Non  	0 
deleted  	tinyint(4) 	Non  	0 
hidden  	tinyint(4) 	Non  	0 
starttime  	int(11) 	Non  	0 
endtime  	int(11) 	Non  	0 
url  	tinytext 	Non  	 
title  	tinytext 	Non  	 
nature  	blob 	Non  	 
dat_approb  	int(11) 	Non  	0 
number  	tinytext 	Non  	 
publication  	tinytext 	Non  	 
desc_2bf7363fc2  	text 	Non  	 
fich_joint  	blob 	Non  	 
kwords  	tinytext 	Non  	 
orig  	tinytext 	Non  	 
other_pages  	blob 	Non  	 
parent_text  	blob 	Non  	 
rtt_attach_type */
			// !!! LE IN A L'ENVERS NE FONCTIONNE PAS !!!
			$rpt=msq ("SELECT * FROM tx_vm19hnreglementation_textes WHERE (pid=$irub OR other_pages LIKE '$irub,%' OR other_pages LIKE '%,$irub,%' OR other_pages LIKE '%,$irub' ) AND rtt_attach_type='' AND deleted=0 AND (starttime=0 OR starttime >= ".time().") AND (endtime=0 OR endtime <= ".time().") ORDER BY dat_approb,sorting");
			
			
			 // on met pas le hidden pour avoir les enfants d'un texte masqué
			 // on le masque dans le code
			//$strt2d.="<H4>Textes de la rubrique<H4>";
			$strt2d.="<H3>".utf8_deconne(RecupLib("pages","uid","title",$irub))."</H3>";
			if (mysql_num_rows($rpt)==0) {
				$strt2d.='<p><span class="reg_natdat">Aucun texte n\'est pr&#233;sent dans cette rubrique</span>';
				$strt2d.='<br/><span class="reg_title">Merci de consulter ses sous-rubriques ...</span></p>';
				}
			else { while ($rwt=mysql_fetch_array($rpt)) {
				$strt2d.=$this->aff1text($rwt);
				}
			}
			return ($strt2d);
		}
	}

// méthode (réentrante) qui affiche 1 texte
	function aff1text($rwt,$indent_n=1) {
		$strt2d.='<p style="padding-left:'.$indent_n*$this->text_indent.'px">';
		
		
		switch ($rwt["rtt_attach_type"]) {
			case "MOD":
			$atttxt="modifi&#233; par ";
			break;
			
			case "ABR":
			$atttxt="abrog&#233; par ";
			break;
			
			case "REPL":
			$atttxt="remplac&#233; par ";
			break;
			
			case "ANN":
			$atttxt="annul&#233; par ";
			break;
			
			case "TRP":
			$atttxt="transpos&#233; par ";
			break;
			
			case "APP":
			$atttxt="appliqu&#233; par ";
			break;
			
			default :
			$atttxt="";
			}
		
		if ($rwt["hidden"]!=1 && !$this->affok[$rwt['uid']]) { // affiche que si pas caché et si texte pas deja affiché
			if ($atttxt!="") $strt2d.='<span class="reg_modpar">'.$atttxt." :<br/></span>";
			$indent_n++;
			$strt2d.='<span class="reg_natdat">'.
				'<a name="Anc'.$rwt["uid"].'" title="uid='.$rwt["uid"].'">&#149; </a>'.
				($rwt["url"]!="" ? '<a href="'.$rwt["url"].'" target="_blank">' : "").
				utf8_deconne(RecupLib("tx_vm19hnreglementation_nature","uid","title",$rwt["nature"])).($rwt["dat_approb"]>0 ? " du ".getDateF($rwt["dat_approb"]): "").
				($rwt["url"]!="" ? '</a>' : "")." </span>";
			$strt2d.=($rwt["title"]!="" ? '<span class="reg_title">'.utf8_deconne($rwt["title"])."</span>" : "");
			$strt2d.=($rwt["number"]!="" ? '<br/><span class="reg_number">Num&#233;ro '.$rwt["number"]."</span>" : "");
			$strt2d.=($rwt["publication"]!="" ? '<br/><span class="reg_publication">Publi&#233; '.utf8_deconne($rwt["publication"])."</span>" : "");
			$strt2d.=($rwt["fich_joint"]!="" ? '<br/><span class="reg_publication">T&#233;l&#233;charger le texte : <a href="'.$this->ChemUpLoads.$rwt["fich_joint"]."\">". $rwt["fich_joint"]."</a></span>" : "");
			$strt2d.=($rwt["desc_2bf7363fc2"]!="" ? '<br/><span class="reg_desc"><a href="#" onclick="togdispt3t(this)" id=a3t'.$rwt["uid"].">
			<img src=\"".$this->ChemImg."f_right12.gif\" id=i3t".$rwt["uid"]."><em> Description </em></a>
			<div id=t3t".$rwt["uid"]." class=\"stDL3Thidd\">".utf8_deconne($rwt["desc_2bf7363fc2"])."</div></span>" : "");
			$strt2d.='<br/><span class="reg_fxhr">------------------------</span></p>';
		} //
		$sqlf="SELECT * FROM tx_vm19hnreglementation_textes WHERE parent_text=".$rwt["uid"]." AND deleted=0 AND (starttime=0 OR starttime >= ".time().") AND (endtime=0 OR endtime <= ".time().") ORDER BY dat_approb,sorting";
		$rpat=msq ($sqlf);
		//$strt2d.="<!-- $sqlf -->\n";
		while($rwpat=mysql_fetch_array($rpat)) {
			$strt2d.=$this->aff1text($rwpat,$indent_n); // on réentre
			}
		$this->affok[$rwt['uid']]=true; // indique que texte deja affiché, pour pas qu'il s'affiche 2 fois.
		//$strt2d.="<HR/>";
		return($strt2d);
	}
	
	// mИthode rИentrante qui rИcupХre les lignes de l'arbre	
	function getLifromDB($parent_id,$clevel=0) {
		//global $tbChilds; 
		//global $str3tree;
		//global $DLtree;
		
		$clevel++;
		$sql = "Select uid,pid,title from pages where pid IN ($parent_id) and deleted=0 and hidden=0 ORDER BY sorting";
		$res = msq ($sql);
		if($res){
			while($row=mysql_fetch_array($res)){
				$id=$row['uid'];
				$title=str_replace('"',"`",str_replace("'","`",utf8_deconne($row['title'])));
				//$tbChilds[$parent_id][]=$id;
				// sert uniquement ц═ savoir si le noeud a des enfants
				//$href=t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $this->pi_getPageLink($id);
				//$onclickJSAction="setirubval(".$id."); ";
				$href="javascript:setirubval(".$id."); ";
				$this->tabTLId.="tabTLId['$id']='".t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $this->pi_getPageLink($id)."';\n";
				$resc=msq("Select uid from pages where pid=$id and deleted=0 and hidden=0");
				
/* ARGUMENTS de la methode echDL3T1line
$id= id du noeud, 
$pid= id parent du noeud; sert uniquement à remplir la propriété-tableau tbChilds
$label=label affiché dans l'arbre
$cur_depth=profondeur (en unités), modifié l'identation
$title=info-bulle du noeud
$href=lien
$onclickJSAction=sans commantaire
$disp=ligne affichée (vraie par défaut)
$leafimg=image du noeud, si non spécifiée, affiche celle spécifiée par les propriétés $this->imgfcloseplus et $this->imgfclose
$dispfoldplus=unuquement si leafimg non spécifiée
$ckbcheked=case à cocher cochée; NB: l'affichage des cases à cocher est conditionnée par la propriété $this->dispckbox
$nodeopen=indique si noeud ouvert
$cdckbox=permet de ne pas afficher une case a cocher meme si elles sont OK via la propriété $this->dispckbox
*/

				$this->str3tree.=$this->DL3tree->echDL3T1line (
				$id,
				$parent_id,
				$title,
				$clevel-1,
				"",
				$href,
				$onclickJSAction,
				($clevel==1 || $_REQUEST['n3o'.$parent_id]=="true"),
				"",
				mysql_num_rows($resc)>0,
				true,
				($_REQUEST['n3o'.$id]=="true" ? "true" : "false"));
				$this->getLifromDB($id,$clevel);
			}
		}
	}
// FONCTIONS UTILITAIRES DE MIGRATION	
	//
	function migrate() {
		$res=msq("select uid from tt_content order by uid desc limit 1");
		$rp=mysql_fetch_row($res);
		$this->uidttc=$rp[0];
		
		$uidp=2688;
		$this->modpages(2688);
		
		return('');
	
	}
	
	function modpages($uid) {
		$rsp=msq("select uid from pages where pid=$uid");
		while ($resp=mysql_fetch_row($rsp)) {
			$this->uidttc ++;
			msq("update pages set doktype=5 where uid=".$resp[0]);
			msq("INSERT INTO `tt_content` VALUES (".$this->uidttc.", ".$resp[0].", 1139397467, 1139397467, 0, 256, 'list', '', '', '', '', 0, 8, '', 0, 0, '', 0, 0, 0, '', 0x33323137, 0, 0, 0, '', 0, 0, 0, '', '', '', 0, 0, 0, 0, '0', '', 0, 0, 0, 0, '0', 'vm19_hn_reglementation_pi1', 0, 0, 0, 0, '', 1, 0, 0, 0, 0, '0', '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 0, 0x613a32313a7b733a353a224354797065223b4e3b733a31363a227379735f6c616e67756167655f756964223b4e3b733a363a22636f6c506f73223b4e3b733a31313a2273706163654265666f7265223b4e3b733a31303a2273706163654166746572223b4e3b733a31333a2273656374696f6e5f6672616d65223b4e3b733a31323a2273656374696f6e496e646578223b4e3b733a393a226c696e6b546f546f70223b4e3b733a363a22686561646572223b4e3b733a31353a226865616465725f706f736974696f6e223b4e3b733a31333a226865616465725f6c61796f7574223b4e3b733a31313a226865616465725f6c696e6b223b4e3b733a343a2264617465223b4e3b733a393a226c6973745f74797065223b4e3b733a31303a2273656c6563745f6b6579223b4e3b733a353a227061676573223b4e3b733a393a22726563757273697665223b4e3b733a363a2268696464656e223b4e3b733a393a22737461727474696d65223b4e3b733a373a22656e6474696d65223b4e3b733a383a2266655f67726f7570223b4e3b7d, '')");
			$this->modpages($resp[0]);
		}
		return('');
	}
	
	function update() {
		$res=msq("select uid from tt_content order by uid desc limit 1");
		$rp=mysql_fetch_row($res);
		$this->uidttc=$rp[0];
		
		$uidp=2688;
		$this->updtpages(2688);
	}
	
	function updtpages($uid) {
		$rsp=msq("select uid from pages where pid=$uid");
		while ($resp=mysql_fetch_row($rsp)) {
			msq("update pages set doktype=5 where uid=".$resp[0]);
			//msq("update tt_content set header='' where pid=".$resp[0]);
			$this->updtpages($resp[0]);
		}
	}
	
	// Ancienne fonction Main gardée pour la syntaxe
	function Oldmain($content,$conf) {
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
			
		$content='
			<strong>This is a few paragraphs:</strong><BR>
			<p>This is line 1</p>
			<p>This is line 2</p>
	
			<h3>This is a form:</h3>
			<form action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'" method="POST">
				<input type="hidden" name="no_cache" value="1">
				<input type="text" name="'.$this->prefixId.'[input_field]" value="'.htmlspecialchars($this->piVars["input_field"]).'">
				<input type="submit" name="'.$this->prefixId.'[submit_button]" value="'.htmlspecialchars($this->pi_getLL("submit_button_label")).'">
			</form>
			<BR>
			<p>You can click here to '.$this->pi_linkToPage("get to this page again",$GLOBALS["TSFE"]->id).'</p>
		';
	
		return $this->pi_wrapInBaseClass($content);
	}
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/vm19_hn_reglementation/pi1/class.tx_vm19hnreglementation_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/vm19_hn_reglementation/pi1/class.tx_vm19hnreglementation_pi1.php"]);
}

function utf8_deconne($str) {
	return($str);
}

?>
