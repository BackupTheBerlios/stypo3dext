<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$TCA["tx_vm19news_news"] = Array (
	"ctrl" => $TCA["tx_vm19news_news"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,starttime,endtime,fe_group,workf_state,title,abstract,small_img,bodytext,big_img,author"
	),
	"feInterface" => $TCA["tx_vm19news_news"]["feInterface"],
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
		"workf_state" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:vm19_news/locallang_db.php:tx_vm19news_news.workf_state",		
			"config" => Array (
				"type" => "check",
				"cols" => 4,
				"items" => Array (
					Array("LLL:EXT:vm19_news/locallang_db.php:tx_vm19news_news.workf_state.I.0", ""),
					Array("LLL:EXT:vm19_news/locallang_db.php:tx_vm19news_news.workf_state.I.1", ""),
					Array("LLL:EXT:vm19_news/locallang_db.php:tx_vm19news_news.workf_state.I.2", ""),
					Array("LLL:EXT:vm19_news/locallang_db.php:tx_vm19news_news.workf_state.I.3", ""),
				),
			)
		),
		"title" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_news/locallang_db.php:tx_vm19news_news.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "50",	
				"eval" => "required,trim",
			)
		),
		"paccdisp" => Array (		
			"exclude" => 1,		
			"label" => "Affichage accroche en page d'accueil",		
			"config" => Array (
				 'type' => 'check',
				 'default' => '0' 
			)
		),

		"abstract" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_news/locallang_db.php:tx_vm19news_news.abstract",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"small_img" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_news/locallang_db.php:tx_vm19news_news.small_img",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => $GLOBALS["TYPO3_CONF_VARS"]["GFX"]["imagefile_ext"],	
				"max_size" => 50,	
				"uploadfolder" => "uploads/tx_vm19news",
				"show_thumbs" => 1,	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"bodytext" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_news/locallang_db.php:tx_vm19news_news.bodytext",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"big_img" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_news/locallang_db.php:tx_vm19news_news.big_img",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => $GLOBALS["TYPO3_CONF_VARS"]["GFX"]["imagefile_ext"],	
				"max_size" => 500,	
				"uploadfolder" => "uploads/tx_vm19news",
				"show_thumbs" => 1,	
// 5 images c'est chiant à gérer	"size" => 5 ,	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		
		"bimg_credit" => Array (		
			"exclude" => 0,		
			"label" => "Credit photo",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "50",	
			)
		),
		
		"bimg_legend" => Array (		
			"exclude" => 0,		
			"label" => "Legende photo",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "100",	
			)
		),

		"document" => Array (		
			"exclude" => 0,		
			"label" => "document",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "",	
				"disallowed" => "php,php3",	
				"max_size" => 5000,	
				"uploadfolder" => "uploads/tx_vm19news/docs",
				"show_thumbs" => 1,
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		
		"otherpages" => Array (		
			"exclude" => 0,		
			"label" => "Autres pages d'affichage",		
			"config" => Array (
				/*"type" => "select",	
				"foreign_table" => "pages",	
				"foreign_table_where" => "AND pages.pid=###CURRENT_PID### ORDER BY pages.uid",	*/
				"type" => "group",
				"internal_type" => "db",
				"allowed" => "pages",
				"size" => 5,	
				"minitems" => 0,
				"maxitems" => 10,	
			)
		),
		
		"actusuite" => Array (		
			"exclude" => 0,		
			"label" => "Actualité suite liée",		
			"config" => Array (
				/*"type" => "select",	
				"foreign_table" => "pages",	
				"foreign_table_where" => "AND pages.pid=###CURRENT_PID### ORDER BY pages.uid",	*/
				"type" => "group",
				"internal_type" => "db",
				"allowed" => "tx_vm19news_news",
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,	
			)
		),
		
		"author" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_news/locallang_db.php:tx_vm19news_news.author",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "fe_users",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
	),
	"types" => Array (
//		"0" => Array("showitem" => "hidden;;1;;1-1-1, workf_state, title;;;;2-2-2, abstract;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_vm19news/rte/];3-3-3, small_img, bodytext;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_vm19news/rte/];4-4-4, big_img, author")
// le chaangte de mode de RTE se fait ici: rte_transform[mode=ts_css
		"0" => Array("showitem" => "hidden;;1;;1-1-1, workf_state, title;;;;2-2-2, paccdisp, abstract;;;richtext[cut|copy|paste|formatblock|bold|italic|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image|line|chMode]:rte_transform[mode=ts|imgpath=uploads/tx_vm19news/rte/];3-3-3, small_img, bodytext;;;richtext[cut|copy|paste|formatblock|bold|italic|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image|line|chMode]:rte_transform[mode=ts|imgpath=uploads/tx_vm19news/rte/];4-4-4, big_img, bimg_credit,bimg_legend, document, otherpages, actusuite, author")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "starttime, endtime, fe_group")
	)
);
?>
