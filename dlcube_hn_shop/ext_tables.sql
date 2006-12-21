#
# Table structure for table 'tx_dlcubehnshop_articles'
#
CREATE TABLE tx_dlcubehnshop_articles (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	ref varchar(255) DEFAULT '' NOT NULL,
	ref2 varchar(255) DEFAULT '' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	auteur tinytext NOT NULL,
	editor blob NOT NULL,
	support blob NOT NULL,
	designation text NOT NULL,
	descdetail text NOT NULL,
	parut tinytext NOT NULL,
	price double(11,2) DEFAULT '0.00' NOT NULL,
	tva double(11,2) DEFAULT '0.00' NOT NULL,
	isbn tinytext NOT NULL,
	weight int(11) DEFAULT '0' NOT NULL,
	nbpages int(11) DEFAULT '0' NOT NULL,
	archive tinyint(3) DEFAULT '0' NOT NULL,
	technicaldegree int(11) DEFAULT '0' NOT NULL,
	img1 blob NOT NULL,
	img2 blob NOT NULL,
	file blob NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_dlcubehnshop_editors'
#
CREATE TABLE tx_dlcubehnshop_editors (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	infos text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_dlcubehnshop_supports'
#
CREATE TABLE tx_dlcubehnshop_supports (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);