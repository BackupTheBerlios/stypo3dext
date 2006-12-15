<?php
header('Pragma: public');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: must-revalidate, pre-check=0, post-check=0, max-age=0');

/***************************************************************
*  Copyright notice
*
*  (c) 2004 Vincent (admin, celui � la pioche) (webtech@haras-nationaux.fr)
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
 * Plugin 'Espace personnalis�' for the 'dlcube04_CAS' extension.
 *
 * @author	Guillaume Tessier <gui.tessier@wanadoo.fr>
 */

//error_reporting(15);
require_once(PATH_tslib."class.tslib_pibase.php");
include_once("typo3conf/ext/dlcube_hn_01/class.WebservicesCompte.php");
include_once("typo3conf/ext/dlcube_hn_01/class.WebservicesAccess.php");

class tx_dlcube04CAS_pi5 extends tslib_pibase {
	var $prefixId = "tx_dlcube04CAS_pi5";		// Same as class name
	var $scriptRelPath = "pi5/class.tx_dlcube04CAS_pi5.php";	// Path to this script relative to the extension dir.
	var $extKey = "dlcube04_CAS";	// The extension key.
	var $personne = null;
	var $ct = null;
	var $isAbonNews = false;
	var $isAbonAlert= false;
	var $nbreNaissance=0;
	var $nbreLieudetention=0;
	var $nbreFactures = 0;
	var $montantFactures = 0;

	var $urlPassageIdentFort = null;
	var $urlDeclaNovelleNaissance = null;
	var $urlDeclaResultNeg = null;
	var $urlModifCompte = null;
	var $urlAchatPoint = null;
	var $urlGererConsulterCheval = null;
	var $urlDeclarerCheval = null;
	var $urlModifSosPoulain = null;
	var $urlAjoutSosPoulain = null;

	/**
	 * Plug-in pour creation de comptes CAS
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$this->pi_loadLL();
		session_start();
		if(!isset($_SESSION["portalId"]) || $_SESSION["portalId"]==""){
        	header("Location: index.php?id=2822");
         }

		$type="dev_ext";
		$url = null;
		if($type=="prod")
			$url = "www4.haras-nationaux.fr:8080";
		else if($type=="dev")
			$url = "xinf-devlinux:8080";
		else if($type=="dev_ext")
			$url = "80.124.158.237:8080";

		/**
		 * declaration des urls de redirection iframe
		 */
		$this->urlPassageIdentFort = $this->pi_getPageLink("3684");
		$this->urlAchatPoint = $this->pi_getPageLink("3675");
		$this->urlGererConsulterCheval = $this->pi_getPageLink("3674");
		$this->urlDeclarerCheval = $this->pi_getPageLink("3673");
		$this->urlModifSosPoulain = $this->pi_getPageLink("3672");
		$this->urlAjoutSosPoulain = $this->pi_getPageLink("3671");
		$this->urlModifCompte=$this->pi_getPageLink("3670");

		/**
		 * Declaration des URL pour accéder aux services externes
		 */
		$this->urlDeclaNovelleNaissance = "http://".$url."/cid-internet-web/declaration-naissance/ReferenceDeSaillieAction.do?dispatch=initDataBeforeLoad&typeDeclaration=POS";
    	$this->urlDeclaResultNeg = "http://".$url."/cid-internet-web/declaration-naissance/ReferenceDeSaillieAction.do?dispatch=initDataBeforeLoad&typeDeclaration=NEG";

		$userId = $_SESSION["portalId"];
		$userId ="faible";
		$param[] = array(
		"login"=>$userId,
		"ctx"=> null);
		$ws = new WebservicesCompte("dev_ext");
		if(!$ws->connectIdent()){
			$content="ERROR:".$ws->getErrorMessage();
			$content = "L'espace priv&eacute; est momentan&eacute;ment indisponible, veuillez nous excuser de ce d&eacute;sagr&eacute;ment.";
			return $content;
		}


