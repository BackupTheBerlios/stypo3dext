<?php

########################################################################
# Extension Manager/Repository config file for ext: 'vm19_key_numbers'
# 
# Auto generated 28-01-2004 19:07
# 
# Manual updates:
# Only the data in the array - anything else is removed by next write
########################################################################

$EM_CONF[$_EXTKEY] = Array (
	'title' => 'Key numbers',
	'description' => 'This extension allows to store and display numbers as indicators, associated with labels, units in a separate mysql table. This numbers are planed to be updated manually or auomatically: to do that, a texte area with a .ini syntax contains configuration datas for the update: server address, mode/protocol (http, ftp, direct sql query...), user & passwd, sql statement and so on...',
	'category' => 'plugin',
	'shy' => 0,
	'dependencies' => 'cms,vm19_toolbox',
	'conflicts' => '',
	'priority' => '',
	'module' => 'tx_vm19keynumbers_numbers_update_seq',
	'state' => 'alpha',
	'internal' => 0,
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author' => 'Vincent MAURY - ArTec - 19',
	'author_email' => 'artec.vm@nerim.net',
	'author_company' => '',
	'private' => 0,
	'download_password' => '',
	'version' => '0.0.4',	// Don't modify this! Managed automatically during upload to repository.
	'_md5_values_when_last_written' => 'a:29:{s:12:"ext_icon.gif";s:4:"c804";s:17:"ext_localconf.php";s:4:"9a44";s:14:"ext_tables.php";s:4:"deab";s:14:"ext_tables.sql";s:4:"bfe4";s:28:"ext_typoscript_constants.txt";s:4:"9846";s:28:"ext_typoscript_editorcfg.txt";s:4:"3ee1";s:24:"ext_typoscript_setup.txt";s:4:"9cdb";s:34:"icon_tx_vm19keynumbers_numbers.gif";s:4:"c804";s:34:"icon_tx_vm19keynumbers_unities.gif";s:4:"0b04";s:13:"locallang.php";s:4:"8189";s:16:"locallang_db.php";s:4:"8918";s:7:"tca.php";s:4:"03cd";s:46:"tx_vm19keynumbers_numbers_update_seq/clear.gif";s:4:"cc11";s:45:"tx_vm19keynumbers_numbers_update_seq/conf.php";s:4:"82d7";s:46:"tx_vm19keynumbers_numbers_update_seq/index.php";s:4:"eec2";s:50:"tx_vm19keynumbers_numbers_update_seq/locallang.php";s:4:"1e68";s:52:"tx_vm19keynumbers_numbers_update_seq/wizard_icon.gif";s:4:"1bdc";s:14:"pi1/ce_wiz.gif";s:4:"c804";s:40:"pi1/class.tx_vm19keynumbers_pi1.orig_wiz";s:4:"f6e9";s:41:"pi1/class.tx_vm19keynumbers_pi1.orig_wiz~";s:4:"879a";s:35:"pi1/class.tx_vm19keynumbers_pi1.php";s:4:"5210";s:43:"pi1/class.tx_vm19keynumbers_pi1_wizicon.php";s:4:"fd31";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.php";s:4:"b3ff";s:19:"doc/wizard_form.dat";s:4:"6ff4";s:20:"doc/wizard_form.html";s:4:"ad84";s:20:".xvpics/ext_icon.gif";s:4:"dfa8";s:42:".xvpics/icon_tx_vm19keynumbers_numbers.gif";s:4:"dfa8";s:42:".xvpics/icon_tx_vm19keynumbers_unities.gif";s:4:"1762";}',
);

?>
