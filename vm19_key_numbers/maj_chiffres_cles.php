<?php

include('../../localconf.php');

$db_id = mysql_connect($typo_db_host, $typo_db_username, $typo_db_password);
mysql_select_db($typo_db, $db_id);

# PARTIE SERVEUR


if($_GET['name'] && $_GET['value'] )
{
	(int)$name  = trim($_GET['name']);
	(int)$value = trim($_GET['value']);
	mysql_query('UPDATE `tx_vm19keynumbers_numbers` SET k_value="'.$value.'" WHERE `update_seq` like "%'.$name.'%";');
}

mysql_close($db_id);
?>
