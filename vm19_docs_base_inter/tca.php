<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$upload_doc_folder="uploads/tx_vm19docsbase";

$TCA["tx_vm19docsbase_topics"] = Array (
	"ctrl" => $TCA["tx_vm19docsbase_topics"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,fe_group,title,comment"
	),
	"feInterface" => $TCA["tx_vm19docsbase_topics"]["feInterface"],
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
		"title" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_topics.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"comment" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_topics.comment",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "50",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, title;;;;2-2-2, comment;;;;3-3-3")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "fe_group")
	)
);



$TCA["tx_vm19docsbase_support"] = Array (
	"ctrl" => $TCA["tx_vm19docsbase_support"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,title,comment"
	),
	"feInterface" => $TCA["tx_vm19docsbase_support"]["feInterface"],
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
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_support.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "50",	
				"eval" => "required,trim",
			)
		),
		"comment" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_support.comment",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, title;;;;2-2-2, comment;;;;3-3-3")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_vm19docsbase_nature"] = Array (
	"ctrl" => $TCA["tx_vm19docsbase_nature"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,title,comment"
	),
	"feInterface" => $TCA["tx_vm19docsbase_nature"]["feInterface"],
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
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_nature.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"comment" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_nature.comment",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "250",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, title;;;;2-2-2, comment;;;;3-3-3")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_vm19docsbase_lang"] = Array (
	"ctrl" => $TCA["tx_vm19docsbase_lang"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,langcode,title"
	),
	"feInterface" => $TCA["tx_vm19docsbase_lang"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"langcode" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_lang.langcode",		
			"config" => Array (
				"type" => "input",	
				"size" => "5",	
				"max" => "5",	
				"eval" => "required,trim,unique",
			)
		),
		"title" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_lang.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "50",	
				"eval" => "required,unique",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, langcode, title;;;;2-2-2")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_vm19docsbase_docs"] = Array (
	"ctrl" => $TCA["tx_vm19docsbase_docs"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,starttime,endtime,fe_group,internal_code,title,topics,int_author,ext_author,support,nature,lang,isbn,keywords,abstract,imagette,workflow_state,document"
	),
	"feInterface" => $TCA["tx_vm19docsbase_docs"]["feInterface"],
	"columns" => Array (
	// rajout VM manuel pour pouvoir modifier la date de création du doc
		"crdate" => Array (		
			"exclude" => 1,	
			"label" => "Date de creation",
			"config" => Array (
				"type" => "none",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"default" => date("d-m-y"),
			)
		),		
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
		"internal_code" => Array (		
			"exclude" => 0,		
			'label_alt' => 'breuillasse y dit qui fo pas l\'enlever',
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_docs.internal_code",		
			"config" => Array (
				"type" => "input",	
				"size" => "15",	
				"max" => "15",	
//				"eval" => "required,trim,unique", plus obligé

			)
		),
		"title" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_docs.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "50",	
				"eval" => "required,trim",
			)
		),
		"topics" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_docs.topics",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_vm19docsbase_topics",	
				"foreign_table_where" => "ORDER BY tx_vm19docsbase_topics.uid",	
				"size" => 15,	
				"minitems" => 1,
				"maxitems" => 5,	
				/* vire les acces pour éditer la liste
				"wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 1,
					"add" => Array(
						"type" => "script",
						"title" => "Create new record",
						"icon" => "add.gif",
						"params" => Array(
							"table"=>"tx_vm19docsbase_topics",
							"pid" => "###CURRENT_PID###",
							"setValue" => "prepend"
						),
						"script" => "wizard_add.php",
					),
					"list" => Array(
						"type" => "script",
						"title" => "List",
						"icon" => "list.gif",
						"params" => Array(
							"table"=>"tx_vm19docsbase_topics",
							"pid" => "###CURRENT_PID###",
						),
						"script" => "wizard_list.php",
					),
					"edit" => Array(
						"type" => "popup",
						"title" => "Edit",
						"script" => "wizard_edit.php",
						"popup_onlyOpenIfSelected" => 1,
						"icon" => "edit2.gif",
						"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
					),
				), */
			)
		),
		"int_author" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_docs.int_author",		
			"config" => Array (
				"type" => "none",	
				"default" => "désactivé pour l`instant",				
//				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "fe_users",	
				"size" => 15,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"ext_author" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_docs.ext_author",		
			"config" => Array (
				"type" => "input",	
				"size" => "48",	
				"max" => "50",	
//				"checkbox" => "",	
				"eval" => "trim",
			)
		),
		"support" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_docs.support",		
			"config" => Array (
				"type" => "select",
				/* on dirait que c'est le defaut
				"items" => Array (
					Array("",0),
				),*/
				"foreign_table" => "tx_vm19docsbase_support",	
				"foreign_table_where" => "ORDER BY tx_vm19docsbase_support.uid",	
				"size" => 7,	
				"minitems" => 1,
				"maxitems" => 1,	
				/*
				"wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 1,
					"add" => Array(
						"type" => "script",
						"title" => "Create new record",
						"icon" => "add.gif",
						"params" => Array(
							"table"=>"tx_vm19docsbase_support",
							"pid" => "###CURRENT_PID###",
							"setValue" => "prepend"
						),
						"script" => "wizard_add.php",
					),
					"list" => Array(
						"type" => "script",
						"title" => "List",
						"icon" => "list.gif",
						"params" => Array(
							"table"=>"tx_vm19docsbase_support",
							"pid" => "###CURRENT_PID###",
						),
						"script" => "wizard_list.php",
					),
					"edit" => Array(
						"type" => "popup",
						"title" => "Edit",
						"script" => "wizard_edit.php",
						"popup_onlyOpenIfSelected" => 1,
						"icon" => "edit2.gif",
						"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
					),
				), */
			)
		),
		"nature" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_docs.nature",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_vm19docsbase_nature",	
				"foreign_table_where" => "ORDER BY tx_vm19docsbase_nature.uid",	
				"size" => 15,	
				"minitems" => 1,
				"maxitems" => 1,	
			)
		),
		"lang" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_docs.lang",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_vm19docsbase_lang",	
				"foreign_table_where" => "ORDER BY tx_vm19docsbase_lang.uid",	
				"size" => 10,	
				"minitems" => 1,
				"maxitems" => 1,	
				/*"wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 1,
					"add" => Array(
						"type" => "script",
						"title" => "Create new record",
						"icon" => "add.gif",
						"params" => Array(
							"table"=>"tx_vm19docsbase_lang",
							"pid" => "###CURRENT_PID###",
							"setValue" => "prepend"
						),
						"script" => "wizard_add.php",
					),
					"list" => Array(
						"type" => "script",
						"title" => "List",
						"icon" => "list.gif",
						"params" => Array(
							"table"=>"tx_vm19docsbase_lang",
							"pid" => "###CURRENT_PID###",
						),
						"script" => "wizard_list.php",
					),
					"edit" => Array(
						"type" => "popup",
						"title" => "Edit",
						"script" => "wizard_edit.php",
						"popup_onlyOpenIfSelected" => 1,
						"icon" => "edit2.gif",
						"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
					),
				),*/
			)
		),
		"source" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_docs.source",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "50",	
				"default" => "LES HARAS NATIONAUX",
				//"eval" => "required",
			)
		),

		"isbn" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_docs.isbn",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "15",	
				"eval" => "trim",
			)
		),
		"keywords" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_docs.keywords",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "50",	
				"eval" => "required",
			)
		),
		"abstract" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_docs.abstract",		
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
		"imagette" => Array (
			"exclude" => 0,
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_docs.imagette",
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "gif,png,jpeg,jpg",
				"max_size" => 500,
				"uploadfolder" => $upload_doc_folder,
				"show_thumbs" => 1,
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"workflow_state" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_docs.workflow_state",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"document" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:vm19_docs_base/locallang_db.php:tx_vm19docsbase_docs.document",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "",	
				"disallowed" => "php,php3",	
				"max_size" => 5000,	
				"uploadfolder" => $upload_doc_folder,
				"show_thumbs" => 1,
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "crdate,hidden;;1;;1-1-1, internal_code, title;;;;2-2-2, topics;;;;3-3-3, int_author, ext_author, support, nature, lang, source, isbn, keywords, abstract;;;richtext[cut|copy|paste|formatblock|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image|line|chMode]:rte_transform[mode=ts], imagette, workflow_state, document")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "starttime, endtime, fe_group")
	)
);
?>
