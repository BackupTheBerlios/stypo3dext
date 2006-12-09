<?
/* moulinette d'exportation de la la base de donnц╘es rц╗glementation vers l'extension typo

*/
if (!$_GET[action]) die ('Entrer <a href="MOULI.php?action=FR">?action=FR</a> ou <a href="MOULI.php?action=EUR">?action=EUR</a> pour MAJ des pages/rubriques FR ou EU, ?action=2 pour MAJ la table des textes, ?action=3 pour MAJ la table des natures');
include_once("fonctions.php");
/* Chez DLCube
$mysql_host = "localhost";
$mysql_user = "root";
$mysql_pasw = "dlcube19230";
$mysql_db_orig   = "haras_reglementation_tmp";
$mysql_db_dest   = "haras_hnrf";
$pid_root[FR]=3421;
$pid_root[EUR]=3422;
$pid_root_nat=3423;
*/

// aux HN
$mysql_user = "root";
$mysql_pasw = "";
$mysql_db_orig   = "reglementation";
$mysql_db_dest   = "hng_test";
$pid_root[FR]=3217;
$pid_root[EUR]=3218;
$pid_root_nat=3216;

$id_nvenregt[FR]=19231;
$id_nvenregt[EUR]=19232;

$link = mysql_pconnect($mysql_host, $mysql_user, $mysql_pasw);

$db = mysql_select_db ($mysql_db_dest);
echo "<PRE>";
// MAJ des rubriques =pages
if($_GET[action]=='FR' || $_GET[action]=='EUR') {
	msq("DELETE FROM pages where tx_impexp_origuid=".$id_nvenregt[$_GET[action]]);
	$db = mysql_select_db ($mysql_db_orig);
	
	$reqo="SELECT * from RUBRIQUES WHERE RUB_orig='".$_GET[action]."' ORDER BY RUB_niv,RUB_prub,RUB_oa ";
	echo $reqo;
	$rubC=-1;
	$reso = msq ($reqo);
		while($row=mysql_fetch_array($reso)) {
			
			if ($rubC!=$row[RUB_prub]) {
				$classt=200;
				$rubC=$row[RUB_prub];
			} else $classt+=10; 
		
			echo $row[RUB_id]." | ".$row[RUB_oa]." | ".$row[RUB_titre]." | ".$row[RUB_desc]." | ".$row[RUB_niv]." | ".$row[RUB_prub]." | ".$row[RUB_pid]."<BR/>";
		
			if ($row[RUB_prub]==0) {
				$pid=$pid_root[$_GET[action]];}
			else  {
				$pid=RecupLib("RUBRIQUES","RUB_id","RUB_pid",$row[RUB_prub]);
				if ($pid==0) die("ya pas bon $row[RUB_titre] $row[RUB_prub] <BR./>");
				}
			$db = mysql_select_db ($mysql_db_dest);
			msq("INSERT INTO pages SET urltype=1,doktype=254,title='".addslashes($row[RUB_titre])."',sorting=$classt,pid=$pid,tx_impexp_origuid=".$id_nvenregt[$_GET[action]]);
			$lastuid=mysql_insert_id();
			echo (" ## dernier Id: $lastuid <BR/>");
			$db = mysql_select_db ($mysql_db_orig);
			msq("UPDATE RUBRIQUES SET RUB_pid=$lastuid WHERE RUB_id=".$row[RUB_id]);
		}
	}
