#
# Table structure for table 'tx_fabformmail_abonne'
#
CREATE TABLE tx_fabformmail_abonne (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	nom tinytext NOT NULL,
	email tinytext NOT NULL,
	newsletter tinyint(4) unsigned DEFAULT '0' NOT NULL,
	comment text NOT NULL,
	typefrequence tinyint(4) unsigned DEFAULT '0' NOT NULL,
	typeetat tinyint(4) unsigned DEFAULT '0' NOT NULL,
	typeextension blob NOT NULL,
	uidpages blob NOT NULL,
	lastsentdate int(11) unsigned DEFAULT '0' NOT NULL
	PRIMARY KEY (uid),
	KEY parent (pid)
);
