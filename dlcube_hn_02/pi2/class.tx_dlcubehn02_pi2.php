
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
 * Plugin 'etalons' for the 'dlcube_hn_02' extension.
 *
 * @author Guillaume Tessier<gtessier@dlcube.com> 
 */
error_reporting(0);
//error_reporting (E_ALL); // par défaut

require_once(PATH_tslib."class.tslib_pibase.php");
include_once("typo3conf/ext/dlcube_hn_01/class.GeoHelper.php");
include_once("typo3conf/ext/dlcube_hn_01/class.WebservicesAccess.php");

class tx_dlcubehn02_pi2 extends tslib_pibase {
	var $prefixId = "tx_dlcubehn02_pi2";		// Same as class name
	var $scriptRelPath = "pi2/class.tx_dlcubehn02_pi2.php";	// Path to this script relative to the extension dir.
	var $extKey = "dlcube_hn_02";// The extension key.
	var $pdf_folder;
	var $fiches_folder;
	var $photos_folder;
	var $geoHelper;
	var $chevalHelper;
	var $urlPDF;
	var $urlINRA;
	var $error;
	/**
	 * [Put your description here]
	 */
	function main($content,$conf){
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->urlPDF = "uploads/tx_dlcubehn02/pdf/";//http://haras-nationaux.jetonline.fr/sirenet_racine/partage/inra/";
		$this->urlINRA = "uploads/tx_dlcubehn02/pdf/NOTE_EXPLICATIVE_INRA.pdf";
		$this->fiches_folder="uploads/tx_dlcubehn02/fichesen/";
		$this->photos_folder="uploads/tx_dlcubehn02/fichesen/photos/";
		
		$this->geoHelper = new GeoHelper();
		$this->chevalHelper = new WebservicesAccess();
		session_start();
		
		
		if(isset($this->piVars["action"])){
			if($this->piVars["action"] == "liste" || $this->piVars["action"] == "newListe"){
				$content=$this->getListeResult();
			}	
		}
		else if(isset($_GET["action"]) && ($_GET["action"]=="liste" || $_GET["action"]=="newListe"))
			$content=$this->getListeResult();
		else if(isset($this->piVars["showUid"]) && $this->piVars["showUid"] != "")
			$content=$this->getDetail();
		else{
			unset($_SESSION["RESULT_ETALON"]);
			$content=$this->getFormulaireVide();
		}
		return $this->pi_wrapInBaseClass($content);
	}
	
