<?php
header('Pragma: public');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
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
require_once (PATH_tslib . "class.tslib_pibase.php");
include_once ("typo3conf/ext/dlcube_hn_01/class.WebservicesCompte.php");
include_once ("typo3conf/ext/dlcube_hn_01/class.WebservicesAccess.php");
error_reporting(0);

class tx_dlcube04CAS_pi5 extends tslib_pibase {
	var $prefixId = "tx_dlcube04CAS_pi5"; // Same as class name
	var $scriptRelPath = "pi5/class.tx_dlcube04CAS_pi5.php"; // Path to this script relative to the extension dir.
	var $extKey = "dlcube04_CAS"; // The extension key.
	var $personne = null;
	var $ct = null;
	var $isAbonNews = false;
	var $isAbonAlert = false;
	var $isAbonMvi = false;
	var $nbreNaissance = 0;
	var $nbreLieudetention = 0;
	var $nbreFactures = 0;
	var $montantFactures = 0;

	var $urlPassageIdentFort = null;
	var $urlDeclaNovelleNaissance = null;
	var $urlDeclaResultNeg = null;
	var $urlModifCompte = null;
	var $urlAchatPoint = null;
	var $urlGererConsulterCheval = null;
	var $urlDeclarerCheval = null;
	//var $urlGererConsulterCheval = null;//"http://xinf-devlinux:8080/cid-internet-mobile-AV-CI/achatvente/ListeChevauxAction.do?dispatch=initDataBeforeLoad";
	//var $urlDeclarerCheval = null;//"http://xinf-devlinux:8080/cid-internet-mobile-AV-CI/achatvente/AchatChevalAction.do?dispatch=initDataBeforeLoad";
	var $urlModifSosPoulain = null;
	var $urlAjoutSosPoulain = null;
	var $urlGererSaillies = null;
	var $urlEditerAttestCertif = null;
	var $urlSuiviSanitaire = null;
	var $urlGestionLieuxDetention = null;
	var $urlTranspondeur = null;
	var $urlControle = null;
	var $urlWebMail = null;
	var $urlInfoCheval = null;
	var $urlGDP2 = null;

	var $nombreEtalonSaillie = 0;
	var $nombreEtalonSaillieIA = 0;

	var $templateLambda = null;
	var $userId;
	var $typeExecution = null; /**dev|dev_ext|prod*/

