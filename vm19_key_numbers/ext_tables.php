<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
$TCA["tx_vm19keynumbers_unities"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:vm19_key_numbers/locallang_db.php:tx_vm19keynumbers_unities",		## WOP:[tables][1][title]
		"label" => "unity",	## WOP:[tables][1][header_field]
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"sortby" => "sorting",	## WOP:[tables][1][sorting]
		"enablecolumns" => Array (		## WOP:[tables][1][add_hidden] / [tables][1][add_starttime] / [tables][1][add_endtime] / [tables][1][add_access]
			"disabled" => "hidden",	## WOP:[tables][1][add_hidden]
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_vm19keynumbers_unities.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, unity, unity_code, comment",
	)
);

$TCA["tx_vm19keynumbers_numbers"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:vm19_key_numbers/locallang_db.php:tx_vm19keynumbers_numbers",		## WOP:[tables][2][title]
		"label" => "title",	## WOP:[tables][2][header_field]
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"sortby" => "sorting",	## WOP:[tables][2][sorting]
		"delete" => "deleted",	## WOP:[tables][2][add_deleted]
		"enablecolumns" => Array (		## WOP:[tables][2][add_hidden] / [tables][2][add_starttime] / [tables][2][add_endtime] / [tables][2][add_access]
			"disabled" => "hidden",	## WOP:[tables][2][add_hidden]
			"starttime" => "starttime",	## WOP:[tables][2][add_starttime]
			"endtime" => "endtime",	## WOP:[tables][2][add_endtime]
			"fe_group" => "fe_group",	## WOP:[tables][2][add_access]
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_vm19keynumbers_numbers.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, starttime, endtime, fe_group, title, k_value, unity, comment, update_type, update_period, update_seq",
	)
);

## WOP:[pi][1][addType]
t3lib_div::loadTCA("tt_content");
## $TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout,select_key";
## ne pas enlever select_key permet d'avoir l'input pour choisir le type d'affichage...
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout";


t3lib_extMgm::addToInsertRecords("tx_vm19keynumbers_numbers");

/*
Cet éssai transforme le champ select_key en liste déroulante ... mais tout le temps qqsot le plgin (gênant )
$TCA["tt_content"]["columns"]["select_key"]["config"]=  Array (
	"type" => "select",
	## WOP:[tables][2][fields][8][conf_select_items]
	"items" => Array (
		Array("0", "Item1"),
		Array("1", "Item2"),
		Array("2", "Item3"),
	),
); */

## WOP:[pi][1][addType]
t3lib_extMgm::addPlugin(Array("LLL:EXT:vm19_key_numbers/locallang_db.php:tt_content.list_type", $_EXTKEY."_pi1"),"list_type");

## WOP:[pi][1][plus_wiz]:
if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_vm19keynumbers_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY)."pi1/class.tx_vm19keynumbers_pi1_wizicon.php";
?>
