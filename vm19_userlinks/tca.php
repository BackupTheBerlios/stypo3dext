<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$TCA["tx_vm19userlinks_ulinks"] = Array (
	"ctrl" => $TCA["tx_vm19userlinks_ulinks"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,user_id,link_ids"
	),
	"feInterface" => $TCA["tx_vm19userlinks_ulinks"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"user_id" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_userlinks/locallang_db.php:tx_vm19userlinks_ulinks.user_id",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "fe_users",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"link_ids" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_userlinks/locallang_db.php:tx_vm19userlinks_ulinks.link_ids",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "tt_links",	
				"size" => 10,	
				"minitems" => 0,
				"maxitems" => 20,
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, user_id, link_ids")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);
?>