	/**
	 * Plug-in pour creation de comptes CAS
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_USER_INT_obj = 1; // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$this->pi_loadLL();
		$this->templateLambda = $this->cObj->fileResource("EXT:dlcube04_CAS/pi5/espace_perso_lambda.tmpl");
		session_start();
		if(!isset($_GET["userid"])){
			if(!isset($_SESSION["portalId"]) || $_SESSION["portalId"]==""){
				header("Location: index.php?id=2822");
			}
		}

		$this->userId = (isset ($_GET["userid"]))?$_GET["userid"]:$_SESSION["portalId"];

		$this->typeExecution = "dev_ext";
		$this->doLoadUrls($this->typeExecution);
		$this->dolLaodServices();

		$espaceTypeA = array (
			"1"
		);
		$espaceTypeB = array (
			"2",
			"3",
			"4",
			"5"
		);
		$espaceTypeC = array (
			"8"
		);
		$espaceTypeD = array (
			"7"
		);

		//$this->personne->profil->id=(isset($_GET["idProfil"]))?$_GET["idProfil"]:1;
		//$this->personne->key->numeroPersonne="12333";
		//print_r($this->personne);

		if (in_array($this->personne->profil->id, $espaceTypeA))
			$content = $this->getEspacePersoTypeA();
		elseif (in_array($this->personne->profil->id, $espaceTypeB)) $content = $this->getEspacePersoTypeB();
		elseif (in_array($this->personne->profil->id, $espaceTypeC)) $content = $this->getEspacePersoTypeC();
		elseif (in_array($this->personne->profil->id, $espaceTypeD) || $this->personne->profil->id > 100) $content = $this->getEspacePersoTypeD();
		else
			$content = $this->getEspacePersoTypeE();

		if ($_GET["debug"])
			$this->getDebug();

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * methode de creation de l'architecture de WebServices
	 */
	function dolLaodServices() {
		$param = array (
			/*"login" => $this->userId,
			"ctx" => null*/
			"in0" => $this->userId,
			"in1" => ""
		);
		$ws = new WebservicesCompte($this->typeExecution);
		if (!$ws->connectIdent()) {
			$content = "ERROR:" . $ws->getErrorMessage();
			$content = "L'espace priv&eacute; est momentan&eacute;ment indisponible, veuillez nous excuser de ce d&eacute;sagr&eacute;ment.";
			return $content;
		}

		$this->personne = $ws->getPersonneByLogin($param)->out;
		//print_r($this->personne);
		if($ws->getErrorMessage() != "") echo "<font color='red'>ERROR RECUPERATION PERSONNE CID (WS:findPersonneByLogin) :".$ws->getErrorMessage()."</font><br/>";

		//exit();
		/**
		 * recuperation du nombre de naissance, de lieux de detention
		 */
		/*$paramCid = array (
			"login" => $this->userId,
			"ctx" => null
		);*/
		$paramCid = array (
			"login" => $this->userId,
			"ctx" => ""
		);

		$wsCid = new WebservicesCompte($this->typeExecution);
		if (!$wsCid->connectCid()) {
			//$content="ERROR:".$wsCid->getErrorMessage();
			$content = "L'espace priv&eacute; est momentan&eacute;ment indisponible, veuillez nous excuser de ce d&eacute;sagr&eacute;ment.";
			return $content;
		}
		$this->nbreNaissance = $wsCid->getNbrNaissanceAnneeEnCours4User($paramCid)->getNbrNaissanceAnneeEnCoursReturn;
		//echo "nbre naissance:";
		//print_r($this->nbreNaissance);
		if($wsCid->getErrorMessage() != "") echo "<font color='red'>ERROR RECUPERATION NOMBRE DE NAISSANCE CID (WS:getNbrNaissanceAnneeEnCours):".$wsCid->getErrorMessage()."</font><br/>";
		$this->nbreLieudetention = $wsCid->getNbrLieuDetention4User($paramCid)->getNbrLieuDetentionReturn;
		//echo "nbre detention:";
		//print_r($this->nbreLieudetention);
		if($wsCid->getErrorMessage() != "") echo "<font color='red'>ERROR RECUPERATION NOMBRE LIEU DE DETENTION CID (WS:getNbrLieuDetention):".$wsCid->getErrorMessage()."</font><br/>";
		$this->nbreChevaux = $wsCid->getNbrChevaux4User($paramCid)->getNbrChevauxReturn;
		//echo "nbre chevaux:";
		//print_r($this->nbreChevaux);
		if($wsCid->getErrorMessage() != "") echo "<font color='red'>ERROR RECUPERATION NOMBRE DE CHEVAUX CID (WS:getNbrChevaux):".$wsCid->getErrorMessage()."</font><br/>";

		/**
		 * recuperation du nombre de factures et le montant total
		 */

		if ($this->personne->key->numeroPersonne != "") {
			$paramPsi[] = $this->personne->key->numeroPersonne;
			$paramPsi[] = $this->personne->key->numeroOrdreAdresse;
			$wsPsi = new WebservicesCompte($this->typeExecution);
			if (!$wsPsi->connectPsi()) {
				//$content="ERROR:".$wsPsi->getErrorMessage();
				$content = "L'espace priv&eacute; est momentan&eacute;ment indisponible, veuillez nous excuser de ce d&eacute;sagr&eacute;ment.";
				return $content;
			} else {
				//Nombre de factures
				$this->nbreFactures = $wsPsi->getNbrFactureARegler4User($paramPsi);
				$this->montantFactures = $wsPsi->getMontantFactureARegler4User($paramPsi);
			}
		}

		/**
		 * Recup des centres tech du departement
		 */
		if ($this->personne->adresse->commune->codePostal != "" && $this->personne->adresse->commune->codePostal > 0) {
			$ws = new WebservicesAccess($this->typeExecution);
			if ($ws->connect()) {
				$objTransfert = new ObjectTransfertWS();
				$objTransfert->setKey("codeDepartement");
				$objTransfert->setValue(substr($this->personne->adresse->commune->codePostal, 0, 2));
				$paramCT[] = $objTransfert;
				$result = $ws->getCentresTEchniques($paramCT);
				//if(!$result && $ws->getErrorMessage()!="") echo "[error resultat:]".$ws->getErrorMessage();
				/**
				 * Calcul du centre le plus proche
				 */
				$precTot = 10000000000;
				foreach ($result as $centre) {
					if ($centre["codePostal"] > $this->personne->adresse->commune->codePostal)
						$result = $centre["codePostal"] - $this->personne->adresse->commune->codePostal;
					else
						$result = $this->personne->adresse->commune->codePostal - $centre["codePostal"];
					if ($result < $precTot) {
						$precTot = $result;
						$this->ct = $centre;
					}
				}
			}
		}

		/**
		 * On regarde si l'utilisateur est abonne aux news et alertes
		 */
		if ($this->personne->coordonnees->email != "") {

			$query = "SELECT * FROM tx_fabformmail_abonne where email='" . $this->personne->coordonnees->email. "'";
			$res = mysql(TYPO3_db, $query) or die("req invalide : $query");

			if (mysql_num_rows($res) > 0) {
				$row = mysql_fetch_array($res);
				if ($row['newsletter'] == "1")
					$this->isAbonNews = true;
				if ($row['hidden'] == "0")
					$this->isAbonAlert = true;
			}
		}

		/**
		 * chargement des infos liees a l'etalonnier'
		 */
		if ($this->personne->profil->id == 3) {
			$wsDPS = new WebservicesCompte($this->typeExecution);
			if ($wsDPS->connectDPS()) {
				$param[] = array ();
				//echo "date:" . date("Y");
				if ($this->typeExecution == "dev" || $this->typeExecution == "dev_ext") {
					$param[] = array (
						"serv" => "DEV",
						"nuPerso" => $this->personne->key->numeroPersonne,
						"anMonte" => date("Y")
					);
				} else {
					$param[] = array (
						"serv" => "PROD",
						"nuPerso" => $this->personne->key->numeroPersonne,
						"anMonte" => date("Y")
					);
				}

				$return = $wsDPS->getNbrSurPlace($param);
				$this->nombreEtalonSaillie = (isset ($return["getNbSurPlaceReturn"]) && $return["getNbSurPlaceReturn"] != "") ? $return["getNbSurPlaceReturn"] : 0;

				$return = $wsDPS->getNbrIA($param);
				$this->nombreEtalonSaillieIA = (isset ($return["getNbIAReturn"]) && $return["getNbIAReturn"] != "") ? $return["getNbIAReturn"] : 0;
			}
		}
	}

