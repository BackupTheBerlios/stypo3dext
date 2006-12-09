<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::allowTableOnStandardPages("tx_dlcubenewsletters_topnews_ids");


t3lib_extMgm::addToInsertRecords("tx_dlcubenewsletters_topnews_ids");

$TCA["tx_dlcubenewsletters_topnews_ids"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:dlcube_newsletters/locallang_db.php:tx_dlcubenewsletters_topnews_ids",		
		"label" => "title",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",

		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	

		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_dlcubenewsletters_topnews_ids.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, title, news_uid",
	)
);


t3lib_extMgm::allowTableOnStandardPages("tx_dlcubenewsletters_region_ids");


t3lib_extMgm::addToInsertRecords("tx_dlcubenewsletters_region_ids");

$TCA["tx_dlcubenewsletters_region_ids"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:dlcube_newsletters/locallang_db.php:tx_dlcubenewsletters_region_ids",		
		"label" => "title",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",

		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_dlcubenewsletters_region_ids.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, title, news_uid",
	)
);


t3lib_extMgm::allowTableOnStandardPages("tx_dlcubenewsletters_presse_ids");


t3lib_extMgm::addToInsertRecords("tx_dlcubenewsletters_presse_ids");

$TCA["tx_dlcubenewsletters_presse_ids"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:dlcube_newsletters/locallang_db.php:tx_dlcubenewsletters_presse_ids",		
		"label" => "title",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",

		"versioning" => "1",	
		"sortby" => "sorting",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_dlcubenewsletters_presse_ids.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, title, news_uid",
	)
);


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(Array('LLL:EXT:dlcube_newsletters/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/','Newsletters - Top News');


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key';


t3lib_extMgm::addPlugin(Array('LLL:EXT:dlcube_newsletters/locallang_db.php:tt_content.list_type_pi2', $_EXTKEY.'_pi2'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,'pi2/static/','Newsletters - Regions News');


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi3']='layout,select_key';


t3lib_extMgm::addPlugin(Array('LLL:EXT:dlcube_newsletters/locallang_db.php:tt_content.list_type_pi3', $_EXTKEY.'_pi3'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,'pi3/static/','Newsletters - Press releases');


if (TYPO3_MODE=="BE")	{
		
	t3lib_extMgm::addModule("web","txdlcubenewslettersM1","",t3lib_extMgm::extPath($_EXTKEY)."mod1/");
}
?>