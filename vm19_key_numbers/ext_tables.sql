# TYPO3 Extension Manager dump 1.0
#
# Host: localhost    Database: typo3_dummy
#--------------------------------------------------------


#
# Table structure for table 'tx_vm19keynumbers_unities'
#
CREATE TABLE tx_vm19keynumbers_unities (
  uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
  pid int(11) unsigned DEFAULT '0' NOT NULL,
  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  sorting int(10) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  unity varchar(50) DEFAULT '' NOT NULL,
  comment tinytext NOT NULL,
  icon blob NOT NULL,
  unity_code varchar(255) DEFAULT '' NOT NULL,
  PRIMARY KEY (uid),
  KEY parent (pid)
);


#
# Table structure for table 'tx_vm19keynumbers_numbers'
#
CREATE TABLE tx_vm19keynumbers_numbers (
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
  title varchar(50) DEFAULT '' NOT NULL,
  k_value float DEFAULT '0' NOT NULL,
  unity int(11) unsigned DEFAULT '0' NOT NULL,
  comment text NOT NULL,
  update_type tinyint(3) unsigned DEFAULT '0' NOT NULL,
  update_seq text NOT NULL,
  update_period int(10) unsigned DEFAULT '30' NOT NULL,
  PRIMARY KEY (uid),
  KEY parent (pid)
);
