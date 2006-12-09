<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$TCA["tx_fabformmail_abonne"] = Array (
	"ctrl" => $TCA["tx_fabformmail_abonne"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,nom,email,comment,cat"
	),
	"feInterface" => $TCA["tx_fabformmail_abonne"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"nom" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:fab_form_mail/locallang_db.php:tx_fabformmail_abonne.nom",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"newsletter" => Array (		
			"exclude" => 1,	
			"label" => "newsletter",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"email" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:fab_form_mail/locallang_db.php:tx_fabformmail_abonne.email",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"comment" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:fab_form_mail/locallang_db.php:tx_fabformmail_abonne.comment",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",	
				"wizards" => Array(
					"_PADDING" => 2,
					"example" => Array(
						"title" => "Example Wizard:",
						"type" => "script",
						"notNewRecords" => 1,
						"icon" => t3lib_extMgm::extRelPath("fab_form_mail")."tx_fabformmail_abonne_comment/wizard_icon.gif",
						"script" => t3lib_extMgm::extRelPath("fab_form_mail")."tx_fabformmail_abonne_comment/index.php",
					),
				),
			)
		),
		"cat" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:fab_form_mail/locallang_db.php:tx_fabformmail_abonne.cat",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_vm19docsbase_file_cat",	
				"foreign_table_where" => "ORDER BY tx_vm19docsbase_file_cat.uid",	
				"size" => 3,	
				"minitems" => 0,
				"maxitems" => 6,	
				"wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 1,
					"list" => Array(
						"type" => "script",
						"title" => "List",
						"icon" => "list.gif",
						"params" => Array(
							"table"=>"tx_vm19docsbase_file_cat",
							"pid" => "###CURRENT_PID###",
						),
						"script" => "wizard_list.php",
					),
				),
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, nom, email, comment, cat")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);
?>
