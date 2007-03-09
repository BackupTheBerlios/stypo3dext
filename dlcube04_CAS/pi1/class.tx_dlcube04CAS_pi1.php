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
 * Plugin 'AUTHENTIFICATION_SSO' for the 'dlcube04_CAS' extension.
 *
 * @author	Guillaume Tessier <gui.tessier@wanadoo.fr>
 */


require_once(PATH_tslib."class.tslib_pibase.php");
require_once("typo3conf/ext/dlcube04_CAS/php_inc/CAS/CAS.php");

class tx_dlcube04CAS_pi1 extends tslib_pibase {
	var $prefixId = "tx_dlcube04CAS_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_dlcube04CAS_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "dlcube04_CAS";	// The extension key.
	var $typeExecution = null; /**dev|dev_ext|prod*/

	/**
	 * Cette methode permet de verifier si l'utilisateur
	 * du portail poss�de d�j� une authentification SSO sur le
	 * serveur CAS.
	 * Si ce dernier ne poss�de pas d'authetification, le plugin redirige l'utilisateur sur
	 * une page d'authentification CAS dans une iframe.
	 */
	function main($content,$conf)	{
		session_start();
		//$idPageAuth = '3434';
		$idPageAuth = '3682';
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

		//debug($_SESSION);
		if($GLOBALS["TSFE"]->page["tx_dlcube04CAS_auth_cas_required"]==1){
			phpCAS::client(CAS_VERSION_2_0,$urlCas,$portCas,'cas','true');
			$auth = phpCAS::checkAuthentication();
			if(!$auth){
				$_SESSION["service_id_auth"]=$GLOBALS["TSFE"]->id;
				header('Location: ' . t3lib_div::locationHeaderUrl($this->pi_getPageLink($idPageAuth,"",array("action"=>"auth"))));
				exit();
			}
			else{
				$_SESSION["portalId"]= phpCAS::getUser();
			}
		}
		if(isset($_GET["action_cas"]) && $_GET["action_cas"]=="logout"){
			unset($_SESSION["portalId"]);
			header('Location: ' . t3lib_div::locationHeaderUrl($this->pi_getPageLink("3683","",array("action"=>"disconnect"))));
		}

		/**
		 * Gestion des langues pour le cookie
		 */
		if(isset($_GET["lang"])){
			if($_GET["lang"] == "fr") $this->cookie_fr();
			if($_GET["lang"] == "en") $this->cookie_en();
		}
	}

	function cookie_en() {
		$expire = 365*24*3600;  // on d�finit la dur�e du cookie, 1 an
		setcookie("langue_hn","en",null,"/",".haras-nationaux.fr");
		setcookie("langue1_hn","eng",null,"/",".haras-nationaux.fr");
	}

	function cookie_fr() {
		$expire = 365*24*3600;  // on d�finit la dur�e du cookie, 1 an
		setcookie("langue_hn","fr",null,"/",".haras-nationaux.fr");
		setcookie("langue1_hn","fra",null,"/",".haras-nationaux.fr");
	}
}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube04_CAS/pi1/class.tx_dlcube04CAS_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube04_CAS/pi1/class.tx_dlcube04CAS_pi1.php"]);
}

?>