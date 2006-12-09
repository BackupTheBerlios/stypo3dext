<?
/* moulinette d'exportation de la la base de donnц╘es des liens vers l'extension typo

*/
include_once("fonctions.php");
include_once("../../localconf.php");
/* dedans il y a ça :
$typo_db_username = 'haras';	//  Modified or inserted by TYPO3 Install Tool.
$typo_db_password = 'azert12';       // Modified or inserted by TYPO3 Install Tool. 
$typo_db_host = 'localhost';	//  Modified or inserted by TYPO3 Install Tool.
$typo_db = 'haras_hnrf';	//  Modified or inserted by TYPO3 Install Tool.
*/
$mysql_host = $typo_db_host; //"localhost";
//$mysql_user = $typo_db_username; //"root";
$mysql_user = "root";
//$mysql_pasw = $typo_db_password; //"dlcube19230";
$mysql_pasw = "dlcube19230";
$mysql_db_orig   = "actualites_hn_temp";
$mysql_db_dest   = $typo_db; // "haras_hnrf";


$pid_root=3011;
$id_nvenregt=19230;
$link = mysql_pconnect($mysql_host, $mysql_user, $mysql_pasw);

$db = mysql_select_db ($mysql_db_dest);

echo "<PRE>";
echo "Moulinette d'importation des avis d'appel d'offres\n";
if (!$_REQUEST["sent"]) {
echo '
<form >
Emplact : <input type="texte" name="Emplact">
Date parution deb : <input type="texte" name="DateDeb">
Date parution fin : <input type="texte" name="Datefin">
Pid_dest : <input type="texte" name="pid">
<input type="hidden" name="sent" value="1">
<input type="submit">
</form>
';
}
else {
	echo "Ca mouline \n\n";
	$db = mysql_select_db ($mysql_db_dest);
	msq ("DELETE FROM tt_content WHERE pid =$_REQUEST[pid] AND tx_gsttopcontent_abstract='totoche'");
	$db = mysql_select_db ($mysql_db_orig);
	$reso=msq("SELECT * FROM demandes where Emplact='$_REQUEST[Emplact]' AND DatDebPar >= '".DateA($_REQUEST['DateDeb'])."' AND DatDebPar <= '".DateA($_REQUEST['Datefin'])."'" );
	while ($rwo=mysql_fetch_array($reso)) {
		$db = mysql_select_db ($mysql_db_dest);
		$tbdatap=explode("-",$rwo[DateDem]); // tableau année|mois|jur
		$tsdatap=mktime(0,0,0,$tbdatap[1],$tbdatap[2],$tbdatap[0]); // en ts unix
		$sqli="INSERT INTO tt_content 
		( pid  , tstamp     , crdate     , hidden , sorting , CType   , header , header_position , bodytext , image , imagewidth , imageorient , imagecaption , imagecols , imageborder , media                , layout , deleted , cols , records , pages , starttime , endtime , colPos , subheader , spaceBefore , spaceAfter , fe_group , header_link , imagecaption_position , image_link , image_zoom , image_noRows , image_effects , image_compression , header_layout , text_align , text_face , text_size , text_color , text_properties , menu_type , list_type , table_border , table_cellspacing , table_cellpadding , table_bgColor , select_key , sectionIndex , linkToTop , filelink_size , section_frame , date , splash_layout , multimedia , image_frames , recursive , imageheight , module_sys_dmail_category , rte_enabled , sys_language_uid , tx_impexp_origuid , tx_pagephpcontent_php_code , t3ver_oid , t3ver_id , t3ver_label , pi_flexform , l18n_parent , l18n_diffsource                               , tx_gsttopcontent_abstract )
		VALUES (
		  $_REQUEST[pid],$tsdatap,$tsdatap,0,514,'uploads','".addslashes($rwo[Titre])."(".DateF($rwo['DatDebPar'])."','','','',0,8,'',
		0,0,'$rwo[FichImg]',0,0,0,'','',0,0,0,'',0,0,0,'','','',0,0,0,0,3,'',0,0,0,0,0,'',0,0,0,0,'',1,0,1,0,0,0,'',0,0,0,0,0,0,0,'',0,0,'','',0,'a:26:{s:5:\"CType\";N;s:16:\"sys_language_uid\";N;s:6:\"colPos\";N;s:11:\"spaceBefore\";N;s:10:\"spaceAfter\";N;s:13:\"section_frame\";N;s:12:\"sectionIndex\";N;s:9:\"linkToTop\";N;s:6:\"header\";N;s:15:\"header_position\";N;s:13:\"header_layout\";N;s:11:\"header_link\";N;s:4:\"date\";N;s:5:\"media\";N;s:10:\"select_key\";N;s:6:\"layout\";N;s:13:\"table_bgColor\";N;s:12:\"table_border\";N;s:17:\"table_cellspacing\";N;s:17:\"table_cellpadding\";N;s:13:\"filelink_size\";N;s:12:\"imagecaption\";N;s:6:\"hidden\";N;s:9:\"starttime\";N;s:7:\"endtime\";N;s:8:\"fe_group\";N;}','totoche')";
		   msq($sqli);
		   $db = mysql_select_db ($mysql_db_orig);
		echo "AO : ".$rwo['Titre']." (".DateF($rwo['DatDebPar']).") fichier ".$rwo['FichImg']." \n";
	}

}


