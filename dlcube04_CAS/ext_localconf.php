<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

t3lib_extMgm::addPItoST43($_EXTKEY,"pi1/class.tx_dlcube04CAS_pi1.php","_pi1","includeLib",0);


  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,"editorcfg","
	tt_content.CSS_editor.ch.tx_dlcube04CAS_pi2 = < plugin.tx_dlcube04CAS_pi2.CSS_editor
",43);

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,"editorcfg","
	tt_content.CSS_editor.ch.tx_dlcube04CAS_pi3 = < plugin.tx_dlcube04CAS_pi3.CSS_editor
",43);

## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,"editorcfg","
	tt_content.CSS_editor.ch.tx_dlcube04CAS_pi4 = < plugin.tx_dlcube04CAS_pi4.CSS_editor
",43);

## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,"editorcfg","
	tt_content.CSS_editor.ch.tx_dlcube04CAS_pi5 = < plugin.tx_dlcube04CAS_pi5.CSS_editor
",43);

## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,"editorcfg","
	tt_content.CSS_editor.ch.tx_dlcube04CAS_pi6 = < plugin.tx_dlcube04CAS_pi6.CSS_editor
",43);

## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,"editorcfg","
	tt_content.CSS_editor.ch.tx_dlcube04CAS_pi7 = < plugin.tx_dlcube04CAS_pi7.CSS_editor
",43);

t3lib_extMgm::addPItoST43($_EXTKEY,"pi2/class.tx_dlcube04CAS_pi2.php","_pi2","list_type",0);
t3lib_extMgm::addPItoST43($_EXTKEY,"pi3/class.tx_dlcube04CAS_pi3.php","_pi3","list_type",0);
t3lib_extMgm::addPItoST43($_EXTKEY,"pi4/class.tx_dlcube04CAS_pi4.php","_pi4","list_type",0);
t3lib_extMgm::addPItoST43($_EXTKEY,"pi5/class.tx_dlcube04CAS_pi5.php","_pi5","list_type",0);
t3lib_extMgm::addPItoST43($_EXTKEY,"pi6/class.tx_dlcube04CAS_pi6.php","_pi6","list_type",0);
t3lib_extMgm::addPItoST43($_EXTKEY,"pi7/class.tx_dlcube04CAS_pi7.php","_pi7","list_type",0);
?>