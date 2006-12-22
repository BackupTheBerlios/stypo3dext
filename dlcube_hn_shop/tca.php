<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_dlcubehnshop_articles"] = Array (
	"ctrl" => $TCA["tx_dlcubehnshop_articles"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,starttime,endtime,fe_group,ref,ref2,title,auteur,editor,support,designation,descdetail,parut,price,tva,isbn,weight,nbpages,archive,technicaldegree,img1,img2,file"
	),
	"feInterface" => $TCA["tx_dlcubehnshop_articles"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"starttime" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.starttime",
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
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.endtime",
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
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.fe_group",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("", 0),
					Array("LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login", -1),
					Array("LLL:EXT:lang/locallang_general.xml:LGL.any_login", -2),
					Array("LLL:EXT:lang/locallang_general.xml:LGL.usergroups", "--div--")
				),
				"foreign_table" => "fe_groups"
			)
		),
		"ref" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.ref",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"ref2" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.ref2",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"title" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"auteur" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.auteur",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"editor" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.editor",		
			"config" => Array (
				"type" => "select",	
				"items" => Array (
					Array("",0),
				),
				"foreign_table" => "tx_dlcubehnshop_editors",	
				"foreign_table_where" => "ORDER BY tx_dlcubehnshop_editors.name",	
				"size" => 3,	
				"minitems" => 0,
				"maxitems" => 1,	
			)
		),
		"support" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.support",		
			"config" => Array (
				"type" => "select",	
				"items" => Array (
					Array("",0),
				),
				"foreign_table" => "tx_dlcubehnshop_supports",	
				"foreign_table_where" => "ORDER BY tx_dlcubehnshop_supports.name",	
				"size" => 3,	
				"minitems" => 0,
				"maxitems" => 1,	
			)
		),
		"designation" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.designation",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
			)
		),
		"descdetail" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.descdetail",		
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
		"parut" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.parut",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"price" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.price",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,double2",
			)
		),
		"tva" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.tva",		
			"config" => Array (
				"type" => "input",	
				"size" => "5",	
				"max" => "6",	
				"eval" => "double2",
			)
		),
		"isbn" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.isbn",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"weight" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.weight",		
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "4",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "1000",
					"lower" => "10"
				),
				"default" => 0
			)
		),
		"nbpages" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.nbpages",		
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "4",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "1000",
					"lower" => "10"
				),
				"default" => 0
			)
		),
		"archive" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.archive",		
			"config" => Array (
				"type" => "check",
			)
		),
		"technicaldegree" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.technicaldegree",		
			"config" => Array (
				"type" => "radio",
				"items" => Array (
					Array("LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.technicaldegree.I.0", "1"),
					Array("LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.technicaldegree.I.1", "2"),
					Array("LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.technicaldegree.I.2", "3"),
				),
			)
		),
		"img1" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.img1",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => $GLOBALS["TYPO3_CONF_VARS"]["GFX"]["imagefile_ext"],	
				"max_size" => 100,	
				"uploadfolder" => "uploads/tx_dlcubehnshop",
				"show_thumbs" => 1,	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"img2" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.img2",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => $GLOBALS["TYPO3_CONF_VARS"]["GFX"]["imagefile_ext"],	
				"max_size" => 500,	
				"uploadfolder" => "uploads/tx_dlcubehnshop",
				"show_thumbs" => 1,	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"file" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles.file",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "",	
				"disallowed" => "php,php3",	
				"max_size" => 1000,	
				"uploadfolder" => "uploads/tx_dlcubehnshop",
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, ref, ref2, title;;;;2-2-2, auteur;;;;3-3-3, editor, support, designation;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts], descdetail;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts], parut, price, tva, isbn, weight, nbpages, archive, technicaldegree, img1, img2, file")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "starttime, endtime, fe_group")
	)
);



$TCA["tx_dlcubehnshop_editors"] = Array (
	"ctrl" => $TCA["tx_dlcubehnshop_editors"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,name,infos"
	),
	"feInterface" => $TCA["tx_dlcubehnshop_editors"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"name" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_editors.name",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"infos" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_editors.infos",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, name, infos")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_dlcubehnshop_supports"] = Array (
	"ctrl" => $TCA["tx_dlcubehnshop_supports"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,name"
	),
	"feInterface" => $TCA["tx_dlcubehnshop_supports"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"name" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_supports.name",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, name")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);
?>