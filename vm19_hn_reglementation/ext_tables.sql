#
# Table structure for table 'tx_vm19hnreglementation_nature'
#
CREATE TABLE tx_vm19hnreglementation_nature (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	level tinytext NOT NULL,
	code tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_vm19hnreglementation_textes'
#
CREATE TABLE tx_vm19hnreglementation_textes (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(10) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	url tinytext NOT NULL,
	title tinytext NOT NULL,
	nature blob NOT NULL,
	dat_approb int(11) DEFAULT '0' NOT NULL,
	number tinytext NOT NULL,
	publication tinytext NOT NULL,
	desc_2bf7363fc2 text NOT NULL,
	fich_joint blob NOT NULL,
	kwords tinytext NOT NULL,
	orig tinytext NOT NULL,
	other_pages blob NOT NULL,
	parent_text blob NOT NULL,
	rtt_attach_type varchar(4) DEFAULT '' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);