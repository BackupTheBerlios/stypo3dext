<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003 Vincent (admin) (vmaury@localhost)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Plugin 'Whats new' for the 'vm19_watsniou' extension.
 *
 * @author	Vincent (admin) <vmaury@localhost>

 ****************************************************
 * This script is designed to be run directly on the command line
 * or to be put in a cron
 *
 * Its goal is to update the temporary table tx_vm19whatsnew with the recent
 * updates or changes of pages or other content elements of a site
!!!
because this function is to be called directly by a script, the MySql connection parameters have to:
- be uncomented above
- included from typo3conf/localconf.php: be careful to the path !

 */

include ("../../localconf.php");
require ("fonctions.php");

/* definition de l'entete du mail envoy�*/
$Mg=2; // marge globale
$indent=10; // indentation des titres
$idT=10; // indent du texte / titres

$admail_expe="info@haras-nationaux.fr <Portail Internet des Haras nationaux>";

$headef='<html>
<head>
<title>Alerte du portail Internet des Haras nationaux</title>
<style type="text/css">
body   { background: white; font-family: Arial, Helvetica, Geneva, Swiss, SunSans-Regular; font-size: 11px; line-height: 13px; word-spacing: 0.4px; letter-spacing: 0.4px ;}
table   { font-family: Arial, Helvetica, Geneva, Swiss, SunSans-Regular; font-size: 11px;  word-spacing: 0.4px; letter-spacing: 0.4px }
a:link {text-decoration:none}
a:visited {text-decoration:none}
a:hover { color: #C1131E; text-decoration: none }

P { line-height: 14px }

.Tit1 { margin-left:'.($Mg+0*$indent).'px; margin-bottom:5px; margin-top:10px; color: #C1131E; font-weight: bold; font-size: 15px; line-height: 16px;  display:block}
.Tit2 { margin-left:'.($Mg+1*$indent).'px; margin-bottom:4px; margin-top:7px; color: #F18C13; font-weight: bold; font-size: 14px; line-height: 15px;  display:block}
.Tit3 { margin-left:'.($Mg+2*$indent).'px; margin-bottom:3px; margin-top:5px; color: #F18C13; font-weight: bold; font-size: 12px; line-height: 13px; display:block}
.Tit4 { margin-left:'.($Mg+3*$indent).'px; margin-bottom:3px;  margin-top:4px; color: #F18C13; font-weight: bold; font-size: 12px; line-height: 13px; display:block}
.Tit5 { margin-left:'.($Mg+4*$indent).'px; margin-bottom:3px;  margin-top:4px; color: #F18C13; font-weight: bold; font-size: 11px; line-height: 12px; display:block}
.Tit6 { margin-left:'.($Mg+5*$indent).'px; margin-bottom:2px;  margin-top:3px; color: #F18C13; font-weight: normal; font-size: 11px; line-height: 12px; display:block}
.typcont {font-size: 12px;  text-decoration: underline; display:block}
.titre {font-weight: bold; font-size: 12px; font-style:italic ; color: #3E3E3E}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
Bonjour  !<BR/>
<BR/>
Vous trouverez ci-dessous toutes les &#233;volutions du contenu des rubriques du portail internet des Haras nationaux que vous avez choisies pour votre alerte d\'information.<br/>
Pour en savoir plus sur une information, un article, cliquez sur le titre de la page correspondante et vous serez directement redirig&#233; au bon endroit dans le portail.<br/><br/>';
/* fin de definition de l'entete du mail envoy
*/



// 35 days before, just longer than 1 month...
$lasttime=time()-35*24*60*60;
$WhereComm=" AND hidden = 0 AND deleted = 0 ";
$WhereSStime=" AND (starttime=0 OR starttime <".time().") AND (endtime=0 OR endtime>".time().") AND tstamp>".$lasttime;
// CONSTANTES DIVERSES
$tableName="tx_vm19watsniou";
$UrlRoot="http://www.haras-nationaux.fr/portail/index.php?id=";
$IdGAMail=2627; // ide la page de gestion des abonnements mail
// nom des champs de formulaire (douvent etre identiques �ceux utilis� dans l'extension fab_formmail
$tbFormnames['fname']="tx_fabformmail_pi1fname";
$tbFormnames['chpemail']="tx_fabformmail_pi1[DATA][email]";

$cvlibtc['tt_content']="Contenu normal";
$cvlibtc['vm19docsbase']="Base documentaire";
//$cvlibtc['vm19keynumbers']="Chiffre(s) cl&#233;s)";
$cvlibtc['vm19news']="Actualit&#233;";
$cvlibtc['vm19_hnlinks']="Base de donn&eacute;e des liens";
$cvlibtc['vm19_hn_reglementation']="Base de donn&eacute;e reglementation";

typomysqlconnect ($typo_db_host,$typo_db_username,$typo_db_password,$typo_db);

// constructs a table that contains all root pages
$req="SELECT pid FROM sys_template WHERE hidden = 0 AND root = 1 AND deleted = 0";
$res=msq($req);
if (mysql_num_rows($res)==0) die ("This site seems to contain no root page");
while ($rep=mysql_fetch_row($res)) $pidroot[]=$rep[0];

// debug root pid tables
foreach ($pidroot as $pidtemp) echo "pid des pages racines: $pidtemp <BR>\n";
/*
Debug fonction RecupArbo
RecupArbo(1519,$Tbarbid,$Tbarbstr);
dbgvar("Tbarbid");
dbgvar("Tbarbstr");
die(); */
//WHERE typcontent!='tt_content' OR tstamp<".$lasttime
/* on est oblig�de bricoler car la table tt_content ne contient pas de champ crdate (seult tstamp) */
/* en fait on rajoute le champ 
commme le champ crdate n'existait pas avant dans la table tt_content, il faut donc le MAJ avec la date de MAJ  */
msq ("UPDATE tt_content SET crdate=tstamp WHERE crdate=0");
msq ("DELETE FROM $tableName WHERE 1");
//msq ("DELETE FROM $tableName WHERE tstamp<".$lasttime);
//msq ("DELETE FROM $tableName WHERE typcontent!='tt_content' OR tstamp<".$lasttime);
// we search the contents...
//MajWatsNiou("tt_content","tt_content","header",false);
// on a rajout�un champ crdate �la table tt_content
MajWatsNiou("tt_content","tt_content","header");
MajWatsNiou("tx_vm19docsbase_docs","vm19docsbase");
//MajWatsNiou("tx_vm19keynumbers_numbers","vm19keynumbers");
MajWatsNiou("tx_vm19news_news","vm19news");
MajWatsNiou("tx_vm19hnreglementation_textes","vm19_hn_reglementation");
MajWatsNiou("tx_vm19hnlinks_urls","vm19_hnlinks","url_title",false);


EnvMailWN(false); // le parametre �true fait affiche le test mais n'envoie pas les mails
// envoi des mails...


// fonction de mise �jour de la table
function MajWatsNiou($exttablename,$typcontent,$NmChpTitle="title",$timestartstop=true) {
	global $WhereComm, $WhereSStime, $tableName,$hiddenP,$deletedP;
	// calcul de la page où sont les liens
	if ($typcontent=="vm19_hnlinks") { // pour les liens on met l'url direct
		$idpl=RecupLib("tt_content","list_type","pid","vm19_hnlinks_pi1",""," Ctype='list'");
		$tabarbo=RecupArbo($idpl);
		$tabarbid=",";
		if (is_array($tabarbo)) {
			foreach ($tabarbo as $id=>$hed) $tabarbid.=substr($id,2).",";
		}
	}
	$req="SELECT * FROM $exttablename WHERE 1 $WhereComm ".($timestartstop ? $WhereSStime : "");
	$res=msq($req);
	while ($rep=mysql_fetch_array($res)) {
		if (($rep[$NmChpTitle]!="" || rtrim(strip_tags($rep[bodytext]))!="") && ($exttablename!="tt_content" || $rep[CType]!="list")) { /* si titre non vide
		ou que le corps du tt_content est non vide (pour �iminer les plugins et les lignes de s�aration html) 
		ou que le tt_content n'est pas un plugin (CType!=list)
		la fonction tronqstrww coupe intelligemment la chaine sans couper les mots*/
			if ($rep[$NmChpTitle]=="") { 
				$rep[$NmChpTitle]=tronqstrww(strip_tags($rep[bodytext]),44);}
			else  $rep[$NmChpTitle]=tronqstrww(strip_tags($rep[$NmChpTitle]),44); // des fois le titre aussi d�asse
			
		 	$hiddenP=0; // on se sert de variables globales
			$deletedP=0; // des que dans la fonction reeentrante ci-dessous on tombe sur une page hidden, on mets ces flages
			if ($typcontent!="vm19_hnlinks") { // recup de la page ou est le new
				$tabarbo=RecupArbo($rep[pid]);
				$tabarbid=",";
				if (is_array($tabarbo)) {
					foreach ($tabarbo as $id=>$hed) $tabarbid.=substr($id,2).",";
				}
			} 
			// pour les chiffres cl�, c'est carrement plus balaise
			if ($typcontent=="vm19keynumbers") {
				$u_code=RecupLib("tx_vm19keynumbers_unities","uid","unity_code",$rep[unity]);

				$f=explode("|",$u_code);
				if (strstr($u_code,"|")) { // existe une s�aration format#unit�
					$f=explode("|",$u_code);
					$unite=$f[0];
					$nbdec=$f[1];
					$dec_sep=($f[2]!='' ? $f[2] : ',');
					$mil_sep=($f[3]!='' ? $f[3] : '');
				}
				else {
					$unite=$u_code;
					$nbdec=1;
					$dec_sep=",";
					$mil_sep="";
					}
				$k_value=number_format($rep[k_value],$nbdec,$dec_sep,$mil_sep)."&nbsp;".$unite;
	
				$title=addslashes($rep[k_value]." ".$f[0]." ".$rep[$NmChpTitle]);
				}
			elseif ($typcontent=="vm19_hnlinks") {
				$title=addslashes($rep[$NmChpTitle]."|".$rep['url_url']);
			}
			else $title=addslashes($rep[$NmChpTitle]);
			
			if ($title=="" || $title ==" " || $title==" [...]" || $title=="[...]") $title="Sans titre"; 

			$SET="sorting=69,pid=".($tabarbo[0]>0 ? $tabarbo[0] : $rep[pid] ).", tstamp=$rep[tstamp],crdate=$rep[crdate],".
			($timestartstop ? "starttime=".$rep['starttime'].",endtime=".$rep['endtime']."," : "").
			"hidden=$hiddenP,deleted=$deletedP,title='$title',tabidarbo='".$tabarbid."',
			tabstrarbo='".addslashes(serialize($tabarbo))."', typcontent='$typcontent'";

			msq ("INSERT INTO $tableName SET uid=$rep[uid], ".$SET);
		} // fin si titre non vide
	}
}

// fonction (r�ntrante) qui revoie un tableau associatif
function RecupArbo($PageId,$profd=0) {
global $pidroot, $hiddenP, $deletedP;
$profd++;
$profstop=15;
if ($profd>$profstop) die ("la profondeur de la page $PageId est superieure �$profstop ! on stoppe l'exploration");
$repIP=msq("SELECT uid,pid,doktype,hidden,deleted,title from pages where uid=".$PageId);
if (mysql_num_rows($repIP)==0) return "";
// on r�up�e ts les champs de la page
$rwIP=mysql_fetch_array($repIP);
$pidsup=$rwIP[pid];
//echo "pidsup=".$pidsup ."\r\n";
//echo "doktype=".RecupLib("pages", "uid", "doktype", $PageId)."\r\n";
$PageDoktype=$rwIP[doktype];
$Tbarbstr=false;
if ($PageDoktype==1 || $PageDoktype==2 || in_array($PageId,$pidroot)) { // si vraie page (normal ou avanc�, pas raccourci, pas dossier syst), 
				// ou que c'est la racine
	// on met la chaine Id devant le id pour que le tableau soit vraiement de hachage; sinon lorsque qu'on le merge, il est consid��comme num�ique et les indices
	// sont m�ang�
	$Tbarbstr['id'.$PageId]=$rwIP[title];
}
// on positionne les variables globales hidden et deleted si besoin
if ($rwIP[hidden]==1) $hiddenP=1;
if ($rwIP[deleted]==1) $deletedP=1;

//if (!(in_array($pidsup,$pidroot)) && $pidsup!=$PageId) { // si niveau du dessus n'est pas la racine
if (!(in_array($PageId,$pidroot)) && $pidsup!=$PageId) { // si niveau courant n'est pas la racine
	$FutTbarbstr=RecupArbo($pidsup,$profd);
	if (is_array($FutTbarbstr) && is_array($Tbarbstr)) {
		$Tbarbstr=array_merge($FutTbarbstr,$Tbarbstr);
		}
	elseif (is_array($FutTbarbstr)) {
		$Tbarbstr=$FutTbarbstr;
	}
}
return ($Tbarbstr);
//else echo "la page au dessus de $PageId est la racine \r\n";
}

function EnvMailWN($test=false) {
	global $WhereComm,$headef,$indent,$Mg,$idT,$UrlRoot,$cvlibtc,$tbFormnames,$IdGAMail,$admail_expe;
	if ($test) {
		echo "<H1>Mode test !! </H1>";
		echo "En mode test, on visualise les mails envoyes, mais la table n'est pas mise a jour <BR><BR>";
		} 
	echo "-----------------------------------------------------------------------<br>\n";
	echo "Envois des mails d'alerte du ".date("d/m/Y",time())."<br>\n";
	echo "liste des adresses envoyees: <br>\n";
	$rep=msq ("SELECT * from tx_fabformmail_abonne where 1 $WhereComm");
	while ($rwm=mysql_fetch_array($rep)) {
		unset($tabposP);
		$nbal=0;
		$nbmail=0;
		$typcp="";
		$corpsAl="";
		$rwm[lastsentdate]=($rwm[lastsentdate]>0 ? $rwm[lastsentdate] :0);
		if ($rwm[typefrequence]==28) $rwm[typefrequence]=30.4375; // nbre de jours moyens dans un mois=365.25/12
		$WET=" AND ".($rwm[typeetat]==0 ? "crdate" : "tstamp")." >".$rwm[lastsentdate];
		if (time()>=($rwm[lastsentdate]+($rwm[typefrequence]*24*3600))) { // il est temps d'envoyer
			$tabid=explode(',',$rwm[uidpages]);
			foreach ($tabid as $idp) {
				$repWN=msq ("SELECT * from tx_vm19watsniou WHERE 1 $WhereComm AND typcontent IN ('".str_replace(",","','",$rwm[typeextension])."') AND tabidarbo LIKE '%,$idp,%' $WET ORDER BY tabidarbo,typcontent DESC, tstamp");
				if (mysql_num_rows($repWN) >0 ) { // YA des news dans la rub s�ectionn�
					while ($rwWN=mysql_fetch_array($repWN)) {
						$tabposC=unserialize($rwWN[tabstrarbo]);
						$p=0;
						foreach ($tabposC as $idp=>$page) {
							$idp=substr($idp,2);
							if ($page!=$tabposP[$p]) {
								$corpsAl.='<a class="Tit'.($p+1).'" href="'.$UrlRoot.$idp.'">'.$page.'</a>';
								$typcp="";
							}
							$tabposP[$p]=$page;
							$p++;
						}
						//$tabposP=$tabposC;
						$corpsAl.='<div style="margin-left:'.($Mg + $idT + (($p-1)*$indent)).'px">';
						if ($rwWN[typcontent]!=$typcp) $corpsAl.='<span class="typcont">'.$cvlibtc[$rwWN[typcontent]].'</span>';
						$typcp=$rwWN['typcontent'];
						if ($typcp!="vm19_hnlinks") {
							$href=$UrlRoot.$rwWN['pid'];
							$title=$rwWN['title'];
						} else {
							$tabisa=explode("|",$rwWN['title']);
							$href=strstr($tabisa[1],"http://") ? $tabisa[1] : "http://".$tabisa[1];
							$title=$tabisa[0];
						}
						$corpsAl.='<a class="titre" href="'.$href.'">'.$title.'</a>,&nbsp; '.($rwWN['tstamp']==$rwWN['crdate'] ? " cr&#233;&#233; le " : " modifi&#233; le ").' '.date("d/m/Y",$rwWN['tstamp']);
						$corpsAl.="</DIV>\n";
						$nbal++;
					} // fin boucle sur les news de la rubrique
				} //// fin si pas de news ds la rub
			} // fin boucle sur les rubriques de l'abonn�
			if ($nbal>0) { // s'il y a des alarmes
				$nbmail++;
				$head=$headef.'Vous recevrez votre prochaine alerte le '.date("d/m/Y",time()+$rwm[typefrequence]*24*3600)."<br>";
				$head.='<FORM action="'.$UrlRoot.$rwm[pid].'" name="fname'.$nbmail.'" method="POST">
              <INPUT type="hidden" name="'.$tbFormnames['chpemail'].'" value="'.$rwm['email'].'">'."\n";
				//$head.='<br/>Pour g&#233;rer votre abonnement, enregistr&#233; pour l\'adresse <b>'.$rwm[email].'</b>, cliquez <a class="titre" href="javascript:document.fname'.$nbmail.'.submit()">-> ICI <- </a><br><br>';
				$head.='<br/>Pour g&#233;rer votre abonnement, enregistr&#233; pour l\'adresse <b>'.$rwm[email].'</b>, cliquez <a class="titre" href="'.$IdGAMail.'">-> ICI <- </a><br><br>';
				$head.='Bonne lecture ! <br><br>';

				$foot='<br/><br/>Vous recevrez votre prochaine alerte le '.date("d/m/Y",time()+$rwm[typefrequence]*24*3600)."<br>";
			
				$foot.='<br/>Pour g&#233;rer votre abonnement, enregistr&#233; pour l\'adresse <b>'.$rwm[email].'</b>, cliquez <a class="titre" href="'.$IdGAMail.'">-> ICI <- </a><br><br>';
				if ($test) {
					echo $head.$corpsAl.$foot;
					echo "<hr>";
					}
				else {
					echo $rwm[email]."  <br>\n";
					mail_html($rwm[email], "Alerte mail du portail internet des Haras nationaux" , $head.$corpsAl.$foot, $admail_expe);
					msq ("UPDATE tx_fabformmail_abonne SET lastsentdate=".time()." WHERE uid=".$rwm[uid]);
				}
			} 
 			//
		} // fin si temps est tel qu'il faut envoyer un mail
	} // fin boucle sur abonn�
	echo "Fin des envois du ".date("d/m/Y",time())."<br>\n";
} // fin fonction

// connection et s�ection �entuelle de la base
function typomysqlconnect ($db_host,$db_username,$db_password,$db) {
     mysql_connect($db_host,$db_username,$db_password) or die ("Impossible de se connecter au serveur $db_host avec le user $db_username, passwd: $db_password* ");
	 mysql_select_db($db) or die ("Impossible d'ouvrir la base de donn�s $db.");
	 }
// function tronqstrww ($strac,$long=50,$strsuite=" [...]") {
// 	if (strlen($strac)<=$long) return $strac;
// 	return strtok(wordwrap($strac,$long,"\n"),"\n").$strsuite;
// }