		$this->personne = $ws->getPersonneByLogin($param);
		print_r($this->personne);
		/**
		 * recuperation du nombre de naissance, de lieux de detention
		 */
		$paramCid[] = array(
		"login"=>$_SESSION["portalId"],
		"ctx"=> null);

		$wsCid = new WebservicesCompte("dev_ext");
		if(!$wsCid->connectCid()){
			//$content="ERROR:".$wsCid->getErrorMessage();
			$content = "L'espace priv&eacute; est momentan&eacute;ment indisponible, veuillez nous excuser de ce d&eacute;sagr&eacute;ment.";
			return $content;
		}
		$this->nbreNaissance = $wsCid->getNbrNaissanceAnneeEnCours4User($paramCid);
		$this->nbreLieudetention = $wsCid->getNbrLieuDetention4User($paramCid);
		$this->nbreChevaux = $wsCid->getNbrChevaux4User($paramCid);

		/**
		 * recuperation du nombre de factures et le montant total
		 */

		if($this->personne["findPersonneByLoginReturn"]["key"]["numeroPersonne"] != ""){
			$paramPsi[] = $this->personne["findPersonneByLoginReturn"]["key"]["numeroPersonne"];
			$paramPsi[] = $this->personne["findPersonneByLoginReturn"]["key"]["numeroOrdreAdresse"];
			$wsPsi = new WebservicesCompte();
			if(!$wsPsi->connectPsi()){
				 //$content="ERROR:".$wsPsi->getErrorMessage();
				$content = "L'espace priv&eacute; est momentan&eacute;ment indisponible, veuillez nous excuser de ce d&eacute;sagr&eacute;ment.";
				return $content;
			}
			else {
				//Nombre de factures
				$this->nbreFactures = $wsPsi->getNbrFactureARegler4User($paramPsi);
				$this->montantFactures = $wsPsi->getMontantFactureARegler4User($paramPsi);
			}
		}

		/**
		 * Recup des centres tech du departement
		 */
		if($this->personne["findPersonneByLoginReturn"]["adresse"]["commune"]["codePostal"] != "" && $this->personne["findPersonneByLoginReturn"]["adresse"]["commune"]["codePostal"]>0){
			$ws = new WebservicesAccess();
			if($ws->connect()){
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("codeDepartement");
				$objTransfert->setValue(substr($this->personne["findPersonneByLoginReturn"]["adresse"]["commune"]["codePostal"],0,2));
				$paramCT[] = $objTransfert;
				$result = $ws->getCentresTEchniques($paramCT);
				//if(!$result && $ws->getErrorMessage()!="") echo "[error resultat:]".$ws->getErrorMessage();
				/**
				 * Calcul du centre le plus proche
				 */
				$precTot = 10000000000;
				foreach($result as $centre){
					if($centre["codePostal"]>$this->personne["findPersonneByLoginReturn"]["adresse"]["commune"]["codePostal"])
						$result = $centre["codePostal"]-$this->personne["findPersonneByLoginReturn"]["adresse"]["commune"]["codePostal"];
					else
						$result = $this->personne["findPersonneByLoginReturn"]["adresse"]["commune"]["codePostal"]-$centre["codePostal"];
					if($result < $precTot){
						$precTot = $result;
						$this->ct = $centre;
					}
				}
			}
		}

		/**
		 * On regarde si l'utilisateur est abonn� aux news et alertes
		 */
		if($this->personne["findPersonneByLoginReturn"]["coordonnees"]["email"] != ""){

			$query = "SELECT * FROM tx_fabformmail_abonne where email='".$this->personne["findPersonneByLoginReturn"]["coordonnees"]["email"]."'";
			$res = mysql(TYPO3_db,$query) or die ("req invalide : $query");

			if (mysql_num_rows($res)>0) {
				$row = mysql_fetch_array($res);
				if($row['newsletter']=="1")$this->isAbonNews = true;
				if($row['hidden']=="0")$this->isAbonAlert= true;
			}
		}
		$content=$this->getEspacePerso();

