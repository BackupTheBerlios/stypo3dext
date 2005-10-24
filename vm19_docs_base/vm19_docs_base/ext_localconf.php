<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_vm19docsbase_topics=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_vm19docsbase_support=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_vm19docsbase_nature=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_vm19docsbase_lang=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_vm19docsbase_docs=1
');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,"editorcfg","
	tt_content.CSS_editor.ch.tx_vm19docsbase_pi1 = < plugin.tx_vm19docsbase_pi1.CSS_editor
",43);


## LE DERNIER 1 Indique si le contenu est mis en cache
t3lib_extMgm::addPItoST43($_EXTKEY,"pi1/class.tx_vm19docsbase_pi1.php","_pi1","list_type",1);


t3lib_extMgm::addTypoScript($_EXTKEY,"setup","
	tt_content.shortcut.20.0.conf.tx_vm19docsbase_docs = < plugin.".t3lib_extMgm::getCN($_EXTKEY)."_pi1
	tt_content.shortcut.20.0.conf.tx_vm19docsbase_docs.CMD = singleView
",43);
?>