	/**
	 * Permet de charger toutes les URLS distantes suivant le type de développement
	 * les possibilité sont: prod,dev,dev_ext
	 * @param String $type
	 */
	function doLoadUrls($type) {
		$urlStandard = null;

		if ($type == "prod") {
			$urlStandard = "www4.haras-nationaux.fr:8080";
			$urlSir = "www4.haras_nationaux.fr:8080";
		}
		elseif ($type == "dev") {
			$urlStandard = "xinf-devlinux:8080";
			$urlSir = "cookie2.haras_nationaux.fr:8080";
		}
		elseif ($type == "dev_ext") {
			$urlStandard = "80.124.158.237:8080";
			$urlSir = "cookie2.haras_nationaux.fr:8080";
		}

		/**
		 * declaration des urls de redirection iframe
		 */
		$this->urlPassageIdentFort = $this->pi_getPageLink("3684");
		$this->urlAchatPoint = $this->pi_getPageLink("3675");
		$this->urlGererConsulterCheval = $this->pi_getPageLink("3674");
		$this->urlDeclarerCheval = $this->pi_getPageLink("3673");
		$this->urlModifSosPoulain = $this->pi_getPageLink("3672");
		$this->urlAjoutSosPoulain = $this->pi_getPageLink("3671");
		$this->urlModifCompte = $this->pi_getPageLink("3670");

		/**
		 * Declaration des URL pour accéder aux services externes
		 */
		$this->urlDeclaNovelleNaissance = "http://" . $urlStandard . "/cid-internet-web/declaration-naissance/ReferenceDeSaillieAction.do?dispatch=initDataBeforeLoad&typeDeclaration=POS";
		$this->urlDeclaResultNeg = "http://" . $urlStandard . "/cid-internet-web/declaration-naissance/ReferenceDeSaillieAction.do?dispatch=initDataBeforeLoad&typeDeclaration=NEG";
		$this->urlTranspondeur;
		"http://" . $urlStandard . "/cid-internet-mobile-AV-CI/signalTranspondeur/RechercheEquideAction.do?dispatch=initDataBeforeLoad";
		$this->urlControle;
		"http://" . $urlStandard . "/cid-internet-mobile-AV-CI/controleIdentite/RechercheControleIdentiteAction.do?dispatch=initDataBeforeLoad";
		$this->urlGererConsulterCheval = "http://" . $urlStandard . "/cid-internet-mobile-AV-CI/achatvente/ListeChevauxAction.do?dispatch=initDataBeforeLoad";
		$this->urlDeclarerCheval = "http://" . $urlStandard . "/cid-internet-mobile-AV-CI/achatvente/AchatChevalAction.do?dispatch=initDataBeforeLoad";

		$this->urlGererSaillies = "http://" . $urlSir . "/DPS_PRIVEES/Etalonnier/Index.jsp?serv=DPS";
		$this->urlEditerAttestCertif = "http://" . $urlSir . "/DPS_PRIVEES/Etalonnier/Index.jsp?serv=EDT";
		$this->urlSuiviSanitaire = "http://" . $urlSir . "/DPS_PRIVEES/Etalonnier/Index.jsp?serv=SAN";

		$this->urlWebMail = "https://mailsire.haras-nationaux.fr/";
		$this->urlInfoCheval = $this->pi_getPageLink("2649");
		$this->urlGDP2 = "http://www.haras-nationaux.fr/gdp2/";

		$this->urlGestionLieuxDetention = "#";
	}

	function getDebug() {
		debug($this->personne);
	}

