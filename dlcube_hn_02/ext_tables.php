<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout";


t3lib_extMgm::addPlugin(Array("LLL:EXT:dlcube_hn_02/locallang_db.php:tt_content.list_type_pi1", $_EXTKEY."_pi1"),"list_type");


if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_dlcubehn02_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY)."pi1/class.tx_dlcubehn02_pi1_wizicon.php";


t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi2"]="layout,select_key";


t3lib_extMgm::addPlugin(Array("LLL:EXT:dlcube_hn_02/locallang_db.php:tt_content.list_type_pi2", $_EXTKEY."_pi2"),"list_type");


if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_dlcubehn02_pi2_wizicon"] = t3lib_extMgm::extPath($_EXTKEY)."pi2/class.tx_dlcubehn02_pi2_wizicon.php";


t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi3"]="layout,select_key";


t3lib_extMgm::addPlugin(Array("LLL:EXT:dlcube_hn_02/locallang_db.php:tt_content.list_type_pi3", $_EXTKEY."_pi3"),"list_type");


if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_dlcubehn02_pi3_wizicon"] = t3lib_extMgm::extPath($_EXTKEY)."pi3/class.tx_dlcubehn02_pi3_wizicon.php";
?>