	function getFormulaireVide(){
		//print_r($GLOBALS["TSFE"]);
		$filter[] = new ObjectTransfertWS("i18n","FR");
		
		$content='
			<div><h2>'.htmlspecialchars($this->pi_getLL("titre_formulaire")).'</h2></div><BR>'.htmlspecialchars($this->pi_getLL("desc_formulaire")).'
			';
		if(isset($this->error)){
			$content.='<div style="color:red;font-size:13px;text-align:center">'.$this->error.'</div>';
		}
		
			$content.='<table>
			<form action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'" method="POST">
				<input type="hidden" name="no_cache" value="1">
				<input type="hidden" name="'.$this->prefixId.'[action]" value="newListe">
				<tr>
				<td><p><strong>'.htmlspecialchars($this->pi_getLL("libelle_cheval_race")).'</strong></p></td>
				<td>
					<select name="'.$this->prefixId.'[groupe_race]">
						<option></option>';
						$listRaces = $this->chevalHelper->getAllRaces($filter);
						if(!$listRaces)
							$content.="<option>".$this->chevalHelper->getErrorMessage()."</option>";
						foreach($listRaces as $race){
							$content.="<option value='".$race['codeGroupeRace']."'>".$race['libelleLong']."</option>";
						}
		$content.='
					</select>
				</td>
				</tr>
				<tr>
				<td><p><strong>'.htmlspecialchars($this->pi_getLL("libelle_cheval_nom")).'</strong></p></td>
				<td>
					<input type="text" name="'.$this->prefixId.'[nomcheval]" value="">
				</td>
				</tr>
				<tr>
				<td><p><strong>'.htmlspecialchars($this->pi_getLL("libelle_code_gen")).'<br/>'.htmlspecialchars($this->pi_getLL("libelle_code_gen_plus")).'</strong></p></td>
				<td>
					<select name="'.$this->prefixId.'[code_gen]">
						<option></option>
						<option value="ELI">Elite</option>
						<option value="TB">Tr&egrave;s bon</option>
						<option value="AME">Am&eacute;liorateur</option>
						<option value="ACC">Acceptable</option>
					</select>
				</td>
				</tr>
				<tr><td colspan="2"><hr></td></tr>
				<tr>
				<td colspan="2"><H3>'.htmlspecialchars($this->pi_getLL("titre_form_geo")).'</H3></td>
				</tr>
				<tr>
				<td colspan="2">'.htmlspecialchars($this->pi_getLL("desc_form_geo")).'</td>
				</tr>
				<tr>
				<td><p><strong>'.htmlspecialchars($this->pi_getLL("libelle_region")).'</strong></p></td>
				<td>
					<select name="'.$this->prefixId.'[region]">
						<option></option>';
						$listRegions = $this->geoHelper->getAllRegions();
						foreach($listRegions as $region){
							if($region['codeRegion'] != "ETR")
								$content.="<option value='".$region['codeRegion']."'>".$region['libelleRegion']."</option>";
						}
		$content.='
					</select>
				</td>
				</tr>
				<tr>
				<tr>
				<td><p><strong>'.htmlspecialchars($this->pi_getLL("libelle_departement")).'</strong></p></td>
				<td>
					<select name="'.$this->prefixId.'[departement]">
						<option></option>';
						$listDepartements = $this->geoHelper->getAllDepartements();
						foreach($listDepartements as $departement){
							$content.="<option value='".$departement['codeDepartement']."'>".$departement['libelle']."</option>";
						}
						
				$content.='
					</select>
				</td>
				</tr>
				<tr>
				<td><p><strong>'.htmlspecialchars($this->pi_getLL("libelle_dispo_IAC")).'</strong></p></td>
				<td>
					<input type="checkbox" name="'.$this->prefixId.'[iac]" value="O">
				</td>
				</tr>
				<tr><td colspan="2"><hr></td></tr>
				<tr>
				<td><p><strong>'.htmlspecialchars($this->pi_getLL("libelle_qualif_loisir")).'</strong></p></td>
				<td>
					<input type="checkbox" name="'.$this->prefixId.'[qualif_loisir]" value="O">
				</td>
				</tr>
				<tr>
				<td><p><strong>'.htmlspecialchars($this->pi_getLL("libelle_qualif_dressage")).'</strong></p></td>
				<td>
					<input type="checkbox" name="'.$this->prefixId.'[qualif_dressage]" value="O">
				</td>
				</tr>
				<tr>
				<td colspan="2" align="right">
					<input class="bouton" type="submit" name="'.$this->prefixId.'[submit_button]" value="'.htmlspecialchars($this->pi_getLL("submit_button_label")).'">
				</td>
				</tr>
			</form>
			</table>
		';
		return $content;
	}
	