	/**
	 * Fabrication de l'espace perso pour les profils 1
	 */
	function getEspacePersoTypeA() {
		//print_r($this->personne);

		$content = "";
		$content .= $this->doLoadEntete();
		$subpartSeparateur = $this->cObj->getSubpart($this->templateLambda, "###SEPARATEUR###");
		$content .= $this->cObj->substituteMarkerArrayCached($subpartSeparateur, array (), array (), array ());

		$content .= $this->doLoadVotreCompte();
		$content .= $this->doLoadCreditPoints();
		$content .= $this->doLoadPrefPortail();
		$content .= $this->doLoadFacture();
		$content .= $this->doLoadChevauxFaible();
		$content .= $this->cObj->substituteMarkerArrayCached($subpartSeparateur, array (), array (), array ());
		$content .= $this->doLoadCentreTechnique();
		$content .= $this->cObj->substituteMarkerArrayCached($subpartSeparateur, array (), array (), array ());
		return $content;
	}

	/**
	 * Fabrication de l'espace perso pour les profils 2,3,4,5
	 */
	function getEspacePersoTypeB() {
		$content = "";
		$content .= $this->doLoadEntete();
		$subpartSeparateur = $this->cObj->getSubpart($this->templateLambda, "###SEPARATEUR###");
		$content .= $this->cObj->substituteMarkerArrayCached($subpartSeparateur, array (), array (), array ());

		$content .= $this->doLoadVotreCompte();
		$content .= $this->doLoadCreditPoints();
		$content .= $this->doLoadPrefPortail();
		$content .= $this->doLoadFacture();

		$content .= $this->cObj->substituteMarkerArrayCached($subpartSeparateur, array (), array (), array ());
		$content .= $this->doLoadNaissances();
		if ($this->personne->profil->id == 3)
			$content .= $this->doLoadSaillie();

		$content .= $this->doLoadChevaux();
		$content .= $this->cObj->substituteMarkerArrayCached($subpartSeparateur, array (), array (), array ());
		$content .= $this->doLoadCentreTechnique();
		$content .= $this->cObj->substituteMarkerArrayCached($subpartSeparateur, array (), array (), array ());
		return $content;
	}

	/**
	 * Fabrication de l'espace perso pour les profils 8
	 */
	function getEspacePersoTypeC() {
		$content = "";
		$content .= $this->doLoadEntete();
		$subpartSeparateur = $this->cObj->getSubpart($this->templateLambda, "###SEPARATEUR###");
		$content .= $this->cObj->substituteMarkerArrayCached($subpartSeparateur, array (), array (), array ());

		$content .= $this->doLoadVotreCompte();
		$content .= $this->doLoadPrefPortail();
		$content .= $this->cObj->substituteMarkerArrayCached($subpartSeparateur, array (), array (), array ());
		$content .= $this->doLoadServicesInternet();
		$content .= $this->cObj->substituteMarkerArrayCached($subpartSeparateur, array (), array (), array ());
		$content .= $this->doLoadCentreTechnique();
		$content .= $this->cObj->substituteMarkerArrayCached($subpartSeparateur, array (), array (), array ());
		return $content;
	}

	function getEspacePersoTypeD() {
		$content = "";
		$subpartSeparateur = $this->cObj->getSubpart($this->templateLambda, "###SEPARATEUR###");
		$content .= $this->cObj->substituteMarkerArrayCached($subpartSeparateur, array (), array (), array ());

		$content .= $this->doLoadVotreComptePersonnelHn();
		$content .= $this->doLoadPrefPortail();
		$content .= $this->cObj->substituteMarkerArrayCached($subpartSeparateur, array (), array (), array ());
		if ($this->personne->profil->id == 7)
			$content .= $this->doLoadServicesInternetPersonnelHnPlus();
		else
			$content .= $this->doLoadServicesInternetPersonnelHn();
		$content .= $this->cObj->substituteMarkerArrayCached($subpartSeparateur, array (), array (), array ());
		return $content;
	}

	function getEspacePersoTypeE() {
		$content = "";
		$subpartSeparateur = $this->cObj->getSubpart($this->templateLambda, "###SEPARATEUR###");
		$content .= $this->cObj->substituteMarkerArrayCached($subpartSeparateur, array (), array (), array ());

		$content .= $this->doLoadVotreCompteNoModif();
		$content .= $this->doLoadPrefPortail();
		$content .= $this->cObj->substituteMarkerArrayCached($subpartSeparateur, array (), array (), array ());
		$content .= $this->doLoadServicesInternetMinimum();
		$content .= $this->cObj->substituteMarkerArrayCached($subpartSeparateur, array (), array (), array ());
		$content .= $this->doLoadCentreTechnique();
		$content .= $this->cObj->substituteMarkerArrayCached($subpartSeparateur, array (), array (), array ());
		return $content;
	}

