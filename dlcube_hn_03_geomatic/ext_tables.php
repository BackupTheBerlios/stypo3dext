<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
$TCA["tx_dlcubehn03geomatic_points"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points',		
		'label' => 'name',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",	
			"starttime" => "starttime",	
			"endtime" => "endtime",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_dlcubehn03geomatic_points.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, starttime, endtime, name, number, type, geo_pos, geo_lat,geo_long,label, adresse1, adresse2, cdpst, ville,region, tel, cell,fax, mail",
	)
);
?>