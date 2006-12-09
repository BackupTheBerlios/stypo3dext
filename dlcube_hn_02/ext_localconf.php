<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
t3lib_extMgm::addPageTSConfig('
	toto=toto
');
t3lib_extMgm::addUserTSConfig('
	toto=toto
');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,"editorcfg","
	tt_content.CSS_editor.ch.tx_dlcubehn02_pi1 = < plugin.tx_dlcubehn02_pi1.CSS_editor
",43);


t3lib_extMgm::addPItoST43($_EXTKEY,"pi1/class.tx_dlcubehn02_pi1.php","_pi1","list_type",1);


  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,"editorcfg","
	tt_content.CSS_editor.ch.tx_dlcubehn02_pi2 = < plugin.tx_dlcubehn02_pi2.CSS_editor
",43);


t3lib_extMgm::addPItoST43($_EXTKEY,"pi2/class.tx_dlcubehn02_pi2.php","_pi2","list_type",1);


  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,"editorcfg","
	tt_content.CSS_editor.ch.tx_dlcubehn02_pi3 = < plugin.tx_dlcubehn02_pi3.CSS_editor
",43);


t3lib_extMgm::addPItoST43($_EXTKEY,"pi3/class.tx_dlcubehn02_pi3.php","_pi3","list_type",1);
?>