#
# Table structure for table 'tx_vm19news_news'
#
CREATE TABLE tx_vm19news_news (
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
	fe_group int(11) DEFAULT '0' NOT NULL,
	workf_state int(11) unsigned DEFAULT '0' NOT NULL,
	title varchar(50) DEFAULT '' NOT NULL,
	paccdisp tinyint(4) unsigned DEFAULT '0' NOT NULL,
	abstract text NOT NULL,
	small_img blob NOT NULL,
	bodytext text NOT NULL,
	big_img blob NOT NULL,
	bimg_credit varchar(50) DEFAULT '' NOT NULL,
	bimg_legend varchar(100) DEFAULT '' NOT NULL,
	author blob NOT NULL,
	document blob NOT NULL,
	otherpages blob NOT NULL,
	actusuite  blob NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);