	function doLoadEntete() {
		$content = "";
		//Affichage et gestion pour passer en profil FORT
		if ($this->personne->niveauIdentification == "FAIBLE") {
			$subpartEnteteFaible = $this->cObj->getSubpart($this->templateLambda, "###ENTETE_FAIBLE###");
			$markerArray["###TXT_NIVEAU_IDENT_FAIBLE###"] = htmlspecialchars($this->pi_getLL("txt_niveau_ident_faible"));
			$markerArray["###URL_PASSAGE_IDENT_FORT###"] = $this->urlPassageIdentFort;
			$markerArray["###LABEL_CLIC_A###"] = htmlspecialchars($this->pi_getLL("label_clic_ici"));

			$markerArray["###TXT_CHGT_PASSWD###"] = htmlspecialchars($this->pi_getLL("txt_chgt_passwd"));
			$markerArray["###URL_CHGT_PASSWD###"] = $this->pi_getPageLink("3681", "", array (
				"no_cache" => "1"
			));
			$markerArray["###LABEL_CLIC_B###"] = htmlspecialchars($this->pi_getLL("label_clic_ici"));
			$content .= $this->cObj->substituteMarkerArrayCached($subpartEnteteFaible, $markerArray, array (), array ());
		} else {
			//Changement de mot de passe
			$markerArray = null;
			$subpartEnteteFort = $this->cObj->getSubpart($this->templateLambda, "###ENTETE_FORT###");
			$markerArray["###TXT_CHGT_PASSWD###"] = htmlspecialchars($this->pi_getLL("txt_chgt_passwd"));
			$markerArray["###URL_CHGT_PASSWD###"] = $this->pi_getPageLink("3681", "", array (
				"no_cache" => "1"
			));
			$markerArray["###LABEL_CLIC_ICI###"] = htmlspecialchars($this->pi_getLL("label_clic_ici"));
			$content .= $this->cObj->substituteMarkerArrayCached($subpartEnteteFort, $markerArray, array (), array ());
		}
		return $content;
	}

	/**
	 * Construction de la boite votre compte
	 */
	function doLoadVotreCompte() {
		$content = "";
		$subpartVotreCompte = $this->cObj->getSubpart($this->templateLambda, "###VOTRE_COMPTE###");
		$markerArray = null;
		$markerArray["###TITRE###"] = $this->personne->titre;
		$markerArray["###PRENOM###"] = $this->personne->prenom;
		$markerArray["###NOM###"] = $this->personne->nom;
		$markerArray["###ADRESSE###"] = $this->personne->adresse->adresse;

		if ($this->personne->adresse->complementAdresse != "") {
			$markerArray["###COMPLEMENT_ADRESSE###"] = $this->personne->adresse->complementAdresse . "<br/>";
		} else
			$markerArray["###COMPLEMENT_ADRESSE###"] = "";

		$markerArray["###CP###"] = $this->personne->adresse->commune->codePostal;
		$markerArray["###VILLE###"] = $this->personne->adresse->commune->libelle;
		$markerArray["###EMAIL###"] = $this->personne->coordonnees->email;
		$markerArray["###URL_MODIF_COMPTE###"] = $this->urlModifCompte;
		$markerArray["###LABEL_MODIFIER###"] = "Modifier";

		$content .= $this->cObj->substituteMarkerArrayCached($subpartVotreCompte, $markerArray, array (), array ());
		return $content;
	}

	/**
	 * Construction de la boite votre compte
	 */
	function doLoadVotreCompteNoModif() {
		$content = "";
		$subpartVotreCompte = $this->cObj->getSubpart($this->templateLambda, "###VOTRE_COMPTE_NO_MODIF###");
		$markerArray = null;
		$markerArray["###TITRE###"] = $this->personne->titre;
		$markerArray["###PRENOM###"] = $this->personne->prenom;
		$markerArray["###NOM###"] = $this->personne->nom;
		$markerArray["###ADRESSE###"] = $this->personne->adresse->adresse;

		if ($this->personne->adresse->complementAdresse != "") {
			$markerArray["###COMPLEMENT_ADRESSE###"] = $this->personne->adresse->complementAdresse . "<br/>";
		} else
			$markerArray["###COMPLEMENT_ADRESSE###"] = "";

		$markerArray["###CP###"] = $this->personne->adresse->commune->codePostal;
		$markerArray["###VILLE###"] = $this->personne->adresse->commune->libelle;
		$markerArray["###EMAIL###"] = $this->personne->coordonnees->email;

		$content .= $this->cObj->substituteMarkerArrayCached($subpartVotreCompte, $markerArray, array (), array ());
		return $content;
	}

