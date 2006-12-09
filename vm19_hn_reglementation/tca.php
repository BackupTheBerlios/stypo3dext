<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$TCA["tx_vm19hnreglementation_nature"] = Array (
	"ctrl" => $TCA["tx_vm19hnreglementation_nature"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "title,level,code"
	),
	"feInterface" => $TCA["tx_vm19hnreglementation_nature"]["feInterface"],
	"columns" => Array (
		"title" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_nature.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"level" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_nature.level",		
			"config" => Array (
				"type" => "input",	
				"size" => "5",	
				"max" => "5",	
				"eval" => "required",
			)
		),
		"code" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_nature.code",		
			"config" => Array (
				"type" => "input",	
				"size" => "10",	
				"max" => "10",	
				"eval" => "required",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "title;;;;2-2-2, level;;;;3-3-3, code")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_vm19hnreglementation_textes"] = Array (
	"ctrl" => $TCA["tx_vm19hnreglementation_textes"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,starttime,endtime,url,title,nature,dat_approb,number,publication,desc_2bf7363fc2,fich_joint,kwords,orig,other_pages,parent_text,rtt_attach_type"
	),
	"feInterface" => $TCA["tx_vm19hnreglementation_textes"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"starttime" => Array (		
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.starttime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"default" => "0",
				"checkbox" => "0"
			)
		),
		"endtime" => Array (		
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.endtime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0",
				"range" => Array (
					"upper" => mktime(0,0,0,12,31,2020),
					"lower" => mktime(0,0,0,date("m")-1,date("d"),date("Y"))
				)
			)
		),
		"url" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.url",		
			"config" => Array (
				"type" => "input",	
				"size" => "48",	
				"max" => "255",
			)
		),
		"title" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "48",	
				"eval" => "required",
			)
		),
		"nature" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.nature",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "tx_vm19hnreglementation_nature",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"dat_approb" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.dat_approb",		
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0"
			)
		),
		"number" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.number",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"publication" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.publication",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"desc_2bf7363fc2" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.desc_2bf7363fc2",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
			)
		),
		"fich_joint" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.fich_joint",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "",	
				"disallowed" => "php,php3",	
				"max_size" => 500,	
				"uploadfolder" => "uploads/tx_vm19hnreglementation",
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"kwords" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.kwords",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"orig" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.orig",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"other_pages" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.other_pages",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "pages",	
				"size" => 5,	
				"minitems" => 0,
				"maxitems" => 5,
			)
		),
		"parent_text" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.parent_text",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "tx_vm19hnreglementation_textes",	
				"size" => 2,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"rtt_attach_type" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.rtt_attach_type",		
			"config" => Array (
				"type" => "radio",
				"items" => Array (
					Array("LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.rtt_attach_type.I.none", ""),
					Array("LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.rtt_attach_type.I.0", "MOD"),
					Array("LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.rtt_attach_type.I.1", "ABR"),
					Array("LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.rtt_attach_type.I.2", "REPL"),
					Array("LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.rtt_attach_type.I.3", "ANN"),
					Array("LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.rtt_attach_type.I.4", "TRP"),
					Array("LLL:EXT:vm19_hn_reglementation/locallang_db.php:tx_vm19hnreglementation_textes.rtt_attach_type.I.5", "APP"),
				),
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, url, title;;;;2-2-2, nature;;;;3-3-3, dat_approb, number, publication, desc_2bf7363fc2;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_vm19hnreglementation/rte/], fich_joint, kwords, orig, other_pages, parent_text, rtt_attach_type")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "starttime, endtime")
	)
);
?>