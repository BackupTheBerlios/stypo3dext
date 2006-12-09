<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_dlcubenewsletters_topnews_ids"] = Array (
	"ctrl" => $TCA["tx_dlcubenewsletters_topnews_ids"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,title,news_uid"
	),
	"feInterface" => $TCA["tx_dlcubenewsletters_topnews_ids"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"title" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:dlcube_newsletters/locallang_db.php:tx_dlcubenewsletters_topnews_ids.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"news_uid" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:dlcube_newsletters/locallang_db.php:tx_dlcubenewsletters_topnews_ids.news_uid",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "tx_vm19news_news",	
				"size" => 3,	
				"minitems" => 0,
				"maxitems" => 3,
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, title;;;;2-2-2, news_uid;;;;3-3-3")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_dlcubenewsletters_region_ids"] = Array (
	"ctrl" => $TCA["tx_dlcubenewsletters_region_ids"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,title,news_uid"
	),
	"feInterface" => $TCA["tx_dlcubenewsletters_region_ids"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"title" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:dlcube_newsletters/locallang_db.php:tx_dlcubenewsletters_region_ids.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"news_uid" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:dlcube_newsletters/locallang_db.php:tx_dlcubenewsletters_region_ids.news_uid",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "tx_vm19news_news",	
				"size" => 6,	
				"minitems" => 0,
				"maxitems" => 10,
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, title;;;;2-2-2, news_uid;;;;3-3-3")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_dlcubenewsletters_presse_ids"] = Array (
	"ctrl" => $TCA["tx_dlcubenewsletters_presse_ids"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,title,news_uid"
	),
	"feInterface" => $TCA["tx_dlcubenewsletters_presse_ids"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"title" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:dlcube_newsletters/locallang_db.php:tx_dlcubenewsletters_presse_ids.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"news_uid" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:dlcube_newsletters/locallang_db.php:tx_dlcubenewsletters_presse_ids.news_uid",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "tx_vm19docsbase_docs",	
				"size" => 3,	
				"minitems" => 0,
				"maxitems" => 3,
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, title;;;;2-2-2, news_uid;;;;3-3-3")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);
?>
