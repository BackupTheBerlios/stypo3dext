
<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Guillaume Tessier <gtessier@dlcube.com>
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
 * Plugin 'etalons-Ã©talothÃ¨que' for the 'dlcube_hn_02' extension.
 * Cette extension contient les fonctionnalitÃ©s suivantes.
 * 1 - Formulaire de recherches des Ã©talons
 * 2 - Liste des Ã©talons
 * 3 - Fiche Ã©talons
 * 4 - comparatif d'Ã©talons'
 *
 * @author Guillaume Tessier<gtessier@dlcube.com>
 */
//error_reporting (E_ALL); // par dÃ©faut

require_once(PATH_tslib."class.tslib_pibase.php");
include_once("typo3conf/ext/dlcube_hn_01/class.GeoHelper.php");
include_once("typo3conf/ext/dlcube_hn_01/class.WebservicesAccess.php");
include_once("typo3conf/ext/dlcube_hn_01/class.WebservicesCriteres.php");

class tx_dlcubehn02_pi2 extends tslib_pibase {
	var $prefixId = "tx_dlcubehn02_pi2";		// Same as class name
	var $scriptRelPath = "pi2/class.tx_dlcubehn02_pi2.php";	// Path to this script relative to the extension dir.
	var $extKey = "dlcube_hn_02";// The extension key.
	var $pdf_folder;
	var $fiches_folder;
	var $photos_folder;
	var $geoHelper;
	var $chevalHelper;
	var $critHelper;
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
		$this->urlPDF = "uploads/tx_dlcubehn02/pdf/";
		$this->urlINRA = "uploads/tx_dlcubehn02/pdf/NOTE_EXPLICATIVE_INRA.pdf";
		$this->fiches_folder="uploads/tx_dlcubehn02/fichesen/";
		$this->photos_folder="uploads/tx_dlcubehn02/fichesen/photos/";

		$this->geoHelper = new GeoHelper();
		$this->chevalHelper = new WebservicesAccess();
		$this->critHelper = new WebservicesCriteres();
		session_start();


		if(isset($this->piVars["action"])){
			if($this->piVars["action"] == "liste" || $this->piVars["action"] == "newListe"){
				return $this->pi_wrapInBaseClass($this->getListeResult());
			}
		}
		else if(isset($_GET["action"]) && ($_GET["action"]=="liste" || $_GET["action"]=="newListe"))
			return $this->pi_wrapInBaseClass($this->getListeResult());
		else if(isset($_GET["action"]) && ($_GET["action"]=="getListCT")){
			$this->viewListCT();
		}
		if(isset($this->piVars["action"]) && ($this->piVars["action"]=="comparer")){
			return $this->pi_wrapInBaseClass($this->getListComparaison());
		}
		if(isset($this->piVars["showUid"]) && $this->piVars["showUid"] != "")
			return $this->pi_wrapInBaseClass($this->getDetail());


