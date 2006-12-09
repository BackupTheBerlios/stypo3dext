ALTER TABLE tx_vm19news_news 	ADD paccdisp tinyint(4) unsigned DEFAULT '0' NOT NULL;
ALTER TABLE tx_vm19news_news ADD bimg_credit varchar(50) DEFAULT '' NOT NULL;
ALTER TABLE tx_vm19news_news 	ADD bimg_legend varchar(100) DEFAULT '' NOT NULL;
ALTER TABLE tx_vm19news_news 	ADD document blob NOT NULL;
ALTER TABLE tx_vm19news_news 	ADD otherpages blob NOT NULL;
ALTER TABLE tx_vm19news_news 	ADD actusuite  blob NOT NULL;
	
	
