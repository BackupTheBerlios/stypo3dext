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
  * $ws = new WebservicesCiteres();
  * if(!$ws->connect())echo $ws->getErrorMessage();
  * $param[] = new ObjectTransfertWS("codeRegion","BRG");
  * $param[] = new ObjectTransfertWS("codeDepartement","71");
  * $ws->getCentresTEchniques($param);
  */
class WebservicesCriteres {
	var $wsdl;
	var $client;
	var $errorMessage;

	function WebservicesCriteres(){
		$this->__construct();

	}

	function __construct(){
		//$this->wsdl = "http://xinf-datastore2:8080/hndto/services/CriteresServicesPortail?wsdl";
		//$this->wsdl = "http://guitessier.dyndns.org:8080/hndto/services/CriteresServicesPortail?wsdl";
		$this->wsdl = "http://localhost:8080/hndto/services/CriteresServicesPortail?wsdl";
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
	 * Function qui retourne les criteres de recherches d'orien,tation à la production
	 *
	 * @return Object[] resultat webservice si OK || false si KO
	 */
	function getAllTypeOrientationProduction(){
		if($this->client != null){
			$result = $this->client->call('getAllTypeOrientationProductionOrderByIndex',"");
			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;
	}

	/**
	 * Function qui retourne toutes les robes
	 * FRA / ENG
	 * @return Object[] resultat webservice si OK || false si KO
	 */
	function getAllRobes($codeLangue){
		if($this->client != null){
			$result = $this->client->call('getAllRobes',array("codeLangue"=>$codeLangue));
			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;
	}

	/**
	 * Function qui retourne tous les points forts
	 * @return Object[] resultat webservice si OK || false si KO
	 */
	function getAllPointsForts(){
		if($this->client != null){
			$result = $this->client->call('getAllPointsFortsOrderByLibelle',"");
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

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_01/class.WebservicesCriteres.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_01/class.WebservicesCiteres.php"]);
}
?>