<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
$TCA["tx_vm19docsbase_topics"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_topics",		
		"label" => "title",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"sortby" => "sorting",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",	
			"fe_group" => "fe_group",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_vm19docsbase_topics.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, fe_group, title, comment",
	)
);

$TCA["tx_vm19docsbase_support"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_support",		
		"label" => "title",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"sortby" => "sorting",
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_vm19docsbase_support.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, title, comment",
	)
);

$TCA["tx_vm19docsbase_nature"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_nature",		
		"label" => "title",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"sortby" => "sorting",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_vm19docsbase_nature.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, title, comment",
	)
);

$TCA["tx_vm19docsbase_lang"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_lang",		
		"label" => "title",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY title",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_vm19docsbase_lang.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, langcode, title",
	)
);

t3lib_extMgm::allowTableOnStandardPages("tx_vm19docsbase_docs");

$TCA["tx_vm19docsbase_docs"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_docs",		
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
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_vm19docsbase_docs.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, starttime, endtime, fe_group, internal_code, title, topics, int_author, ext_author, support, nature, lang, isbn, keywords, abstract, imagette, workflow_state, document",
	)
);


t3lib_div::loadTCA("tt_content");
//$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout,select_key";
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout";

t3lib_extMgm::addToInsertRecords("tx_vm19docsbase_docs");

t3lib_extMgm::addPlugin(Array("LLL:EXT:vm19_docs_base/locallang_db.php:tt_content.list_type", $_EXTKEY."_pi1"),"list_type");


if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_vm19docsbase_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY)."pi1/class.tx_vm19docsbase_pi1_wizicon.php";
?>
