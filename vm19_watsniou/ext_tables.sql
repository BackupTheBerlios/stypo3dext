#
# Structure de la table `tx_vm19watsniou`
#

CREATE TABLE tx_vm19watsniou (
  uid int(11) unsigned NOT NULL default '0',
  pid int(11) unsigned NOT NULL default '0',
  tstamp int(11) unsigned NOT NULL default '0',
  crdate int(11) unsigned NOT NULL default '0',
  sorting bigint(20) unsigned NOT NULL default '0',
  starttime int(11) unsigned NOT NULL default '0',
  endtime int(11) unsigned NOT NULL default '0',
  deleted tinyint(4) unsigned NOT NULL default '0',
  hidden tinyint(4) unsigned NOT NULL default '0',
  title varchar(50) NOT NULL default '',
  tabidarbo varchar(50) NOT NULL default '',
  tabstrarbo varchar(255) NOT NULL default '',
  typcontent varchar(50) NOT NULL default '',
  
  PRIMARY KEY  (uid,typcontent),
  KEY parent (pid),
  KEY pid (pid),
  KEY tstamp (tstamp),
  KEY crdate (crdate)
);

ALTER TABLE tt_content ADD crdate INT( 11 ) DEFAULT '0' NOT NULL AFTER tstamp ;
