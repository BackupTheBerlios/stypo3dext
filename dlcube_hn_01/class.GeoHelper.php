<?php
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

class GeoHelper {
	var $wsdl;
	var $client;
	var $errorMessage;
	
	function GeoHelper(){
		$this->__construct();
	}
	
	function __construct(){
		$this->wsdl = "http://xinf-datastore2:8080/hndto/services/GEOServicesPortail?wsdl";
		//$this->wsdl = "http://guitessier.dyndns.org:8080/hndto/services/GEOServicesPortail?wsdl";
		//$this->wsdl = "http://localhost:8080/hndto/services/GEOServicesPortail?wsdl";
		$this->client = new soapclient_nusoap($this->wsdl, 'wsdl');
	}
	
	function getAllDepartements(){
		if($this->client != null){
			$result = $this->client->call('getAllDepartements', array());
			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;
	}
	
	/**
	 * Methode retournant une liste de regions sans les départements
	 * @return Liste de regions
	 */
	function getAllRegions(){
		if($this->client != null){
			$result = $this->client->call('getAllRegions', array());
			if( !$this->client->getError() ){
				return $result;
			}
		}
		$this->errorMessage = $this->client->getError();
		return false;
	}
	
	function getErrorMessage(){
		return $this->errorMessage;
	}
	
}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_01/class.GeoHelper.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_01/class.GeoHelper.php"]);
}
?>
