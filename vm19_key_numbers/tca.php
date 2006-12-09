<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$TCA["tx_vm19keynumbers_unities"] = Array (
	"ctrl" => $TCA["tx_vm19keynumbers_unities"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,unity,unity_code,comment"
	),
	"feInterface" => $TCA["tx_vm19keynumbers_unities"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		## WOP:[tables][1][add_hidden]
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"unity" => Array (		## WOP:[tables][1][fields][1][fieldname]
			"exclude" => 1,		## WOP:[tables][1][fields][1][excludeField]
			"label" => "LLL:EXT:vm19_key_numbers/locallang_db.php:tx_vm19keynumbers_unities.unity",		## WOP:[tables][1][fields][1][title]
			"config" => Array (
				"type" => "input",	## WOP:[tables][1][fields][1][type]
				"size" => "48",	## WOP:[tables][1][fields][1][conf_size]
				"max" => "50",	## WOP:[tables][1][fields][1][conf_max]
				"eval" => "required,trim,unique",	## WOP:[tables][1][fields][1][conf_required] / [tables][1][fields][1][conf_varchar] / [tables][1][fields][1][conf_unique] = Global (unique in whole database)
			)
		),
		"unity_code" => Array (		## WOP:[tables][1][fields][3][fieldname]
			"exclude" => 1,		## WOP:[tables][1][fields][3][excludeField]
			"label" => "LLL:EXT:vm19_key_numbers/locallang_db.php:tx_vm19keynumbers_unities.unity_code",		## WOP:[tables][1][fields][3][title]
			"config" => Array (
				"type" => "input",	## WOP:[tables][1][fields][3][type]
				"size" => "30",	## WOP:[tables][1][fields][3][conf_size]
				"eval" => "required,trim",	## WOP:[tables][1][fields][3][conf_required] / [tables][1][fields][3][conf_varchar]
			)
		),
		"icon" => Array (
			"exclude" => 0,
			"label" => "LLL:EXT:vm19_key_numbers/locallang_db.php:tx_vm19keynumbers_unities.icon",
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "gif,png,jpeg,jpg",
				"max_size" => 500,
				"uploadfolder" => "uploads/tx_vm19keynumbers",
				"show_thumbs" => 1,
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),

		"comment" => Array (		## WOP:[tables][1][fields][2][fieldname]
			"exclude" => 1,		## WOP:[tables][1][fields][2][excludeField]
			"label" => "LLL:EXT:vm19_key_numbers/locallang_db.php:tx_vm19keynumbers_unities.comment",		## WOP:[tables][1][fields][2][title]
			"config" => Array (
				"type" => "input",	## WOP:[tables][1][fields][2][type]
				"size" => "48",	## WOP:[tables][1][fields][2][conf_size]
				"max" => "250",	## WOP:[tables][1][fields][2][conf_max]
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, unity, unity_code, icon, comment")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_vm19keynumbers_numbers"] = Array (
	"ctrl" => $TCA["tx_vm19keynumbers_numbers"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,starttime,endtime,fe_group,title,k_value,unity,comment,update_type,update_period,update_seq"
	),
	"feInterface" => $TCA["tx_vm19keynumbers_numbers"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		## WOP:[tables][2][add_hidden]
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"starttime" => Array (		## WOP:[tables][2][add_starttime]
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
		"endtime" => Array (		## WOP:[tables][2][add_endtime]
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
		"fe_group" => Array (		## WOP:[tables][2][add_access]
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
		"title" => Array (		## WOP:[tables][2][fields][2][fieldname]
			"exclude" => 1,		## WOP:[tables][2][fields][2][excludeField]
			"label" => "LLL:EXT:vm19_key_numbers/locallang_db.php:tx_vm19keynumbers_numbers.title",		## WOP:[tables][2][fields][2][title]
			"config" => Array (
				"type" => "input",	## WOP:[tables][2][fields][2][type]
				"size" => "30",	## WOP:[tables][2][fields][2][conf_size]
				"max" => "50",	## WOP:[tables][2][fields][2][conf_max]
				"eval" => "required,trim",	## WOP:[tables][2][fields][2][conf_required] / [tables][2][fields][2][conf_varchar]
			)
		),
		"k_value" => Array (		## WOP:[tables][2][fields][3][fieldname]
			"exclude" => 1,		## WOP:[tables][2][fields][3][excludeField]
			"label" => "LLL:EXT:vm19_key_numbers/locallang_db.php:tx_vm19keynumbers_numbers.k_value",		## WOP:[tables][2][fields][3][title]
			"config" => Array (
				"type" => "input",	## WOP:[tables][2][fields][3][type]
				"size" => "20",	## WOP:[tables][2][fields][3][conf_size]
				##"eval" => "required,double2",	## WOP:[tables][2][fields][3][conf_required] / [tables][2][fields][3][conf_eval]
				"default" => "0",
				"eval" => "required",	## WOP:[tables][2][fields][3][conf_required] / [tables][2][fields][3][conf_eval]
			)
		),
		"unity" => Array (		## WOP:[tables][2][fields][4][fieldname]
			"exclude" => 1,		## WOP:[tables][2][fields][4][excludeField]
			"label" => "LLL:EXT:vm19_key_numbers/locallang_db.php:tx_vm19keynumbers_numbers.unity",		## WOP:[tables][2][fields][4][title]
			"config" => Array (
				"type" => "select",	## WOP:[tables][2][fields][4][conf_rel_type]
				"foreign_table" => "tx_vm19keynumbers_unities",	## WOP:[tables][2][fields][4][conf_rel_table]
				"foreign_table_where" => "ORDER BY tx_vm19keynumbers_unities.sorting",	## WOP:[tables][2][fields][4][conf_rel_type]
				"size" => 4,	## WOP:[tables][2][fields][4][conf_relations_selsize]
				"minitems" => 1,
				"maxitems" => 1,	## WOP:[tables][2][fields][4][conf_relations]
			)
		),
		"comment" => Array (		## WOP:[tables][2][fields][5][fieldname]
			"exclude" => 1,		## WOP:[tables][2][fields][5][excludeField]
			"label" => "LLL:EXT:vm19_key_numbers/locallang_db.php:tx_vm19keynumbers_numbers.comment",		## WOP:[tables][2][fields][5][title]
			"config" => Array (
				"type" => "text",
				"cols" => "30",	## WOP:[tables][2][fields][5][conf_cols]
				"rows" => "3",	## WOP:[tables][2][fields][5][conf_rows]
			)
		),
		"update_type" => Array (		## WOP:[tables][2][fields][6][fieldname]
			"exclude" => 1,		## WOP:[tables][2][fields][6][excludeField]
			"label" => "LLL:EXT:vm19_key_numbers/locallang_db.php:tx_vm19keynumbers_numbers.update_type",		## WOP:[tables][2][fields][6][title]
			"config" => Array (
				"type" => "check",
			)
		),
		"update_period" => Array (		## WOP:[tables][2][fields][8][fieldname]
			"exclude" => 1,		## WOP:[tables][2][fields][8][excludeField]
			"label" => "LLL:EXT:vm19_key_numbers/locallang_db.php:tx_vm19keynumbers_numbers.update_period",		## WOP:[tables][2][fields][8][title]
			"config" => Array (
				"type" => "input",	## WOP:[tables][2][fields][8][type]
				"size" => "4",	## WOP:[tables][2][fields][8][conf_size]
				"default" => "30",	## WOP:[tables][2][fields][8][conf_size]
				"eval" => "required",	## WOP:[tables][2][fields][8][conf_required]
			)
		),
		"update_seq" => Array (		## WOP:[tables][2][fields][7][fieldname]
			"exclude" => 1,		## WOP:[tables][2][fields][7][excludeField]
			"label" => "LLL:EXT:vm19_key_numbers/locallang_db.php:tx_vm19keynumbers_numbers.update_seq",		## WOP:[tables][2][fields][7][title]
			"config" => Array (
				"type" => "text",
				"cols" => "40",	## WOP:[tables][2][fields][7][conf_cols]
				"rows" => "10",	## WOP:[tables][2][fields][7][conf_rows]
				"wizards" => Array(
					"_PADDING" => 2,
					## WOP:[tables][2][fields][7][conf_wiz_example]
					"example" => Array(
						"title" => "Example Wizard:",
						"type" => "script",
						"notNewRecords" => 1,
						"icon" => t3lib_extMgm::extRelPath("vm19_key_numbers")."tx_vm19keynumbers_numbers_update_seq/wizard_icon.gif",
						"script" => t3lib_extMgm::extRelPath("vm19_key_numbers")."tx_vm19keynumbers_numbers_update_seq/index.php",
					),
				),
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, title;;;;2-2-2, k_value;;;;3-3-3, unity, comment, update_type, update_period, update_seq")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "starttime, endtime, fe_group")
	)
);
?>
