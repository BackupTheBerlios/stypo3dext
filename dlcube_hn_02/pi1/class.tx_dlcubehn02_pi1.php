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
 * Plugin 'centres techniques' for the 'dlcube_hn_02' extension.
 *
 * @author	Guillaume Tessier <gtessier@dlcube.com>
 */

require_once(PATH_tslib."class.tslib_pibase.php");
include_once("typo3conf/ext/dlcube_hn_01/class.GeoHelper.php");
include_once("typo3conf/ext/dlcube_hn_01/class.WebservicesAccess.php");
include_once("typo3conf/ext/dlcube_hn_03_geomatic/class.GoogleMapsTool.php");

class tx_dlcubehn02_pi1 extends tslib_pibase {
	var $prefixId = "tx_dlcubehn02_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_dlcubehn02_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "dlcube_hn_02";	// The extension key.
	var $geoHelper;
	var $afficheRegion = false;
	var $searchConfig="GoogleMapDL3Ext"; // "WSDatastore" pour revenir a la config originale
	
	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->geoHelper = new GeoHelper();
		
		//value="'.htmlspecialchars($this->piVars["input_field"])
		//<BR><p>You can click here to '.$this->pi_linkToPage("get to this page again",$GLOBALS["TSFE"]->id).'</p>
		/*
		 * Recuperation du code region passe en argument du plug-in
		 * voir le champ code (select_key)
		 */
		$data = $this->cObj->data;
		
		if(isset($data["select_key"]) && $data["select_key"] != "" && !strstr($data["select_key"],"SPC_MAJ")){
			$this->afficheRegion = true;
			$this->piVars["region"] = $data["select_key"];
			//debug($this->piVars);
			$content=$this->getListeResult();
		}
		