	function doLoadVotreComptePersonnelHn() {
		$content = "";
		$subpartVotreCompte = $this->cObj->getSubpart($this->templateLambda, "###VOTRE_COMPTE_PERSONNEL_HN###");
		$markerArray = null;

		$markerArray["###EMAIL###"] = $this->personne->coordonnees->email;
		$markerArray["###TITRE###"] = $this->personne->titre;
		$markerArray["###PRENOM###"] = $this->personne->prenom;
		$markerArray["###NOM###"] = $this->personne->nom;
		$markerArray["###ADRESSE###"] = $this->personne->adresse->adresse;
		$markerArray["###CP###"] = $this->personne->adresse->commune->codePostal;
		$markerArray["###VILLE###"] = $this->personne->adresse->commune->libelle;

		if ($this->personne->adresse->complementAdresse != "") {
			$markerArray["###COMPLEMENT_ADRESSE###"] = $this->personne->adresse->complementAdresse . "<br/>";
		} else
			$markerArray["###COMPLEMENT_ADRESSE###"] = "";

		$content .= $this->cObj->substituteMarkerArrayCached($subpartVotreCompte, $markerArray, array (), array ());
		return $content;
	}

	function doLoadCreditPoints() {
		$content = "";
		$subpart = $this->cObj->getSubpart($this->templateLambda, "###CREDIT_POINT###");
		$markerArray = null;
		if ($this->personne->nombrePoint == "" || $this->personne->nombrePoint == "0")
			$markerArray["###INFO_POINTS###"] = 'vous n\'avez pas de cr&eacute;dit de points';
		else
			$markerArray["###INFO_POINTS###"] = "votre cr&eacute;dit de points s'&eacute:l&egrave;ve &agrave;: <strong>" . (($this->personne->nombrePoint == "") ? 0 : $this->personne->nombrePoint) . " pts</strong>.</p>";

		$markerArray["###URL_ACHAT_POINT###"] = $this->urlAchatPoint;

		$content .= $this->cObj->substituteMarkerArrayCached($subpart, $markerArray, array (), array ());
		return $content;
	}

	function doLoadPrefPortail() {
		$content = "";
		$subpart = $this->cObj->getSubpart($this->templateLambda, "###PREF_PORTAIL###");
		$markerArray = null;

		if (!isset ($this->personne->coordonnees->email) || $this->personne->coordonnees->email == "") {
			$markerArray["###CONTENT###"] = 'Vous n\'avez pas sp&eacute;cifi&eacute; de mail, veuillez modifier votre compte en cons&eacute;quence afin d\'utiliser ce service.';
		} else {
			$markerArray["###CONTENT###"] = '<form action="' . $this->pi_getPageLink("2627") . '" name="tx_fabformmail_pi1fname" method="POST">';
			$markerArray["###CONTENT###"] .= '<input type="hidden" name="tx_fabformmail_pi1[DATA][email]" value="' . $this->personne->coordonnees->email . '">';
			$markerArray["###CONTENT###"] .= '</form>';
			$markerArray["###CONTENT###"] .= '<p><input type="checkbox" id="name" ' . (($this->isAbonNews) ? "checked" : "") . ' disabled>Inscrit &agrave; la news letter</p>';
			$markerArray["###CONTENT###"] .= '<p><input type="checkbox" id="name" ' . (($this->isAbonAlert) ? "checked" : "") . ' disabled>Abonnement aux alertes mails</p>';
			if ($this->personne->profil->id == 8) {
				$markerArray["###CONTENT###"] .= '<p><input type="checkbox" id="name" ' . (($this->isAbonMvi) ? "checked" : "") . ' disabled>Abonnement au groupe MVI</p>';
			}

		}
		$content .= $this->cObj->substituteMarkerArrayCached($subpart, $markerArray, array (), array ());
		return $content;
	}

	function doLoadFacture() {
		$content = "";
		$subpart = $this->cObj->getSubpart($this->templateLambda, "###FACTURES###");
		$markerArray = null;

		if ($this->nbreFactures != "" || $this->nbreFactures > 0) {
			$markerArray["###CONTENT###"] = 'Il reste <STRONG>' . (($this->nbreFactures == "") ? 0 : $this->nbreFactures) . '</STRONG> factures en attente de r&eacute;glement pour un montant total de <STRONG>' . (($this->montantFactures == "") ? 0 : $this->montantFactures) . '</STRONG> euros TTC.';
		} else {
			$markerArray["###CONTENT###"] = 'Aucune facture en attente.';
		}

		$markerArray["###URL_GESTION_FACTURES###"] = 'http://www4.haras-nationaux.fr/compte/Gestion_Compte.php?idClient=1&coope=&client=' . $this->personne->key->numeroPersonne;

		$content .= $this->cObj->substituteMarkerArrayCached($subpart, $markerArray, array (), array ());
		return $content;
	}

	function doLoadNaissances() {
		$subpart = $this->cObj->getSubpart($this->templateLambda, "###NAISSANCES###");
		//$markerArray = null;
		if ($this->nbreNaissance == "" || $this->nbreNaissance == "0") {
			$markerArray["###CONTENT###"] = 'Vous n\'avez pas d&eacute;clar&eacute; de naissance';
		} else {
			$markerArray["###CONTENT###"] = 'Vous avez d&eacute;clar&eacute; <strong>' . (($this->nbreNaissance == "") ? 0 : $this->nbreNaissance) . '</strong> naissances en 2006.';
		}

		$markerArray["###URL_DECLA_NOUVELLE_NAISSANCE###"] = $this->urlDeclaNovelleNaissance;
		$markerArray["###URL_DECLA_RESULT_NEGATIF###"] = $this->urlDeclaResultNeg;
		$markerArray["###URL_CONSULT_DOSSIER###"] = "http://www.haras-nationaux.fr/portail/index.php?id=3573";

		$content = $this->cObj->substituteMarkerArrayCached($subpart, $markerArray, array (), array ());
		return $content;
	}

