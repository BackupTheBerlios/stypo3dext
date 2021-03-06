<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
$TCA["tx_vm19userlinks_ulinks"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:vm19_userlinks/locallang_db.php:tx_vm19userlinks_ulinks",		
		"label" => "uid",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY user_id",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_vm19userlinks_ulinks.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, user_id, link_ids",
	)
);


t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout,select_key";


t3lib_extMgm::addPlugin(Array("LLL:EXT:vm19_userlinks/locallang_db.php:tt_content.list_type_pi1", $_EXTKEY."_pi1"),"list_type");


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","User links edition");
?>