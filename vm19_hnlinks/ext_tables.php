<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

t3lib_extMgm::addToInsertRecords("tx_vm19hnlinks_urls");

$TCA["tx_vm19hnlinks_urls"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:vm19_hnlinks/locallang_db.php:tx_vm19hnlinks_urls",		
		"label" => "url_title",	
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
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_vm19hnlinks_urls.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, fe_group, url_url, url_title, url_desc, url_kwords, url_state, url_mailwb, url_lang, url_othercateg, url_datev",
	)
);


t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout,recursive";
// Add CODE field to plugin : en fait il suffit de ne PAS l'enlever ci-dessus
//$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='select_key';


t3lib_extMgm::addPlugin(Array("LLL:EXT:vm19_hnlinks/locallang_db.php:tt_content.list_type_pi1", $_EXTKEY."_pi1"),"list_type");


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","hn_links");
?>