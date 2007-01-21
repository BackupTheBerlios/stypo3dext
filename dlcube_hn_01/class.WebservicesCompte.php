<?php
include_once("typo3conf/ext/dlcube_hn_01/data/ObjectTransfertWS.php");
include_once("typo3conf/ext/dlcube_hn_01/php_inc/nusoap/mynusoap.php");
/**************************************************************
*
*  Copyright notice
*
*  (c) 2005 Guillaume Tessier<gtessier@dlcube.com>
*  All rights reserved**  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Classe de gestion de webservices
 * @author	Guillaume Tessier<gtessier@dlcube.com>
 */

 /**
  * Exemple d'appel
  * $ws = new WebServicesPortail();
  * if(!$ws->connect())echo $ws->getErrorMessage();
  * $param[] = new ObjectTransfertWS("codeRegion","BRG");
  * $param[] = new ObjectTransfertWS("codeDepartement","71");
  * $ws->getCentresTEchniques($param);
  */
class WebservicesCompte {
	var $wsdlServices;
	var $wsdlIdent;
	var $wsdlCid;
	var $wsdlPsi;
	var $client;
	var $errorMessage;

	function WebservicesCompte($type="prod"){
		$this->__construct($type);
	}

	function __construct($type="prod"){
		if($type=="prod")
			$url = "www4.haras-nationaux.fr:8080";
		else if($type=="dev")
			$url = "xinf-devlinux:8080";
		else if($type=="dev_ext")
			$url = "80.124.158.237:8080";
		else if($type=="local")
			$url = "127.0.0.1:8080";

		$this->wsdlServices = "http://".$url."/aih-services/services/PortailServicesImpl?wsdl";
		$this->wsdlIdent = "http://".$url."/aih-services/services/IdentificationServicesImpl?wsdl";
		$this->wsdlCid = "http://".$url."/cid-services/services/InformationsDeclarantServicesImpl?wsdl";
		$this->wsdlPsi = "http://".$url."/psi/services/WsPsi?wsdl";
		$this->wsdlDPS = "http://".$url."/DPS_PRIVEES/services/WSNbEtalon?wsdl";
	}

	function connectServices(){
		$this->client = new soapclient_nusoap($this->wsdlServices, 'wsdl');
		if( !$this->client->getError())
			return true;
		$this->errorMessage = $this->client->getError();
		$this->client = null;
		return false;
	}

	function connectPsi(){
		$this->client = new soapclient_nusoap($this->wsdlPsi, 'wsdl');
		if( !$this->client->getError())
			return true;
		$this->errorMessage = $this->client->getError();
		$this->client = null;
		return false;
	}

	function connectIdent(){
		/*
		$this->client = new soapclient_nusoap($this->wsdlIdent, 'wsdl');
		if( !$this->client->getError())
			return true;
		$this->errorMessage = $this->client->getError();
		$this->client = null;
		return false;*/
		try {
		    // Nouvelle instance de la classe soapClient
		    $client = new SoapClient($this->wsdlIdent, array('trace' => 1, 'soap_version'  => SOAP_1_1));
		    // appel de la m�thode getServerDate du service web
		    //$O =  $client -> __call('getServerDate', array());
		    // Affichage du r�sultat
		    //echo $O->date ;
		    return true;
		} catch (SoapFault $fault) {
		    $this->errorMessage = $fault;
		}
		 return false;
	}

	function connectCid(){
		$this->client = new soapclient_nusoap($this->wsdlCid, 'wsdl');
		if( !$this->client->getError())
			return true;
		$this->errorMessage = $this->client->getError();
		$this->client = null;
		return false;
	}

	function connectDPS(){
		$this->client = new soapclient_nusoap($this->wsdlDPS, 'wsdl');
		if( !$this->client->getError())
			return true;
		$this->errorMessage = $this->client->getError();
		$this->client = null;
		return false;
	}

	/**
	 * Function de creation de compte CAS en WS CID
	 *-------------------------------------------------------------------
	 * Exemple:
	 * $param[] = array("p1"=>"val1");
	 * $param[] = array("p2"=>"val2");
	 * ------------------------------------------------------------------
	 * @param $param tableau d'objets de ObjectTransfertWS
	 * @return int|null
	 */
	function createCompte($parametres){
		/*if($this->client != null){
			$result = $this->client->call('createCompteCasForPortail', $parametres);
			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;*/
		$this->errorMessage=null;
		try {
		    // Nouvelle instance de la classe soapClient
		     $client = new SoapClient($this->wsdlServices, array('trace' => 1, 'soap_version'=> SOAP_1_1));
		    $result =  $client->createCompteCasForPortail($parametres);
		    return $result;
		} catch (SoapFault $fault) {
		    $this->errorMessage =$fault->faultstring;
		}
		return false;
	}

	/**
	 * Function de modification de compte CAS en WS CID
	 *-------------------------------------------------------------------
	 * Exemple:
	 * $param[] = array("p1"=>"val1");
	 * $param[] = array("p2"=>"val2");
	 * ------------------------------------------------------------------
	 * @param $param tableau d'objets de ObjectTransfertWS
	 * @return int|null
	 */
	function updateCompte($parametres){
		/*if($this->client != null){
			$result = $this->client->call('updateCompteCasForPortail', $parametres);
			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;*/
		$this->errorMessage=null;
		try {
		    // Nouvelle instance de la classe soapClient
		     $client = new SoapClient($this->wsdlServices, array('trace' => 1, 'soap_version'=> SOAP_1_1));
		    $result =  $client->updateCompteCasForPortail($parametres);
		    return $result;
		} catch (SoapFault $fault) {
		    $this->errorMessage =$fault->faultstring;
		}
		return false;
	}

