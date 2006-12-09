<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 DLCube (bnorrin@dlcube.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
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
 * Module 'Newsletters' for the 'dlcube_newsletters' extension.
 *
 * @author	DLCube <bnorrin@dlcube.com>
 */



	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ("conf.php");
require ($BACK_PATH."init.php");
require ($BACK_PATH."template.php");
$LANG->includeLLFile("EXT:dlcube_newsletters/mod1/locallang.php");
#include ("locallang.php");
require_once (PATH_t3lib."class.t3lib_scbase.php");
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

class tx_dlcubenewsletters_module1 extends t3lib_SCbase {
	var $pageinfo;
	var $from = 'Newsletters@Haras-nationaux.fr';
	var $sujet;

	/**
	 *
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();

		/*
		if (t3lib_div::_GP("clear_all_cache"))	{
			$this->include_once[]=PATH_t3lib."class.t3lib_tcemain.php";
		}
		*/
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 */
	function menuConfig()	{
		global $LANG;
		$this->MOD_MENU = Array (
			"function" => Array (
				"1" => $LANG->getLL("function1"),
				"2" => $LANG->getLL("function2"),
				"3" => $LANG->getLL("function3"),
			)
		);
		parent::menuConfig();
	}

		// If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	/**
	 * Main function of the module. Write the content to $this->content
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user["admin"] && !$this->id))	{

				// Draw the header.
			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="" method="POST">';

				// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
			';
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = '.intval($this->id).';
				</script>
			';

			$headerSection = $this->doc->getHeader("pages",$this->pageinfo,$this->pageinfo["_thePath"])."<br>".$LANG->sL("LLL:EXT:lang/locallang_core.php:labels.path").": ".t3lib_div::fixed_lgd_pre($this->pageinfo["_thePath"],50);

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			


			// Render content:
			if($this->pageinfo["doktype"] != 1) {
				$this->content .= "Merci de selectionner une page.";
			} else {
				$this->content.=$this->doc->section("",$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,"SET[function]",$this->MOD_SETTINGS["function"],$this->MOD_MENU["function"])));
				$this->content.=$this->doc->divider(5);
				$this->moduleContent();
			}

			$this->content.=$this->doc->spacer(10);
		} else {
				// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 */
	function printContent()	{

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 */
	function moduleContent()	{

		switch((string)$this->MOD_SETTINGS["function"])	{
			case 1:
				$content  = "Cette extension vous permez d'envoyer une page comme Newsletter à l'ensemble des personnes incrites sur le site.<br><br>";
				$content .= '<b>'.$this->NumRegisters()." personnes</b> sont inscrites au service de Newsletters.<br>";
				$this->content.=$this->doc->section("Newsletter",$content,0,1);
			break;
			case 2:
				$content="Dans cette partie vous pouvez vous envoyer une newsletter d'essai afin de voir si le rendu est correct.<HR><BR>";
				$content .= $this->FormTestMail();
				$content .= $this->FormTestMail_Valid();
				$this->content.=$this->doc->section("Envoi d'une version d'essai",$content,0,1);
			break;
			case 3:
				$content="Envoyez cette newsletter à l'ensemble des inscrits<HR><br>";
				$content .= $this->MassMail();
				$content .= $this->MassMail_Valid();
				$this->content.=$this->doc->section("Envoi Massif",$content,0,1);
			break;
		}
	}


	function FormTestMail() {
		$out .= '<form method="post" action="'.$_SERVER['REQUEST_URI'].'" method="post">';
		$out .= 'E-mails (séparés par des virgules) : <input type="texte" name="emails" size="80"><br><input type="submit" value="Envoyer">';
		$out .= '</form>';
		return $out;
	}

	function FormTestMail_Valid() {
		global $_POST;

		if($_POST) {
			$newsletter = $this->PrepareNewsletter();
			$newsletter['sujet'] = '[ESSAI] '.$newsletter['sujet'];
			$newsletter['to'] = $_POST['emails'];
			$output = $this->SendEmails($newsletter);

			if($output) {
				return "<br><br><b>Envoie réussi</b>";
			} else {
				return "<br><br>Envoie échoué !!</b>";
			}
		}
	}

	function MassMail() {
		$out = 'Etes vous prêt à envoyer cette Newsletter ? <a href="'.$_SERVER['REQUEST_URI'].'&valid=true" onclick="javascript:return confirm(\'Envoyer maintenant ?\')">Cliquez ici</a><br>';
		$out .= 'Cet page sera envoyée à '.$this->NumRegisters().' personnes.<br>';
		return $out;
	}

	function MassMail_Valid() {
		global $_GET;

		if($_GET['valid'] == true) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
	                	'email',         					// SELECT ...
	                	' tx_fabformmail_abonne',     		// FROM ...
	                	'newsletter=1',    					// WHERE...
	                	'',            						// GROUP BY...
	                	'uid DESC',    						// ORDER BY...
	                	''            						// LIMIT to 10 rows, starting with number 5 (MySQL compat.)
	            		);

			$newsletter = $this->PrepareNewsletter();

			while( $data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ) {
					$newsletter['to'] = $data['email'];
					//$output .= $this->SendEmails($newsletter);
			}
			
			$out .= '<br><hr><br>';
			if(ereg('0', $output)) {
				$out .= 'Résultat : Une erreur s\'est produite au cours de l\'envoi !!!!';
			} else {
				$out .= 'Résultat : Envoi réussi.';
			}

			$out .= '<br><br><b>ATTENTION !! Ne pas revenir en arrière, merci d\'utiliser les boutons disponibles !</b>';
		}
		
		return $out;
	}


	function PrepareNewsletter() {
		global $_GET, $TYPO3_CONF_VARS;

		$message_html = file_get_contents($TYPO3_CONF_VARS["SYS"]["address"]."index.php?id=".$_GET['id']);
		$message_text = file_get_contents($TYPO3_CONF_VARS["SYS"]["address"]."index.php?id=".$_GET['id'].'&type=99');

		$newsletter['limite'] = "_parties_".md5 (uniqid (rand()));

		$newsletter['entete'] = "Reply-to: ".$this->from."\n";
		$newsletter['entete'] .= "From:".$this->from."\n";
		$newsletter['entete'] .= "Date: ".date("l j F Y, G:i")."\n";
		$newsletter['entete'] .= "MIME-Version: 1.0\n";
		$newsletter['entete'] .= "Content-Type: multipart/alternative;\n";
		$newsletter['entete'] .= " boundary=\"----=$limite\"\n\n";

		//Le message en texte simple pour les navigateurs qui n'acceptent pas le HTML
		$newsletter['texte_simple'] = "This is a multi-part message in MIME format.\n";
		$newsletter['texte_simple'] .= "Ceci est un message est au format MIME.\n";
		$newsletter['texte_simple'] .= "------=$limite\n";
		$newsletter['texte_simple'] .= "Content-Type: text/plain; charset=\"iso-8859-1\"\r\n";
		$newsletter['texte_simple'] .= "Content-Transfer-Encoding: 8bit\n\n";
		$newsletter['texte_simple'] .=  $message_text;
		$newsletter['texte_simple'] .= "\n\n";

		//le message en html original
		$newsletter['texte_html'] = "------=$limite\n";
		$newsletter['texte_html'] .= "Content-Type: text/html; charset=\"iso-8859-1\"\r\n";
		$newsletter['texte_html'] .= "Content-Transfer-Encoding: 8bit\n\n";
		$newsletter['texte_html'] .= $message_html;
		$newsletter['texte_html'] .= "\n\n\n------=$limite--\n";
		$newsletter['texte_html'] = str_replace('"uploads/', '"'.$TYPO3_CONF_VARS["SYS"]["address"].'uploads/', $newsletter['texte_html']);
		$newsletter['texte_html'] = str_replace('"fileadmin/', '"'.$TYPO3_CONF_VARS["SYS"]["address"].'fileadmin/', $newsletter['texte_html']);
		$newsletter['texte_html'] = str_replace('"index.php', '"'.$TYPO3_CONF_VARS["SYS"]["address"].'index.php', $newsletter['texte_html']);

		$newsletter['sujet'] = $this->pageinfo["title"];

		return $newsletter;
	}


	function SendEmails($newsletter) {
		//echo 'mail("'.$newsletter['to'].'", "'.$newsletter['sujet'].'", "texte", "")'."\n";
		return mail($newsletter['to'], $newsletter['sujet'], $newsletter['texte_simple'].$newsletter['texte_html'], $newsletter['entete']);
	}

	function NumRegisters() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
	                	'COUNT(*)',         				// SELECT ...
	                	'tx_fabformmail_abonne',     		// FROM ...
	                	'newsletter=1',    					// WHERE...
	                	'',            						// GROUP BY...
	                	'uid DESC',    						// ORDER BY...
	                	'1'            						// LIMIT to 10 rows, starting with number 5 (MySQL compat.)
	            		);
	
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		return $row['COUNT(*)'];
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dlcube_newsletters/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dlcube_newsletters/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_dlcubenewsletters_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>