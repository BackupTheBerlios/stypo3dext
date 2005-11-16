<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$TCA["tx_vm19hnlinks_urls"] = Array (
	"ctrl" => $TCA["tx_vm19hnlinks_urls"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,fe_group,url_url,url_title,url_desc,url_kwords,url_state,url_mailwb,url_lang,url_othercateg,url_datev"
	),
	"feInterface" => $TCA["tx_vm19hnlinks_urls"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"fe_group" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.fe_group",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("", 0),
					Array("LLL:EXT:lang/locallang_general.php:LGL.hide_at_login", -1),
					Array("LLL:EXT:lang/locallang_general.php:LGL.any_login", -2),
					Array("LLL:EXT:lang/locallang_general.php:LGL.usergroups", "--div--")
				),
				"foreign_table" => "fe_groups"
			)
		),
		"url_url" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hnlinks/locallang_db.php:tx_vm19hnlinks_urls.url_url",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"url_title" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hnlinks/locallang_db.php:tx_vm19hnlinks_urls.url_title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"url_desc" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hnlinks/locallang_db.php:tx_vm19hnlinks_urls.url_desc",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
		"url_kwords" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hnlinks/locallang_db.php:tx_vm19hnlinks_urls.url_kwords",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"url_state" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hnlinks/locallang_db.php:tx_vm19hnlinks_urls.url_state",		
			"config" => Array (
				"type" => "radio",
				"items" => Array (
					Array("LLL:EXT:vm19_hnlinks/locallang_db.php:tx_vm19hnlinks_urls.url_state.I.0", "0"),
					Array("LLL:EXT:vm19_hnlinks/locallang_db.php:tx_vm19hnlinks_urls.url_state.I.1", "1"),
					Array("LLL:EXT:vm19_hnlinks/locallang_db.php:tx_vm19hnlinks_urls.url_state.I.2", "2"),
				),
			)
		),
		"url_mailwb" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hnlinks/locallang_db.php:tx_vm19hnlinks_urls.url_mailwb",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"url_lang" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:vm19_hnlinks/locallang_db.php:tx_vm19hnlinks_urls.url_lang",		
			"config" => Array (
				"type" => "check",
				"cols" => 4,
				"items" => Array (
					Array("LLL:EXT:vm19_hnlinks/locallang_db.php:tx_vm19hnlinks_urls.url_lang.I.0", ""),
					Array("LLL:EXT:vm19_hnlinks/locallang_db.php:tx_vm19hnlinks_urls.url_lang.I.1", ""),
					Array("LLL:EXT:vm19_hnlinks/locallang_db.php:tx_vm19hnlinks_urls.url_lang.I.2", ""),
					Array("LLL:EXT:vm19_hnlinks/locallang_db.php:tx_vm19hnlinks_urls.url_lang.I.3", ""),
				),
			)
		),
		"url_othercateg" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hnlinks/locallang_db.php:tx_vm19hnlinks_urls.url_othercateg",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "pages",	
				"foreign_table_where" => "AND pages.pid=###CURRENT_PID### ORDER BY pages.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 5,	
				"wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 1,
					"add" => Array(
						"type" => "script",
						"title" => "Create new record",
						"icon" => "add.gif",
						"params" => Array(
							"table"=>"pages",
							"pid" => "###CURRENT_PID###",
							"setValue" => "prepend"
						),
						"script" => "wizard_add.php",
					),
					"edit" => Array(
						"type" => "popup",
						"title" => "Edit",
						"script" => "wizard_edit.php",
						"popup_onlyOpenIfSelected" => 1,
						"icon" => "edit2.gif",
						"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
					),
				),
			)
		),
		"url_datev" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hnlinks/locallang_db.php:tx_vm19hnlinks_urls.url_datev",		
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0"
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, url_url, url_title, url_desc, url_kwords, url_state, url_mailwb, url_lang, url_othercateg, url_datev")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "fe_group")
	)
);
?>