	/**
	 * Methode de gestion de la fiche du cheval
	 * @return String
	 */
	function getDetail(){
		$ws = new WebservicesAccess();
		$content='<div><h2>'.htmlspecialchars($this->pi_getLL("titre_fiche")).'</h2></div><BR>';
		$objTransfert = new ObjectTransfertWS();
		$objTransfert->setKey("codeCheval");
		$objTransfert->setValue($this->piVars["showUid"]);
		
		$filtres[] = $objTransfert;
		$etalon = $ws->getEtalon($filtres);
		
		if($etalon){
			//Header
			$content .='<div style="float: left;">';
			$content .='<div style="float:left;width:330px;display:block;border: solid 1px #BFBFBF;">
				<p>&nbsp;</p>
				<p><h3 style="margin-left:20px"><strong>'. $etalon["nomCheval"].'</strong></h3></p>
				
				<p>'. $etalon["raceLibelle"];
				if($etalon["pourcentageSangArabe"]>0 && ($etalon["codeGroupeRace"]=="AA" || $etalon["codeGroupeRace"]=="PFS" || $etalon["codeGroupeRace"]=="AB")){
					$content .=' - ('. $etalon["pourcentageSangArabe"].'% arabe)';
				}
				if($etalon["robeLibelle"] != ""){
					$content .=' - '. $etalon["robeLibelle"];
				}
				if($etalon["taille"] != ""){
					$content .=' - '. $etalon["taille"];
				}
				$content .=' - '.$this->pi_getLL("libelle_neen").' '.$etalon["anneeNaissanceCheval"].'</p>
				<br>
				<p>'. $this->pi_getLL("libelle_par").' '.$etalon["nomChevalPere"].' '.$this->pi_getLL("libelle_et").' '.$etalon["nomChevalMere"].' '. $this->pi_getLL("libelle_par").' '.$etalon["nomPereChevalMere"].'</p>
				<br>';
				if($etalon["proprietaire"] != ""){
					$content .=htmlspecialchars($this->pi_getLL("libelle_proprietaire")).' : '.$etalon["proprietaire"].' <br>';
				}
			
				$content .='<div style="float: left;"><ul><li style="display: list-item;list-style-image : url(fileadmin/templates/images/elts_reccurents/detail.gif);list-style-position: outside;"><a href="'.$this->fiches_folder.$etalon["codeCheval"].'.php" target="_blank">'.$this->pi_getLL("libelle_fiche").'</a></li></ul></div><br>';
				
				if($etalon["urlVideo"] != "" ){
					$content .='<div style="float: left;">
					<ul><li style="display: list-item;list-style-image : url(fileadmin/templates/images/elts_reccurents/video.gif);list-style-position: outside;">
					<a href="'.$etalon["urlVideo"].'" target="_blank" style="color:#F18C13">'.htmlspecialchars($this->pi_getLL("libelle_video")).'</a></strong></ul></li></div><br/>';
				}
				$content .='</p>';
				
				if($etalon["iso"] != null || $etalon["icc"] != null || $etalon["idr"] != null || $etalon["itr"] != null ||	
					$etalon["iaa"] != null || $etalon["bso"] != null || $etalon["bcc"] != null || $etalon["bdr"] != null || 
					$etalon["btr"] != null){
					//print_r($etalon);
					if($etalon["codeRace"]=="TF"){
						$content .='<p>&nbsp;<br/><br/></p>
						<h4 style="font-size:12px">'. $this->pi_getLL("libelle_perf").':</h4><p>';
						$content .=($etalon["itr"] != null)?'ITR = '.$etalon["itr"].'('.$etalon["anneeItr"].')<br>':'';
						$content .=($etalon["btr"] != null && $etalon["coefBtr"] > '0.20')?'BTR = '.$etalon["btr"].'('.$etalon["coefBtr"].')<br>':'';
						$content .='</p>';
					} else {
						$content .='<p>&nbsp;<br/><br/></p>
						<h4 style="font-size:12px">'. $this->pi_getLL("libelle_perf").':</h4><p>';
						$content .=($etalon["iso"] != null)?'ISO = '.$etalon["iso"].'('.$etalon["anneeIso"].')<br>':'';
						$content .=($etalon["icc"] != null)?'ICC = '.$etalon["icc"].'('.$etalon["anneeIcc"].')<br>':'';
						$content .=($etalon["idr"] != null)?'IDR = '.$etalon["idr"].'('.$etalon["anneeIdr"].')<br>':'';
						$content .=($etalon["itr"] != null)?'ITR = '.$etalon["itr"].'('.$etalon["anneeItr"].')<br>':'';
						$content .=($etalon["iaa"] != null)?'IAA = '.$etalon["iaa"].'('.$etalon["anneeIaa"].')<br>':'';
						$content .=($etalon["bso"] != null && $etalon["coefBso"] > '0.20')?'BSO = '.$etalon["bso"].'('.$etalon["coefBso"].')<br>':'';
						$content .=($etalon["bcc"] != null && $etalon["coefBcc"] > '0.20')?'BCC = '.$etalon["bcc"].'('.$etalon["coefBcc"].')<br>':'';
						$content .=($etalon["bdr"] != null && $etalon["coefBdr"] > '0.20')?'BDR = '.$etalon["bdr"].'('.$etalon["coefBdr"].')<br>':'';
						$content .=($etalon["btr"] != null && $etalon["coefBtr"] > '0.20')?'BTR = '.$etalon["btr"].'('.$etalon["coefBtr"].')<br>':'';
						$content .='</p>';
					}
				}
			$content .='</div>';
			//Remplacer l'url par une ressource local au deploiement
			
			if(file_exists($this->photos_folder.$etalon["codeCheval"].".jpg")){
				$content .='<span style="float:right;display:block;margin-right:1px;font-size:8px>
					<img src="'.$this->photos_folder.$etalon["codeCheval"].'.jpg">
					<br>'.$this->pi_getLL("copyright_hn").'
					</span>';
			}
			$content .='</div>';
			$content .='<div style="float: left;width:730px;"><hr><br/></div>';
			$content .='<div style="float: left;width:730px;">';
			$content .='<span style="float:left;width:330px;display:block;">';
			$content .='<a name="reproduction"></a>
			<h4 style="font-size:12px">'. $this->pi_getLL("libelle_repro").':</h4><p>
			'.$this->pi_getLL("txt_repro").' '.$etalon["anneeReproduction"].'<br>';
			if($etalon["libelleCentreTechnique"] == ""){
				$content .=htmlspecialchars($this->pi_getLL("libelle_attente_affectation")).'<br>';
			} else {
				$content .=$etalon["libelleCentreTechnique"].'<br>';
			}
			$content .=$this->pi_getLL("text_station").'<br><br>';
			$listPrix = $etalon["prix"];
			if(is_array($listPrix) && count($listPrix)>0){
				foreach($listPrix as $prix)
					$content .= $this->pi_getLL("libelle_prix_saut").': '.$this->pi_getLL("libelle_prix_saut_".$prix["codeTypeProduit"]).' '.$prix["prix"].' euros<br>';
			}
			$content .='<br><a target="_blank" href="http://www.haras-nationaux.fr/portail/uploads/tx_vm19docsbase/Bulletin_toute_race_01.doc">'.htmlspecialchars($this->pi_getLL("libelle_reservation_saillie")).'</a>';
			$content .='</p></span>';
			$content .='<span style="float:right;width:350px;display:block;">';
			if($etalon["pointFort1"]!= "" || $etalon["pointFort2"] != "" || $etalon["pointFort3"]!=""){
				$content .='<h4 style="font-size:12px">'. htmlspecialchars($this->pi_getLL("libelle_3points_fort")).':</h4>
				<p>';
				$content .=$etalon["pointFort1"];
				if($etalon["pointFort2"] != "")$content .=", ".$etalon["pointFort2"];
				if($etalon["pointFort2"] != "")$content .=", ".$etalon["pointFort3"];
				$content .='</p>';
			}
//			print_r($etalon);
			if($etalon["commentaire"] != ""){
				$content .='<h4 style="font-size:12px">'. htmlspecialchars($this->pi_getLL("libelle_aretenir")).':</h4>';
				$content .='<p>'.utf8_decode($etalon["commentaire"]).'</p>';
			}
			if($etalon["conseilCroisement"] != ""){
				$content .='<h4 style="font-size:12px">'. htmlspecialchars($this->pi_getLL("libelle_conseil_croisement")).':</h4>';
				$content .='<p>'.utf8_decode($etalon["conseilCroisement"]).'</p>';
			}
			$content .='</span>';
			$content .='</div>';
			//Reproductions
			$content .='<div style="float: left;width:730px;"><br/><hr><br/></div>';
			$tab_fiches = array("PAGR"=> null,"PACH"=> null,"TRGR"=> null,"TRCH"=> null,"SAGR"=> null,"SACH"=> null,"COGR"=> null,"COCH"=> null);
			$afficheParagraphe = false;
			foreach($tab_fiches as $key=>$value){
				//echo $this->urlPDF.$etalon["codeCheval"]."_".strtolower($key).".pdf<br>";
				if(file_exists($this->urlPDF.$etalon["codeCheval"]."_".strtolower($key).".pdf")){
					$tab_stat = stat($this->urlPDF.$etalon["codeCheval"]."_".strtolower($key).".pdf");
					$tim_modif = $tab_stat[9];
					$arrayValue = array(
						"key"=>$key,
						"url"=>$this->urlPDF.$etalon["codeCheval"]."_".strtolower($key).".pdf",
						"size"=>round(filesize($this->urlPDF.$etalon["codeCheval"]."_".strtolower($key).".pdf")/1024,2)." ko",
						"dateModif"=>date("d-m-Y",$tim_modif),
					);
					$tab_fiches[$key]=$arrayValue;
					$afficheParagraphe=true;
				}
			}
			
			if($afficheParagraphe){
				$content .='<div style="float: left;width:730px;">
				<a name="test"></a>
				<h4 style="font-size:12px"><strong>'. $this->pi_getLL("libelle_tests").':</strong></h4><br>';
				foreach($tab_fiches as $fiche){
					if(isset($fiche["key"])){
						$content .='<a target=""_blank" href="'.$fiche["url"].'">'.$this->pi_getLL("libelle_pdf_".$fiche["key"]).'</a> ('.$fiche["size"].', modifi&eacute; le '.$fiche["dateModif"].')<br>';
					}
				}
				$content .="<br>".$this->pi_getLL("phrase_compare_inra").'<a target="_blank" href="'.$this->urlINRA.'">'.$this->pi_getLL("labelle_cliquez_ici").'</a>';
				
				$content .='</div>';
			}
			$content .='<div style="float: left;">';
			$content .="<p> <br/> </p>";
			$content .='<a href="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'" id="lienFonctionPetit" style="color:white;text-decoration:none">'.htmlspecialchars($this->pi_getLL("libelle_nouvelle_recherche")).'</a>';
			$content .="<p> <br/> </p>";
			$content .="</div>";
			$content .='<div style="float: right;">';
			$content .="<p> <br/> </p>";
			$content .='<a href="javascript:history.back()" id="lienFonctionPetit" style="color:white;text-decoration:none">'.htmlspecialchars($this->pi_getLL("libelle_retour_liste")).'</a>';
			$content .="<p> <br/>";
			$content .="</div>";
		} else {
			$content .=$ws->getErrorMessage();
		}
		
		return $content;
	}
	