		unset($_SESSION["RESULT_ETALON"]);
		return $this->pi_wrapInBaseClass($this->getFormulaireVide());
	}

	function viewListCT(){
		$param = array();
		$objTransfert = new ObjectTransfertWS();
		$objTransfert->setKey("codeCheval");
		$objTransfert->setValue($_GET["codeCheval"]);
		$param[count($param)] = $objTransfert;
		$wsCt = new WebservicesAccess();
		if($wsCt->connect()){
			// return $ws->getErrorMessage();
			$resultCT = $wsCt->getAllCT4Etalon($param);
			//echo "<div> nombre de ct : ".count($resultCT)."</div>";
			//echo "<div> Error:".$wsCt->getErrorMessage()."</div>";
			//echo "<p>".print_r($resultCT,true)."</p>";
			//if($ws->getErrorMessage()!="")echo "<p>".$ws->getErrorMessage()."</p>";

			foreach($resultCT as $ct){
				echo"<p style='align:left;'>".$ct["nom"]." ".substr($ct["codePostal"],0,2)."</p>";
			}
		}
		else {
			echo "<div> Error:".$wsCt->getErrorMessage()."</div>";
		}
		exit();
	}

	function getScript(){
		$content='
			<script>
					function makeRequest(url) {
	        				var http_request = false;
					        if (window.XMLHttpRequest) { // Mozilla, Safari,...
					            http_request = new XMLHttpRequest();
					            if (http_request.overrideMimeType) {
					                http_request.overrideMimeType(\'text/xml\');
					                // Voir la note ci-dessous à propos de cette ligne
					            }
					        } else if (window.ActiveXObject) { // IE
					            try {
					                http_request = new ActiveXObject("Msxml2.XMLHTTP");
					            } catch (e) {
					                try {
					                    http_request = new ActiveXObject("Microsoft.XMLHTTP");
					                } catch (e) {}
					            }
					        }

					        if (!http_request) {
					            alert(\'Abandon :( Impossible de créer une instance XMLHTTP\');
					            return false;
					        }
					        http_request.onreadystatechange = function() { viewContents(http_request); };
					        http_request.open(\'GET\', url, true);
					        http_request.send(null);
    				}
					function divaffiche(id){
						document.getElementById(id).style.display = "inline";
					}

					function divcache(id){
						document.getElementById(id).style.display = "none";
					}

				    function viewContents(http_request) {
						 if (http_request.readyState == 4) {
						 	document.getElementById("LISTE_DETAIL").style.visibility=\'visible\';
				    		document.getElementById("LISTE_DETAIL").style.display=\'inline\';
				            if (http_request.status == 200) {
				            	//alert(http_request.responseText);
				                document.getElementById("LISTE_DETAIL").innerHTML=http_request.responseText;
								document.getElementById("LISTE_CT").style.visibility=\'visible\';
				    			document.getElementById("LISTE_CT").style.display=\'block\';
				            } else {
				                document.getElementById("LISTE_DETAIL").innerHTML="Un problème est survenu avec la requête.";
				            }
				        }
				    }
					function getItemInList(list){
						return list.value;
					}
					function checkMaxCmp(){
						var count =0;
						for(var i=0; i<document.formCmp.length;i++){
							if(document.formCmp[i]["name"]=="cmp[]")
								if(document.formCmp[i]["checked"]) count++;
						}
						if(count < 2) alert("Vous devez sélectionner au minimum 2 étalons");
						else if(count > 5) alert("Vous pouvez sélectionner au maximum 5 étalons");
						else document.formCmp.submit();
					}
			</script>';
			return $content;
	}

	function getListComparaison(){
		$content ="<h2>Comparatif en ligne</h2>";

		$param = array();

		foreach($_POST["cmp"] as $codeChevalCmp){
			$objTransfert = new ObjectTransfertWS();
			$objTransfert->setKey("codeCheval");
			$objTransfert->setValue($codeChevalCmp);
			$param[count($param)] = $objTransfert;
		}

		$wschcmp = new WebservicesAccess();
		if($wschcmp->connect()){
			$resultCh = $wschcmp->getAllListEtalon2Cmp($param);
			$content.='<table cellspacing="0" width="95%" cellpading="0">';
			$content.='<TR><TH style="border-width:thin;border-color:red;border-style:solid;">Nom du cheval</TH><TH style="border-width:thin;border-color:red;border-style:solid;">race</TH><TH style="border-width:thin;border-color:red;border-style:solid;">robe</TH><TH style="border-width:thin;border-color:red;border-style:solid;">prix</TH><TH style="border-width:thin;border-color:red;border-style:solid;">points forts</TH><TH style="border-width:thin;border-color:red;border-style:solid;">Voir</TH></TR>';
			foreach($resultCh as $cheval){
				$content.='<TR><TD align="center" style="border-bottom-color:red;border-bottom-width:thin;border-bottom-style:dotted;">'.$cheval["nomCheval"].'</TD>';
				$content.='<TD align="center" style="border-bottom-color:red;border-bottom-width:thin;border-bottom-style:dotted;">'.$cheval["raceLibelle"].'</TD>';
				$content.='<TD align="center" style="border-bottom-color:red;border-bottom-width:thin;border-bottom-style:dotted;">'.$cheval["robeLibelle"].'</TD>';
				$content.='<TD align="center" style="border-bottom-color:red;border-bottom-width:thin;border-bottom-style:dotted;">Réservation:'.$cheval["prixReservation"].' &euro;<br/>';
				$content.='Saillie:'.$cheval["prixSaillie"].'&euro;<br/>';
				$content.='Naissance:'.$cheval["prixNaissance"].'&euro;<br/></TD>';
				$content.='<TD align="center" style="border-bottom-color:red;border-bottom-width:thin;border-bottom-style:dotted;">&nbsp;';
				if($cheval["pointFort1"]!= "")
					$content.=$cheval["pointFort1"].'<br/>';
				if($cheval["pointFort2"]!= "")
					$content.=$cheval["pointFort2"].'<br/>';
				if($cheval["pointFort3"]!= "")
					$content.=$cheval["pointFort3"].'<br/>';
				$content.='</TD>';
				$content.='<TD align="center" style="border-bottom-color:red;border-bottom-width:thin;border-bottom-style:dotted;">';
				$content.=$this->pi_list_linkSingle("Fiche détaillée",$cheval["codeCheval"],1)."<br/>";
				if(file_exists($this->photos_folder.$cheval["codeCheval"].".jpg")){
					$content .='<a href="'.$this->photos_folder.$cheval["codeCheval"].'.jpg">Photo</a><br/>';
				}
				if($cheval["urlVideo"] != "" ){
					$content .='<a href="'.$cheval["urlVideo"].'" target="_blank">Vidéo</a>';
				}
				$content.='</TD></TR>';
			}
			$content.='</table>';
			$content .='<br/><br/><a style="color:white;text-decoration:none" id="lienFonctionPetit" href="index.php?id='.$GLOBALS["TSFE"]->id.'">'.htmlspecialchars($this->pi_getLL("libelle_nouvelle_recherche")).'</a>';
		}
		else {
			echo "<div> Error:".$wsCt->getErrorMessage()."</div>";
		}

		return $content;
	}

	function getFormulaireVide(){
		//print_r($GLOBALS["TSFE"]);
		$filter[] = new ObjectTransfertWS("i18n","FR");

		$content= $this->getScript();
		$content.='
			<div><h2>'.htmlspecialchars($this->pi_getLL("titre_formulaire")).'</h2></div><BR>'.htmlspecialchars($this->pi_getLL("desc_formulaire")).'
			';
		if(isset($this->error)){
			$content.='<div style="color:red;font-size:13px;text-align:center">'.$this->error.'</div>';
		}

			$content.='
			<form action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'" name="formulaire_recherche" method="POST">
				<input type="hidden" name="no_cache" value="1">
				<input type="hidden" name="'.$this->prefixId.'[action]" value="newListe">
				<div>
					<p style="float:left;width:50%;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_orientation_production")).'</strong></p>
					<div style="float:right;width:50%;">
						<select name="'.$this->prefixId.'[orientation_production]">
							<option></option>';
							$listOrientation = $this->critHelper->getAllTypeOrientationProduction();
							if(!$listOrientation)
								$content.="<option>".$this->critHelper->getErrorMessage()."</option>";
							foreach($listOrientation as $orientation){
								$content.="<option value='".$orientation['codeFinalite']."'>".$orientation['libelleLong']."</option>";
							}
			$content.='
						</select>
					</div>
				</div>
				<div>
					<p style="float:left;width:50%;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_type_equide")).'</strong></p>
					<div style="float:right;width:50%;">
						<select name="'.$this->prefixId.'[type_equide]'.'">
							<option></option>
							<option value="S">sang</option>
							<option value="P">poney</option>
							<option value="T">trait</option>
							<option value="A">âne</option>
						</select>
					</div>
				</div>
				<div>
					<p style="float:left;width:50%;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_cheval_race")).'</strong></p>
					<div style="float:right;width:50%;">
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
					</div>
				</div>
				<div>
					<p style="float:left;width:50%;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_cheval_nom")).'</strong></p>
					<div style="float:right;width:50%;">
						<input type="text" name="'.$this->prefixId.'[nomcheval]" value="">
					</div>
				</div>
				<div>
					<p style="float:left;width:50%;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_code_gen")).'<br/>'.htmlspecialchars($this->pi_getLL("libelle_code_gen_plus")).'</strong></p></td>
					<div style="float:right;width:50%;">
						<select name="'.$this->prefixId.'[code_gen]">
							<option></option>
							<option value="ELI">Elite</option>
							<option value="TB">Tr&egrave;s bon</option>
							<option value="AME">Am&eacute;liorateur</option>
						</select>
					</div>
				</div>
				<div style="float:left;width:100%;"><hr></div>
				<div style="float:left;width:100%;">
				<H3>'.htmlspecialchars($this->pi_getLL("titre_form_geo")).'</H3>
				</div>
				<!--
				<div>
				'.htmlspecialchars($this->pi_getLL("desc_form_geo")).'
				</div>
				-->
				<div>
					<p style="float:left;width:50%;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_region")).'</strong></p>
					<div style="float:right;width:50%;">
						<select name="'.$this->prefixId.'[region]">
							<option></option>';
							$listRegions = $this->geoHelper->getAllRegions();
							foreach($listRegions as $region){
								if($region['codeRegion'] != "ETR")
									$content.="<option value='".$region['codeRegion']."'>".$region['libelleRegion']."</option>";
							}
						$content.='
						</select>
					</div>
				</div>
				<div>
					<p style="float:left;width:50%;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_dispo_IAC")).'</strong></p>
					<div style="float:right;width:50%;">
						<input type="checkbox" name="'.$this->prefixId.'[iac]" value="O">
					</div>
				</div>
				<div style="float:left;width:100%;"><hr></div>
				<div style="float:left;width:100%;">
				<H3>'.htmlspecialchars($this->pi_getLL("titre_form_prix")).'</H3>
				</div>
				<div>
					<div>
						<p style="float:left;width:50%;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_prix_max")).'</strong></p>
						<div style="float:right;width:50%;">
							<input type="text" name="'.$this->prefixId.'[prix_max]">
						</div>
					</div>
					<div>
						<p style="float:left;width:50%;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_prix_min")).'</strong></p>
						<div style="float:right;width:50%;">
							<input type="text" name="'.$this->prefixId.'[prix_min]">
						</div>
					</div>
					<div>
						<p style="float:left;width:50%;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_paiement_naissance")).'</strong></p>
						<div style="float:right;width:50%;">
							<input type="checkbox" name="'.$this->prefixId.'[paiement_naissance]" value="O">
						</div>
					</div>
				</div>

				<div style="float:left;width:100%;"><hr></div>
				<div>
					<p style="float:left;width:50%;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_poitns_forts")).'</strong></p>
					<div style="float:right;width:50%;">
						<select name="'.$this->prefixId.'[point_fort]">
							<option></option>';
							$pointsForts = $this->critHelper->getAllPointsForts();
							foreach($pointsForts as $pointFort){
								$content.="<option value='".$pointFort['codePointFort']."'>".$pointFort['libelle']."</option>";
							}
						$content.='
						</select>
					</div>
				</div>
				<div>
					<p style="float:left;width:50%;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_robe")).'</strong></p>
					<div style="float:right;width:50%;">
						<select name="'.$this->prefixId.'[robe]">
							<option></option>';
							$listRobes = $this->critHelper->getAllRobes("FRA");
							foreach($listRobes as $robe){
								$content.="<option value='".$robe['codeRobe']."'>".$robe['libelle']."</option>";
							}
						$content.='
						</select>
					</div>
				</div>
				<div style="float:left;width:100%;"><hr></div>
				<div>
					<div style="float:right">
						<input class="bouton" type="submit" name="'.$this->prefixId.'[submit_button]" value="'.htmlspecialchars($this->pi_getLL("submit_button_label")).'">
					</div>
				</div>
			</form>
		';
		return $content;
	}

	/**
	 * Methode de gestion de la fiche du cheval
	 * @return String
	 */
	function getDetail(){
		$ws = new WebservicesAccess();
		$content= $this->getScript();
		$content.='<div><h2>'.htmlspecialchars($this->pi_getLL("titre_fiche")).'</h2></div><BR>';
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

				$content .='<div style="float: left;"><ul><li style="display: list-item;list-style-image : url(fileadmin/templates/images/elts_reccurents/detail.gif);list-style-position: outside;"><a href="'.$this->fiches_folder.$etalon["codeCheval"].'.pdf" target="_blank">'.$this->pi_getLL("libelle_fiche").'</a></li></ul></div><br>';

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

			$content .='<span style="float:right;display:block;margin-right:1px;>';
			if(file_exists($this->photos_folder.$etalon["codeCheval"].".jpg")){
				$content .='<img src="'.$this->photos_folder.$etalon["codeCheval"].'.jpg">';
			}
			if($etalon["urlVideo"] != "" ){
				$content .='<div style="float: left;">
				<ul><li style="display: list-item;list-style-image : url(fileadmin/templates/images/elts_reccurents/video.gif);list-style-position: outside;">
				<a href="'.$etalon["urlVideo"].'" target="_blank" style="color:#F18C13">'.htmlspecialchars($this->pi_getLL("libelle_video")).'</a></strong></ul></li></div><br/>';
			}
			if(file_exists($this->photos_folder.$etalon["codeCheval"].".jpg")){
			$content .='<span style="font-size:6px"><br>'.$this->pi_getLL("copyright_hn").'</span>';
			}
			$content .='</span>';

			$content .='</div>';
			$content .='<div style="float: left;width:730px;"><hr><br/></div>';
			$content .='<div style="float: left;width:730px;">';
			$content .='<span style="float:left;width:330px;display:block;">';
			//ICI commence le détail du cheval

			if($etalon["pointFort1"]!= "" || $etalon["pointFort2"] != "" || $etalon["pointFort3"]!=""){
				$content .='<h4 style="font-size:12px">'. htmlspecialchars($this->pi_getLL("libelle_3points_fort")).':</h4>
				<p>';
				$content .=$etalon["pointFort1"];
				if($etalon["pointFort2"] != "")$content .=", ".$etalon["pointFort2"];
				if($etalon["pointFort2"] != "")$content .=", ".$etalon["pointFort3"];
				$content .='</p>';
			}
			if($etalon["commentaire"] != ""){
				$content .='<h4 style="font-size:12px">'. htmlspecialchars($this->pi_getLL("libelle_aretenir")).':</h4>';
				$content .='<p>'.utf8_decode($etalon["commentaire"]).'</p>';
			}
			if($etalon["conseilCroisement"] != ""){
				$content .='<h4 style="font-size:12px">'. htmlspecialchars($this->pi_getLL("libelle_conseil_croisement")).':</h4>';
				$content .='<p>'.utf8_decode($etalon["conseilCroisement"]).'</p>';
			}
			$tab_fiches = array("PAGR"=> null,"PACH"=> null,"TRGR"=> null,"TRCH"=> null,"SAGR"=> null,"SACH"=> null,"COGR"=> null,"COCH"=> null);
			$afficheParagraphe = false;
			foreach($tab_fiches as $key=>$value){
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
				//width:730px;
				$content .='<div style="float: left;">
				<a name="test"></a>
				<h4 style="font-size:12px"><strong>'. $this->pi_getLL("libelle_tests").':</strong></h4>';
				foreach($tab_fiches as $fiche){
					if(isset($fiche["key"])){
						$content .='<a target=""_blank" href="'.$fiche["url"].'" title="('.$fiche["size"].', modifi&eacute; le '.$fiche["dateModif"].')">'.$this->pi_getLL("libelle_pdf_".$fiche["key"]).'</a><br>';
					}
				}
				$content .="<br>".$this->pi_getLL("phrase_compare_inra").'<a target="_blank" href="'.$this->urlINRA.'">'.$this->pi_getLL("labelle_cliquez_ici").'</a>';

				$content .='</div>';
			}
			$content .='</span>';
			$content .='<span style="float:right;width:350px;display:block;">';
			$content .='<h4 style="font-size:12px">'.$this->pi_getLL("libelle_repro").':</h4><p>
			En '.$etalon["anneeReproduction"].', '.$this->pi_getLL("txt_repro");
			if($etalon["libelleCentreTechnique"] == ""){
				$content .=htmlspecialchars($this->pi_getLL("libelle_attente_affectation")).'<br>';
			} else {
				$content .=$etalon["libelleCentreTechnique"].'<br>';
			}
			$content .='<h4>Visible à cet endroit sur rendez-vous uniquement.</h4><br/>';
			$content .='<p id="txt_cliquez_ici"><br/><br/><a href="javascript:divcache(\'txt_cliquez_ici\');makeRequest(\''.$this->pi_getPageLink($GLOBALS["TSFE"]->id,"",array("codeCheval"=>$etalon["codeCheval"],"no_cache"=>1,"action"=>"getListCT")).'\')">Cliquez ici</a> pour connaitre la liste des Centres techniques dans lesquels la semance est disponible<br><br>';
			$content .='<div id="LISTE_CT" style="float:left;width:95%;visibility:hidden;display:none;border-style:solid;border-width:thin">
					<h4 style="width:100%;float:left"><strong>'.htmlspecialchars($this->pi_getLL("libelle_liste_ct")).'</strong></h4><br/>

					<div id="LISTE_DETAIL">&nbsp;</div>
				</div>';
			if($etalon["codeDispoEnIac"]=="O")$content .='<br/><h4>Disponible en IAC</h4>';
			$content .='</p><br/><h4 style="font-size:12px">Prix de la saillie:</h4>
			<p>
				'.$etalon["prixReservation"].' &euro; payables à la réservation<br/>
				'.$etalon["prixSaillie"].' &euro; payables à la saillie<br/>
				'.$etalon["prixNaissance"].' &euro; payables poulain vivant à 48 h<br/>
			</p>';
			$content .='<br/><h4 style="font-size:12px">Frais technique de monte:</h4><p>
			Pour être mis en relation avec un conseiller des Haras Nationaux, contactez l\'accueil au 0811 90 21 31 de 9h à 17h ou par mail <a href="mailto:info@haras-nationaux.fr">info@haras-nationaux.fr</a>
			</p>';
			//$content .='<br><a target="_blank" href="http://www.haras-nationaux.fr/portail/uploads/tx_vm19docsbase/Bulletin_toute_race_01.doc">'.htmlspecialchars($this->pi_getLL("libelle_reservation_saillie")).'</a>';
			$content .='</p>';

			$content .='</span>';
			$content .='</div>';
			//Reproductions
			$content .='<div style="float: left;width:730px;"><br/><hr><br/></div>';

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
	 * Methode de recherche qui retourne une liste de rï¿½onses.
	 * 1. Si la liste est dans la session alors le systï¿½e retourne une plage de la liste
	 * 2. Si la liste ne se trouve pas dans la session, le systï¿½e fait appel au webservice et calcul la liste
	 * @return String
	 */
	function getListeResult(){
		$result = null;
		$content = $this->getScript();
		$content.='<div><h2>'.htmlspecialchars($this->pi_getLL("titre_list")).'</h2></div><BR>';
		$param = array();

		if( (isset($this->piVars["action"]) && $this->piVars["action"]=="newListe") || (isset($_GET["action"]) && $_GET["action"]=="liste"))
			unset($_SESSION["RESULT_ETALON"]);

		if(!isset($_SESSION["RESULT_ETALON"])){
			if(isset($this->piVars["nomcheval"]) && $this->piVars["nomcheval"] != ""){
				$nomCheval = str_replace("%","",$this->piVars["nomcheval"]);
				if(strlen($nomCheval)<2){
					$this->error = "Le nombre de lettres composant le nom du cheval doit être supérieur ou égal à deux.";
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
			if(isset($this->piVars["orientation_production"]) && $this->piVars["orientation_production"] != ""){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("orientationProduction");
				$objTransfert->setValue($this->piVars["orientation_production"]);
				$param[count($param)] = $objTransfert;
			}
			if(isset($this->piVars["type_equide"]) && $this->piVars["type_equide"] != ""){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("typeEquide");
				$objTransfert->setValue($this->piVars["type_equide"]);
				$param[count($param)] = $objTransfert;
			}
			if(isset($this->piVars["groupe_race"]) && $this->piVars["groupe_race"] != ""){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("groupeRace");
				$objTransfert->setValue($this->piVars["groupe_race"]);
				$param[count($param)] = $objTransfert;
			}
			if(isset($this->piVars["code_gen"]) && $this->piVars["code_gen"] != ""){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("codeGenetique");
				$objTransfert->setValue($this->piVars["code_gen"]);
				$param[count($param)] = $objTransfert;
			}
			if(isset($this->piVars["region"]) && $this->piVars["region"] != ""){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("region");
				$objTransfert->setValue($this->piVars["region"]);
				$param[count($param)] = $objTransfert;
			}
			if(isset($this->piVars["centre_tech"]) && $this->piVars["centre_tech"] != ""){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("centreTechnique");
				$objTransfert->setValue($this->piVars["centre_tech"]);
				$param[count($param)] = $objTransfert;
			}
			if(isset($this->piVars["iac"]) && $this->piVars["iac"] == "O"){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("iac");
				$objTransfert->setValue("true");
				$param[count($param)] = $objTransfert;

			}
			if(isset($this->piVars["prix_max"]) && $this->piVars["prix_max"] != ""){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("prixMax");
				$objTransfert->setValue($this->piVars["prix_max"]);
				$param[count($param)] = $objTransfert;
			}
			if(isset($this->piVars["prix_min"]) && $this->piVars["prix_min"] != ""){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("prixMin");
				$objTransfert->setValue($this->piVars["prix_min"]);
				$param[count($param)] = $objTransfert;
			}
			if(isset($this->piVars["paiement_naissance"]) && $this->piVars["paiement_naissance"] == "O"){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("paiementNaissance");
				$objTransfert->setValue("true");
				$param[count($param)] = $objTransfert;
			}
			if(isset($this->piVars["point_fort"]) && $this->piVars["point_fort"] != ""){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("pointFort");
				$objTransfert->setValue($this->piVars["point_fort"]);
				$param[count($param)] = $objTransfert;
			}
			if(isset($this->piVars["robe"]) && $this->piVars["robe"] != ""){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("robe");
				$objTransfert->setValue($this->piVars["robe"]);
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
		if(count($result)==1){
			Header('Location: '.$this->pi_getPageLink($GLOBALS["TSFE"]->id,"",array($this->prefixId."[showUid]"=>$result[0]["codeCheval"],"no_cache"=>1)));
		}
		if(count($result)>99){
			$content .="<div id='message' style='color:red;font-size:15px;text-align:center'>";
			$content .=count($result)." étalons correspondent à votre critères; cliquer sur OK pour afficher la liste des 100 premiers ou cliquer sur ANNULER pour affiner vos critères.<br/>";
			$content .='<a href="javascript:divcache(\'message\');divaffiche(\'result\')" style="color:white;text-decoration:none" id="lienFonctionPetit">OK</a>   ';
			$content .='<a href="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'" style="color:white;text-decoration:none" id="lienFonctionPetit">ANNULER</a>';
			$content .="</div>";
		}
		$content .="<div id='result' ".(((count($result))>99)?"style='display:none":"")."'>";
		$content .='<form name="formCmp" method="post" action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'">';
		$content .='<input type="hidden" name="no_cache" value="1">';
		$content .='<input type="hidden" name="'.$this->prefixId.'[action]" value="comparer">';
		$i=0;
		foreach($result as $etalon){
			if($i <100){
				$content .="<p><input type=\"checkbox\" name=\"cmp[]\" value='".$etalon["codeCheval"]."'><strong>".$this->pi_list_linkSingle($etalon["nomCheval"],$etalon["codeCheval"],1)."</strong>, ".$etalon["raceLibelle"];
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
			$i++;
		}
		$content .='</form>';
		$content .='<a href="javascript:checkMaxCmp();" style="color:white;text-decoration:none" id="lienFonctionPetit">comparer</a>  ';
		if(!isset($_GET["action"])){
			/*$content .='<span id="boutonLien">';
			$content .=$this->pi_linkToPage(htmlspecialchars($this->pi_getLL("libelle_nouvelle_recherche")),$GLOBALS["TSFE"]->id);
			$content .='</span>';*/
			$content .='<a style="color:white;text-decoration:none" id="lienFonctionPetit" href="index.php?id='.$GLOBALS["TSFE"]->id.'">'.htmlspecialchars($this->pi_getLL("libelle_nouvelle_recherche")).'</a>';
		}
		else
			$content .='<a href="javascript:history.back()" style="color:white;text-decoration:none" id="lienFonctionPetit">'.htmlspecialchars($this->pi_getLL("libelle_retour_liste")).'</a>';
		$content .="</div>";
		return $content;
	}
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_02/pi2/class.tx_dlcubehn02_pi2.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_02/pi2/class.tx_dlcubehn02_pi2.php"]);
}

?>
