<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_dlcubenewsletters_topnews_ids=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_dlcubenewsletters_region_ids=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_dlcubenewsletters_presse_ids=1
');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_dlcubenewsletters_pi1 = < plugin.tx_dlcubenewsletters_pi1.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_dlcubenewsletters_pi1.php','_pi1','list_type',0);


t3lib_extMgm::addTypoScript($_EXTKEY,'setup','
	tt_content.shortcut.20.0.conf.tx_dlcubenewsletters_topnews_ids = < plugin.'.t3lib_extMgm::getCN($_EXTKEY).'_pi1
	tt_content.shortcut.20.0.conf.tx_dlcubenewsletters_topnews_ids.CMD = singleView
',43);


  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_dlcubenewsletters_pi2 = < plugin.tx_dlcubenewsletters_pi2.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'pi2/class.tx_dlcubenewsletters_pi2.php','_pi2','list_type',0);


t3lib_extMgm::addTypoScript($_EXTKEY,'setup','
	tt_content.shortcut.20.0.conf.tx_dlcubenewsletters_region_ids = < plugin.'.t3lib_extMgm::getCN($_EXTKEY).'_pi2
	tt_content.shortcut.20.0.conf.tx_dlcubenewsletters_region_ids.CMD = singleView
',43);


  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_dlcubenewsletters_pi3 = < plugin.tx_dlcubenewsletters_pi3.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'pi3/class.tx_dlcubenewsletters_pi3.php','_pi3','list_type',0);


t3lib_extMgm::addTypoScript($_EXTKEY,'setup','
	tt_content.shortcut.20.0.conf.tx_dlcubenewsletters_presse_ids = < plugin.'.t3lib_extMgm::getCN($_EXTKEY).'_pi3
	tt_content.shortcut.20.0.conf.tx_dlcubenewsletters_presse_ids.CMD = singleView
',43);
?>