	function getEspacePerso_Old() {
		if ($this->nbreNaissance == "" || $this->nbreNaissance == "0") {
			$markerArray["###CONTENT###"] = 'Vous n\'avez pas d&eacute;clar&eacute; de naissance';
		} else {
			$markerArray["###CONTENT###"] = 'Vous avez d&eacute;clar&eacute; <strong>' . (($this->nbreNaissance == "") ? 0 : $this->nbreNaissance) . '</strong> naissances en 2006.';
		}

		$markerArray["###URL_DECLA_NOUVELLE_NAISSANCE###"] = $this->urlDeclaNovelleNaissance;
		$markerArray["###URL_DECLA_RESULT_NEGATIF###"] = $this->urlDeclaResultNeg;
		$markerArray["###URL_CONSULT_DOSSIER###"] = "http://www.haras-nationaux.fr/portail/index.php?id=3573";

		$content = $this->cObj->substituteMarkerArrayCached($subpart, $markerArray, array (), array ());
		return $content;

	}

	function doLoadSaillie() {
		$subpart = $this->cObj->getSubpart($this->templateLambda, "###SAILLIES###");
		$markerArray = null;

		//$this->nbreLieudetention["getNbrLieuDetentionReturn"]
		$markerArray["###CONTENT###"] = 'Vous g&eacute;rez ' . $this->nombreEtalonSaillie . ' &eacute;talons sur internet dont ' . $this->nombreEtalonSaillieIA . ' en IA';

		$markerArray["###URL_GERER_SAILLIES###"] = $this->urlGererSaillies;
		$markerArray["###URL_EDITER_ATTESTATIONS_CERTIFICATS###"] = $this->urlEditerAttestCertif;
		$markerArray["###URL_SUIVI_SANITAIRE###"] = $this->urlSuiviSanitaire;

		$content = $this->cObj->substituteMarkerArrayCached($subpart, $markerArray, array (), array ());
		return $content;
	}

	function doLoadLieuxDetention() {
		$subpart = $this->cObj->getSubpart($this->templateLambda, "###LIEUX_DETENTION###");
		$markerArray = null;
		if ($this->nbreLieudetention == "" || $this->nbreLieudetention == "0") {
			$content .= '<p>Vous n\'avez pas de lieu de d&eacute;tention en gestion</p>';
		} else {
			$content .= '<p>Vous g&eacute;rez <strong>' . (($this->nbreLieudetention == "") ? 0 : $this->nbreLieudetention) . '</strong> lieux de d&eacute;tention d\'&eacute;quid&eacute;s.</p>';
		}
		$markerArray["###CONTENT###"] = $content;
		$markerArray["###URL_GESTION_LIEUX_DETENTION###"] = $this->urlGestionLieuxDetention;

		$content = $this->cObj->substituteMarkerArrayCached($subpart, $markerArray, array (), array ());
		return $content;

	}

	function doLoadChevauxFaible() {
		$subpart = $this->cObj->getSubpart($this->templateLambda, "###CHEVAUXFAIBLE###");
		$markerArray = null;

		$markerArray["###URL_AJOUT_SOS_POULAIN###"] = $this->urlAjoutSosPoulain;
		$markerArray["###URL_MODIF_SOS_POULAIN###"] = $this->urlModifSosPoulain;
		$markerArray["###URL_GERER_CHEVAUX_VENDRE###"] = $this->urlGererConsulterCheval;
		$markerArray["###URL_DECLARER_CHEVAL_VENDRE###"] = $this->urlDeclarerCheval;

		$content = $this->cObj->substituteMarkerArrayCached($subpart, $markerArray, array (), array ());
		return $content;
	}

	function doLoadChevaux() {
		$subpart = $this->cObj->getSubpart($this->templateLambda, "###CHEVAUX###");
		$markerArray = null;

		$markerArray["###NOMBRE_CHEVAUX###"] = (($this->nbreChevaux == "") ? 0 : $this->nbreChevaux);
		$markerArray["###CHEVAL_ACCORD###"] = (($this->nbreChevaux < 2) ? "cheval" : "chevaux");

		$markerArray["###URL_GERER_CONSULTER_CHEVAL###"] = $this->urlGererConsulterCheval;
		$markerArray["###URL_DECLARER_CHEVAL###"] = $this->urlDeclarerCheval;
		// pas d'autres modifs VM
		$markerArray["###URL_AJOUT_SOS_POULAIN###"] = $this->urlAjoutSosPoulain;
		$markerArray["###URL_MODIF_SOS_POULAIN###"] = $this->urlModifSosPoulain;
		$markerArray["###URL_GERER_CHEVAUX_VENDRE###"] = $this->urlGererConsulterCheval;
		$markerArray["###URL_DECLARER_CHEVAL_VENDRE###"] = $this->urlDeclarerCheval;

		$content = $this->cObj->substituteMarkerArrayCached($subpart, $markerArray, array (), array ());
		return $content;
	}

