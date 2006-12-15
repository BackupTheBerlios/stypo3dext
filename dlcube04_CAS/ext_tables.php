<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
$tempColumns = Array (
	"tx_dlcube04CAS_auth_cas_required" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:dlcube04_CAS/locallang_db.php:pages.tx_dlcube04CAS_auth_cas_required",		
		"config" => Array (
			"type" => "check",
		)
	),
);


t3lib_div::loadTCA("pages");
t3lib_extMgm::addTCAcolumns("pages",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("pages","tx_dlcube04CAS_auth_cas_required;;;;1-1-1");


t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi2"]="layout,select_key";


t3lib_extMgm::addPlugin(Array("LLL:EXT:dlcube04_CAS/locallang_db.php:tt_content.list_type_pi2", $_EXTKEY."_pi2"),"list_type");

t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi3"]="layout,select_key";

t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi4"]="layout,select_key";

t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi5"]="layout,select_key";

t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi6"]="layout,select_key";

t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi7"]="layout,select_key";

t3lib_extMgm::addPlugin(Array("LLL:EXT:dlcube04_CAS/locallang_db.php:tt_content.list_type_pi3", $_EXTKEY."_pi3"),"list_type");
t3lib_extMgm::addPlugin(Array("LLL:EXT:dlcube04_CAS/locallang_db.php:tt_content.list_type_pi4", $_EXTKEY."_pi4"),"list_type");
t3lib_extMgm::addPlugin(Array("LLL:EXT:dlcube04_CAS/locallang_db.php:tt_content.list_type_pi5", $_EXTKEY."_pi5"),"list_type");
t3lib_extMgm::addPlugin(Array("LLL:EXT:dlcube04_CAS/locallang_db.php:tt_content.list_type_pi6", $_EXTKEY."_pi6"),"list_type");
t3lib_extMgm::addPlugin(Array("LLL:EXT:dlcube04_CAS/locallang_db.php:tt_content.list_type_pi7", $_EXTKEY."_pi7"),"list_type");

if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_dlcube04CAS_pi3_wizicon"] = t3lib_extMgm::extPath($_EXTKEY)."pi3/class.tx_dlcube04CAS_pi3_wizicon.php";
if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_dlcube04CAS_pi4_wizicon"] = t3lib_extMgm::extPath($_EXTKEY)."pi4/class.tx_dlcube04CAS_pi4_wizicon.php";
if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_dlcube04CAS_pi5_wizicon"] = t3lib_extMgm::extPath($_EXTKEY)."pi5/class.tx_dlcube04CAS_pi5_wizicon.php";
if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_dlcube04CAS_pi6_wizicon"] = t3lib_extMgm::extPath($_EXTKEY)."pi6/class.tx_dlcube04CAS_pi6_wizicon.php";
if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_dlcube04CAS_pi7_wizicon"] = t3lib_extMgm::extPath($_EXTKEY)."pi7/class.tx_dlcube04CAS_pi7_wizicon.php";
?>