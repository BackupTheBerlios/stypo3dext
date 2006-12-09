<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
$TCA["tx_fabformmail_abonne"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:fab_form_mail/locallang_db.php:tx_fabformmail_abonne",		
		"label" => "email",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_fabformmail_abonne.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, nom, email, comment, cat",
	)
);


t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout,select_key";


t3lib_extMgm::addPlugin(Array("LLL:EXT:fab_form_mail/locallang_db.php:tt_content.list_type", $_EXTKEY."_pi1"),"list_type");


if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_fabformmail_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY)."pi1/class.tx_fabformmail_pi1_wizicon.php";
?>