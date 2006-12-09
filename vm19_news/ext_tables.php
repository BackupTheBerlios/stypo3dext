<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
t3lib_extMgm::allowTableOnStandardPages("tx_vm19news_news");

$TCA["tx_vm19news_news"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:vm19_news/locallang_db.php:tx_vm19news_news",		
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
			"fe_group" => "fe_group",
		),
		"typeicon_column" => "type",
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_vm19news_news.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, starttime, endtime, fe_group, workf_state, title, abstract, small_img, bodytext, big_img, author",
	)
);


t3lib_div::loadTCA("tt_content");
//$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout,select_key";
//$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout";

t3lib_extMgm::addToInsertRecords("tx_vm19news_news");

t3lib_extMgm::addPlugin(Array("LLL:EXT:vm19_news/locallang_db.php:tt_content.list_type", $_EXTKEY."_pi1"),"list_type");
//t3lib_extMgm::addPlugin(Array("LLL:EXT:vm19_news/locallang_db.php:tt_content.list_type", $_EXTKEY."_pi1"),"1");
// rajout pour tester si 
//t3lib_extMgm::allowTableOnStandardPages("tx_vm19news_news");
//t3lib_extMgm::addToInsertRecords("tx_vm19news_news");


if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_vm19news_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY)."pi1/class.tx_vm19news_pi1_wizicon.php";
?>