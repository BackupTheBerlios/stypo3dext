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
 * 2 - Liste des étalons
 * 3 - Fiche étalon
 * 4 - comparatif d'étalons
 *
 * @author Guillaume Tessier<gtessier@dlcube.com>
 */
//error_reporting (E_ALL);

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
	var $typeDev = "prod";
	var $baseUrl;
	var $baseUrl4PDF;
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

		$this->typeDev="dev";//dev_ext";

		if($this->typeDev =="prod"){
			$this->baseUrl = "http://www.haras-nationaux.fr/portail/";
			$this->baseUrl4PDF = "http://www.haras-nationaux.fr/portail/";
		}
		else if($this->typeDev =="dev"){
			$this->baseUrl = "http://www.haras-nationaux.fr/portail_dev/";
			$this->baseUrl4PDF = "http://webmaster:stella19@www.haras-nationaux.fr/portail_dev/";
		}
		else if($this->typeDev =="dev_ext"){
			$this->baseUrl = "http://hn.projets.dlcube.com/portail/";
			$this->baseUrl4PDF = "http://hn.projets.dlcube.com/portail/";
		}
		else if($this->typeDev =="local"){
			$this->baseUrl = "http://127.0.0.1/portail/";
			$this->baseUrl4PDF = "http://127.0.0.1/portail/";
		}

		$this->geoHelper = new GeoHelper($this->typeDev);
		$this->chevalHelper = new WebservicesAccess($this->typeDev);
		$this->critHelper = new WebservicesCriteres($this->typeDev);

		if(isset($_GET["action"]) && ($_GET["action"]=="getFiche4PDF") && isset($_GET["codecheval"])){
			$this->getPDF($_GET["codecheval"]);
		}

		if(isset($this->piVars["action"])){
			if($this->piVars["action"] == "liste" || $this->piVars["action"] == "newListe"){
				session_start();
				$_SESSION["POST_FORM_HN"] =$_POST;
				return $this->pi_wrapInBaseClass($this->getListeResult());
			}
		}
		else if(isset($_GET["action"]) && ($_GET["action"]=="liste" || $_GET["action"]=="newListe")){
			return $this->pi_wrapInBaseClass($this->getListeResult());
		}
		else if(isset($_GET["action"]) && ($_GET["action"]=="getListCT")){
			echo $this->viewListCT($_GET["codeCheval"]);
			exit();
		}

		if(isset($this->piVars["action"]) && ($this->piVars["action"]=="comparer")){
			return $this->pi_wrapInBaseClass($this->getListComparaison());
		}
		if(isset($this->piVars["showUid"]) && $this->piVars["showUid"] != ""){
			return $this->pi_wrapInBaseClass($this->getDetail($this->piVars["showUid"]));
		}
		unset($_SESSION["RESULT_ETALON"]);
		return $this->pi_wrapInBaseClass($this->getFormulaireVide());
	}

	function viewListCT($codeChevalViewList){
		$content="";
		$param = array();
		$objTransfert = new ObjectTransfertWS();
		$objTransfert->setKey("codeCheval");
		$objTransfert->setValue($codeChevalViewList);
		$param[count($param)] = $objTransfert;
		$wsCt = new WebservicesAccess($this->typeDev);
		if($wsCt->connect()){
			$resultCT = $wsCt->getAllCT4Etalon($param);
			if(is_array($resultCT)){
				foreach($resultCT as $ct){
					$content.="<p style='align:left;'>".$ct["nom"]." ".substr($ct["codePostal"],0,2)." ".$ct["telephone"]."</p>";
				}
			}
			else {
				$content.="<p style='align:left;'>Aucune tourn&eacute;e pour cet &eacute;talon.</p>";
			}
		}
		else {
			$content.= "<div> Error:".$wsCt->getErrorMessage()."</div>";
		}
		return $content;
	}

	function getScript(){
		$content='
			<script type="text/javascript">
			/*<![CDATA[*/
				<!--
					function selPointsForts(){
						var num =0;
						var selectione="";

						for (var i=0;i<document.formulaire_recherche["tx_dlcubehn02_pi2[point_fort][]"].options.length;i++) {

							if (document.formulaire_recherche["tx_dlcubehn02_pi2[point_fort][]"].options[i].selected ) {
						    	num=Number(num+1);
						    	if(num <=3){
									selectione +=document.formulaire_recherche["tx_dlcubehn02_pi2[point_fort][]"].options[i].text+"<br/>";
								}

						  	}

							if(num >3){
								document.formulaire_recherche["tx_dlcubehn02_pi2[point_fort][]"].options[i].selected=false;
							}
						}

						if(num > 3){
							alert("Le nombre maximum de points forts ne peut être supérieur à 3");
						}
						if(num ==0){
							document.getElementById("showPtsForts").innerHTML="AUCUN";
						}else{
							document.getElementById("showPtsForts").innerHTML=selectione;
						}
					}

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
						document.getElementById(id).style.visibility=\'visible\';
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
			// -->
			/*]]>*/
			</script>
			';
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

		$wschcmp = new WebservicesAccess($this->typeDev);
		if($wschcmp->connect()){
			$resultCh = $wschcmp->getAllListEtalon2Cmp($param);
			$content.='<table cellspacing="0" border="0" cellpading="0" style="border-collapse: collapse;width:690px;">';
			$content.='<TR>
				<TH style="border-width:thin;border-color:#C1131E;background-color:#ff7800;border-style:solid;font-size:medium;font-family: verdana, arial, sans-serif;color:white;font-weight:normal;width:130px;">nom de l\'étalon</TH>
				<TH style="border-width:thin;border-color:#C1131E;background-color:#ff7800;border-style:solid;font-size:medium;font-family: verdana, arial, sans-serif;color:white;font-weight:normal;width:110px;">race</TH>
				<TH style="border-width:thin;border-color:#C1131E;background-color:#ff7800;border-style:solid;font-size:medium;color:white;font-weight:normal;font-family: verdana, arial, sans-serif;width:110px;">robe</TH>
				<TH style="border-width:thin;border-color:#C1131E;background-color:#ff7800;border-style:solid;color:white;font-weight:normal;font-size:medium;font-family: verdana, arial, sans-serif;width:140px;">prix</TH>
				<TH style="border-width:thin;border-color:#C1131E;background-color:#ff7800;border-style:solid;color:white;font-weight:normal;font-size:medium;font-family: verdana, arial, sans-serif;width:140px;">points forts</TH>
				<TH style="border-width:thin;border-color:#C1131E;background-color:#ff7800;border-style:solid;color:white;font-weight:normal;font-size:medium;font-family: verdana, arial, sans-serif;width:60px;">voir</TH>
			</TR>';
			foreach($resultCh as $cheval){
				$content.='<TR><TD align="left" style="border-bottom-color:red;border-bottom-width:thin;border-bottom-style:dotted;width:130px;">'.$cheval["nomCheval"].'</TD>';
				$content.='<TD align="center" style="border-bottom-color:red;border-bottom-width:thin;border-bottom-style:dotted;width:110px;">'.$cheval["raceLibelle"].'</TD>';
				$content.='<TD align="center" style="border-bottom-color:red;border-bottom-width:thin;border-bottom-style:dotted;width:110px;">'.$cheval["robeLibelle"].'</TD>';
				$content.='<TD align="center" style="border-bottom-color:red;border-bottom-width:thin;border-bottom-style:dotted;width:140px;">';
				$content.='<span style="float:left">Réservation </span><span style="float:right">'.$cheval["prixReservation"].'&euro;</span><br/><br/>';
				$content.='<span style="float:left">Saillie </span><span style="float:right">'.$cheval["prixSaillie"].'&euro;</span><br/><br/>';
				$content.='<span style="float:left">Poulain vivant </span><span style="float:right">'.$cheval["prixNaissance"].'&euro;</span><br/><br/></TD>';
				$content.='<TD align="center" style="border-bottom-color:red;border-bottom-width:thin;border-bottom-style:dotted;width:140px;">&nbsp;';
				if($cheval["pointFort1"]!= "")
					$content.=$cheval["pointFort1"].'<br/>';
				if($cheval["pointFort2"]!= "")
					$content.=$cheval["pointFort2"].'<br/>';
				if($cheval["pointFort3"]!= "")
					$content.=$cheval["pointFort3"].'<br/>';
				$content.='</TD>';
				$content.='<TD align="center" style="border-bottom-color:red;border-bottom-width:thin;border-bottom-style:dotted;width:60px;">';
				$content.=$this->pi_list_linkSingle("Fiche détaillée avec photo",$cheval["codeCheval"],1)."<br/>";
				if($cheval["urlVideo"] != "" ){
					$content .='<a href="'.$cheval["urlVideo"].'" target="_blank">Vidéo</a>';
				}
				$content.='</TD></TR>';
			}
			$content .='</table>';
			$content .='<br/><br/><a style="color:white;text-decoration:none" id="lienFonctionPetit" href="index.php?id='.$GLOBALS["TSFE"]->id.'">'.htmlspecialchars($this->pi_getLL("libelle_nouvelle_recherche")).'</a>';
			$content .='  <a href="javascript:history.back()" id="lienFonctionPetit" style="color:white;text-decoration:none">'.htmlspecialchars($this->pi_getLL("libelle_retour_liste")).'</a>';
		}
		else {
			echo "<div> Error:".$wsCt->getErrorMessage()."</div>";
		}

		return $content;
	}

	function getFormulaireVide(){
		session_start();
		$filter[] = new ObjectTransfertWS("i18n","FR");
		$content= $this->getScript();
		$content.='<div><h2>'.htmlspecialchars($this->pi_getLL("titre_formulaire")).'</h2></div><BR>'.htmlspecialchars($this->pi_getLL("desc_formulaire"));
		if(isset($this->error)){
			$content.='<div style="color:red;font-size:13px;text-align:center">'.$this->error.'</div>';
		}

		$content.='<form action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'" name="formulaire_recherche" method="POST">
				<input type="hidden" name="no_cache" value="1">
				<input type="hidden" name="'.$this->prefixId.'[action]" value="newListe">
				<div>
					<div style="float:left;width:100%;margin-bottom:2px;">
						Seules les races d\'étalon disponibles aux Haras nationaux sont proposées.
					</div>
					<p style="float:left;width:40%;margin-bottom:2px;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_cheval_race")).'</strong></p>
					<div style="float:right;width:50%;margin-bottom:2px;">
						&nbsp;<select name="'.$this->prefixId.'[groupe_race]">
							<option></option>';
							$listRaces = $this->chevalHelper->getAllRaces($filter);
							if(!$listRaces)
								$content.="<option>".$this->chevalHelper->getErrorMessage()."</option>";
							foreach($listRaces as $race){
								$content.="<option value='".$race['codeGroupeRace']."'";
								if(isset($_SESSION["POST_FORM_HN"][$this->prefixId]["groupe_race"]) && $_SESSION["POST_FORM_HN"][$this->prefixId]["groupe_race"]==$race['codeGroupeRace'])
									$content.="selected";
								$content.=">".$race['libelleLong']."</option>";
							}
			$content.='
						</select>
					</div>
				</div>
				<div>
					<p style="float:left;width:40%;margin-bottom:2px;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_orientation_production")).'</strong></p>
					<div style="float:right;width:50%;margin-bottom:2px;">
						&nbsp;<select name="'.$this->prefixId.'[orientation_production]">
							<option></option>';
							$listOrientation = $this->critHelper->getAllTypeOrientationProduction();
							if(!$listOrientation)
								$content.="<option>".$this->critHelper->getErrorMessage()."</option>";
							foreach($listOrientation as $orientation){
								$content.="<option value='".$orientation['codeFinalite']."' ".((isset($_SESSION["POST_FORM_HN"][$this->prefixId]['orientation_production']) && $_SESSION["POST_FORM_HN"][$this->prefixId]['orientation_production']==$orientation['codeFinalite'])?"selected":"").">".$orientation['libelleLong']."</option>";
							}
			$content.='
						</select>
					</div>
				</div>
				<div>
					<p style="float:left;width:40%;margin-bottom:2px;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_code_gen")).'</strong><br/><span style="font-size:small;">'.htmlspecialchars($this->pi_getLL("libelle_code_gen_plus")).'</span></p></td>
					<div style="float:right;width:50%;margin-bottom:2px;">
						&nbsp;<select name="'.$this->prefixId.'[code_gen]">
							<option></option>
							<option value="ELI" '.((isset($_SESSION["POST_FORM_HN"][$this->prefixId]['code_gen']) && $_SESSION["POST_FORM_HN"][$this->prefixId]['code_gen']=="ELI")?"selected":"").'>Elite</option>
							<option value="TB" '.((isset($_SESSION["POST_FORM_HN"][$this->prefixId]['code_gen']) && $_SESSION["POST_FORM_HN"][$this->prefixId]['code_gen']=="TB")?"selected":"").'>Tr&egrave;s bon</option>
							<option value="AME" '.((isset($_SESSION["POST_FORM_HN"][$this->prefixId]['code_gen']) && $_SESSION["POST_FORM_HN"][$this->prefixId]['code_gen']=="AME")?"selected":"").'>Am&eacute;liorateur</option>
						</select>
					</div>
				</div>
				<div style="float:left;width:100%;"><hr></div>
				<div>
					<p style="float:left;width:40%;margin-bottom:2px;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_cheval_nom")).'</strong></p>
					<div style="float:right;width:50%;margin-bottom:2px;">
						&nbsp;<input type="text" name="'.$this->prefixId.'[nomcheval]" value="'.((isset($_SESSION["POST_FORM_HN"][$this->prefixId]["nomcheval"]))?$_SESSION["POST_FORM_HN"][$this->prefixId]["nomcheval"]:"").'">
					</div>
				</div>
				<div>
					<p style="float:left;width:40%;margin-bottom:2px;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_type_equide")).'</strong></p>
					<div style="float:right;width:50%;margin-bottom:2px;">
						&nbsp;<select name="'.$this->prefixId.'[type_equide]'.'">
							<option></option>
							<option value="S" '.((isset($_SESSION["POST_FORM_HN"][$this->prefixId]["type_equide"]) && $_SESSION["POST_FORM_HN"][$this->prefixId]["type_equide"]=="S")?"selected":"").'>sang</option>
							<option value="P" '.((isset($_SESSION["POST_FORM_HN"][$this->prefixId]["type_equide"]) && $_SESSION["POST_FORM_HN"][$this->prefixId]["type_equide"]=="P")?"selected":"").'>poney</option>
							<option value="T" '.((isset($_SESSION["POST_FORM_HN"][$this->prefixId]["type_equide"]) && $_SESSION["POST_FORM_HN"][$this->prefixId]["type_equide"]=="T")?"selected":"").'>trait</option>
							<option value="A" '.((isset($_SESSION["POST_FORM_HN"][$this->prefixId]["type_equide"]) && $_SESSION["POST_FORM_HN"][$this->prefixId]["type_equide"]=="A")?"selected":"").'>âne</option>
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
					<p style="float:left;width:40%;margin-bottom:2px;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_region")).'</strong></p>
					<div style="float:right;width:50%;margin-bottom:2px;">
						&nbsp;<select name="'.$this->prefixId.'[region]">
							<option></option>';
							$listRegions = $this->geoHelper->getAllRegions();
							foreach($listRegions as $region){
								if($region['codeRegion'] != "ETR")
									$content.="<option value='".$region['codeRegion']."' ".((isset($_SESSION["POST_FORM_HN"][$this->prefixId]['region']) && $_SESSION["POST_FORM_HN"][$this->prefixId]['region']==$region['codeRegion'])?"selected":"").">".$region['libelleRegion']."</option>";
							}
						$content.='
						</select>
					</div>
				</div>
				<div>
					<p style="float:left;width:40%;margin-bottom:2px;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_dispo_IAC")).'</strong></p>
					<div style="float:right;width:50%;margin-bottom:2px;">
						<input type="checkbox" name="'.$this->prefixId.'[iac]" '.((isset($_SESSION["POST_FORM_HN"][$this->prefixId]['iac']))?"checked":"").' value="O">
					</div>
				</div>
				<div style="float:left;width:100%;"><hr></div>
				<div style="float:left;width:100%;">
				<H3>'.htmlspecialchars($this->pi_getLL("titre_form_prix")).'</H3>
				Ce tarif comprend le montant des fractions à la réservation, à la saillie et poulain vivant. Il n\'inclut pas les frais techniques de monte.<br/><br/>
				</div>
				<div>
					<div style="float:left;width:50%;margin-bottom:2px;">
						<p style="float:left;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_prix_min")).'&nbsp;</strong></p>
						<div style="float:left;">
							<input type="text" name="'.$this->prefixId.'[prix_min]" value="'.((isset($_SESSION["POST_FORM_HN"][$this->prefixId]['prix_min']))?$_SESSION["POST_FORM_HN"][$this->prefixId]['prix_min']:"").'">
						</div>
					</div>
					<div style="float:left;width:40%;margin-bottom:2px;">
						<p style="float:left;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_prix_max")).'&nbsp;</strong></p>
						<div style="float:left;">
							<input type="text" name="'.$this->prefixId.'[prix_max]" value="'.((isset($_SESSION["POST_FORM_HN"][$this->prefixId]['prix_max']))?$_SESSION["POST_FORM_HN"][$this->prefixId]['prix_max']:"").'">
						</div>
					</div>
					<div>
						<p style="float:left;width:100%;margin-bottom:2px;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_paiement_naissance")).' <input type="checkbox" name="'.$this->prefixId.'[paiement_naissance]" '.((isset($_SESSION["POST_FORM_HN"][$this->prefixId]['paiement_naissance']))?"checked":"").' value="O"></strong></p>
					</div>
				</div>

				<div style="float:left;width:100%;"><hr></div>
				<div>
					<p style="float:left;width:100%;margin-bottom:4px;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_poitns_forts")).'</strong></p>
					<div style="float:left;width:40%;margin-bottom:6px;">
					Maintenez la touche Ctrl de votre clavier pour sélectionner jusqu\'à 3 points forts.
					</div>
					<div style="float:right;width:50%;margin-bottom:2px;">
						&nbsp;<select onChange="selPointsForts()" multiple="true" size="10" name="'.$this->prefixId.'[point_fort][]">';
							$pointsForts = $this->critHelper->getAllPointsForts();
							foreach($pointsForts as $pointFort){
								$content.="<option value='".$pointFort['codePointFort']."' ".((isset($_SESSION["POST_FORM_HN"][$this->prefixId]['point_fort']) && $_SESSION["POST_FORM_HN"][$this->prefixId]['point_fort']==$pointFort['codePointFort'])?"selected":"").">".$pointFort['libelle']."</option>";
							}
						$content.='
						</select>
					</div>
					<div style="float:left;border:solid 1px #fe7d19;">
						<div><strong>Points forts sélectionnés</strong></div>
						<div style="width:auto;" id="showPtsForts"> AUCUN </div>
					</div>
				</div>
				<div style="float:left;width:100%;"><hr></div>
				<div>
					<div style="float:left;width:100%;margin-bottom:2px;">Seules les robes d\'étalon disponibles aux Haras nationaux sont proposées.</div>
					<p style="float:left;width:40%;margin-bottom:2px;"><strong>'.htmlspecialchars($this->pi_getLL("libelle_robe")).'</strong></p>
					<div style="float:right;width:50%;margin-bottom:2px;">
						&nbsp;<select name="'.$this->prefixId.'[robe]">
							<option></option>';
							$listRobes = $this->critHelper->getAllRobes("FRA");
							foreach($listRobes as $robe){
								$content.="<option value='".$robe['codeRobe']."' ".((isset($_SESSION["POST_FORM_HN"][$this->prefixId]['robe']) && $_SESSION["POST_FORM_HN"][$this->prefixId]['robe']==$robe['codeRobe'])?"selected":"").">".$robe['libelle']."</option>";
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
	function getPDF($codeCheval){
		echo $this->getDetail4PDF($codeCheval);
		exit();
	}

	function getDetail4PDF($codeCheval){
		$ws = new WebservicesAccess($this->typeDev);
		$objTransfert = new ObjectTransfertWS();
		$objTransfert->setKey("codeCheval");
		$objTransfert->setValue($codeCheval);

		$filtres[] = $objTransfert;
		$etalon = $ws->getEtalon($filtres);

		if($etalon){
			$content ='<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "htpp://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<style type="text/css">
<!--
body {
margin: 0;
padding: 0;
background-color: white;
font-family: verdana, arial, sans-serif;
}
h2 {color: #C1131E;font-size:25px}
h3 {color: #F18C13;}
h4 {color: #3E3E3E;margin: 5px;padding: 0;font-size:18px}
h5 {font-size: 105%;color: #666666;margin: 5px;margin-left: 10px;padding: 0;}
p {margin: 0;color: #666666;padding-left:20px;font-size:15px}
a {color: #F18C13; }
a:hover {color: #C1131E;text-decoration: none;}
a:visited {color: #C1131E;text-decoration: underline;}
p {margin: 0;color: #666666;padding-left: 20px;}


-->
</style>
</head>
<body>
	<table style="width:21cm" border="0">
		<tr>
		<td style="width:10,5cm;" align="left"><img src="'.$this->baseUrl4PDF.'typo3conf/ext/dlcube_hn_02/pi2/logo.gif"></td>
		<td style="width:10,5cm;" align="right">'.date("d/m/Y").'</td>
		</tr>
	</table>
        <table style="width:21cm" border="0">
          <tr><!--border:solid 1px #fe7d19;-->
            <td style="width:10,5cm;" ><div style="float: left;">
                  <h2 style="text-align:center"><strong>'.$etalon["nomCheval"].'</strong></h2>
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
					<!-- <br>
					<p>'. $this->pi_getLL("libelle_par").' '.$etalon["nomChevalPere"].' '.$this->pi_getLL("libelle_et").' '.$etalon["nomChevalMere"].' '. $this->pi_getLL("libelle_par").' '.$etalon["nomPereChevalMere"].'</p>
					<br> -->';
					if($etalon["proprietaire"] != ""){
						$content .='<p>'.htmlspecialchars($this->pi_getLL("libelle_proprietaire")).' : '.$etalon["proprietaire"].'</p><br>';
					}
				$content .='<p>&nbsp;<br/><br/></p>';
				if($etalon["iso"] > 0  || $etalon["icc"] > 0 || $etalon["idr"] > 0 || $etalon["itr"] > 0 ||
					$etalon["iaa"] > 0 || ($etalon["bso"] > 0 && $etalon["coefBso"]>'0.30')|| ($etalon["bcc"] > 0 && $etalon["coefBcc"]>'0.30') || ($etalon["bdr"] > 0 && $etalon["coefBdr"]>'0.30') ||
					($etalon["btr"] > 0 && $etalon["coefBtr"]>'0.30')){
					if($etalon["codeRace"]=="TF"){
						$content .='
						<h4 style="font-size:12px">'. $this->pi_getLL("libelle_perf").'</h4><p>';
						$content .=($etalon["itr"] != null && $etalon["itr"] > 0)?'ITR = '.$etalon["itr"].'('.$etalon["anneeItr"].')<br>':'';
						$content .=($etalon["btr"] != null && $etalon["coefBtr"] > '0.20')?'BTR = '.$etalon["btr"].'('.$etalon["coefBtr"].')<br>':'';
						$content .='</p>';
					} else {
						$content .='<p>&nbsp;<br/><br/></p>
						<h4 style="font-size:12px">'. $this->pi_getLL("libelle_perf").'</h4><p>';
						$content .=($etalon["iso"] != null && $etalon["iso"] > 0)?'ISO = '.$etalon["iso"].'('.$etalon["anneeIso"].')<br>':'';
						$content .=($etalon["icc"] != null && $etalon["icc"] >0)?'ICC = '.$etalon["icc"].'('.$etalon["anneeIcc"].')<br>':'';
						$content .=($etalon["idr"] != null && $etalon["idr"] >0)?'IDR = '.$etalon["idr"].'('.$etalon["anneeIdr"].')<br>':'';
						$content .=($etalon["itr"] != null && $etalon["itr"] >0)?'ITR = '.$etalon["itr"].'('.$etalon["anneeItr"].')<br>':'';
						$content .=($etalon["iaa"] != null && $etalon["iaa"] >0)?'IAA = '.$etalon["iaa"].'('.$etalon["anneeIaa"].')<br>':'';
						$content .=($etalon["bso"] > 0 && $etalon["coefBso"] > '0.30')?'BSO = '.$etalon["bso"].'('.$etalon["coefBso"].')<br>':'';
						$content .=($etalon["bcc"] > 0 && $etalon["coefBcc"] > '0.30')?'BCC = '.$etalon["bcc"].'('.$etalon["coefBcc"].')<br>':'';
						$content .=($etalon["bdr"] > 0 && $etalon["coefBdr"] > '0.30')?'BDR = '.$etalon["bdr"].'('.$etalon["coefBdr"].')<br>':'';
						$content .=($etalon["btr"] > 0 && $etalon["coefBtr"] > '0.30')?'BTR = '.$etalon["btr"].'('.$etalon["coefBtr"].')<br>':'';
						$content .='</p>';
					}
				}
			$content .='<table cellspacing="0" cellpading="0" style="border-collapse:collapse;margin-bottom:1px;width:10cm" align="center" border="0">
					  <!--ligne 1 -->
					  <tr>
					    <td rowspan="4" style="overflow: hidden;border:solid thin gray;font-size:smaller">'.$etalon["nomChevalPere"].'</td>
					    <td rowspan="2" style="overflow: hidden;border:solid thin gray;font-size:smaller">'.$etalon["nomPerePere"].'</td>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller">'.$etalon["nomPerePerePere"].'</td>
					  </tr>
					  <tr>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller">'.$etalon["nomMerePerePere"].'</td>
					  </tr>
					  <tr>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller" rowspan="2">'.$etalon["nomMerePere"].'</td>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller" >'.$etalon["nomPereMerePere"].'</td>
					  </tr>
					  <tr>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller">'.$etalon["nomMereMerePere"].'</td>
					  </tr>
					  <!--ligne 2 -->
					  <tr>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller" rowspan="4">'.$etalon["nomChevalMere"].'</td>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller" rowspan="2">'.$etalon["nomPereChevalMere"].'</td>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller">'.$etalon["nomPerePereMere"].'</td>
					  </tr>
					  <tr>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller">'.$etalon["nomMerePereMere"].'</td>
					  </tr>
					  <tr>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller" rowspan="2">'.$etalon["nomMereMere"].'</td>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller">'.$etalon["nomPereMereMere"].'</td>
					  </tr>
					  <tr>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller">'.$etalon["nomMereMereMere"].'</td>
					  </tr>';
			$content .='</table>
            </td>
            <td width="50%" valign="top">';
            if(file_exists($this->photos_folder.$etalon["codeCheval"].".jpg")){
				$content .='<img src="'.$this->baseUrl4PDF.$this->photos_folder.$etalon["codeCheval"].'.jpg">';
			}
			if(file_exists($this->photos_folder.$etalon["codeCheval"].".jpg")){
			$content .='<span style="font-size:6px"><br>'.$this->pi_getLL("copyright_hn").'</span>';
			}
			$content.='</td>
          </tr>
          <tr>

    <td style="width:10,5cm">';
	  if($etalon["pointFort1"]!= "" || $etalon["pointFort2"] != "" || $etalon["pointFort3"]!=""){
			$content .='<h4>'. htmlspecialchars($this->pi_getLL("libelle_3points_fort")).'</h4>
			<p>';
			$content .=$etalon["pointFort1"];
			if($etalon["pointFort2"] != "")$content .=", ".$etalon["pointFort2"];
			if($etalon["pointFort2"] != "")$content .=", ".$etalon["pointFort3"];
			$content .='</p>';
		}
	  $content.='
      <p>&nbsp;</p>
      <span style="float:left;width:330px;display:block;">';
	  if($etalon["commentaire"] != ""){
			$content .='<h4>'. htmlspecialchars($this->pi_getLL("libelle_aretenir")).'</h4>';
			$content .='<p>'.$etalon["commentaire"].'</p>';
	  }
	  $content .='
      <p>&nbsp;</p>
      </span></td>

    <td style="width:10,5cm" valign="top">
      <h4>Localisation géographique</h4>
      <p>Cet étalon qui fait la monte depuis '.$etalon["anneeReproduction"].', ';

			if($etalon["libelleCentreTechnique"] == ""){
				$content .=' est aujourd\'hui '.htmlspecialchars($this->pi_getLL("libelle_attente_affectation")).'<br>';
			} else {
				$content .=' est aujourd\'hui disponible au '.$etalon["libelleCentreTechnique"].'<br>';
			}
			$content .='Visible à cet endroit sur rendez-vous.</p>';

		$param = array();
		$objTransfert = new ObjectTransfertWS();
		$objTransfert->setKey("codeCheval");
		$objTransfert->setValue($etalon["codeCheval"]);
		$param[count($param)] = $objTransfert;
		$wsCt = new WebservicesAccess($this->typeDev);
		if($wsCt->connect()){
			$resultCT = $wsCt->getAllCT4Etalon($param);
			if(is_array($resultCT)){
				$content .='<div id="LISTE_CT" style="float:left;width:95%;border-style:solid;border-width:thin">
    			<h4 style="width:100%;float:left"><strong>Centre(s) où la semence est disponible</strong></h4><br />
         		<div id="LISTE_DETAIL">';
				foreach($resultCT as $ct){
					$content.="<p style='align:left;'>".$ct["nom"]." ".substr($ct["codePostal"],0,2)." ".$ct["telephone"]."</p>";
				}
				$content .='</div>
       			</div>';
			}
		}
	$content.='
    </td>
    </tr>
    <tr>

    <td style="width:10,5cm" valign="top"><span style="float:left;width:330px;display:block;">';
    if($etalon["conseilCroisement"] != ""){
		$content .='<h4 >'. htmlspecialchars($this->pi_getLL("libelle_conseil_croisement")).'</h4>';
		$content .='<p>'.$etalon["conseilCroisement"].'</p>';
	}
	$tab_fiches = array("PAGR"=> null,"PACH"=> null,"TRGR"=> null,"TRCH"=> null,"SAGR"=> null,"SACH"=> null,"COGR"=> null,"COCH"=> null);
	$afficheParagraphe = false;
	/******************************** supprimer le 9 03 2007 *********************************
	 * foreach($tab_fiches as $key=>$value){
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
		$content .='
		<div id="test_saut_allure" style="width:10cm;float:left;border-style:solid;border-width:thin">
		<h4 style="font-size:12px"><strong>'. $this->pi_getLL("libelle_tests").'</strong></h4>';
		foreach($tab_fiches as $fiche){
			if(isset($fiche["key"])){
				$content .='<a target=""_blank" href="'.$fiche["url"].'" title="('.$fiche["size"].', modifi&eacute; le '.$fiche["dateModif"].')">'.$this->pi_getLL("libelle_pdf_".$fiche["key"]).'</a><br>';
			}
		}
		$content .="<p><br>".$this->pi_getLL("phrase_compare_inra").'<a target="_blank" href="'.$this->urlINRA.'">'.$this->pi_getLL("labelle_cliquez_ici").'</a></p>';
		$content .='</div>';
	}*****************************************************************************************/
	$content.='
	</td>
	<td>
      <h4>Prix de la saillie</h4>
              <p>
				'.$etalon["prixReservation"].' &euro; payables à la réservation<br/>
				'.$etalon["prixSaillie"].' &euro; payables à la saillie<br/>
				'.$etalon["prixNaissance"].' &euro; payables poulain vivant à 48 h<br/>
				hors frais techniques de monte.<br/>
			</p>
              <br />
              <p> Pour être mis en relation avec un conseiller <br />
                des Haras Nationaux, contactez l\'accueil au <br />
                0811 90 21 31 de 9h à 17h ou par mail <br />
                <a href="mailto:info@haras-nationaux.fr">info@haras-nationaux.fr</a>
              </p>
             </td>
          </tr>

  		  <tr>
    		 <td style="width:10,5cm">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
	</body>
</html>';
		}
		return $content;
	}


	/**
	 * Methode de gestion de la fiche du cheval
	 * @return String
	 */
	function getDetail($codeCheval){
		$ws = new WebservicesAccess($this->typeDev);
		$content= $this->getScript();
		//$content.='<div><h2>'.htmlspecialchars($this->pi_getLL("titre_fiche")).'</h2></div><BR>';
		$objTransfert = new ObjectTransfertWS();
		$objTransfert->setKey("codeCheval");
		$objTransfert->setValue($codeCheval);

		$filtres[] = $objTransfert;
		$etalon = $ws->getEtalon($filtres);

		if($etalon){
			//Header
			$content .='<div style="float:right;margin:5px"><a href="typo3conf/ext/dlcube_hn_02/pi2/cheval_pdf.php?id='.$GLOBALS["TSFE"]->id.'&nomcheval='.$etalon["nomCheval"].'&codecheval='.$codeCheval.'&action=pdf" id="lienFonctionPetit" style="color:white;text-decoration:none">version imprimable</a></div>';
			$content .='<div style="float: left;">';
			$content .='<div style="float:left;width:330px;/*display:block;border:solid 1px #fe7d19;*/">
				<h2 style="text-align:center;"><strong>'. $etalon["nomCheval"].'</strong></h2>
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
				<!-- <br>
				<p>'. $this->pi_getLL("libelle_par").' '.$etalon["nomChevalPere"].' '.$this->pi_getLL("libelle_et").' '.$etalon["nomChevalMere"].' '. $this->pi_getLL("libelle_par").' '.$etalon["nomPereChevalMere"].'</p>
				<br> -->';
				if($etalon["proprietaire"] != ""){
					$content .='<p>'.htmlspecialchars($this->pi_getLL("libelle_proprietaire")).' : '.$etalon["proprietaire"].'</p><br>';
				}

				$content .='<div style="float: left;"><ul><li style="display: list-item;list-style-image : url(fileadmin/templates/images/elts_reccurents/detail.gif);list-style-position: outside;"><a href="'.$this->fiches_folder.$etalon["codeCheval"].'.pdf" target="_blank">'.$this->pi_getLL("libelle_fiche").'</a></li></ul></div><br>';

				$content .='</p>';
				$content .='<p>&nbsp;<br/><br/></p>';
				if($etalon["iso"] > 0  || $etalon["icc"] > 0 || $etalon["idr"] > 0 || $etalon["itr"] > 0 ||
					$etalon["iaa"] > 0 || ($etalon["bso"] > 0 && $etalon["coefBso"]>'0.30')|| ($etalon["bcc"] > 0 && $etalon["coefBcc"]>'0.30') || ($etalon["bdr"] > 0 && $etalon["coefBdr"]>'0.30') ||
					($etalon["btr"] > 0 && $etalon["coefBtr"]>'0.30')){
					if($etalon["codeRace"]=="TF"){
						$content .='
						<h4 style="font-size:12px">'. $this->pi_getLL("libelle_perf").'</h4><p>';
						$content .=($etalon["itr"] != null && $etalon["itr"] > 0)?'ITR = '.$etalon["itr"].'('.$etalon["anneeItr"].')<br>':'';
						$content .=($etalon["btr"] != null && $etalon["coefBtr"] > '0.20')?'BTR = '.$etalon["btr"].'('.$etalon["coefBtr"].')<br>':'';
						$content .='</p>';
					} else {
						$content .='<p>&nbsp;<br/><br/></p>
						<h4 style="font-size:12px">'. $this->pi_getLL("libelle_perf").'</h4><p>';
						$content .=($etalon["iso"] != null && $etalon["iso"] > 0)?'ISO = '.$etalon["iso"].'('.$etalon["anneeIso"].')<br>':'';
						$content .=($etalon["icc"] != null && $etalon["icc"] >0)?'ICC = '.$etalon["icc"].'('.$etalon["anneeIcc"].')<br>':'';
						$content .=($etalon["idr"] != null && $etalon["idr"] >0)?'IDR = '.$etalon["idr"].'('.$etalon["anneeIdr"].')<br>':'';
						$content .=($etalon["itr"] != null && $etalon["itr"] >0)?'ITR = '.$etalon["itr"].'('.$etalon["anneeItr"].')<br>':'';
						$content .=($etalon["iaa"] != null && $etalon["iaa"] >0)?'IAA = '.$etalon["iaa"].'('.$etalon["anneeIaa"].')<br>':'';
						$content .=($etalon["bso"] > 0 && $etalon["coefBso"] > '0.30')?'BSO = '.$etalon["bso"].'('.$etalon["coefBso"].')<br>':'';
						$content .=($etalon["bcc"] > 0 && $etalon["coefBcc"] > '0.30')?'BCC = '.$etalon["bcc"].'('.$etalon["coefBcc"].')<br>':'';
						$content .=($etalon["bdr"] > 0 && $etalon["coefBdr"] > '0.30')?'BDR = '.$etalon["bdr"].'('.$etalon["coefBdr"].')<br>':'';
						$content .=($etalon["btr"] > 0 && $etalon["coefBtr"] > '0.30')?'BTR = '.$etalon["btr"].'('.$etalon["coefBtr"].')<br>':'';
						$content .='</p>';
					}
				}
				//print_r($etalon);
			$content .='<table width="98%" cellspacing="0" cellpading="0" style="border-collapse:collapse;margin-bottom:1px;" align="center" border="0">
					  <!--ligne 1 -->
					  <tr>
					    <td rowspan="4" style="overflow: hidden;border:solid thin gray;font-size:smaller">'.$etalon["nomChevalPere"].'</td>
					    <td rowspan="2" style="overflow: hidden;border:solid thin gray;font-size:smaller">'.$etalon["nomPerePere"].'</td>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller">'.$etalon["nomPerePerePere"].'</td>
					  </tr>
					  <tr>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller">'.$etalon["nomMerePerePere"].'</td>
					  </tr>
					  <tr>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller" rowspan="2">'.$etalon["nomMerePere"].'</td>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller" >'.$etalon["nomPereMerePere"].'</td>
					  </tr>
					  <tr>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller">'.$etalon["nomMereMerePere"].'</td>
					  </tr>
					  <!--ligne 2 -->
					  <tr>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller" rowspan="4">'.$etalon["nomChevalMere"].'</td>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller" rowspan="2">'.$etalon["nomPereChevalMere"].'</td>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller">'.$etalon["nomPerePereMere"].'</td>
					  </tr>
					  <tr>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller">'.$etalon["nomMerePereMere"].'</td>
					  </tr>
					  <tr>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller" rowspan="2">'.$etalon["nomMereMere"].'</td>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller">'.$etalon["nomPereMereMere"].'</td>
					  </tr>
					  <tr>
					    <td style="overflow: hidden;border:solid thin gray;font-size:smaller">'.$etalon["nomMereMereMere"].'</td>
					  </tr>';
			$content .='</table>';
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
				$content .='<h4 style="font-size:12px">'. htmlspecialchars($this->pi_getLL("libelle_3points_fort")).'</h4>
				<p>';
				$content .=$etalon["pointFort1"];
				if($etalon["pointFort2"] != "")$content .=", ".$etalon["pointFort2"];
				if($etalon["pointFort2"] != "")$content .=", ".$etalon["pointFort3"];
				$content .='</p>';
			}
			if($etalon["commentaire"] != ""){
				$content .='<h4 style="font-size:12px">'. htmlspecialchars($this->pi_getLL("libelle_aretenir")).'</h4>';
				$content .='<p>'.$etalon["commentaire"].'</p>';
			}
			if($etalon["conseilCroisement"] != ""){
				$content .='<h4 style="font-size:12px">'. htmlspecialchars($this->pi_getLL("libelle_conseil_croisement")).'</h4>';
				$content .='<p>'.$etalon["conseilCroisement"].'</p>';
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
				$content .='<div id="lien_affiche_saut" style="margin-top:5px;float:left;"><a href="javascript:divcache(\'lien_affiche_saut\');divaffiche(\'test_saut_allure\')">tests de saut, d\'allures et de conformation</a></div>
				<div id="test_saut_allure" style="float:left;visibility:hidden;display:none;border-style:solid;border-width:thin">
				<h4 style="font-size:12px"><strong>'. $this->pi_getLL("libelle_tests").'</strong></h4>';
				foreach($tab_fiches as $fiche){
					if(isset($fiche["key"])){
						$content .='<a target=""_blank" href="'.$fiche["url"].'" title="('.$fiche["size"].', modifi&eacute; le '.$fiche["dateModif"].')">'.$this->pi_getLL("libelle_pdf_".$fiche["key"]).'</a><br>';
					}
				}
				$content .="<p><br>".$this->pi_getLL("phrase_compare_inra").'<a target="_blank" href="'.$this->urlINRA.'">'.$this->pi_getLL("labelle_cliquez_ici").'</a></p>';
				$content .='</div>';
			}
			$content .='</span>';
			$content .='<span style="float:right;width:350px;display:block;">';
			$content .='<h4 style="font-size:12px">'.$this->pi_getLL("libelle_repro").'</h4><p>
			Cet étalon qui fait la monte depuis '.$etalon["anneeReproduction"].', ';

			if($etalon["libelleCentreTechnique"] == ""){
				$content .=' est aujourd\'hui '.htmlspecialchars($this->pi_getLL("libelle_attente_affectation")).'<br>';
			} else {
				$content .=' est aujourd\'hui disponible au '.$etalon["libelleCentreTechnique"].'<br>';
			}
			$content .='Visible à cet endroit sur rendez-vous.';
			$content .='<p id="txt_cliquez_ici"><br/><br/><a href="javascript:divcache(\'txt_cliquez_ici\');makeRequest(\''.$this->baseUrl.$this->pi_getPageLink($GLOBALS["TSFE"]->id,"",array("codeCheval"=>$etalon["codeCheval"],"no_cache"=>1,"action"=>"getListCT")).'\')">Cliquez ici</a> pour connaitre la liste des Centres techniques dans lesquels la semence est disponible<br><br>';
			$content .='<div id="LISTE_CT" style="float:left;width:95%;visibility:hidden;display:none;border-style:solid;border-width:thin">
					<h4 style="width:100%;float:left"><strong>'.htmlspecialchars($this->pi_getLL("libelle_liste_ct")).'</strong></h4><br/>
					<div id="LISTE_DETAIL">&nbsp;</div>
				</div>';
			if($etalon["codeDispoEnIac"]=="O")$content .='<br/><h4>Disponible en IAC</h4>';
			$content .='</p><br/><h4 style="font-size:12px">Prix de la saillie</h4>
			<p>
				'.$etalon["prixReservation"].' &euro; payables à la réservation<br/>
				'.$etalon["prixSaillie"].' &euro; payables à la saillie<br/>
				'.$etalon["prixNaissance"].' &euro; payables poulain vivant à 48 h<br/>
				hors frais techniques de monte.<br/>
			</p>';
			$content .='<br/><p>
			Pour être mis en relation avec un conseiller <br/>des Haras Nationaux, contactez l\'accueil au <br/>0811 90 21 31 de 9h à 17h ou par mail <br/><a href="mailto:info@haras-nationaux.fr">info@haras-nationaux.fr</a>
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
		$content.='<div><h2>'.htmlspecialchars($this->pi_getLL("titre_list")).'</h2></div><br/>';
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
				$value = null;
				foreach($this->piVars["point_fort"] as $pointFort){
					$value = ($value==null)?$pointFort:$value.",".$pointFort;
				}

				$objTransfert->setValue($value);
				$param[count($param)] = $objTransfert;
			}
			if(isset($this->piVars["robe"]) && $this->piVars["robe"] != ""){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("robe");
				$objTransfert->setValue($this->piVars["robe"]);
				$param[count($param)] = $objTransfert;
			}


			$ws = new WebservicesAccess($this->typeDev);
			if(!$ws->connect()) return "erreur a la connexion:".$ws->getErrorMessage();
			$result = $ws->getEtalons($param);
			if(!$result && $ws->getErrorMessage() != "" ) return "erreur dans resultat:".$ws->getErrorMessage();
			$_SESSION["RESULT_ETALON"] = $result;
		} else {
			$result = $_SESSION["RESULT_ETALON"];
		}
		if(!$result){
			$content .="<div style='color:red;font-size:15px;text-align:center'>".$this->pi_getLL("libelle_no_result")."</div>";
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
		$content.='<div>Vous pouvez effectuer un comparatif sur les principaux critères entre 2 et 5 étalons. Il suffit pour cela de cocher les cases des étalons choisis, puis de cliquer sur "Comparer" en bas de la liste.</div>';
		$content .= "<h4>".count($result)." ".$this->pi_getLL("libelle_compteur")."</h4><br>";
		if(count($result)==1){
			$url = $this->pi_getPageLink($GLOBALS["TSFE"]->id,"toto",array("tx_dlcubehn02_pi2[showUid]"=>$result[0]["codeCheval"],"no_cache"=>"1"));
            Header('Location: '.$this->baseUrl.$url);
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
		if(!isset($_GET["action"])){
			/*$content .='<span id="boutonLien">';
			$content .=$this->pi_linkToPage(htmlspecialchars($this->pi_getLL("libelle_nouvelle_recherche")),$GLOBALS["TSFE"]->id);
			$content .='</span>';*/
			$content .='<a style="color:white;text-decoration:none" id="lienFonctionPetit" href="index.php?id='.$GLOBALS["TSFE"]->id.'">'.htmlspecialchars($this->pi_getLL("libelle_nouvelle_recherche")).'</a>  ';
		}
		else
			$content .='<a href="javascript:history.back()" style="color:white;text-decoration:none" id="lienFonctionPetit">'.htmlspecialchars($this->pi_getLL("libelle_retour_liste")).'</a>  ';
		$content .='<a href="javascript:checkMaxCmp();" style="color:white;text-decoration:none" id="lienFonctionPetit">Comparer</a>  ';
		$content .="</div>";
		return $content;
	}
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_02/pi2/class.tx_dlcubehn02_pi2.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_02/pi2/class.tx_dlcubehn02_pi2.php"]);
}

?>
