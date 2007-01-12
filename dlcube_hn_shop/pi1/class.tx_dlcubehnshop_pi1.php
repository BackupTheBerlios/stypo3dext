<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2004 Vincent (admin, celui ? la pioche) (webtech@haras-nationaux.fr)
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
 * Plugin BOUTIQUE
 *
 * @author	Vincent MAURY <vmauy@dlcube.com>
 */
require_once(PATH_tslib."class.tslib_pibase.php");
require_once("typo3conf/ext/vm19_toolbox/functions.php");
// pour access WS recup infos personne
include_once("typo3conf/ext/dlcube_hn_01/class.WebservicesCompte.php");
include_once("typo3conf/ext/dlcube_hn_01/class.WebservicesAccess.php");

require_once("fonctions.php");

class tx_dlcubehnshop_pi1 extends tslib_pibase {
	var $prefixId = "tx_dlcubehnshop_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_dlcubehnshop_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "dlcube_hn_shop";	// The extension key.
	var $upload_doc_folder="uploads/tx_dlcubehnshop";
	var $ImgMwidth=100;
	var $modtestPID=true; // mode test portal Id
	//var $searchConfig="WSDatastore"; 
	
	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
	
		$this->uid = substr(strstr($GLOBALS[GLOBALS][TSFE]->currentRecord,":"),1);
		$content.='<H2><img src="'.$this->conf["iconDir"].'librairie.gif" class="picto"> '.$this->pi_getLL("plugtitle")."</H2>";
		
		if ($_REQUEST["hnkart_mode"]=="false") {
			$_SESSION["hnkart_mode"]=false;
		} elseif($_REQUEST["hnkart_mode"]!="") 
			$_SESSION["hnkart_mode"]=$_REQUEST["hnkart_mode"];
			
		
		if ($_REQUEST["add_art"]) {
			$_SESSION['hn_kart'][$_REQUEST["add_art"]]++;
		}
		
		if ($_REQUEST["dec_art"]) {
			$_SESSION['hn_kart'][$_REQUEST["dec_art"]]--;
			if ($_SESSION['hn_kart'][$_REQUEST["dec_art"]]==0) unset($_SESSION['hn_kart'][$_REQUEST["dec_art"]]);
		}
		if ($_REQUEST["del_art"])  unset($_SESSION['hn_kart'][$_REQUEST["del_art"]]);
		
		if ($_SESSION["hnkart_mode"]=="true") { // vue contenu de la carriole ou bon de commande
			return $this->pi_wrapInBaseClass($this->viewCart($content));
		} elseif ($_SESSION["hnkart_mode"]!="") {
			return $this->pi_wrapInBaseClass($this->viewBCommand($content));
		} else {

			switch((string)$conf["CMD"])	{
				case "singleView":
					list($t) = explode(":",$this->cObj->currentRecord);
					$this->internal["currentTable"]=$t;
					$this->internal["currentRow"]=$this->cObj->data;
					return $this->pi_wrapInBaseClass($this->singleView($content));
				break;
				default:
					if (strstr($this->cObj->currentRecord,"tt_content"))	{
						$conf["pidList"] = $this->cObj->data["pages"];
						$conf["recursive"] = $this->cObj->data["recursive"];
						//debug($this->cObj->data);
					}
					return $this->pi_wrapInBaseClass($this->listView($content));
				break;
				}
		} // fin si pas contenu cariole
	} // fin methode main
	
/**
	 * vue carriole de commande
	 */
	function viewCart($content,$recap=false) {
		 
		 if (!$recap) $content.='<h3><img src="'.$this->conf["iconDir"].'cariole_big.gif" class="picto"> '.$this->pi_getLL('kart_content').'</H3>';
		 
		if (empty($_SESSION['hn_kart'])) {
			$content.=$this->pi_getLL("empty_kart","[empty_kart]");
			$content.='<p align="center"><A class="fxbutton" HREF="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'?cHash='.$cHash.'&hnkart_mode=false">'.$this->pi_getLL("closek","[closek]").'</a>&nbsp;&nbsp;&nbsp;</p>';

		} else  {
			$content.='<table><tr class="docTableHead">
		 		<td align="left">Ref</td>
				<td align="left">'.$this->pi_getLL('listFieldHeader_title').'</td>
				<td align="left">'.$this->pi_getLL('listFieldHeader_price').'</td>
				<td align="left">'.$this->pi_getLL('listFieldHeader_qte').'</td>
				<td align="left">'.$this->pi_getLL('listFieldHeader_totline').'</td>
			</tr>';
			foreach ($_SESSION['hn_kart'] as $uid=>$qte) {
				$tbuid[]=$uid;
				}
			$tbuid=implode(",",$tbuid);
			$rep=db_qr_comprass("select uid,title,ref,price from tx_dlcubehnshop_articles where uid IN ($tbuid)");
			foreach ($rep as $tbrep) {
				$qte=$_SESSION['hn_kart'][$tbrep['uid']];
				$totl=$tbrep['price']*$qte;
				$totgen+=$totl;
				$content.='<tr class="'.($c%2 ? 'doclist_rowodd' : 'doclist_roweven').'">
		 		<td align="left">'.$tbrep['ref'].'</td>
				<td align="left">'.$tbrep['title'].'</td>
				<td align="center">'.$tbrep['price'].' &#8364;</td>
				<td align="left"><strong> &nbsp;'.$qte.' &nbsp;&nbsp; 
				 </strong>';
				 if (!$recap) $content.='<a href="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'?cHash='.$cHash.'&hnkart_mode=true&add_art='.$tbrep['uid'].'" title="'.$this->pi_getLL("kartinc","[kartinc]").'"><img src="'.$this->conf["iconDir"].'add.gif" class="picto"  alt="'.$this->pi_getLL("kartinc","[kartinc]").'"></a>
				 <a href="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'?cHash='.$cHash.'&hnkart_mode=true&dec_art='.$tbrep['uid'].'" title="'.$this->pi_getLL("kartdec","[kartdec]").'"><img src="'.$this->conf["iconDir"].'dec.gif" class="picto"  alt="'.$this->pi_getLL("kartdec","[kartdec]").'"></a>
				<a href="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'?cHash='.$cHash.'&hnkart_mode=true&del_art='.$tbrep['uid'].'" title="'.$this->pi_getLL("kartdel","[kartdel]").'"><img src="'.$this->conf["iconDir"].'trash.gif" class="picto"  alt="'.$this->pi_getLL("kartdel","[kartdel]").'"></a>';
				
				$content.='
				</td>
				<td align="center">'.$totl.' &#8364;</td>
				</tr>';
				$c++;
			}
			$content.='<tr class="docTableHead"><td colspan="4" align="right">'.$this->pi_getLL('total_gen').'</td><td align="center">'.($totgen+0.00).' &#8364; </td></tr>';
			$content.='</table>';
			//print_r($_SESSION['hn_kart']);
			if (!$recap) {
				$content.='<p align="center"><A class="fxbutton" HREF="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'?cHash='.$cHash.'&hnkart_mode=false">'.$this->pi_getLL("closek","[closek]").'</a>&nbsp;&nbsp;&nbsp;';
				$content.='<A class="fxbutton" HREF="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'?cHash='.$cHash.'&hnkart_mode=bc1">'.$this->pi_getLL("2command","[2command]").'</a></p>';
			}
		}
		
		return ($content);
	}
