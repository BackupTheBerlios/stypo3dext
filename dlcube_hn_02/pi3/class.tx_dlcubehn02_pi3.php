<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004 Vincent (admin, celui à la pioche) (webtech@haras-nationaux.fr)
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
 * Plugin 'etalons_nouveaux' for the 'dlcube_hn_02' extension.
 *
 * @author	Vincent (admin, celui à la pioche) <webtech@haras-nationaux.fr>
 */


require_once(PATH_tslib."class.tslib_pibase.php");
include_once("typo3conf/ext/dlcube_hn_01/class.GeoHelper.php");
include_once("typo3conf/ext/dlcube_hn_01/class.WebservicesAccess.php");

class tx_dlcubehn02_pi3 extends tslib_pibase {
	var $prefixId = "tx_dlcubehn02_pi3";		// Same as class name
	var $scriptRelPath = "pi3/class.tx_dlcubehn02_pi3.php";	// Path to this script relative to the extension dir.
	var $extKey = "dlcube_hn_02";	// The extension key.
	var $urlPhoto;
	var $urlFiches;

	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->urlPhoto = "uploads/tx_dlcubehn02/fichesen/photos/";
		$this->urlFiches = "uploads/tx_dlcubehn02/fichesen/";

		/*$content='
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
		';*/
		$content = $this->getListeResult();
		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Methode de recherche qui retourne une liste de réponses.
	 * 1. Si la liste est dans la session alors le système retourne une plage de la liste
	 * 2. Si la liste ne se trouve pas dans la session, le système fait appel au webservice et calcul la liste
	 * @return String
	 */
	function getListeResult(){
		$result = null;
		$content='<div><h2>'.htmlspecialchars($this->pi_getLL("titre_list")).'</h2>';
		$param = array();
		if(!isset($_SESSION["RESULT_NEW"])){
			$ws = new WebservicesAccess("dev_ext");
			if(!$ws->connect()) return "erreur a la connexion:".$ws->getErrorMessage();

			$objTransfert = new ObjectTransfertWS();
			$objTransfert->setKey("flagNouveau");
			$objTransfert->setValue("O");
			$param[count($param)] = $objTransfert;

			$result = $ws->getNouveauxEtalons($param);
			if(!$result && $ws->getErrorMessage() != "" ) return "erreur dans resultat:".$ws->getErrorMessage();
			$_SESSION["RESULT_NEW"] = $result;
		} else {
			$result = $_SESSION["RESULT_NEW"];
		}
		if(!$result){
			$content .="<div style='color:red;font-size:9px;text-align:center'>".$this->pi_getLL("libelle_no_result")."</div>";
			return $content;
		}

		if(count($result)==1 && isset($result[0]["numError"])){
			$content .="<div style='color:red;font-size:9px;text-align:center'>".$this->pi_getLL("libelle_error_count")."</div>";
			return $content;
		}

		$content.='
		<table border="0" cellpadding="0" cellspacing="0" border="0">';
		foreach($result as $etalon){
			if($count==0){
				$content.='';
			}
			$img = false;
			if (file_exists($this->urlPhoto.$etalon["codeCheval"].'.jpg')) $img = true;

				$content.='<tr valign="top"><td valign="top" width="600px" style="border:solid 1px #C1131E;padding:15px;">
					<table border="0" cellspacing="0" cellpadding="0" class="imgtext-nowrap">
						<tr>
							<td valign="top">';
								if($img){
								$content.='
								<table width="111" border="0" cellspacing="0" cellpadding="0" class="imgtext-table">
									<tr>
										<td colspan="1">
											<img src="clear.gif" width="100" height="1" alt="" />
										</td>
										<td rowspan="2" valign="top">
											<img src="clear.gif" width="10" height="1" alt="" title="" />
										</td>
									</tr>
									<tr>
										<td valign="top">
											<a href="#" onclick="vHWin=window.open(\''.$this->urlPhoto.$etalon["codeCheval"].'.jpg\',\'FEopenLink\',\'width=400,height=300\');vHWin.focus();return false;">
												<img src="'.$this->urlPhoto.$etalon["codeCheval"].'.jpg" width="100" height="82" border="0" align="top" alt="" title="" />
											</a>
											<br />
										</td>
									</tr>
								  </table>';
								}
							$content.='
							</td>
							<td valign="top">';
								if(!$img)
									$content.='<h3 style="margin-left:20px">'.$etalon["nomCheval"].'</h3>';
								else
									$content.='<h3>'.$etalon["nomCheval"].'</h3>';
								$content .='
								<p>'.$etalon["raceLibelle"];
								if($etalon["pourcentageSangArabe"]>0){
									$content .=', '. $etalon["pourcentageSangArabe"].'% arabe';
								}
								if($etalon["robeLibelle"] != ""){
									$content .=', '. $etalon["robeLibelle"];
								}
								$content .=' '.$etalon["anneeNaissanceCheval"].' '.$this->pi_getLL("libelle_par").' '.$etalon["nomChevalPere"].' '.$this->pi_getLL("libelle_et").' '.$etalon["nomChevalMere"].' '. $this->pi_getLL("libelle_par").' '.$etalon["nomPereChevalMere"].'</p>
								<p>&nbsp;</p>
								<p>&nbsp;</p>
							</td>
					</tr>
				</table>';
				if($etalon["proprietaire"] != ""){
					$content .='<p>'.htmlspecialchars($this->pi_getLL("libelle_proprietaire")).' : '.$etalon["proprietaire"].' </p>';
				}
				if($etalon["libelleCentreTechnique"]==""){
					$content .=htmlspecialchars($this->pi_getLL("libelle_attente_affectation")).'<br>';
				} else {
					$content .='<p>'.htmlspecialchars($this->pi_getLL("libelle_station")).' : '.$etalon["libelleCentreTechnique"].'</p>';
				}

				$content .='<p>'.htmlspecialchars($this->pi_getLL("libelle_prix_saut")).' : ';
				$listPrix = $etalon["prix"];
				if(is_array($listPrix) && count($listPrix)>0){
					foreach($listPrix as $prix)
						$content .= $this->pi_getLL("libelle_prix_saut_".$prix["codeTypeProduit"]).' '.$prix["prix"].' euros<br>';
				}
				$content .='</p>';
				if($etalon["pointFort1"]!= "" || $etalon["pointFort2"] != "" || $etalon["pointFort3"]!=""){
				$content .='<h4>'.htmlspecialchars($this->pi_getLL("libelle_3points_fort")).' :</h4>
				<p>';
				$content .=$etalon["pointFort1"];
				if($etalon["pointFort2"] != "")$content .=", ".$etalon["pointFort2"];
				if($etalon["pointFort2"] != "")$content .=", ".$etalon["pointFort3"];
				$content .='</p>';
				}
				if($etalon["commentaire"] != ""){
					$content .='<h4>'. htmlspecialchars($this->pi_getLL("libelle_aretenir")).':</h4>';
					$content .='<p>'.$etalon["commentaire"].'</p>';
				}
				if($etalon["conseilCroisement"] != ""){
					$content .='<h4>'. htmlspecialchars($this->pi_getLL("libelle_conseil_croisement")).':</h4>';
					$content .='<p>'.$etalon["conseilCroisement"].'</p>';
				}
				$content .='<p>&nbsp;</p>';
				$content .='
				<table border="0" cellspacing="0" cellpadding="0" class="imgtext-nowrap">
					<tr>
						<td valign="top"></td>
						<td valign="top">
							<table width="100%" border="0" cellspacing="0" cellpadding="0" class="imgtext-table">
								<tr>
									<td valign="top">
										<img src="clear.gif" width="10" height="1" alt="" title="" />
									</td>
									<td colspan="1">
										<img src="clear.gif" width="20" height="1" alt="" />
									</td>
								</tr>';
								$content .='
								<tr>
									<td valign="top">
										<img src="clear.gif" width="10" height="1" alt="" title="" />
									</td>
									<td valign="top" height="25x">
										<a href="http://www.haras-nationaux.fr/portail/uploads/tx_vm19docsbase/Bulletin_toute_race_sauf_si_tirage_au_sort.doc" style="height:40px;padding-bottom:10px;padding-left:25px;background-repeat: no-repeat;background-image:url(fileadmin/templates/images/elts_reccurents/valid1.gif);">'.htmlspecialchars($this->pi_getLL("libelle_reservation_saillie")).'</a>
									</td>
								</tr>';
								if($etalon["urlVideo"] != ""){
								$content .='
								<tr>
									<td valign="top">
										<img src="clear.gif" width="10" height="1" alt="" title="" />
									</td>
									<td valign="top" height="25x">
										<a href="#" onclick="vHWin=window.open(\''.$etalon["urlVideo"].'\',\'FEopenLink\',\'width=400,height=300\');vHWin.focus();return false;" style="height:40px;padding-bottom:10px;padding-left:25px;background-repeat: no-repeat;background-image:url(fileadmin/templates/images/elts_reccurents/video.gif);">'.htmlspecialchars($this->pi_getLL("libelle_voir_video")).'</a>
									</td>
								</tr>';
								}
								$content .='
								<tr>
									<td valign="top">
										<img src="clear.gif" width="10" height="1" alt="" title="" />
									</td>
									<td valign="top" height="25px">';
										$lien = $this->pi_getPageLink(2973,'',array("tx_dlcubehn02_pi2[showUid]"=>$etalon["codeCheval"],"no_cache"=>1));
										$content .='<a href="'.$lien.'" style="height:40px;padding-bottom:10px;padding-left:25px;background-repeat: no-repeat;background-image:url(fileadmin/templates/images/elts_reccurents/detail.gif);">'.htmlspecialchars($this->pi_getLL("libelle_savoir_plus")).'</a>';
									$content .='<br/>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td></tr><tr><td><br></td></tr>';
			if($count==1){
				$count=0;
			} else $count=1;
		}
		$content.='</table></div>';
		return $content;
	}
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_02/pi3/class.tx_dlcubehn02_pi3.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_02/pi3/class.tx_dlcubehn02_pi3.php"]);
}

?>