	/**
	 * Methode de recherche qui retourne une liste de r�onses.
	 * 1. Si la liste est dans la session alors le syst�e retourne une plage de la liste
	 * 2. Si la liste ne se trouve pas dans la session, le syst�e fait appel au webservice et calcul la liste	
	 * @return String
	 */
	function getListeResult(){
		$result = null;
		$content='<div><h2>'.htmlspecialchars($this->pi_getLL("titre_list")).'</h2></div><BR>';
		$param = array();
		
		if( (isset($this->piVars["action"]) && $this->piVars["action"]=="newListe") || (isset($_GET["action"]) && $_GET["action"]=="liste"))
			unset($_SESSION["RESULT_ETALON"]);
		
		if(!isset($_SESSION["RESULT_ETALON"])){
			if(isset($this->piVars["nomcheval"]) && $this->piVars["nomcheval"] != ""){
				$nomCheval = str_replace("%","",$this->piVars["nomcheval"]);
				if(strlen($nomCheval)<2){
					$this->error = "Le nombre de lettres composant le nom du cheval doit �re sup�ieur ou �al �deux.";
					return $this->getFormulaireVide();
				}
				
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("nomCheval");
				$objTransfert->setValue($this->piVars["nomcheval"]);
				$param[count($param)] = $objTransfert;
			}
			if(isset($_GET["numPerso"]) && $_GET["numPerso"]!=""){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("numPerso");
				$objTransfert->setValue($_GET["numPerso"]);
				$param[count($param)] = $objTransfert;
			}
			if(isset($this->piVars["groupe_race"]) && $this->piVars["groupe_race"] != ""){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("groupeRace");
				$objTransfert->setValue($this->piVars["groupe_race"]);
				$param[count($param)] = $objTransfert;
			}
			if(isset($this->piVars["region"]) && $this->piVars["region"] != ""){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("region");
				$objTransfert->setValue($this->piVars["region"]);
				$param[count($param)] = $objTransfert;
			}
			if(isset($this->piVars["departement"]) && $this->piVars["departement"] != ""){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("departement");
				$objTransfert->setValue($this->piVars["departement"]);
				$param[count($param)] = $objTransfert;
			}
			if(isset($this->piVars["iac"]) && $this->piVars["iac"] == "O"){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("iac");
				$objTransfert->setValue("true");
				$param[count($param)] = $objTransfert;
				
			}
			if(isset($this->piVars["qualif_loisir"]) && $this->piVars["qualif_loisir"] == "O"){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("loisir");
				$objTransfert->setValue("true");
				$param[count($param)] = $objTransfert;
			}
			if(isset($this->piVars["qualif_dressage"]) && $this->piVars["qualif_dressage"] == "O"){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("dressage");
				$objTransfert->setValue("true");
				$param[count($param)] = $objTransfert;
			}
			if(isset($this->piVars["code_gen"]) && $this->piVars["code_gen"] != ""){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("codeGenetique");
				$objTransfert->setValue($this->piVars["code_gen"]);
				$param[count($param)] = $objTransfert;
			}
			
			$ws = new WebservicesAccess();
			if(!$ws->connect()) return "erreur a la connexion:".$ws->getErrorMessage();
			$result = $ws->getEtalons($param);
			if(!$result && $ws->getErrorMessage() != "" ) return "erreur dans resultat:".$ws->getErrorMessage();
			$_SESSION["RESULT_ETALON"] = $result;
		} else {
			$result = $_SESSION["RESULT_ETALON"];
		}
		if(!$result){
			$content .="<div style='color:red;font-size:9px;text-align:center'>".$this->pi_getLL("libelle_no_result")."</div>";
			if(!isset($_GET["action"]))
				$content .=$this->pi_linkToPage(htmlspecialchars($this->pi_getLL("libelle_nouvelle_recherche")),$GLOBALS["TSFE"]->id);
			return $content;
		}

		if(count($result)==1 && isset($result[0]["numError"])){
			$content .="<div style='color:red;font-size:9px;text-align:center'>".$this->pi_getLL("libelle_error_count")."</div>";
			if(!isset($_GET["action"]))
				$content .=$this->pi_linkToPage(htmlspecialchars($this->pi_getLL("libelle_nouvelle_recherche")),$GLOBALS["TSFE"]->id);
			return $content;
		}
		
		$content .= "<h4>".count($result)." ".$this->pi_getLL("libelle_compteur")."</h4><br>";
		
		foreach($result as $etalon){
			$content .="<p><strong>".$this->pi_list_linkSingle($etalon["nomCheval"],$etalon["codeCheval"],1)."</strong>, ".$etalon["raceLibelle"];
			if($etalon["pourcentageSangArabe"] > 0 && ($etalon["codeGroupeRace"]=="AA" || $etalon["codeGroupeRace"]=="PFS" || $etalon["codeGroupeRace"]=="AB"))
				$content .=", ".$etalon["pourcentageSangArabe"]." % arabe";
			if($etalon["robeLibelle"] != "")
				$content .=", ".$etalon["robeLibelle"];
			if($etalon["anneeNaissanceCheval"] != "")
				$content .=", ".$etalon["anneeNaissanceCheval"];
			$content .="<br />
			". $this->pi_getLL("libelle_par")." ".$etalon["nomChevalPere"]." ".$this->pi_getLL("libelle_et")." ".$etalon["nomChevalMere"]." ". $this->pi_getLL("libelle_par")." ".$etalon["nomPereChevalMere"];
			if($etalon["urlVideo"] != "" ){
				$content .='   <a href="'.$etalon["urlVideo"].'" target="_blank"><img src="fileadmin/templates/images/elts_reccurents/video.gif" border="0" title="'.htmlspecialchars($this->pi_getLL("libelle_video")).'"></a>';
			}
			$content .="</p>";
			$content .="<hr>";
		}
		if(!isset($_GET["action"])){
			/*$content .='<span id="boutonLien">';
			$content .=$this->pi_linkToPage(htmlspecialchars($this->pi_getLL("libelle_nouvelle_recherche")),$GLOBALS["TSFE"]->id);
			$content .='</span>';*/
			$content .='<a style="color:white;text-decoration:none" id="lienFonctionPetit" href="index.php?id='.$GLOBALS["TSFE"]->id.'">'.htmlspecialchars($this->pi_getLL("libelle_nouvelle_recherche")).'</a>';
		}
		else
			$content .='<a href="javascript:history.back()" style="color:white;text-decoration:none" id="lienFonctionPetit">'.htmlspecialchars($this->pi_getLL("libelle_retour_liste")).'</a>';
		return $content;
	}
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_02/pi2/class.tx_dlcubehn02_pi2.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_02/pi2/class.tx_dlcubehn02_pi2.php"]);
}

?>
