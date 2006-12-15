<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2004 Administrateur TYPO3.7.1 (webtech@haras-nationaux.fr)
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
 *
 * @author	Administrateur TYPO3.7.1 <webtech@haras-nationaux.fr>
 */

require_once("typo3conf/ext/dlcube04_CAS/pi1/class.tx_dlcube04CAS_pi1.php");

class user_logincas extends tx_dlcube04CAS_pi1{

	function display ($content,$conf) {
	   	$this->pi_loadLL();
	   	$this->conf=$conf;
	   	$this->pi_setPiVarDefaults();

		session_start();
		if(isset($_REQUEST["ticket"])){
				$_SESSION["ticket"]=$_REQUEST["ticket"];
		}
			
		if(isset($_GET["debug"])){
			debug($_REQUEST);
			debug($_SESSION);
		}
		if(!isset($_SESSION["portalId"]) && isset($_COOKIE["netid"]) && !isset($_GET["action"])){
			/*if(phpCAS::getClient() == null){
				debug(phpCAS::getClient());
				phpCAS::client(CAS_VERSION_2_0,'cerbere.haras-nationaux.fr',443,'cas','true');
			}
			$auth = phpCAS::checkAuthentication();
			$_SESSION["portalId"]= phpCAS::getUser();*/
		}
		if(isset($_SESSION["portalId"])){
			$content= '<ul id="menulogin">
			<li>'.$_SESSION["portalId"].' [<a href="index.php?action_cas=logout" style="background-color: none;text-decoration:none;color:white" >se déconnecter</a>]</li>
	    	</ul>';
		}
		return ($content);
	}
}

?>
