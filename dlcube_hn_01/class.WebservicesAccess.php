<?php
include_once("typo3conf/ext/dlcube_hn_01/data/ObjectTransfertWS.php");
include_once("typo3conf/ext/dlcube_hn_01/php_inc/nusoap/nusoap.php");
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
class WebservicesAccess {
	var $wsdl;
	var $client;
	var $errorMessage;

	function WebservicesAccess(){
		$this->__construct();
	}

	function __construct(){
		//$this->wsdl = "http://xinf-datastore2:8080/hndto/services/CTServicesPortail?wsdl";
		//$this->wsdl = "http://guitessier.dyndns.org:8080/hndto/services/CTServicesPortail?wsdl";
		$this->wsdl = "http://localhost:8080/hndto/services/CTServicesPortail?wsdl";
		$this->client = new soapclient_nusoap($this->wsdl, 'wsdl');
	}

	function connect(){
		$this->client = new soapclient_nusoap($this->wsdl, 'wsdl');
		if( !$this->client->getError())
			return true;

		$this->errorMessage = $this->client->getError();
		$this->client = null;
		return false;
	}

	/**
	 * Function qui retourne les centres techniques avec
	 * comme filtre de recherche le tableau d'objets passÃ© en paramÃ¨tre
	 *-------------------------------------------------------------------
	 * Exemple:
	 * $parametres[] = new ObjectTransfertWS("codeRegion","BRG");
	 * $parametres[] = new ObjectTransfertWS("codeDepartement","71");
	 * ------------------------------------------------------------------
	 * @param $filtres tableau d'objes de ObjectTransfertWS
	 * @param $max nombre maximum de rÃ©ponses
	 * @return Object[] resultat webservice si OK || false si KO
	 */
	function getCentresTEchniques($filtres,$max=50){
		if($this->client != null){
			$result = $this->client->call('chercheCentreTechnique', array('keyFields'=>$filtres));
			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;
	}

	/**
	 * Function qui retourne les etalons avec
	 * comme filtre de recherche le tableau d'objets passes en parametre
	 *-------------------------------------------------------------------
	 * Exemple:
	 * $filtres[] = new ObjectTransfertWS("codeRegion","BRG");
	 * $filtres[] = new ObjectTransfertWS("codeDepartement","71");
	 * ------------------------------------------------------------------
	 * @param $filtres[] tableau d'objes de ObjectTransfertWS
	 * @param $max nombre maximum de reponses
	 * @return Object[] resultat webservice si OK || false si KO
	 */
	function getEtalons($filtres,$max=100){
		if($this->client != null){
			$result = $this->client->call('chercheEtalonsI18N', array('keyFields'=>$filtres));

			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;
	}

	/**
	 * Function qui retourne les etalons avec
	 * comme filtre de recherche le tableau d'objets passes en parametre
	 *-------------------------------------------------------------------
	 * Exemple:
	 * $filtres[] = new ObjectTransfertWS("codeRegion","BRG");
	 * $filtres[] = new ObjectTransfertWS("codeDepartement","71");
	 * ------------------------------------------------------------------
	 * @param $filtres[] tableau d'objes de ObjectTransfertWS
	 * @param $max nombre maximum de reponses
	 * @return Object[] resultat webservice si OK || false si KO
	 */
	function getNouveauxEtalons($filtres,$max=100){
		if($this->client != null){
			$result = $this->client->call('chercheNouveauxEtalonsI18N', array('keyFields'=>$filtres));

			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;
	}

	/**
	 * Function retournant un etalon par son codeCheval
	 * @param $filtres tableau d'objes de ObjectTransfertWS
	 * @return Object
	 */
	function getEtalon($filtres){
		if($this->client != null){
			$result = $this->client->call('chercheEtalonI18N', array('keyFields'=>$filtres));
			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;
	}

	/**
	 * Function qui retourne la liste des races suivant
	 * la langue demandée passee dans le tableau de parametres.
	 * *-------------------------------------------------------------------
	 * Exemple:
	 * $filtres[] = new ObjectTransfertWS("i18n","EN");
	 * OU
	 * $filtres[] = new ObjectTransfertWS("i18n","FR");
	 * ------------------------------------------------------------------
	 * @param $filtres[] (FR|EN)
	 * @return void
	 */
	function getAllRaces($filtres){
		if($this->client != null){
			$result = $this->client->call('getAllGroupesRaceI18N', array('keyFields'=>$filtres));
			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;
	}

	/**
	 * Function qui retourne la liste des centres techniques faisant partie de la tournee
	 * liée à la production pour un cheval donné
	 * *-------------------------------------------------------------------
	 * Exemple:
	 * $filtres[] = new ObjectTransfertWS("i18n","EN");
	 * OU
	 * $filtres[] = new ObjectTransfertWS("i18n","FR");
	 * ------------------------------------------------------------------
	 * @param $filtres[] (FR|EN)
	 * @return void
	 */
	function getAllCT4Etalon($filtres){
		if($this->client != null){
			$result = $this->client->call('chercheCentreTechEtalonI18N', array('keyFields'=>$filtres));
			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;
	}

	/**
	 * Function qui retourne des chevaux pour comparaison
	 * *-------------------------------------------------------------------
	 * ------------------------------------------------------------------
	 * @param $filtres[] (codeCheval*)
	 * @return void
	 */
	function getAllListEtalon2Cmp($filtres){
		if($this->client != null){
			$result = $this->client->call('getEtalons2Cmp', array('keyFields'=>$filtres));
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

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_01/class.WebservicesAccess.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_01/class.WebservicesAccess.php"]);
}
?>
