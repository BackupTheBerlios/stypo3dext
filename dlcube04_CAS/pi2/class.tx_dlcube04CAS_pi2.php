<?php
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
 * Plugin 'FORMULAIRE_AUTH' for the 'dlcube04_CAS' extension.
 *
 * @author	Guillaume Tessier <gui.tessier@wanadoo.fr>
 */


require_once(PATH_tslib."class.tslib_pibase.php");
require_once("typo3conf/ext/dlcube04_CAS/php_inc/CAS/CAS.php");

class tx_dlcube04CAS_pi2 extends tslib_pibase {
	var $prefixId = "tx_dlcube04CAS_pi2";		// Same as class name
	var $scriptRelPath = "pi2/class.tx_dlcube04CAS_pi2.php";	// Path to this script relative to the extension dir.
	var $extKey = "dlcube04_CAS";	// The extension key.
	var $typeExecution = null; /**dev|dev_ext|prod*/

	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$this->pi_loadLL();
		session_start();

		$this->typeExecution="prod";
		$urlCas = "none";
		$portCas = "none";
		 if($this->typeExecution=="dev"){
		 	$urlCas = "xinf-devlinux.intranet.haras-nationaux.fr";
			$portCas = 7777;
		 }
		 else if($this->typeExecution=="prod"){
		 	$urlCas = "cerbere.haras-nationaux.fr";
			$portCas = 443;
		 }

		if(isset($_GET["userdebug"])){
			include_once("typo3conf/ext/dlcube_hn_01/class.WebservicesCompte.php");
			include_once("typo3conf/ext/dlcube_hn_01/class.WebservicesAccess.php");

			$param[] = array(
			"login"=>$_GET["userdebug"],
			"ctx"=> null);
			$ws = new WebservicesCompte();
			if(!$ws->connectIdent()){
				$content="ERROR:".$ws->getErrorMessage();
				return $content;
			}
			$result = $ws->getPersonneByLogin($param);
			debug($result);
			print_r($result);
		}
		if(isset($_GET["action"]) &&  $_GET["action"]=="auth" && !isset($_GET["ticket"])){
			phpCAS::setDebug();
			phpCAS::client(CAS_VERSION_2_0,$urlCas,$portCas,'cas','true');
			$ur = phpCAS::getServerLoginURL(false);
			$content.='<IFRAME src="'.$ur.'" frameborder="no" height="600" width="670"></IFRAME>';
			return $this->pi_wrapInBaseClass($content);
		}
		phpCAS::client(CAS_VERSION_2_0,$urlCas,$portCas,'cas','true');
		phpCAS::checkAuthentication();
		$_SESSION["portalId"]= phpCAS::getUser();
		echo '<html><body><script type="text/javascript">
		    window.open( "'.$this->pi_getPageLink($_SESSION["service_id_auth"]).'", "_top");
		</script></body>';
		exit();
	}
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube04_CAS/pi2/class.tx_dlcube04CAS_pi2.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube04_CAS/pi2/class.tx_dlcube04CAS_pi2.php"]);
}

?>
