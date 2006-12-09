#
# Table structure for table 'tx_vm19userlinks_ulinks'
#
CREATE TABLE tx_vm19userlinks_ulinks (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	user_id blob NOT NULL,
	link_ids blob NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);