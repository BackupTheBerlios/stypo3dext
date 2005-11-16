#
# Table structure for table 'tx_vm19hnlinks_urls'
#
CREATE TABLE tx_vm19hnlinks_urls (
	uid int(11) DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(10) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	url_url varchar(255) DEFAULT '' NOT NULL,
	url_title varchar(255) DEFAULT '' NOT NULL,
	url_desc text NOT NULL,
	url_kwords tinytext NOT NULL,
	url_state int(11) unsigned DEFAULT '0' NOT NULL,
	url_mailwb tinytext NOT NULL,
	url_lang int(11) unsigned DEFAULT '0' NOT NULL,
	url_othercateg blob NOT NULL,
	url_datev int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);