	/**
	 * Function qui retourne les centres techniques avec
	 * comme filtre de recherche le tableau d'objets passé en paramètre
	 *-------------------------------------------------------------------
	 * Exemple:
	 * $param[] = array("p1"=>"val1");
	 * $param[] = array("p2"=>"val2");
	 * ------------------------------------------------------------------
	 * @param $param tableau d'objets de ObjectTransfertWS
	 * @return Object[] resultat webservice si OK || false si KO
	 */
	function getPersonneByLogin($parametres){
		$this->errorMessage=null;
		try {
		    // Nouvelle instance de la classe soapClient
		     $client = new SoapClient($this->wsdlIdent, array('trace' => 1, 'soap_version'=> SOAP_1_1));
		    $result =  $client->findPersonneByLogin($parametres);
		    return $result;
		} catch (SoapFault $fault) {
		    $this->errorMessage =$fault->faultstring;
		}
		return false;
	}

	/**
	 * Function qui retourne le nombre de chevaux du proprietaire
	 *-------------------------------------------------------------------
	 * Exemple:
	 * $param[] = array("p1"=>"val1");
	 * $param[] = array("p2"=>"val2");
	 * ------------------------------------------------------------------
	 * @param $param tableau d'objets de ObjectTransfertWS
	 * @return int resultat webservice
	 */
	function getNbrChevaux4User($parametres){
		/*$this->errorMessage=null;
		if($this->client != null){
			$result = $this->client->call('getNbrChevaux', $parametres);
			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;*/
		$this->errorMessage=null;
		try {
		    // Nouvelle instance de la classe soapClient
		    $client = new SoapClient($this->wsdlCid, array('trace' => 1, 'soap_version'=> SOAP_1_1));
		    $result =  $client->getNbrChevaux($parametres);
		    return $result;
		} catch (SoapFault $fault) {
		    $this->errorMessage =$fault->faultstring;
		}
		return false;
	}

	/**
	 * Function qui retourne le nombre de lieux de detention du proprietaire
	 *-------------------------------------------------------------------
	 * Exemple:
	 * $param[] = array("p1"=>"val1");
	 * $param[] = array("p2"=>"val2");
	 * ------------------------------------------------------------------
	 * @param $param tableau d'objets de ObjectTransfertWS
	 * @return int resultat webservice
	 */
	function getNbrLieuDetention4User($parametres){
		/*$this->errorMessage = null;
		if($this->client != null){
			$result = $this->client->call('getNbrLieuDetention', $parametres);
			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;*/
		$this->errorMessage=null;
		try {
		    // Nouvelle instance de la classe soapClient
		    $client = new SoapClient($this->wsdlCid, array('trace' => 1, 'soap_version'=> SOAP_1_1));
		    $result =  $client->getNbrLieuDetention($parametres);
		    return $result;
		} catch (SoapFault $fault) {
		    $this->errorMessage =$fault->faultstring;
		}
		return false;
	}

	/**
	 * Function qui retourne le nombre de naissance du proprietaire sur l'annee en cours
	 *-------------------------------------------------------------------
	 * Exemple:
	 * $param[] = array("p1"=>"val1");
	 * $param[] = array("p2"=>"val2");
	 * ------------------------------------------------------------------
	 * @param $param tableau d'objets de ObjectTransfertWS
	 * @return int resultat webservice
	 */
	function getNbrNaissanceAnneeEnCours4User($parametres){
		$this->errorMessage=null;
		try {
		    // Nouvelle instance de la classe soapClient
		    $client = new SoapClient($this->wsdlCid, array('trace' => 1, 'soap_version'=> SOAP_1_1));
		    $result =  $client->getNbrNaissanceAnneeEnCours($parametres);
		    return $result;
		} catch (SoapFault $fault) {
		    $this->errorMessage =$fault->faultstring;
		}
		return false;
/*
		$this->errorMessage=null;
		if($this->client != null){
			$result = $this->client->call('getNbrNaissanceAnneeEnCours', $parametres);
			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;*/
	}

	/**
	 * Function qui retourne le nombre de facture a regler
	 *-------------------------------------------------------------------
	 * Exemple:
	 * $param[] = array("p1"=>"val1");
	 * $param[] = array("p2"=>"val2");
	 * ------------------------------------------------------------------
	 * @param $param tableau d'objets de ObjectTransfertWS
	 * @return int resultat webservice
	 */
	function getNbrFactureARegler4User($parametres){
		if($this->client != null){
			$result = $this->client->call('getNombreFactureARegler', $parametres);
			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;
	}

	/**
	 * Function qui retourne le montant des factures a regler
	 *-------------------------------------------------------------------
	 * Exemple:
	 * $param[] = array("p1"=>"val1");
	 * $param[] = array("p2"=>"val2");
	 * ------------------------------------------------------------------
	 * @param $param tableau d'objets de ObjectTransfertWS
	 * @return int resultat webservice
	 */
	function getMontantFactureARegler4User($parametres){
		if($this->client != null){
			$result = $this->client->call('getMontantFactureARegler', $parametres);
			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;
	}

	/**
	 * Function qui retourne le nombre d'étalons d'un étalonnier sur place
	 */
	function getNbrSurPlace($parametres){
		if($this->client != null){
			$result = $this->client->call('getNbSurPlace', $parametres);
			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;
	}

	/**
	 * Function qui retourne le nombre d'étalons d'un étalonnier en IA
	 */
	function getNbrIA($parametres){
		if($this->client != null){
			$result = $this->client->call('getNbIA', $parametres);
			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;
	}

	/**
	 * 	Retourne un message d'eereur en provenance du client SOAP.
	 * @return String
	 */
	function getErrorMessage(){
		return $this->errorMessage;
	}

}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_01/class.WebservicesCompte.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_01/class.WebservicesCompte.php"]);
}
?>