die("coucou");
// MAJ des rubriques =pages
if($_GET[action]==1) {
	msq("DELETE FROM pages where tx_impexp_origuid=$id_nvenregt");
	$db = mysql_select_db ($mysql_db_orig);
	
	$reqo="SELECT * from RUBRIQUES ORDER BY RUB_niv,RUB_prub,RUB_oa";
	$reso = msq ($reqo);
		while($row=mysql_fetch_array($reso))
		{
			echo $row[RUB_id]." | ".$row[RUB_oa]." | ".$row[RUB_titre]." | ".$row[RUB_desc]." | ".$row[RUB_niv]." | ".$row[RUB_prub]." | ".$row[RUB_pid]."<BR/>";
	
		if ($row[RUB_prub]==0) {
			$pid=$pid_root;}
		else  {
			$pid=RecupLib("RUBRIQUES","RUB_id","RUB_pid",$row[RUB_prub]);
			if ($pid==0) die("ya pas bon $row[RUB_titre] $row[RUB_prub] <BR./>");
			}
		$db = mysql_select_db ($mysql_db_dest);
		msq("INSERT INTO pages SET urltype=1,doktype=254,title='".addslashes($row[RUB_titre])."',sorting=256,pid=$pid,tx_impexp_origuid=$id_nvenregt");
		$lastuid=mysql_insert_id();
		echo (" ## dernier Id: $lastuid <BR/>");
		$db = mysql_select_db ($mysql_db_orig);
		msq("UPDATE RUBRIQUES SET RUB_pid=$lastuid WHERE RUB_id=".$row[RUB_id]);
		}
	}
elseif($_GET[action]==2) {
	msq("DELETE FROM tx_vm19hnlinks_urls where 1");
	$db = mysql_select_db ($mysql_db_orig);
	//die( "yes, ca va gazer");	
	$reqo="SELECT * from RUR ORDER BY RUR_url";
	$urlC=-1;
	$reso = msq ($reqo);
		while($row=mysql_fetch_array($reso))
		{
			if ($urlC==-1) {
				$urlC=$row[RUR_url];
			}
			if ($row[RUR_url]!=$urlC) { // on change...
				$resu=msq("select * from URL where URL_id=$urlC");
				$rowu=mysql_fetch_array($resu);
				echo "Url: ";
				print_r($rowu);
				echo "affect: ".implode(",",$tbrub)."<BR/>";
				$db = mysql_select_db ($mysql_db_dest);
				msq("INSERT INTO tx_vm19hnlinks_urls SET pid=".$tbrub[0].",tstamp=".time().",crdate=".time().",sorting=256,url_url='".$rowu[URL_url]."',url_title='".addslashes($rowu[URL_titre])."',url_desc='".addslashes($rowu[URL_desc])."',url_kwords='".addslashes($rowu[URL_kwords])."',url_mailwb='".$rowu[URL_mail_wb]."',url_othercateg='".implode(",",$tbrub)."'");
				$db = mysql_select_db ($mysql_db_orig);
				

				$urlC=$row[RUR_url];
				unset($tbrub);
			}
			$tbrub[]=RecupLib("RUBRIQUES","RUB_id","RUB_pid",$row[RUR_rub]) or die ("rubrique PID de $row[RUR_rub] inexistante");
	
				
/*							
		$db = mysql_select_db ($mysql_db_dest);
		msq("INSERT INTO pages SET urltype=1,doktype=254,title='".addslashes($row[RUB_titre])."',sorting=256,pid=$pid,tx_impexp_origuid=$id_nvenregt");
		$lastuid=mysql_insert_id();
		echo (" ## dernier Id: $lastuid <BR/>");
		$db = mysql_select_db ($mysql_db_orig);
		msq("UPDATE RUBRIQUES SET RUB_pid=$lastuid WHERE RUB_id=".$row[RUB_id]);
*/
		}
	}
?>