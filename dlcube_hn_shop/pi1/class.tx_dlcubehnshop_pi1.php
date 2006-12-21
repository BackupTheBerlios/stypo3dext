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
 * @author	Vincent MAURY <vmauy@dlcube.com>
 */

require_once(PATH_tslib."class.tslib_pibase.php");

class tx_dlcubehnshop_pi1 extends tslib_pibase {
	var $prefixId = "tx_dlcubehnshop_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_dlcubehnshop_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "dlcube_hn_shop";	// The extension key.
	
	//var $searchConfig="WSDatastore"; 
	
	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
		
		//value="'.htmlspecialchars($this->piVars["input_field"])
		//<BR><p>You can click here to '.$this->pi_linkToPage("get to this page again",$GLOBALS["TSFE"]->id).'</p>
		/*
		 * Recuperation du code region passe en argument du plug-in
		 * voir le champ code (select_key)
		 */
		$data = $this->cObj->data;
		
		$content="Salut ducon";
		return $this->pi_wrapInBaseClass($content);
	}
	

}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_shop/pi1/class.tx_dlcubehnshop_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_shop/pi1/class.tx_dlcubehnshop_pi1.php"]);
}

?>