		else if(isset($this->piVars["action"])){
			if($this->piVars["action"] == "liste"){
				$content=$this->getListeResult();
			}
		}
		else{
			unset($_SESSION["RESULT_CT"]);
			$content=$this->getFormulaireVide();
		}
		return $this->pi_wrapInBaseClass($content);
	}
	
	function getFormulaireVide(){
		$listRegions = $this->geoHelper->getAllRegions();
		// on le vire
		//$listDepartements = $this->geoHelper->getAllDepartements();
		$content='
			<div><h2>'.htmlspecialchars($this->pi_getLL("titre_formulaire")).'</h2></div><br/>
			'.htmlspecialchars($this->pi_getLL("desc_formulaire")).'
			
			<table>
			<form action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'" method="POST">
				<input type="hidden" name="no_cache" value="1">
				<input type="hidden" name="'.$this->prefixId.'[action]" value="liste">
				<tr>
				<td><p>'.htmlspecialchars($this->pi_getLL("libelle_region")).'</p></td>
				<td>
					<select name="'.$this->prefixId.'[region]">
						<option></option>';
						foreach($listRegions as $region){
							if($region['codeRegion'] != "ETR") {
								$content.="<option value='".$region['codeRegion']."'>".$region['libelleRegion']."</option>";
								// a utiliser !! 1 FOIS !! pour rempli la table des régions
								//msq("INSERT INTO tx_dlcubehn03geomatic_points SET pid=3711,name='".addslashes($region['libelleRegion'])."',region='".addslashes($region['codeRegion'])."',type='CENT_REGIO'");
								}
						}
		$content.='
					</select>
				</td>
				</tr>
				<tr>
				<td><p>'.$this->pi_getLL("libelle_commune").'</p></td>
				<td>
					<input type="text" size="40" name="'.$this->prefixId.'[commune]" value="">
					<select name="'.$this->prefixId.'[pays]">
					<option value="FRANCE" selected="selected">FRANCE</option>
					<option value="GERMANY">ALLEMAGNE</option>
					<option value="BELGIUM">BELGIQUE</option>
					<option value="SPAIN">ESPAGNE</option>
					<option value="ITALY">ITALIE</option>
					<option value="LUXEMBOURG">LUXEMBOURG</option>
					<option value="NEEDERLANDS">PAYS BAS</option>
					<option value="UNITED KINGDOM">ROYAUME UNIS</option>
					<option value="SWISS">SUISSE</option>
					</select>
				</td>
				</tr>
				<tr>
				<td colspan="2"><hr/></td>
				</tr>
				<tr>
				<td><p>'.htmlspecialchars($this->pi_getLL("libelle_centre")).'</p></td>
				<td>
					<input type="text" name="'.$this->prefixId.'[centre]" value="">
				</td>
				</tr>
				<tr>
				<tr>
				<td><p>'.htmlspecialchars($this->pi_getLL("libelle_type_centre")).'</p></td>
				<td>
					<select name="'.$this->prefixId.'[type_centre]">
						<option></option>
						<option value="mp">centre de mise en place</option>
						<option value="pr">centre de production</option>
						<option value="tre">centre de transfert embryonnaire</option>
					</select>
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
	 * Methode de recherche qui retourne une liste de reponses.
	 * 1. Si la liste est dans la session alors le systeme retourne une plage de la liste
	 * 2. Si la liste ne se trouve pas dans la session, le systeme fait appel au webservice et calcul la liste	
	 * @return String
	 */
	function getListeResult(){
		$this->GoogleMapsTool = new GoogleMapsTool();
		$this->GoogleMapsTool->outJSB=false; // pas de balises JS, c'est typo qui les met
		
		// echoise la clé Google dans le header
		$GLOBALS['TSFE']->additionalHeaderData['OutGMJSWK_0']=$this->GoogleMapsTool->OutGMJSWK();
		
		$result = null;
		$content='<div><h2>'.htmlspecialchars($this->pi_getLL("titre_list")).'</h2></div><br />';
		
		$content.=$this->GoogleMapsTool->OutMapIdS();
		$param = array();
		
		if(isset($this->piVars["commune"]) && $this->piVars["commune"] != ""){
			$objTransfert = new ObjectTransfertWS();
			$objTransfert->setKey("commune");
			$objTransfert->setValue($this->piVars["commune"]);
			$param[count($param)] = $objTransfert;
			$address=addslashes($this->piVars["commune"]).",".$this->piVars["pays"];
		}
		if(isset($this->piVars["region"]) && $this->piVars["region"] != ""){
			$objTransfert = new ObjectTransfertWS();
			$objTransfert->setKey("codeRegion");
			$objTransfert->setValue($this->piVars["region"]);
			$param[count($param)] = $objTransfert;
		}
		if(isset($this->piVars["departement"]) && $this->piVars["departement"] != ""){
			$objTransfert = new ObjectTransfertWS();
			$objTransfert->setKey("codeDepartement");
			$objTransfert->setValue($this->piVars["departement"]);
			$param[count($param)] = $objTransfert;
		}
		if(isset($this->piVars["centre"]) && $this->piVars["centre"] != ""){
			$objTransfert = new ObjectTransfertWS();
			$objTransfert->setKey("centre");
			$objTransfert->setValue($this->piVars["centre"]);
			$param[count($param)] = $objTransfert;
		}
		if(isset($this->piVars["type_centre"]) && $this->piVars["type_centre"] != ""){
			$objTransfert = new ObjectTransfertWS();
			$objTransfert->setKey("typeCentre");
			$objTransfert->setValue("ct".$this->piVars["type_centre"]);
			$param[count($param)] = $objTransfert;
		}
		
		$GLOBALS["TSFE"]->setJS("OutGMJSWK_1",$this->GoogleMapsTool->InitMap(false)); // sera eventuellement ecrasé apres la fin de la liste des CT

		if ($this->searchConfig=="WSDatastore") {
			$ws = new WebservicesAccess();
			if(!$ws->connect()) return $ws->getErrorMessage();
			
			$result = $ws->getCentresTEchniques($param);
			if(!$result && $ws->getErrorMessage()!="") return "[error resultat:]".$ws->getErrorMessage();
		} elseif ($this->piVars["commune"] != "") { // que pour la commune ie l'adresse
			if ($this->GoogleMapsTool->map_geocoder($address)) {
				$content.="<small>".$this->pi_getLL("vos_coords")."lattitude: ".$this->GoogleMapsTool->GeoCodeAddressData[2]."&#176;, longitude: ".$this->GoogleMapsTool->GeoCodeAddressData[3]."&#176;</small>";
				$addwhere=$this->GoogleMapsTool->retWhere($param);
				$this->GoogleMapsTool->DataScript.=$this->GoogleMapsTool->look4closest($this->GoogleMapsTool->GeoCodeAddressData[2],$this->GoogleMapsTool->GeoCodeAddressData[3],5,"CT",$addwhere,$address);
				$result=$this->GoogleMapsTool->tbresult;
//				$this->GoogleMapsTool->DataScript.="showAddress('$address');"; // fait dans look4closer
			} else $content.=$this->pi_getLL("libelle_no_address_geoc");
			
		} else {
			// rajoute le type CT
			$objTransfert = new ObjectTransfertWS();
			$objTransfert->setKey("type");
			$objTransfert->setValue("CT");
			$param[count($param)] = $objTransfert;

			$this->GoogleMapsTool->DataScript.=$this->GoogleMapsTool->getPoints($param);
			$result = $this->GoogleMapsTool->tbresult;
		}
		//print_r($result);
		
		$_SESSION["RESULT_CT"] = $result;

		if(!$result){
			$content .="<H4>".$this->pi_getLL("libelle_no_result")."</H4>";
			if(!$this->afficheRegion )
			$content .=$this->pi_linkToPage(htmlspecialchars($this->pi_getLL("libelle_nouvelle_recherche")),$GLOBALS["TSFE"]->id);
			return $content;
		}

		if(count($result)==1 && isset($result[0]["numError"])){
			$content .="<H4>".$this->pi_getLL("libelle_error_count")."</H4>";
			if(!$this->afficheRegion )
			$content .=$this->pi_linkToPage(htmlspecialchars($this->pi_getLL("libelle_nouvelle_recherche")),$GLOBALS["TSFE"]->id);
			return $content;
		}
		
		
		// adresse du centre de dun le palestel
		//http://maps.google.fr/maps?f=q&hl=fr&q=1+PROMENADE+DE+LA+FORET%3B+23800+DUN+LE+PALESTEL&ie=UTF8&om=1&z=11&ll=46.318007,1.663055&spn=0.312047,0.858994&iwloc=A
		// verrue pour mise à jour
		if(strstr($this->cObj->data["select_key"],"SPC_MAJ")) {
			$SpcModInsert=true;
			$tbtp=explode(":",$this->cObj->data["select_key"]);
			$pidgeo=$tbtp[1];
			//print_r($tbtp);
		}
				
		$content .= "<H4>".count($result)." centre(s) technique(s) trouv&eacute;s)</H4><br>";
		
		// si région choisie, centre directement sur elle
		$recentrg=false;
		if ($this->piVars["region"]) {
			$rc=$this->GoogleMapsTool->RegionReCenter($this->piVars["region"]);
			if ($rc) {
				$this->GoogleMapsTool->DataScript.=$rc;
				$recentrg=true;
			}
		}

		foreach($result as $ce){
			if ($ce['dist']>0) $dist=" (".$ce['dist']." km)";
			$content .='<p style="height:20px;padding-bottom:5px";><strong><a href="'.$this->pi_getPageLink(2973,'',array("numPerso"=>$ce["numPerso"],"action"=>"liste","no_cache"=>1)).'" title="voir la liste des &eacute;talons pr&eacute;sents" style="height:40px;padding-bottom:20px;padding-left:25px;background-repeat: no-repeat;background-image:url(fileadmin/templates/images/elts_reccurents/cheval.gif);">'.$ce["nom"].'</a></strong> '.$dist.'<br></p>';

			
			
			if ($SpcModInsert) { // mode insertion/maj des points dans la table
				
				// hidden, starttime, endtime, name, number, type, geo_pos, label, adresse1, adresse2, cdpst, ville, tel, cell,fax, mail
				$set = "SET tstamp=".time().",name='".addslashes($ce["nom"])."', number='".$ce["numPerso"]."', type='CT', adresse1='".addslashes($ce["adresse1"])."', adresse2='".addslashes($ce["adresse2"])."', cdpst='".$ce["codePostal"]."', ville='".addslashes($ce["libelleCommune"])."', tel='".$ce["telephone"]."', cell='".$ce["telPortable"]."',fax='".$ce["telecopie"]."', mail='".$ce["mail"]."', pid=".$pidgeo.", region='".addslashes($ce["coRegion"])."'";
				if ($ce['actCtmp']=="O") $type_det="_mp_";
				if ($ce['actCtpr']=="O") $type_det.="_pr_";
				if ($ce['actCttre']=="O") $type_det.="_tre_";
				$set.=", type_det='$type_det'";
				//print_r($ce);
				$rep=msq("SELECT * FROM tx_dlcubehn03geomatic_points WHERE number=".$ce["numPerso"]);
				if (mysql_num_rows($rep)>0) {
					msq("UPDATE tx_dlcubehn03geomatic_points $set WHERE number=".$ce["numPerso"]);
				} else msq("INSERT INTO tx_dlcubehn03geomatic_points $set,crdate=".time());
				
				
			}
			
			$txt = null;
			if($ce["actCtmp"]=="O")
				$txt =($txt == null)?"Centre de mise en place":$txt." et Centre de mise en place";
			if($ce["actCtpr"]=="O")
				$txt =($txt == null)?"Centre de production":$txt." et Centre de production";
			if($ce["actCttre"]=="O")
				$txt =($txt == null)?"Centre de transfert":$txt." et centre de transfert embryonnaire";	

			if($txt != null) $content .="<p>".$txt."</p>";
			
			// affiche le point sur la carte, qu'en mode webservice datastore
			// sinon c'est fait dans le calcul du result
			if (!$this->searchConfig=="WSDatastore") {
				$this->GoogleMapsTool->IconImageFile="http://www.haras-nationaux.fr/portail/fileadmin/templates/images/elts_reccurents/cheval.gif";
				$this->GoogleMapsTool->PointInfo=$txIconImageFilet.'<br/>'.$ce["adresse1"]."<br/>".$ce["adresse2"]."<br/>".$ce["codePostal"]." ".$ce["libelleCommune"];
				$this->GoogleMapsTool->DataScript.=$this->GoogleMapsTool->displayPointbyNumber($ce["numPerso"],!$recentrg);
			}

			$content .="<p><span id='adresseCT'>".$ce["adresse1"]."</span> <span id='adresseCT'>".$ce["adresse2"]."</span> <span id='codepostalCT'>".$ce["codePostal"]."</span> <span id='communeCT'>".$ce["libelleCommune"]."</span></p>";
			$content .="<p>";
			if($ce["telephone"] != "")
			$content .=$this->pi_getLL("libelle_telephone")." : ".$ce["telephone"];
			if($ce["telecopie"] != ""){
				$content .=" ".$ce["telecopie"];
			}
			if($ce["telPortable"]){
				$content .=" ".$this->pi_getLL("libelle_telport")." : ".$ce["telPortable"];
			}
			if($ce["mail"]){
				$content .=" ".$this->pi_getLL("libelle_mail")." : ".$ce["mail"];
			}
			$content .="</p>";
			$content .="<hr>";
		
		
		} // fin boucle sur centres techniques
		// sort tous le Jscript Google Maps, mais dans le header, pour que ca marche avec ce putain d'explorer
		$GLOBALS["TSFE"]->setJS("OutGMJSWK_1",$this->GoogleMapsTool->InitMap(false));
		
		if(!$this->afficheRegion ){
			//echo "test:".$this->pi_getPageLink($GLOBALS["TSFE"]->id);
			$content .='<a style="color:white;text-decoration:none" id="lienFonctionPetit" href="index.php?id='.$GLOBALS["TSFE"]->id.'">'.htmlspecialchars($this->pi_getLL("libelle_nouvelle_recherche")).'</a>';
		}
		return $content;
	}
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_02/pi1/class.tx_dlcubehn02_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_02/pi1/class.tx_dlcubehn02_pi1.php"]);
}

?>