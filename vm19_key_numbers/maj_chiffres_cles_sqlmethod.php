<?php

include('../../localconf.php');

$db_id = mysql_connect($typo_db_host, $typo_db_username, $typo_db_password);
mysql_select_db($typo_db, $db_id);
$req = mysql_query('SELECT uid,update_seq FROM tx_vm19keynumbers_numbers WHERE update_seq LIKE "%SQLMETHOD%"') or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

if($req)
{
        while($data = mysql_fetch_array($req))
        {
                $tmp    = explode("\n", $data['update_seq']);         
                $ligne2 = $tmp[1];
                $ligne3 = $tmp[2];
                
                $tmp = explode('|', $ligne2);
                $t_dbtype = $tmp[0];
                $t_dbhost = $tmp[1];
                $t_dblog  = $tmp[2];
                $t_dbpass = $tmp[3];
                $t_dbbase = $tmp[4];

		if($t_dbtype == 'mysql') 
		{
                	$t_id = mysql_connect($t_dbhost, $t_dblog, $t_dbpass) or die('Erreur de connexion !<br>'.mysql_error());
                	mysql_select_db($t_dbbase, $t_id);
                	$t_req = mysql_result( mysql_query($ligne3, $t_id), 0) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
			mysql_select_db('hng_test', $t_id);
			mysql_query('UPDATE `tx_vm19keynumbers_numbers` SET `k_value` = "'.$t_req.'" WHERE uid="'.$data['uid'].'"', $t_id) or die('Erreur SQL !<br>'.mysql_error());

                	mysql_close($t_id);
		}
        }
}

mysql_close($db_id);
?>
