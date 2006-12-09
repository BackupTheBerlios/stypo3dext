#
# Table structure for table 'tx_dlcubehn03geomatic_points'
#
CREATE TABLE tx_dlcubehn03geomatic_points (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	number varchar(255) DEFAULT '' NOT NULL,
	type varchar(10) DEFAULT 'AUTRE' NOT NULL,
	type_det varchar(100) DEFAULT '' NOT NULL,
	geo_pos varchar(255) DEFAULT '' NOT NULL,
	geo_lat float DEFAULT 0 NOT NULL,
	geo_long float DEFAULT 0 NOT NULL,
	label tinytext NOT NULL,
	adresse1 tinytext NOT NULL,
	adresse2 tinytext NOT NULL,
	cdpst tinytext NOT NULL,
	ville tinytext NOT NULL,
	region tinytext NOT NULL,
	tel tinytext NOT NULL,
	fax tinytext NOT NULL,
	cell tinytext NOT NULL,
	mail tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);