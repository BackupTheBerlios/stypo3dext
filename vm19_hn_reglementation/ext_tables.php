<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
$TCA["tx_vm19hnreglementation_nature"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_nature",		
		"label" => "title",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_vm19hnreglementation_nature.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "title, level, code",
	)
);


t3lib_extMgm::addToInsertRecords("tx_vm19hnreglementation_textes");

$TCA["tx_vm19hnreglementation_textes"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes",		
		"label" => "title",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"sortby" => "sorting",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",	
			"starttime" => "starttime",	
			"endtime" => "endtime",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_vm19hnreglementation_textes.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, starttime, endtime, url, title, nature, dat_approb, number, publication, desc_2bf7363fc2, fich_joint, kwords, orig, other_pages, parent_text, rtt_attach_type",
	)
);



t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout,select_key";

t3lib_extMgm::addToInsertRecords("tx_vm19hnreglementation_textes");
t3lib_extMgm::allowTableOnStandardPages("tx_vm19hnreglementation_textes");

t3lib_extMgm::addPlugin(Array("LLL:EXT:vm19_hn_reglementation/locallang_db.php:tt_content.list_type_pi1", $_EXTKEY."_pi1"),"list_type");


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","reglementation");
?>