	function doLoadCentreTechnique() {
		$subpart = $this->cObj->getSubpart($this->templateLambda, "###CENTRE_TECHNIQUE###");
		$markerArray = null;

		$markerArray["###NOM###"] = $this->ct["nom"];
		$markerArray["###ADRESSE###"] = $this->ct["adresse1"];
		$markerArray["###ADRESSE2###"] = $this->ct["adresse2"];
		$markerArray["###CP###"] = $this->ct["codePostal"];
		$markerArray["###VILLE###"] = $this->ct["libelleCommune"];

		$txt = null;
		if ($this->ct["actCtmp"] == "O")
			$txt = ($txt == null) ? "Centre de mise en place" : $txt . " et Centre de mise en place";
		if ($this->ct["actCtpr"] == "O")
			$txt = ($txt == null) ? "Centre de production" : $txt . " et Centre de production";
		if ($this->ct["actCttre"] == "O")
			$txt = ($txt == null) ? "Centre de transfert" : $txt . " et centre de transfert embryonnaire";
		if ($txt != null)
			$markerArray["###TEXT###"] = $txt . "<br/>";
		else
			$markerArray["###TEXT###"] = "";

		$markerArray["###TEL###"] = $this->ct["telephone"];
		$markerArray["###FAX###"] = $this->ct["telecopie"];
		$markerArray["###PORT###"] = $this->ct["telPortable"];
		$markerArray["###MAIL###"] = $this->ct["mail"];

		$content = $this->cObj->substituteMarkerArrayCached($subpart, $markerArray, array (), array ());
		return $content;
	}

	function doLoadServicesInternetMinimum() {
		$subpart = $this->cObj->getSubpart($this->templateLambda, "###SERVICES_INTERNET_MINIMUM###");
		$markerArray = null;
		$markerArray["###URL_CONTROLE###"] = $this->urlControle;
		$content = $this->cObj->substituteMarkerArrayCached($subpart, $markerArray, array (), array ());
		return $content;
	}

	function doLoadServicesInternet() {
		$subpart = $this->cObj->getSubpart($this->templateLambda, "###SERVICES_VETO_IDENT###");
		$markerArray = null;

		$markerArray["###URL_TRANSPONDEUR###"] = $this->urlTranspondeur;
		$markerArray["###URL_CONTROLE###"] = $this->urlControle;
		$markerArray["###URL_COMMANDER###"] = "#";

		$content = $this->cObj->substituteMarkerArrayCached($subpart, $markerArray, array (), array ());
		return $content;
	}

	function doLoadServicesInternetPersonnelHn() {
		$subpart = $this->cObj->getSubpart($this->templateLambda, "###SERVICES_PERSONNEL_HN###");
		$markerArray = null;

		$markerArray["###URL_COMMANDER###"] = "#";
		$markerArray["###URL_WEBMAIL###"] = $this->urlWebMail;
		$markerArray["###URL_AGENDA###"] = "#";
		$markerArray["###URL_INFO_CHEVAL###"] = $this->pi_getPageLink("2649");
		$markerArray["###URL_GDP###"] = $this->urlGDP2;

		$content = $this->cObj->substituteMarkerArrayCached($subpart, $markerArray, array (), array ());
		return $content;
	}

	function doLoadServicesInternetPersonnelHnPlus() {
		$subpart = $this->cObj->getSubpart($this->templateLambda, "###SERVICES_PERSONNEL_HN_PLUS###");
		$markerArray = null;

		$markerArray["###URL_TRANSPONDEUR###"] = $this->urlTranspondeur;
		$markerArray["###URL_CONTROLE###"] = $this->urlControle;
		$markerArray["###URL_COMMANDER###"] = "#";
		$markerArray["###URL_WEBMAIL###"] = $this->urlWebMail;
		$markerArray["###URL_AGENDA###"] = "#";
		$markerArray["###URL_INFO_CHEVAL###"] = $this->urlInfoCheval;
		$markerArray["###URL_GDP###"] = $this->urlGDP2;

		$content = $this->cObj->substituteMarkerArrayCached($subpart, $markerArray, array (), array ());
		return $content;
	}
}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube04_CAS/pi5/class.tx_dlcube04CAS_pi5.php"]) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube04_CAS/pi5/class.tx_dlcube04CAS_pi5.php"]);
}
?>