elseif($_GET[action]==2) {
// algo avec racarbo: a priori on ne s'en sert plus . On indiquait pour les textes l'id du texte "en haut" de l'arboresecence.
// maintenant ceux en haut de l'arbo sont ceux qui n'ont pas de texte parent

	msq("DELETE FROM tx_vm19hnreglementation_textes where 1"); // efface tout
	$db = mysql_select_db ($mysql_db_orig);
	//die( "yes, ca va gazer");	
	$reqo="SELECT * from RTR ORDER BY RTR_rub, RTR_classt";
	$rubC=-1;
	$reso = msq ($reqo);
		while($row=mysql_fetch_array($reso))
			{
			if ($rubC!=$row[RTR_rub]) {
				$classt=200;
				$rubC=$row[RTR_rub];
				} else $classt+=10; 
			
			/*
	
	number tinytext NOT NULL,
	publication tinytext NOT NULL,
	desc_2bf7363fc2 text NOT NULL,
	fich_joint blob NOT NULL,
	kwords tinytext NOT NULL,
	orig tinytext NOT NULL,
	other_pages blob NOT NULL,
	parent_text blob NOT NULL,
	rtt_attach_type varchar(4) DEFAULT '' NOT NULL,
			*/
			$db = mysql_select_db ($mysql_db_orig);
			$pid=RecupLib("RUBRIQUES","RUB_id","RUB_pid",$row[RTR_rub]); // recup le pid
			$reqt="SELECT * from TEXTE WHERE TEXTE_id=".$row[RTR_texte];
			$rest = msq ($reqt);
			if (mysql_num_rows($rest)>0) {
				$rwt=mysql_fetch_array($rest);
				echo "texte id=$row[RTR_texte] (clsst=$classt): \n";
				$rqrtt="SELECT * from RTT where RTT_text_fils= ".$row[RTR_texte];
				$rprtt=msq($rqrtt);
				if (mysql_num_rows($rprtt)>1) echo "Attention, texte id=$row[RTR_texte] fils de PLUSIEURS autres...";
				if (mysql_num_rows($rprtt)==0)   echo "Attention, texte id=$row[RTR_texte] fils de PUTE...";
				$parent_text=""; // reinit
				$rtt_attach="";
				$tbdatap=explode("-",$rwt[TEXTE_dat_approb]); // tableau année|mois|jur
				$tsdatap=mktime(0,0,0,$tbdatap[1],$tbdatap[2],$tbdatap[0]); // en ts unix
				
				$first=true;
				
				while (($rwtt=mysql_fetch_array($rprtt)) || $first) { // boucle sur les textes parents, si pas on passe au moins une fois	
					print_r($rwtt);
					$first=false;
					$parent_text=$rwtt[RTT_text_par]; // peut etre vide...
					//$rtt_attach= calcul ici du type d\'attachement;
					$typatt=substr($rwtt[RTT_typ_rat],0,5); // on fait ca pour pas etre emmerdé par les accents en fin de mot
					echo "Attachement : $typatt \n";
					switch ($typatt) {
						case "modif":
						$rtt_attach="MOD";
						break;
						
						case "abrog":
						$rtt_attach="ABR";
						break;
						
						case "rempl":
						$rtt_attach="REPL";
						break;
						
						case "annul":
						$rtt_attach="ANN";
						break;
						
						case "trans":
						$rtt_attach="TRP";
						break;
						
						case "appli":
						$rtt_attach="APP";
						break;
					}
					
					$db = mysql_select_db ($mysql_db_dest);
					$sqladd="INSERT INTO tx_vm19hnreglementation_textes SET 
						pid=$pid,
						tstamp=".time().",
						crdate=".time().",
						hidden=".($rwt[TEXTE_etat]=="Actif" ? 0 : 1).",
						sorting=$classt,
						cruser_id=43, 
						url='".addslashes($rwt[TEXTE_url])."',
						title='".addslashes($rwt[TEXTE_titre])."',
						nature=".RecupLib("tx_vm19hnreglementation_nature","code","uid",$rwt[TEXTE_nature])." ,
						dat_approb=$tsdatap,
						number='$rwt[TEXTE_numero]',
						publication='$rwt[TEXTE_publication]',
						desc_2bf7363fc2='".addslashes($rwt[TEXTE_desc])."',
						fich_joint='$rwt[TEXTE_fich_joint]',
						kwords='$rwt[TEXTE_kwords]',
						orig='$rwt[TEXTE_orig]',
						parent_text='$parent_text',
						rtt_attach_type='$rtt_attach'	
						";
					msq($sqladd);
					$nid=mysql_insert_id();
					$db = mysql_select_db ($mysql_db_orig);
					msq("UPDATE TEXTE set TEXTE_nid=$nid WHERE TEXTE_id=$row[RTR_texte]");
					//$db = mysql_select_db ($mysql_db_dest);
				} // fin boucle sur les parents: s'il y en a plusieurs, on "duplique" les enregistrements...
			} else {
				echo "Erreur : !! texte id n╟ $row[RTR_texte] existant dans RTR mais pas dans TEXTE !! \n";
			}
					
/*							
		$db = mysql_select_db ($mysql_db_dest);
		msq("INSERT INTO pages SET urltype=1,doktype=254,title='".addslashes($row[RUB_titre])."',sorting=256,pid=$pid,tx_impexp_origuid=$id_nvenregt");
		$lastuid=mysql_insert_id();
		echo (" ## dernier Id: $lastuid <BR/>");
		$db = mysql_select_db ($mysql_db_orig);
		msq("UPDATE RUBRIQUES SET RUB_pid=$lastuid WHERE RUB_id=".$row[RUB_id]);
*/
		} // fin boucle sur la table RTR ie rubriques...
		
	// maintenant on MAJ les id parents (si diff ed vide) avec les nvlles Id
	$db = mysql_select_db ($mysql_db_dest);
	$rqmajnid="SELECT uid, parent_text FROM tx_vm19hnreglementation_textes where parent_text != ''";
	$rp=msq($rqmajnid);
	while ($rwmajnid=mysql_fetch_array($rp)) {
		$db = mysql_select_db ($mysql_db_orig);
		$nidp=RecupLib("TEXTE","TEXTE_id","TEXTE_nid",$rwmajnid[parent_text]);
		$db = mysql_select_db ($mysql_db_dest);
		msq("UPDATE tx_vm19hnreglementation_textes SET parent_text=$nidp WHERE uid=$rwmajnid[uid]");
		}
		
	} // fin action =2
	
elseif ( $_GET[action]==3 ) {
	//msq("DELETE FROM pages where tx_impexp_origuid=".$id_nvenregt[$_GET[action]]);
	// title, level, code
	$db = mysql_select_db ($mysql_db_orig);
	
	$reqo="SELECT * from NATURE ORDER BY NAT_niveau";
	echo $reqo;
	$reso = msq ($reqo);
		while($row=mysql_fetch_array($reso)) {
			$db = mysql_select_db ($mysql_db_dest);
			msq ("INSERT INTO tx_vm19hnreglementation_nature SET pid=$pid_root_nat, title='$row[NAT_lbnature]', level=$row[NAT_niveau], code='$row[NAT_conature]'");
		}
	}
?>