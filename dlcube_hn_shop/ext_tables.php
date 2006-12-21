<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


$TCA["tx_dlcubehnshop_articles"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_articles',		
		'label' => 'title',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		"sortby" => "sorting",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",	
			"starttime" => "starttime",	
			"endtime" => "endtime",	
			"fe_group" => "fe_group",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_dlcubehnshop_articles.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, starttime, endtime, fe_group, ref, ref2, title, auteur, editor, support, designation, descdetail, parut, price, tva, isbn, weight, nbpages, archive, technicaldegree, img1, img2, file",
	)
);

$TCA["tx_dlcubehnshop_editors"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_editors',		
		'label' => 'name',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_dlcubehnshop_editors.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, name, infos",
	)
);

$TCA["tx_dlcubehnshop_supports"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:dlcube_hn_shop/locallang_db.xml:tx_dlcubehnshop_supports',		
		'label' => 'name',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_dlcubehnshop_supports.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, name",
	)
);

t3lib_div::loadTCA("tt_content");
//$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout,select_key";
//$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout";

#t3lib_extMgm::addPlugin(Array("LLL:EXT:dlcube_hn_shop/locallang_db.php:tt_content.list_type", $_EXTKEY."_pi1"),"list_type");
t3lib_extMgm::addPlugin(Array("Rayon de boutique HN", $_EXTKEY."_pi1"),"list_type");

t3lib_extMgm::allowTableOnStandardPages("tx_dlcubehnshop_articles");


t3lib_extMgm::addToInsertRecords("tx_dlcubehnshop_articles");

?>