<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2003 Vincent (admin) (artec.vm@nerim.net)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is 
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
 * Class that adds the wizard icon.
 *
 * @author	Vincent (admin) <artec.vm@nerim.net>
 */



class tx_vm19keynumbers_pi1_wizicon {
	function proc($wizardItems)	{
		global $LANG;

		$LL = $this->includeLocalLang();

		$wizardItems["plugins_tx_vm19keynumbers_pi1"] = array(
			"icon"=>t3lib_extMgm::extRelPath("vm19_key_numbers")."pi1/ce_wiz.gif",
			"title"=>$LANG->getLLL("pi1_title",$LL),
			"description"=>$LANG->getLLL("pi1_plus_wiz_description",$LL),
			"params"=>"&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=vm19_key_numbers_pi1"
		);

		return $wizardItems;
	}
	function includeLocalLang()	{
		include(t3lib_extMgm::extPath("vm19_key_numbers")."locallang.php");
		return $LOCAL_LANG;
	}
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/vm19_key_numbers/pi1/class.tx_vm19keynumbers_pi1_wizicon.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/vm19_key_numbers/pi1/class.tx_vm19keynumbers_pi1_wizicon.php"]);
}

?>