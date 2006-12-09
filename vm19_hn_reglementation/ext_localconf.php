<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_vm19hnreglementation_nature=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_vm19hnreglementation_textes=1
');
t3lib_extMgm::addPageTSConfig('

	# ***************************************************************************************
	# CONFIGURATION of RTE in table "tx_vm19hnreglementation_textes", field "desc_2bf7363fc2"
	# ***************************************************************************************
RTE.config.tx_vm19hnreglementation_textes.desc_2bf7363fc2 {
  hidePStyleItems = H1, H4, H5, H6
  proc.exitHTMLparser_db=1
  proc.exitHTMLparser_db {
    keepNonMatchedTags=1
    tags.font.allowedAttribs= color
    tags.font.rmTagIfNoAttrib = 1
    tags.font.nesting = global
  }
}
');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,"editorcfg","
	tt_content.CSS_editor.ch.tx_vm19hnreglementation_pi1 = < plugin.tx_vm19hnreglementation_pi1.CSS_editor
",43);

//t3lib_extMgm::addPItoST43($_EXTKEY,"pi1/class.tx_vm19hnreglementation_pi1.php","_pi1","list_type",1);


// Le dernier paramètre (1 ou 0) permet d'activer ou désactiver le "cachage" de l'extension
t3lib_extMgm::addPItoST43($_EXTKEY,"pi1/class.tx_vm19hnreglementation_pi1.php","_pi1","list_type",1);
?>