		if($_GET["debug"]){
			$this->getDebug();
		}

		return $this->pi_wrapInBaseClass($content);
	}

	function getDebug(){
		debug($this->personne);
	}

	function getEspacePerso(){
		$content='
		<p>
			<ul>';
				if($this->personne["findPersonneByLoginReturn"]["niveauIdentification"]!="FORT"){
				$content.='
				<li>
				'.htmlspecialchars($this->pi_getLL("txt_niveau_ident_faible")).'  <a href="'.$this->urlPassageIdentFort.'">'.htmlspecialchars($this->pi_getLL("label_clic_ici")).'</a>.
				</li>';
				}
				$content.='<li>
				'.htmlspecialchars($this->pi_getLL("txt_chgt_passwd")).', <a href="'.$this->pi_getPageLink("3681","",array("no_cache"=>"1")).'">'.htmlspecialchars($this->pi_getLL("label_clic_ici")).'</a>.
				</li>
			</ul>
		</p>
		<p>&nbsp;</p>
		<hr>
		<p>&nbsp;</p>
		<!-- le contenu de l\'espace prive -->
		<div id="ligneA">
			<div id="left">
				<fieldset>
					<legend>Votre compte</legend>
					<div id="contenuBox">
						<p>
						'.$this->personne["findPersonneByLoginReturn"]["titre"].' '.$this->personne["findPersonneByLoginReturn"]["prenom"].' '.$this->personne["findPersonneByLoginReturn"]["nom"].'<br/>
						'.$this->personne["findPersonneByLoginReturn"]["adresse"]["adresse"].'<br/>';
						if($this->personne["findPersonneByLoginReturn"]["adresse"]["complementAdresse"] != ""){
							$content .=$this->personne["findPersonneByLoginReturn"]["adresse"]["complementAdresse"].'<br/>';
						}
						$content .=
						$this->personne["findPersonneByLoginReturn"]["adresse"]["commune"]["codePostal"].' '.$this->personne["findPersonneByLoginReturn"]["adresse"]["commune"]["libelle"].'<br/>
						'.$this->personne["findPersonneByLoginReturn"]["coordonnees"]["email"] .'<br/>
						</p>
					</div>
					<div id="boutonBoitePosition">
						<a id="lienFonctionPetit" style="color:white;text-decoration:none" href="'.$this->urlModifCompte.'">Modifier</a>
					</div>
				</fieldset>
			</div>
			<div id="right">
				<fieldset>
					<legend>Votre cr�dit de points</legend>
					<div id="contenuBox">
						<p>Actuellement, ';
						if($this->personne["findPersonneByLoginReturn"]["nombrePoint"]=="" || $this->personne["findPersonneByLoginReturn"]["nombrePoint"]=="0"){
							$content .='vous n\'avez pas de cr&eacute;dit de points';
						}
						else{
						$content .='
							<br/>votre cr�dit de points s\'�l�ve �: <strong>'.(($this->personne["findPersonneByLoginReturn"]["nombrePoint"]=="")?0:$this->personne["findPersonneByLoginReturn"]["nombrePoint"]).' pts</strong>.</p>';
						}
						$content.='
					</div>
					<div id="boutonBoitePosition">
						<a id="lienFonctionPetit" style="color:white;text-decoration:none" href="'.$this->urlAchatPoint.'">Acheter</a>
					</div>
				</fieldset>
			</div>
		</div>
		<div id="ligneB">
			<div id="left">
				<fieldset>
					<legend>Vos pr�f�rences portail</legend>
					<div id="contenuBox">
					<p>
					';
					if(!isset($this->personne["findPersonneByLoginReturn"]["coordonnees"]["email"]) || $this->personne["findPersonneByLoginReturn"]["coordonnees"]["email"] ==""){
						$content.='Vous n\'avez pas sp�cifi� de mail, veuillez modifier votre compte en cons�quence afin d\'utiliser ce service.';
					} else {

					$content.='
					<form action="'.$this->pi_getPageLink("2627").'" name="tx_fabformmail_pi1fname" method="POST">
					 	<input type="hidden" name="tx_fabformmail_pi1[DATA][email]" value="'.$this->personne["findPersonneByLoginReturn"]["coordonnees"]["email"].'">
					</form>
						<div style="float:left;width:330px">
							<p><input type="checkbox" id="name" '.(($this->isAbonNews)?"checked":"").' disabled>Inscrit � la news letter</p>
						</div>
						<div style="float:left;width:330px">
							<p><input type="checkbox" id="name" '.(($this->isAbonAlert)?"checked":"").' disabled>Abonnement aux alertes mails</p>
						</div>
					';
					}
					$content.='
					</p>
					</div>
					<div id="boutonBoitePosition">
						<a id="lienFonctionPetit" style="color:white;text-decoration:none" href="javascript:document.tx_fabformmail_pi1fname.submit();">Modifier</a>
					</div>
				</fieldset>
			</div>';
		if($this->personne["findPersonneByLoginReturn"]["niveauIdentification"]=="FAIBLE"){
			$content .='
			<div id="right">
				<fieldset>
					<legend>Vos factures</legend>
					<div id="contenuBox">&nbsp;</div>
					<div id="boutonBoitePosition">
						&nbsp;
						<a id="lienFonctionPetit" style="color:white;text-decoration:none" target="_blank" href="http://www4.haras-nationaux.fr/compte/Gestion_Compte.php?idClient=0&coope='.$_SESSION["portalId"].'&client=">T�l�charger mes factures et avoir</a>
					</div>
				</fieldset>
			</div>
		</div>';
		}

			if($this->personne["findPersonneByLoginReturn"]["niveauIdentification"]=="FORT"){
			$content .='
			<!-- Affiche si fort -->
			<div id="right">
				<fieldset>
					<legend>Vos factures</legend>
					<div id="contenuBox">
					<p>
					';
					if($this->nbreFactures !="" || $this->nbreFactures >0){
						$content.='Il reste <STRONG>'.(($this->nbreFactures =="")?0:$this->nbreFactures).'</STRONG> factures en attente de r�glement pour un montant total de <STRONG>'.(($this->montantFactures == "")?0:$this->montantFactures).'</STRONG> euros TTC.';
					}
					else{
						$content.='Aucune facture en attente.';
					}
					$content.='
					</p>
					</div>
					<div id="boutonBoitePosition">
						&nbsp;
						<a id="lienFonctionPetit" style="color:white;text-decoration:none" target="_blank" href="http://www4.haras-nationaux.fr/compte/Gestion_Compte.php?idClient=1&coope=&client='.$this->personne["findPersonneByLoginReturn"]["key"]["numeroPersonne"].'">T�l�charger mes factures et avoir</a>
					</div>
				</fieldset>
			</div>
		</div>
		<hr STYLE="margin-top:20px;margin-bottom:10px;width:685px;float:left;">
		<div id="ligneC">
			<div id="left">
				<fieldset>
					<legend>Vos naissances</legend>
					<div id="contenuBox">
					<p>';
					if($this->nbreNaissance["getNbrNaissanceAnneeEnCoursReturn"]=="" || $this->nbreNaissance["getNbrNaissanceAnneeEnCoursReturn"]=="0"){
						$content .='Vous n\'avez pas d&eacute;clar&eacute; de naissance';
					}
					else{
						$content .='Vous avez d�clar� <strong>'.(($this->nbreNaissance["getNbrNaissanceAnneeEnCoursReturn"]=="")?0:$this->nbreNaissance["getNbrNaissanceAnneeEnCoursReturn"]).'</strong> naissances en 2006.';
					}
					$content .='
					</p>
					<div id="boutonBoitePosition">
						<a id="lienFonctionPetit" style="float:right;color:white;text-decoration:none" href="'.$this->urlDeclaNovelleNaissance.'" target="_blank">Consulter - G�rer vos naissances</a>
					</div>
					<div id="boutonBoitePosition">
					<a id="lienFonctionPetit" style="float:right;color:white;text-decoration:none" href="'.$this->urlDeclaResultNeg.'" target="_blank">D�clarer un r�sultat n�gatif</a>
					</div>
					<div id="boutonBoitePosition">
					<a id="lienFonctionPetit" style="float:right;color:white;text-decoration:none" href="http://www.haras-nationaux.fr/portail/index.php?id=3573">Consulter un dossier d\'�levage</a>
					</div>
					</div>
				</fieldset>
			</div>
			<!--
			<div id="right">
				<fieldset>
					<legend>Vos lieux de d�tention</legend>
					<div id="contenuBox">';
					if($this->nbreLieudetention["getNbrLieuDetentionReturn"]=="" || $this->nbreLieudetention["getNbrLieuDetentionReturn"]=="0"){
						$content .= '<p>Vous n\'avez pas de lieu de d�tention en gestion</p>';
					} else{
						$content .= '<p>Vous g�rez <strong>'.(($this->nbreLieudetention["getNbrLieuDetentionReturn"]=="")?0:$this->nbreLieudetention["getNbrLieuDetentionReturn"]).'</strong> lieux de d�tention d\'�quid�s.</p>';
					}
					$content .='
					</div>
					<div id="boutonBoitePosition"><a id="lienFonctionPetit" style="float:right;color:white;text-decoration:none" href="#">Consulter - G�rer</a></div>
				</fieldset>
			</div>
			-->
		</div>
		<div id="ligneD">
			<div id="large">
				<fieldset>
					<legend>Vos chevaux</legend>
						<div style="float:left;width:330px">
							<p>Vous �tes propri�taire de <strong>'.(($this->nbreChevaux["getNbrChevauxReturn"]=="")?0:$this->nbreChevaux["getNbrChevauxReturn"]).'</strong> '.(($this->nbreChevaux["getNbrChevauxReturn"]<2)?"cheval":"chevaux").'
							<!--
							<div style="float:right;margin:2px;">
								<a id="lienFonctionPetit" style="color:white;text-decoration:none" href="'.$this->urlGererConsulterCheval.'">Consulter - G�rer</a>
								<a id="lienFonctionPetit" style="color:white;text-decoration:none" href="'.$this->urlDeclarerCheval.'"">D�clarer un achat</a>
							</div>
							-->
							</p>
						</div>
						<div style="float:right;width:330px">
							<p><!-- Vous avez <strong>KO</strong> --> Annonces S.O.S poulain.
							<div style="float:right;margin:2px;">
								<a id="lienFonctionPetit" style="color:white;text-decoration:none" href="'.$this->urlAjoutSosPoulain.'">Modifier</a>
								<a id="lienFonctionPetit" style="color:white;text-decoration:none" href="'.$this->urlModifSosPoulain.'">Ajouter</a>
							</div>
							</p>
						</div>
						<div style="float:left;width:330px;margin-top:10px">
							&nbsp;
						</div>
						<div style="float:right;width:330px;margin-top:10px">
							<!--<p>Vous avez <strong>PAS DE WEB SERVICE</strong> annonces chevaux � vendre.
							<div style="float:right;margin:2px;">
								<a id="lienFonctionPetit" style="color:white;text-decoration:none" href="#">Modifier</a>
								<a id="lienFonctionPetit" style="color:white;text-decoration:none" href="#">Ajouter</a>
							</div>-->
							<p>Vos annonces chevaux � vendre.
							<div style="float:right;margin:2px;">
								<a id="lienFonctionPetit" style="color:white;text-decoration:none" href="'.$this->urlGererConsulterCheval.'">Modifier</a>
								<a id="lienFonctionPetit" style="color:white;text-decoration:none" href="'.$this->urlDeclarerCheval.'"">Ajouter</a>
							</div>
							</p>
						</div>
				</fieldset>
			</div>
		</div>
		<!-- END -->';
		}
		//////////////////////////////////////////////////////////////
		else if($this->personne["findPersonneByLoginReturn"]["niveauIdentification"]=="FAIBLE"){
			$content .='
		<div id="ligneD">
			<div id="large">
				<fieldset>
					<legend>Vos chevaux</legend>
						<div style="float:left;width:330px;">
							<p>Vos annonces chevaux � vendre.
							<div style="float:right;margin:2px;">
								<a id="lienFonctionPetit" style="color:white;text-decoration:none" href="'.$this->urlGererConsulterCheval.'">Modifier</a>
								<a id="lienFonctionPetit" style="color:white;text-decoration:none" href="'.$this->urlDeclarerCheval.'"">Ajouter</a>
							</div>
							</p>
						</div>
						<div style="float:right;width:330px;">
							<p>Vos annonces S.O.S poulain.
							<div style="float:right;margin:2px;">
								<a id="lienFonctionPetit" style="color:white;text-decoration:none" href="'.$this->urlAjoutSosPoulain.'">Modifier</a>
								<a id="lienFonctionPetit" style="color:white;text-decoration:none" href="'.$this->urlModifSosPoulain.'">Ajouter</a>
							</div>
							</p>
						</div>
				</fieldset>
			</div>
		</div>
		<!-- END -->';
		} else {
			$content .='</div>';
		}
		$content .='
		<hr STYLE="margin-top:20px;margin-bottom:10px;width:685px;float:left;">';
		if($this->ct != null){
		$content .='
		<div id="ligneD">
			<div id="large">
				<fieldset>
					<legend>Votre interlocuteur des Haras nationaux le plus proche</legend><p>
					'.$this->ct["nom"].'<br/>';
					//$this->pi_linkToPage($this->ct["nom"],2973,'',array("numPerso"=>$this->ct["numPerso"],"action"=>"liste","no_cache"=>1)).'<br/>';
					$txt = null;
					if($this->ct["actCtmp"]=="O")
						$txt =($txt == null)?"Centre de mise en place":$txt." et Centre de mise en place";
					if($this->ct["actCtpr"]=="O")
						$txt =($txt == null)?"Centre de production":$txt." et Centre de production";
					if($this->ct["actCttre"]=="O")
						$txt =($txt == null)?"Centre de transfert":$txt." et centre de transfert embryonnaire";
					if($txt != null) $content .=$txt."<br />";
					$content .="<span id='adresseCT'>".$this->ct["adresse1"]."</span> <span id='adresseCT'>".$this->ct["adresse2"]."</span> <span id='codepostalCT'>".$this->ct["codePostal"]."</span> <span id='communeCT'>".$this->ct["libelleCommune"]."</span><br/>";
					if($this->ct["telephone"] != "")
					$content .=$this->pi_getLL("libelle_telephone")." : ".$this->ct["telephone"];
					if($this->ct["telecopie"] != ""){
						$content .=" ".$this->ct["telecopie"];
					}
					if($this->ct["telPortable"]){
						$content .=" ".$this->pi_getLL("libelle_telport")." : ".$this->ct["telPortable"];
					}
					if($this->ct["mail"]){
						$content .=" ".$this->pi_getLL("libelle_mail")." : ".$this->ct["mail"];
					}
					$content.='
				</fieldset>
			</div>
		</div>
		<hr STYLE="margin-top:20px;margin-bottom:10px;width:685px;float:left;">';
		}
		//$content .="</div>";
		return $content;
	}
}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube04_CAS/pi5/class.tx_dlcube04CAS_pi5.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube04_CAS/pi5/class.tx_dlcube04CAS_pi5.php"]);
}

?>