/**
	 * vue �apes de commande..
	 */
	function viewBCommand($content)	{
		$this->getPersInfos(); // recup infos pers
		
		if (empty($_SESSION['hn_kart'])) {
			$content.=$this->pi_getLL("empty_kart","[empty_kart]");
			$content.='<p align="center"><A class="fxbutton" HREF="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'?cHash='.$cHash.'&hnkart_mode=false">'.$this->pi_getLL("closek","[closek]").'</a>&nbsp;&nbsp;&nbsp;</p>';
		} else  {
	
			$content.='<h3><img src="'.$this->conf["iconDir"].'bcommand.gif" class="picto"> '.$this->pi_getLL('bc_etap_'.$_SESSION["hnkart_mode"]).'</H3>';
			
			if (!isset($_SESSION["portalId"]) || $_SESSION["portalId"]=="") {
				$content.='<p>'.$this->pi_getLL('mustid4command').'</p>';
				$content.='<p>Veuillez cliquez ici: <A class="fxbutton" href="index.php?id=2822">ICI</a> pour vous identifier ou cr�r un compte; revenez ensuite �la boutique pour poursuivre votre commande</p>';
			
			} else {
				$content.='<FORM action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'" name="'.$this->prefixId.'fname" method="GET">';
				if (isset($_REQUEST[$this->prefixId]))  {
					$_SESSION[$this->prefixId]=$_REQUEST[$this->prefixId]; // enregistre les variables
					if ($_SESSION[$this->prefixId]['otheraddr'] !="") {
						if ($_SESSION[$this->prefixId]['nomexp']=="" || $_SESSION[$this->prefixId]['adresse']=="" || $_SESSION[$this->prefixId]['cp']=="" || $_SESSION[$this->prefixId]['ville']=="") {
							$err='<p style="color:red">'.$this->pi_getLL("erroa","erreur adresse").'</p>';
							$_SESSION["hnkart_mode"]="bc1";
						}
					}
					if ($_SESSION["hnkart_mode"]=="bc3" && $_SESSION[$this->prefixId]['acccgv']=="") {
						$_SESSION["hnkart_mode"]="bc2";
						$errcgv='<p><span class="smallred">'.$this->pi_getLL("cgvnv").'</span></p>';
						
					}
				}
				switch ($_SESSION["hnkart_mode"]) {
					
					case "bc1": // etape 1: identif, adresse exp.
						$content.='<input type="hidden" name="hnkart_mode" size="30" value="bc2"/>';
						$content.='<p>'.$this->pi_getLL('yourcoords').': <UL>'.
							$this->personne["findPersonneByLoginReturn"]["prenom"]." ".
							$this->personne["findPersonneByLoginReturn"]["nom"].'<BR/>'.
							$this->personne["findPersonneByLoginReturn"]["adresse"]["adresse"].'<BR/>'.
							$this->personne["findPersonneByLoginReturn"]["adresse"]["complementAdresse"].'<BR/>'.
							$this->personne["findPersonneByLoginReturn"]["adresse"]["commune"]["codePostal"].' '.
							$this->personne["findPersonneByLoginReturn"]["adresse"]["commune"]["libelle"].'<BR/> email: '.
							$this->personne["findPersonneByLoginReturn"]["coordonnees"]["email"].
							'</UL></p>';
						$content.='<p>'.$this->pi_getLL('bc1lib1').'<br/>';
						$content.=$this->pi_getLL('otheraddr').'<INPUT type="checkbox" name="'.$this->prefixId.'[otheraddr]" value="otheraddr" onchange="togglevisoa(this)" '.($_SESSION[$this->prefixId]['otheraddr'] !="" ? 'checked="checked"' : "").'>';
						$content.='<script language="javascript">
							function togglevisoa(theckb) {
								if (theckb.checked) {
									document.getElementById(\'otheraddr\').style.display=\'block\';

								} else {
									document.getElementById(\'otheraddr\').style.display=\'none\';
								}
							}
							</script>';
						
						$content.='<p>&nbsp;<div id="otheraddr" style="display:'.($_SESSION[$this->prefixId]['otheraddr'] !="" ? "block" : "none").';">'.$err.'<ul><table><tr><td>'.
							$this->pi_getLL("nomexp","[nomexp]").' <span class="smallred">*</span></td><td> <input type="text" name="'.$this->prefixId.'[nomexp]" size="30" value="'.$_SESSION[$this->prefixId]['nomexp'].'"/></td></tr>
							<tr><td>'.
							$this->pi_getLL("adresse","[adresse]").' <span class="smallred">*</span></td><td> <input type="text" name="'.$this->prefixId.'[adresse]" size="30" value="'.$_SESSION[$this->prefixId]['adresse'].'"/></td></tr>
							<tr><td>'.
							$this->pi_getLL("adresse2","[adresse2]").'</td><td> <input type="text" name="'.$this->prefixId.'[adresse2]" size="30" value="'.$_SESSION[$this->prefixId]['adresse2'].'"/></td></tr>
							<tr><td>'.
							$this->pi_getLL("cpville","[cpville]").' <span class="smallred">*</span></td><td> <input type="text" name="'.$this->prefixId.'[cp]" size="10" value="'.$_SESSION[$this->prefixId]['cp'].'"/> <input type="text" name="'.$this->prefixId.'[ville]" size="20" value="'.$_SESSION[$this->prefixId]['ville'].'"/></td></tr>
							<tr><td>
							<span class="smallred">* '.$this->pi_getLL("obligchp","[obligchp]").'</span>
							</td><td>&nbsp;</td></tr></table></ul></div></p>';
							
						$content.='<p>'.
							$this->pi_getLL("commentcomm","[commentcomm]").'<br/><textarea name="'.$this->prefixId.'[comment]" cols="30" rows="5">'.$_REQUEST[$this->prefixId]['comment'].'</textarea></p>
							<p>
							<A class="fxbutton" href="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'?cHash='.$cHash.'&hnkart_mode=false">'.
							$this->pi_getLL("stopc","[stop]").'</a>&nbsp;&nbsp;&nbsp;&nbsp;'.
							$this->vklink(false).
							'<INPUT '.$this->pi_classParam("submit-button").' type="submit" name="'.$this->prefixId.'[submit_button]" value="'.$this->pi_getLL("continuer","continuer").'"></p>';
					break;
					
					case "bc2": // recap, choix du mode de paiement
						$content.='<input type="hidden" name="hnkart_mode" size="30" value="bc3"/>';
						$content.='<h4>'.$this->pi_getLL("recapcomm","[recapcomm]").'</h4>';
						$content.=$this->viewCart("",true);
						$content.='<p><h4>'.$this->pi_getLL('addfact').':</h4><UL>'.
							$this->personne["findPersonneByLoginReturn"]["prenom"]." ".
							$this->personne["findPersonneByLoginReturn"]["nom"].'<BR/>'.
							$this->personne["findPersonneByLoginReturn"]["adresse"]["adresse"].'<BR/>'.
							$this->personne["findPersonneByLoginReturn"]["adresse"]["complementAdresse"].'<BR/>'.
							$this->personne["findPersonneByLoginReturn"]["adresse"]["commune"]["codePostal"].' '.
							$this->personne["findPersonneByLoginReturn"]["adresse"]["commune"]["libelle"].'<BR/> email: '.
							$this->personne["findPersonneByLoginReturn"]["coordonnees"]["email"].
							'</UL></p>';
						$content.='<p><h4>'.$this->pi_getLL('addliv').':</h4> <UL>';
						if ($_SESSION[$this->prefixId]['otheraddr'] !="") {
							$content.=$_SESSION[$this->prefixId]['nomexp'].'<BR/>'.
							$_SESSION[$this->prefixId]['adresse'].'<BR/>'.
							$_SESSION[$this->prefixId]['adresse2'].'<BR/>'.
							$_SESSION[$this->prefixId]['cp'].' '.
							$_SESSION[$this->prefixId]['ville'];
						} else $content.=$this->pi_getLL("adliveqadfact","identique");
						$content.='</UL></p>';
						$content.='<p><h4>'.$this->pi_getLL('paym').':</h4><p>';
						if ($this->personne["findPersonneByLoginReturn"]["nombrePoint"]>0) {
							$content.=$this->pi_getLL('dispnbpoints').$this->personne["findPersonneByLoginReturn"]["nombrePoint"]." points";
						} else $content.='<span class="smallred">'.$this->pi_getLL('nopoints').'</span>';
						$content.='</p><input type="radio" name="'.$this->prefixId.'[paymmod]" value="cheque"> '.$this->pi_getLL("cheque").'&nbsp;&nbsp;&nbsp;&nbsp;';
						$content.='<input type="radio" name="'.$this->prefixId.'[paymmod]" value="cb"> '.$this->pi_getLL("cb").'&nbsp;&nbsp;&nbsp;&nbsp;';
						$content.='<input type="radio" name="'.$this->prefixId.'[paymmod]" value="points"> '.$this->pi_getLL("points").'&nbsp;&nbsp;&nbsp;&nbsp;';
						$content.='</p>';
						
						$content.='<p><h4>'.$this->pi_getLL('cgv').':</h4>';
						$content.=$errcgv.$this->pi_getLL('acccgv').'<INPUT type="checkbox" name="'.$this->prefixId.'[acccgv]" value="cgvok"></p>';
						
	
						$content.='<p>'.$this->vklink(false).
						'&nbsp;&nbsp;&nbsp;&nbsp;<A class="fxbutton" href="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'?cHash='.$cHash.'&hnkart_mode=bc1">'.
							$this->pi_getLL("ecprec","[ecprec]").'</a>&nbsp;&nbsp;&nbsp;&nbsp;
							<A class="fxbutton" href="#" onclick="self.print()">'.
							$this->pi_getLL("imprim","[print]").'</a>&nbsp;&nbsp;&nbsp;&nbsp;
							<INPUT '.$this->pi_classParam("submit-button").' type="submit" name="'.$this->prefixId.'[submit_button]" value="'.$this->pi_getLL("continuer","continuer").'"></p>';
					break;
	
					case "bc3": // paiement
						$content.='<input type="hidden" name="hnkart_mode" size="30" value="bc4"/>';
						$content.='<h4>'.$this->pi_getLL("recapcomm","[recapcomm]").'</h4>';
	
						$content.='<p>'.$this->vklink(false).
						'&nbsp;&nbsp;&nbsp;&nbsp;<A class="fxbutton" href="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'?cHash='.$cHash.'&hnkart_mode=bc2">'.
							$this->pi_getLL("ecprec","[ecprec]").'</a>&nbsp;&nbsp;&nbsp;&nbsp;<INPUT '.$this->pi_classParam("submit-button").' type="submit" name="'.$this->prefixId.'[submit_button]" value="'.$this->pi_getLL("continuer","continuer").'"></p>';
					break;
					
					case "bc4": // fin : remerciement.
						$content.='<input type="hidden" name="hnkart_mode" size="30" value="false"/>';
						$content.='<h4>'.$this->pi_getLL("recapcomm","[recapcomm]").'</h4>';
						$content.=$this->viewCart("",true);
						$content.='<p><h4>'.$this->pi_getLL('addfact').':</h4><UL>'.
							$this->personne["findPersonneByLoginReturn"]["prenom"]." ".
							$this->personne["findPersonneByLoginReturn"]["nom"].'<BR/>'.
							$this->personne["findPersonneByLoginReturn"]["adresse"]["adresse"].'<BR/>'.
							$this->personne["findPersonneByLoginReturn"]["adresse"]["complementAdresse"].'<BR/>'.
							$this->personne["findPersonneByLoginReturn"]["adresse"]["commune"]["codePostal"].' '.
							$this->personne["findPersonneByLoginReturn"]["adresse"]["commune"]["libelle"].'<BR/> email: '.
							$this->personne["findPersonneByLoginReturn"]["coordonnees"]["email"].
							'</UL></p>';
						$content.='<p><h4>'.$this->pi_getLL('addliv').':</h4> <UL>';
						if ($_SESSION[$this->prefixId]['otheraddr'] !="") {
							$content.=$_SESSION[$this->prefixId]['nomexp'].'<BR/>'.
							$_SESSION[$this->prefixId]['adresse'].'<BR/>'.
							$_SESSION[$this->prefixId]['adresse2'].'<BR/>'.
							$_SESSION[$this->prefixId]['cp'].' '.
							$_SESSION[$this->prefixId]['ville'];
						} else $content.=$this->pi_getLL("adliveqadfact","identique");
						$content.='</UL></p>';
						$content.='<p><h4>'.$this->pi_getLL('paym').':</h4><p>';
						if ($this->personne["findPersonneByLoginReturn"]["nombrePoint"]>0) {
							$content.=$this->pi_getLL('dispnbpoints').$this->personne["findPersonneByLoginReturn"]["nombrePoint"]." points";
						} else $content.='<span class="smallred">'.$this->pi_getLL('nopoints').'</span>';
						$content.='</p><input type="radio" name="'.$this->prefixId.'[paymmod]" value="cheque"> '.$this->pi_getLL("cheque").'&nbsp;&nbsp;&nbsp;&nbsp;';
						$content.='<input type="radio" name="'.$this->prefixId.'[paymmod]" value="cb"> '.$this->pi_getLL("cb").'&nbsp;&nbsp;&nbsp;&nbsp;';
						$content.='<input type="radio" name="'.$this->prefixId.'[paymmod]" value="points"> '.$this->pi_getLL("points").'&nbsp;&nbsp;&nbsp;&nbsp;';
						$content.='</p>';
						
						$content.='<p><h4>'.$this->pi_getLL('cgv').':</h4>';
						$content.=$this->pi_getLL('acccgv').'<INPUT type="checkbox" name="'.$this->prefixId.'[acccgv]" value="cgvok"></p>';
						
	
						$content.='<p>'.$this->vklink(false).
						'&nbsp;&nbsp;&nbsp;&nbsp;<A class="fxbutton" href="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'?cHash='.$cHash.'&hnkart_mode=bc1">'.
							$this->pi_getLL("ecprec","[ecprec]").'</a>&nbsp;&nbsp;&nbsp;&nbsp;<INPUT '.$this->pi_classParam("submit-button").' type="submit" name="'.$this->prefixId.'[submit_button]" value="'.$this->pi_getLL("continuer","continuer").'"></p>';
					break;
	
				}
				$content.='</FORM>';
			}
		} // fin si carriole pas vide
		return $content;
	}
	/**
	 * vue mode fiche article
	 */
	function listView($content)	{
		$content.="<H3>".$this->pi_getLL('rubrik')." : ".$GLOBALS[GLOBALS][TSFE]->page['title']."</H3>";
		
		$this->uid = substr(strstr($GLOBALS[GLOBALS][TSFE]->currentRecord,":"),1);

		//debug($this->uid);
		// $this->uid correspond a l'uid du tt_content contenant le plugin

		$lConf = $this->conf["listView."];	// Local settings for the listView function
		//print_r($lConf);
		/* !-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-
		  modif: on ne se met et en mode singleView que si showUid!=" ET SURTOUT que si l'uid tt_content pass? correspond
		// au courant
		// voir aussi utilisation du dernier argument optionel de la la fonction
		pi_list_linkSingle qui permet de passer un tableau de hachage contenant autant d'arguments suppl?entaires que l'on veut
 		!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-
		*/
		if ($this->piVars["showUid"] && $this->piVars["ttc_uid"]==$this->uid)	{	// If a single element should be displayed:
			$this->internal["currentTable"] = "tx_dlcubehnshop_articles";
			$this->internal["currentRow"] = $this->pi_getRecord("tx_dlcubehnshop_articles",$this->piVars["showUid"]);

			$content .= $this->singleView($content,$conf);
			return $content;
		} else  {
			$fullTable=$content;
			if (!isset($this->piVars["pointer"]))	$this->piVars["pointer"]=0;
			if (!isset($this->piVars["mode"]))	$this->piVars["mode"]=1;

				// Initializing the query parameters:
			list($this->internal["orderBy"],$this->internal["descFlag"]) = explode(":",$this->piVars["sort"]);
			// si aucun classement specifie, par d?aut on classe par date d?roissante
			if ($this->internal["orderBy"]=="") {
				$this->internal["orderBy"]="title";
				//$this->internal["descFlag"]=($this->mod_vignette ? "" : "1");
				}
			//debug ($this->piVars["sort"]);
			$this->internal["results_at_a_time"]=t3lib_div::intInRange( $lConf["results_at_a_time"],0,1000,3);		// Number of results to show in a listing.
			$this->internal["maxPages"]=t3lib_div::intInRange($lConf["maxPages"],0,1000,3);		// The maximum number of "pages" in the browse-box: "Page 1", "Page 2", etc.
			$this->internal["orderByList"]="title,crdate";


			$query = $this->pi_list_query("tx_dlcubehnshop_articles",1);

			$res = mysql(TYPO3_db,$query);
			if (mysql_error())	debug(array(mysql_error(),$query));
			
			list($this->internal["res_count"]) = mysql_fetch_row($res);
			if ($this->internal["res_count"]>0) {
				// Make listing query, pass query to MySQL:
				//$query = $this->pi_list_query("tx_dlcubehnshop_articles",0,"","","","ORDER BY crdate DESC, title ASC");
				$query = $this->pi_list_query("tx_dlcubehnshop_articles");
				$res = mysql(TYPO3_db,$query);
				if (mysql_error())	debug(array(mysql_error(),$query));
				$this->internal["currentTable"] = "tx_dlcubehnshop_articles";
				unset ($_SESSION['art_table']);
				$fullTable.=$this->pi_list_makelist($res);

				// Adds the result browser, seulement s'il y a assez de r?ultats
				if ($this->internal["res_count"]>$this->internal["results_at_a_time"]) $fullTable.=$this->pi_list_browseresults();
				
			} // fin si il y a des r?ultats
			else $fullTable.=$this->pi_getLL("no_articles","[no_docs]");
			$fullTable.=$this->vklink(); // lien vers cariole
				// Returns the content from the plugin.
			return $fullTable;
		}
	}
	
	/**
	 * mode vue unique (1 seul doc)
	 */
	function singleView($content)	{
		$this->getPersInfos(); // sert si le gonze veut recommander à un ami...
			// This sets the title of the page for use in indexed search results:
		if ($this->internal["currentRow"]["title"])	$GLOBALS["TSFE"]->indexedDocTitle=$this->internal["currentRow"]["title"];
		//print_r($_SESSION['art_table']);
		if (is_array($_SESSION['art_table'])) {
			foreach($_SESSION['art_table'] as $i=>$v) {
				if ($v==$this->getFieldContent("uid")) $ci=$i;
			}
		}
		//echo $ci;
		$this->ImgMwidth=$this->conf["Img2MaxWidth"];
		$cHash = md5(time().$this->internal["currentRow"]["title"]);
		$content='<A name="Anc'.$this->getFieldContent("uid").'></A>';
		$content.='<DIV class="txvm19docs_single">';
		$content.=$this->RetEntete($this->internal["currentRow"]["title"]);
		//	<H2><img src="'.$this->conf["iconDir"].'picto_documents.gif" align="middle">&nbsp;&nbsp;'.$this->internal["currentRow"]["title"].'</H2>
		$content.='<div>'.$this->getFieldLine("img2").
				$this->getFieldLine("descdetail").
				'</div><p>&nbsp;</p><div style="align:center;border:1px solid; width:300px">'.
				$this->getFieldLine("ref").
				$this->getFieldLine("price").
				$this->getFieldLine("auteur").
				$this->getFieldLine("parut").
				$this->getFieldLine("support").
				$this->getFieldLine("nbpages").
				'</div>'.
				'<br/><br/>';
				if ($_SESSION['art_table'][$ci +1] !="") 
					$content.=$this->pi_list_linkSingle($this->pi_getLL("art_suiv","[art_suiv]"),$_SESSION['art_table'][$ci +1]);
				if ($_SESSION['art_table'][$ci -1] !="") 
					$content.=$this->pi_list_linkSingle($this->pi_getLL("art_prec","[art_prec]"),$_SESSION['art_table'][$ci -1]);	

				//<P'.$this->pi_classParam("singleViewField-document").'><strong>'.$this->getFieldHeader("document").':</strong> '.$this->getFieldContent("document").'</P>';

		$content.='<p align="center"><span class="picto">'.str_replace('"><img',"#Anc".$this->getFieldContent("uid").'"><img',$this->pi_list_linkSingle($this->pi_getLL("go_back","[go_back]"),0)).'</span> &nbsp;&nbsp;&nbsp;&nbsp;';
		$content.=$this->vklink(); // lien vers cariole
		$content.=' &nbsp;&nbsp;&nbsp;&nbsp;<A class="fxbutton" href="#sendaf" onclick="document.getElementById(\'sendafriend\').style.display=\'block\'">'.$this->pi_getLL("recomm","[recomm]").'</a>';
		print_r($_REQUEST[$this->prefixId]);
		$content.='</p></DIV>'.$this->pi_getEditPanel();
		$content.='<FORM action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'#sendaf" name="'.$this->prefixId.'fname" method="GET">';
		// calcul des valeurs des champs, et test validite
		$error=false;
		$yourname=($_REQUEST[$this->prefixId]['yourname']!= "" ? $_REQUEST[$this->prefixId]['yourname'] : $this->personne["findPersonneByLoginReturn"]["prenom"]." ".$this->personne["findPersonneByLoginReturn"]["nom"]);
		if ($_REQUEST[$this->prefixId]['fsent'] && $yourname=="") $error=$this->pi_getLL("errnom");
		$yourmail=($_REQUEST[$this->prefixId]['yourmail'] != "" ? $_REQUEST[$this->prefixId]['yourmail'] : $this->personne["findPersonneByLoginReturn"]["coordonnees"]["email"]);
		if ($_REQUEST[$this->prefixId]['fsent'] && (!VerifAdMail($yourmail) || $yourmail=="")) $error.="<br/> ".$this->pi_getLL("errmail");
		if ($_REQUEST[$this->prefixId]['fsent'] && (!VerifAdMail($_REQUEST[$this->prefixId]['mail2send']) || $_REQUEST[$this->prefixId]['mail2send']=="")) $error.="<br/>".$this->pi_getLL("errmail2send");
		
		$content.='<a name="sendaf"></a><div id="sendafriend" style="display:'.($_REQUEST[$this->prefixId]['fsent'] ? "block" : "none").'"><H3>'.$this->pi_getLL("recomm","[recomm]").' : </h3>';
			
		if ($error) {
			$content.='<div style="color:red">'.$error.'</div>';
		} 
		if ($_REQUEST[$this->prefixId]['fsent']=="" || $error) {
			$content.='<input type="hidden" name="'.$this->prefixId.'[fsent]" value="true">
			<input type="hidden" name="'.$this->prefixId.'[showUid]" value="'.$this->getFieldContent("uid").'">
			<input type="hidden" name="'.$this->prefixId.'[ttc_uid]" value="'.$this->uid.'">
			<input type="hidden" name="'.$this->prefixId.'[pointer]" value="'.$_REQUEST[$this->prefixId.'[pointer]'].'">
			<input type="hidden" name="cHash" value="'.$cHash.'">
			<table><tr><td>'.$this->pi_getLL("yourname","[yourname]").' *</td><td><input type="text" name="'.$this->prefixId.'[yourname]" size="30" value="'.$yourname.'"/></td></tr>
			<tr><td>'.
			$this->pi_getLL("yourmail","[yourmail]").' *</td><td><input type="text" name="'.$this->prefixId.'[yourmail]" size="30" value="'.$yourmail.'"/></td></tr>
			<tr><td>'.
			$this->pi_getLL("mail2send","[mail2send]").' *<br/><small>'.
			$this->pi_getLL("mail2sendcomm","[mail2sendcomm]").'</small></td><td> <input type="text" name="'.$this->prefixId.'[mail2send]" size="30" value="'.$_REQUEST[$this->prefixId]['mail2send'].'"/></td></tr>
			<tr><td>'.
			$this->pi_getLL("comment","[comment]").'</td><td><textarea name="'.$this->prefixId.'[comment]" cols="30" rows="5">'.$_REQUEST[$this->prefixId]['comment'].'</textarea></td></tr><tr><td>
			* <span class="smallred">'.$this->pi_getLL("obligchp","[obligchp]").'</span>
			</td><td><INPUT '.$this->pi_classParam("submit-button").' type="submit" name="'.$this->prefixId.'[submit_button]" value="'.$this->pi_getLL("envoyer","Envoyer").'"></td></tr></table>';
		} else {
			// marche pas car trimballe TOUS les variables GET !!
			//$mylink=$this->pi_list_linkSingle("produit suivant",$this->getFieldContent("uid"));
			$params[$this->prefixId.'[showUid]']=$this->getFieldContent("uid");
			$params[$this->prefixId.'[ttc_uid]']=$this->uid;
			$mylink="http://".$_SERVER['SERVER_NAME'].str_replace("index.php","",$_SERVER['PHP_SELF']);
			$mylink=str_replace('href="','href="'.$mylink,$this->pi_linkTP ("produit suivant", $params));
			
			$mailcont='Bonjour,<br/><br/>

	Un de vos amis, '.$_REQUEST[$this->prefixId]['yourname'].' ('.$_REQUEST[$this->prefixId]['yourmail'].'), vous a transmis ce message pour vous recommander le '.$mylink.' sur le site des Haras nationaux.';
			
			if ($_REQUEST[$this->prefixId]['comment']!="") 
				$mailcont.='
				
				<br/><br/>Il a ajoute ce commentaire : <br/>
	'.$_REQUEST[$this->prefixId]['comment'];
			
			$content.='Le message suivant a ete envoye a '.$_REQUEST[$this->prefixId]['mail2send'].":<br/><i>";
			$content.=$mailcont;
			$content.='</i>';
			mail_html($_REQUEST[$this->prefixId]['mail2send'], "Message de "
			.$_REQUEST[$this->prefixId]['yourname'] , $mailcont,  $_REQUEST[$this->prefixId]['yourmail']);
			
		}	
		$content.='</div></form>';

	

		return $content;
	}
// fonction qui retourne une ligne compl?e, si le champ n'est pas vide, avec titre du champ et valeur avec traitemenst ?entuels
	function getFieldLine($fN) {
		// n'affiche que si le champ est non vide et <>0
		$valret="";

		if ($fN=="author" || ($this->internal["currentRow"][$fN]!="" && $this->internal["currentRow"][$fN]!="0") || $fN=="imagette") { //&& $this->getFieldContent($fN)!="0"
			switch ($fN) {
				case "img2":
				case "img1":
					$imgsrc=$this->retImagette($this->getFieldContent($fN),$this->getFieldContent('file'),false);
					
					if (file_exists($this->upload_doc_folder.'/'.$this->getFieldContent("file"))) {
						$valret.='<a href="'.$this->upload_doc_folder.'/'.$this->getFieldContent("file").'" target="_blank">'.$imgsrc.'</a>';
						}
					else $valret.=$imgsrc;

				break;
				
				case "descdetail":
					$valret= '<P>'.$this->getFieldContent($fN).'</P>';
				break;

				case "lang":
					if ($this->conf["langCodeND"]!=$this->internal["currentRow"][$fN]) $valret= '<P><span class="descDoc">'.$this->getFieldHeader($fN).':</span> '.$this->getFieldContent($fN).'</P>';
					break;

				case "file":
					if (file_exists($this->upload_doc_folder.'/'.$this->getFieldContent($fN))) {
						$valret= '<P><b>'.$this->getFieldHeader($fN).':</b> '.$this->getFieldContent($fN).'&nbsp;<a href="'.$this->upload_doc_folder.'/'.$this->getFieldContent($fN).'" target="_blank"><img src="'.$this->conf["iconDir"].'telecharger.gif" class="picto"  title="'.$this->pi_getLL("download","[download]").'"></a>&nbsp;'.DFSIL($this->upload_doc_folder.'/'.$this->getFieldContent($fN)).'</P>';
					}
				break;
				
	
				default:
					$valret= '<P><b>'.$this->getFieldHeader($fN).':</b> '.$this->getFieldContent($fN).'</P>';
				break;
			}
		}
		return $valret;
	}

	/**
	 * Affichage d'une ligne
	 */
	function pi_list_row($c)	{
		$editPanel = $this->pi_getEditPanel();
		if ($editPanel)	$editPanel="<TD>".$editPanel."</TD>";
		$_SESSION['art_table'][]=$this->internal["currentRow"]["uid"];

		$lConf = $this->conf["listView."];

		$loupe='<img src="'.$this->conf["iconDir"].'loupe_plus.gif" border="0" title="'.$this->pi_getLL("details","[details]").'">';

		/*
		la fonction pi_list_linkSingle poss?e u dernier argument aoptionel qui permet de passer un tableau de hachage contenant autant d'arguments suppl?entaires que l'on veut !
		ici on y passe l'uid du tt_content contenant le plugin de fa?n ?les diff?encier
 		!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-
		*/

		$Margt['ttc_uid']=$this->uid;
		// petites corrections: on passe aussi le pointer, qui indique la position dans les enregistrements, sinon il est perdu
		$Margt['pointer']=$this->piVars["pointer"];
		// ainsi que le classement qui n'?ait pas m?oris?si on passait en singleview
		$Margt['sort']=$this->piVars["sort"];
		
		// asctuce pour rajouter une ancre ?la fin du lien
		$mylink=$this->pi_list_linkSingle("salut_blaireau",$this->internal["currentRow"]["uid"],1,$Margt);
		$mylink=str_replace('">salut_blaireau',"#Anc".$this->internal["currentRow"]["uid"].'">salut_blaireau',$mylink);
		$loupe='&nbsp;&nbsp;'.str_replace("salut_blaireau",$loupe,$mylink).'&nbsp;';
	
		//$loupe=$this->pi_list_linkSingle($loupe,$this->internal["currentRow"]["uid"],1,$Margt).'&nbsp;';	// The "1" means that the display of single items is CACHED! Set to zero to disable caching.

		// la puce par d?aut, c'est tux
		$puce='<img src="'.$this->conf["iconDir"].'tux.gif" border="0" title="'.$this->pi_getLL("other_doc","[other_doc]").'">';
		$this->ImgMwidth=$this->conf["Img1MaxWidth"];
		$puce=$this->retImagette($this->getFieldContent('img1'),$this->getFieldContent('file'),false);
		$file_name=$this->getFieldContent("file");
		$CfN=$this->upload_doc_folder.'/'.$file_name;
		// test existance document
		if (file_exists($CfN) && $file_name!="" ) {
				// Plus que l'icone
				$puce=$this->getFileIconHTML($file_name,$file_name);
				$CDocN='&nbsp;<a href="'.$CfN.'" target="_blank"><img src="'.$this->conf["iconDir"].'telecharger.gif" border="0" title="'.$this->pi_getLL("download","[download]").'"></a>&nbsp;'.DFSIL($CfN);
				}
	// l'ancre sert a ce qu'au retour d'une loupe on puisse repointer au m�e endroit..
		return '<a name="Anc'.$this->getFieldContent("uid").'"></a><tr class="'.($c%2 ? 'doclist_rowodd' : 'doclist_roweven').'">
				<td>'.$puce.'</td>
				<td style="text-align:left">'.$this->getFieldContent("support").'<br/>'.$this->getFieldContent("nbpages").'</td>
				<td style="text-align:left">'.str_replace("salut_blaireau",$this->getFieldContent("title"),$mylink).'</td>
				<td style="text-align:left">'.$this->getFieldContent("designation")." (".str_replace("salut_blaireau",$this->pi_getLL("lire_suite","[lire_suite]"),$mylink).')</td>
				<td style="text-align:center">'.$this->getFieldContent("technicaldegree").'</td>
				<td style="text-align:center">'.$this->getFieldContent("parut").'</td>
				<td style="text-align:left">'.$this->getFieldContent("price").'<br/><br/>
				<a href="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'?cHash='.$cHash.'&hnkart_mode=true&add_art='.$this->internal["currentRow"]["uid"].'" title="'.$this->pi_getLL("add2kart","[add2kart]").'"><img src="'.$this->conf["iconDir"].'cariole.gif"/></a>
				</td>
			</tr>';
	}
	/**
	 * Generer l'ent�e du tableau

	 */
	function pi_list_header()	{
		return '<tr class="docTableHead">
		 		<td>&nbsp;</td>
				<td width="100" align="left">'.$this->getFieldHeader_sortLink("support").'</td>
				<td width="200" align="left">'.$this->getFieldHeader_sortLink("title").'</td>
				<td width="200" align="left">'.$this->getFieldHeader("designation").'</td>
				<td align="center">'.$this->getFieldHeader_sortLink("technicaldegree").'</td>
				<td align="left">'.$this->getFieldHeader("parut").'</td>
				<td align="left">'.$this->getFieldHeader_sortLink("price").'</td>
			</tr>';
	}
	/**
	 * [Put your description here]
	 */
	function getFieldContent($fN,$mode="L")	{
		switch($fN) {
			case "uid":
				return $this->internal["currentRow"]["uid"];	
			break;
			
			case "editor":
			case "support":
				return txRecupLib("tx_dlcubehnshop_".$fN."s","uid","name",$this->internal["currentRow"][$fN]);
			break;
			
			case "price":
				return $this->internal["currentRow"][$fN]."&nbsp;&#8364;";
			break;
			
			case "tva":
				return $this->internal["currentRow"][$fN]."&nbsp;&#037;";
			break;
			
			case "weight":
				return $this->internal["currentRow"][$fN]." g";
			break;
			
			case "nbpages":
				return ($this->internal["currentRow"][$fN]>0 ? $this->internal["currentRow"][$fN]."&nbsp;pages" : "");
			break;
			
			case "tstamp":
			case "crdate":
			case "endtime":
				return getDateF($this->internal["currentRow"][$fN]);
			break;
			
			case "designation":
			case "descdetail":
			//return $this->pi_RTEcssText($this->internal["currentRow"][$fN]);
				/*
				modifications apport?s au traitements de la m?hode getFieldContent($fN)
				la ligne d'origine ci-dessus est g??? automatiquement, mais dans ce cas, les liens (entres autres) ins?? avec le RTE
				ne sont pas affich? dans le FE correctement bien qu'ils soient enregistr?.
				Pour que le changement ci-dessous fonctionne, il faut l'associer ?la directive plac? dans le gabarit principal suivante:
				plugin.tx_macledextensionsansunderscores_pi1.RTEcontent_stdWrap.parseFunc < tt_content.text.20.parseFunc */
				return $this->cObj->stdWrap( $this->pi_RTEcssText($this->internal["currentRow"][$fN]), $this->conf['RTEcontent_stdWrap.']);
			break;

			default:
				return $this->internal["currentRow"][$fN];
			break;
		}
	}
	/**
	 * [Put your description here]
	 */
	function getFieldHeader($fN)	{
				return $this->pi_getLL("listFieldHeader_".$fN,"[".$fN."]");
		}

	/**
	 * [Put your description here]
	 */
	function getFieldHeader_sortLink($fN)	{
	    // on n'affiche plus le nom du champ en lien mais une ic?e de classement
		// pour les tris, attention que le champ de tri soit list?dans la prorpri??d?lar? ci-dessus
		//			$this->internal["orderByList"]="title,tstamp";

		//return $this->pi_linkTP_keepPIvars($this->getFieldHeader($fN),array("sort"=>$fN.":".($this->internal["descFlag"]?0:1)));
		return $this->getFieldHeader($fN).'&nbsp;&nbsp;'.$this->pi_linkTP_keepPIvars('<img src="'.$this->conf["iconDir"].'b_updown.gif" align="middle" border="0" title="'.$this->pi_getLL("order_records","[order_records]").$this->getFieldHeader($fN).'">',array("sort"=>$fN.":".($this->internal["descFlag"]?0:1)));
	}

/** affiche imagette ou apercu pdf ... ou rien/image pr??inie **/

    	function retImagette($imagette="",$doc="",$disp_imggen=false,$classImg="imgDoc") {
		if ($imagette!="" && file_exists($this->upload_doc_folder.'/'.$imagette)) {
			//$size=getimagesize($this->upload_doc_folder.'/'.$this->getFieldContent($fN));
			$img = t3lib_div::makeInstance('t3lib_stdGraphic');
			//$img->mayScaleUp = 1;
			$img->init(); // renvoie 1... donc OK
			
			// mettre les images �la bonne largeur cad $this->conf["ImgMaxWidth"]
			$imgInfo = $img->imageMagickConvert($this->upload_doc_folder.'/'.$imagette,'jpg',$this->ImgMwidth,'',"","",'',1);
			
			//debug ($imgInfo); // vide
			if ($imgInfo[3]) $valret.='<img src="'.$imgInfo[3].'" class="'.$classImg.'">';

/*			$ConfI["file"] = $this->upload_doc_folder.'/'.$imagette;
			$ConfI["file."]["maxW"] = $this->ImgMwidth;
					echo "coucou";
			
			$valret.= str_replace(">",' class="'.$classImg.'">',$this->cObj->IMAGE($ConfI));*/
			//$valret= '<img src="'.$this->upload_doc_folder.'/'.$this->getFieldContent($fN).'" align="left" border="0">';
		} else {
			if ($doc!="" && file_exists($this->upload_doc_folder.'/'.$doc)) {
				$fI=t3lib_div::split_fileref($this->upload_doc_folder."/".$doc);
				if (strstr("pdf jpeg gif png jpg tiff",strtolower($fI["fileext"]))) { 
					$img = t3lib_div::makeInstance('t3lib_stdGraphic');
					//$img->mayScaleUp = 1;
					$img->init(); // renvoie 1... donc OK
					
					// mettre les images �la bonne largeur cad $this->conf["ImgMaxWidth"]
					$imgInfo = $img->imageMagickConvert($this->upload_doc_folder.'/'.$doc,'jpg',$this->ImgMwidth,'',"","",'',1);
					
					//debug ($imgInfo); // vide
					if ($imgInfo[3]) $valret.='<img src="'.$imgInfo[3].'" class="'.$classImg.'">';
				
				} elseif ($disp_imggen) {
					$valret='<img src="'.$this->conf['imggen'].'" class="'.$classImg.'">';
				}
			}	
			
		}
	
		return($valret);	
	}
	
	/*** retourne l'icone d'un fichier **/
	
    function getFileIconHTML($file,$altText) {
//ancienne synatxe: $iconDir = "t3lib/gfx/fileicons"; //
// ces valeurs sont maintenant renseign�s dans le fichier ext_typoscript_setup.txt
		$altIconDir=$this->conf["altIconDir"];
		$defaultIcon=$this->conf["defaultIcon"];
		$iconDir = $this->conf["iconDir"];

		$fI=t3lib_div::split_fileref($this->params["path"]."/".$file);
        if (file_exists($iconDir."/".$fI["fileext"].".gif")) {
            $img = $iconDir."/".$fI["fileext"].".gif";
        } else {
            $img = file_exists($altIconDir."/".$fI["fileext"].".gif") ? $altIconDir."/".$fI["fileext"].".gif" : $defaultIcon;
        }
        return '<IMG src="'.$img.'" border="0" title="'.$altText.'">';
    }

	
	function RetEntete($title="Document") {
		
		// SUR INTRANET ON PRENAIT LE NOM DU DOSSIER SYSTEME, Plus sur Internet
		$DStitle=txRecupLib("pages","uid","title",$this->conf['pidList']);
		
		
		$entete='<H2 class="titreDossier"><img src="'.$this->conf["iconDir"].'picto_documents.gif" class="picto">&nbsp;&nbsp;'.$title.'</H2>';
			
		return($entete);
	}

/**
	 * gerene un lien vers la cariole s'il y a 
	 */
	function vklink($br=true) {
		if (!empty($_SESSION['hn_kart'])) return (($br ? '<br/>': '').'<A HREF="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'?cHash='.$cHash.'&hnkart_mode=true" title="'.$this->pi_getLL("view_kart","[view_cart]").count($_SESSION['hn_kart']).$this->pi_getLL("view_kart2","[view_cart2]").'"><img src="'.$this->conf["iconDir"].'cariole_big.gif" class="picto"></a>');
	}
	
	/** recupere les infos de la personne par WS */
	
	function getPersInfos() {
		if (isset($_SESSION["portalId"]) && $_SESSION["portalId"]!="" && !$this->modtestPID) {
			$userId = $_SESSION["portalId"];
			//$userId ="etalonnier";//pignol";//"etalonnier";//"faible";
	
			$param[] = array(
			"login"=>$userId,
			"ctx"=> null);
			$ws = new WebservicesCompte($this->typeExecution);
			if(!$ws->connectIdent()){
				$content="ERROR:".$ws->getErrorMessage();
				$content = "L'espace priv&eacute; est momentan&eacute;ment indisponible, veuillez nous excuser de ce d&eacute;sagr&eacute;ment.";
				return $content;
			}
	
			$this->personne = $ws->getPersonneByLogin($param);
			/*
			$markerArray["###TITRE###"]=$this->personne["findPersonneByLoginReturn"]["titre"];
		$markerArray["###PRENOM###"]=$this->personne["findPersonneByLoginReturn"]["prenom"];
		$markerArray["###NOM###"]=$this->personne["findPersonneByLoginReturn"]["nom"];
		$markerArray["###ADRESSE###"]=$this->personne["findPersonneByLoginReturn"]["adresse"]["adresse"];
		$markerArray["###COMPLEMENT_ADRESSE###"]=$this->personne["findPersonneByLoginReturn"]["adresse"]["complementAdresse"].";
		$markerArray["###CP###"]=$this->personne["findPersonneByLoginReturn"]["adresse"]["commune"]["codePostal"];
		$markerArray["###VILLE###"]=$this->personne["findPersonneByLoginReturn"]["adresse"]["commune"]["libelle"];
		$markerArray["###EMAIL###"]=$this->personne["findPersonneByLoginReturn"]["coordonnees"]["email"];
			print_r($this->personne);
			*/
		} else {
			$this->personne=array();
			if ($this->modtestPID) {
				$_SESSION["portalId"]="vmaury";
				$this->personne["findPersonneByLoginReturn"]["prenom"]="Vincent";
				$this->personne["findPersonneByLoginReturn"]["nom"]="MAURY";
				$this->personne["findPersonneByLoginReturn"]["adresse"]["adresse"]="7 rue de l'abbatiale";
				$this->personne["findPersonneByLoginReturn"]["adresse"]["complementAdresse"]="Le bourg d'arnac";
				$this->personne["findPersonneByLoginReturn"]["adresse"]["commune"]["codePostal"]="19230";
				$this->personne["findPersonneByLoginReturn"]["adresse"]["commune"]["libelle"]="POMPADOUR";
				$this->personne["findPersonneByLoginReturn"]["coordonnees"]["email"]="nospam19@arnac.net";
			}
		}
	}
} // fin def de classe


if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_shop/pi1/class.tx_dlcubehnshop_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_shop/pi1/class.tx_dlcubehnshop_pi1.php"]);
}

?>