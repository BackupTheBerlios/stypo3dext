<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_dlcubehn03geomatic_points"] = Array (
	"ctrl" => $TCA["tx_dlcubehn03geomatic_points"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,starttime,endtime,name,number,type,geo_pos,label,adresse1,adresse2,cdpst,ville,region,tel,cell,fax,mail"
	),
	"feInterface" => $TCA["tx_dlcubehn03geomatic_points"]["feInterface"],
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
		"name" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.name",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"number" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.number",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"type" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.type",		
			"config" => Array (
				"type" => "radio",
				"items" => Array (
					Array("LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.type.I.0", "CT"),
					Array("LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.type.I.1", "POLE"),
					Array("LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.type.I.2", "DR"),
					Array("LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.type.I.3", "AUTRE"),
					Array("LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.type.I.4", "CENT_REGIO"),
				),
			)
		),
		"type_det" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.type_det",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"geo_pos" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.geo_pos",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"geo_lat" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.geo_lat",		
			"config" => Array (
				"type" => "input",	
				"size" => "15",	
				"eval" => "required,trim",
			)
		),
		"geo_long" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.geo_long",		
			"config" => Array (
				"type" => "input",	
				"size" => "15",	
				"eval" => "required,trim",
			)
		),
		"label" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.label",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"adresse1" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.adresse1",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"adresse2" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.adresse2",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"cdpst" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.cdpst",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"ville" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.ville",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),

				
		
		"region" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.region",		
			"config" => Array (
				"type" => "select",
				"items" => Array (				
					Array("Alsace","ALS"),
					Array("Aquitaine","AQU"),
					Array("Auvergne","AUV"),
					Array("Bourgogne","BRG"),
					Array("Bretagne","BRT"),
					Array("Centre","CTR"),
					Array("Champagne-Ardenne","CHP"),
					Array("Corse","COR"),
					Array("Franche Comte","FRC"),
					Array("Ile de France","IDF"),
					Array("Languedoc-Roussillon","LGR"),
					Array("Limousin","LIM"),
					Array("Lorraine","LOR"),
					Array("Midi-Pyrenees","MID"),
					Array("Basse Normandie","BND"),
					Array("Haute Normandie","HND"),
					Array("Nord-Pas de Calais","NPC"),
					Array("Pays de la Loire","PDL"),
					Array("Picardie","PIC"),
					Array("Poitou-Charentes","PCH"),
					Array("Provence-Alpes-Cote dAzur","PAC"),
					Array("Rhone-Alpes","RHA"),
					Array("Dep. et terr. d'Outre Mer","O.M"),
				)
			)
		),
		"tel" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.tel",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"cell" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.cell",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"fax" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.fax",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"mail" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:dlcube_hn_03_geomatic/locallang_db.xml:tx_dlcubehn03geomatic_points.mail",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, name, type, type_det, number, geo_pos, geo_lat,geo_long,label, adresse1, adresse2, cdpst, ville, region,tel, cell, fax, mail")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "starttime, endtime")
	)
);
?>