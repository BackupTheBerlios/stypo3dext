<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003 Vincent (admin) (artec.vm@nerim.net)
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
 * Plugin 'Key numbers' for the 'vm19_key_numbers' extension.
 *
 * @author	Vincent (admin) <artec.vm@nerim.net>
 */


require_once(PATH_tslib."class.tslib_pibase.php");

require_once("typo3conf/ext/vm19_toolbox/functions.php");

// VOIR tslib/class.tslib_pibase.php, qui contient ttes les méthodes
class tx_vm19keynumbers_pi1 extends tslib_pibase {
	var $prefixId = "tx_vm19keynumbers_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_vm19keynumbers_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "vm19_key_numbers";	// The extension key.
	var $upload_doc_folder="uploads/tx_vm19keynumbers";


	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		switch((string)$conf["CMD"])	{
			case "singleView":
				list($t) = explode(":",$this->cObj->currentRecord);
				$this->internal["currentTable"]=$t;
				$this->internal["currentRow"]=$this->cObj->data;
				return $this->pi_wrapInBaseClass($this->singleView($content,$conf));
			break;
			default:
				if (strstr($this->cObj->currentRecord,"tt_content"))	{
					$conf["pidList"] = $this->cObj->data["pages"];
					$conf["recursive"] = $this->cObj->data["recursive"];
				}
				return $this->pi_wrapInBaseClass($this->listView($content,$conf));
			break;
		}
	}
	/**
	 * [Put your description here]
	 */

/* Rabbio des chiffres clés */
function listView($content,$conf)	{
		//global $TYPO3_CONF_VARS;
        //$GLOBALS["TSFE"]->set_no_cache();

		// affiche le code langue, si rien défaut donc en anglais ...
		//debug ($GLOBALS["TSFE"]->config["config"]["language"]);

		// *************************************
		// *** getting configuration values:
		// *************************************

		$this->conf=$conf;
//		debug ($this->conf);
        $this->pi_loadLL();
		$this->pi_setPiVarDefaults();

        ///$this->params = unserialize($TYPO3_CONF_VARS["EXT"]["extConf"]["vm19_key_numbers"]);


		$lConf = $this->conf["listView."];	// Local settings for the listView function

		///$lConf["results_at_a_time"]=2;
		///$lConf["maxPages"]=4;

		///$fullTable="<b><U>on deboggue..</U></b><br><br>";

		if ($this->piVars["showUid"])	{	// If a single element should be displayed:
			$this->internal["currentTable"] = "tx_vm19keynumbers_numbers";
			$this->internal["currentRow"] = $this->pi_getRecord("tx_vm19keynumbers_numbers",$this->piVars["showUid"]);
			$content = $this->singleView($content,$conf);
			return $content;
		} else {
			/* Sert pour le s"lecteur de Mode, on s'en fout
			$items=array(
				"1"=> $this->pi_getLL("list_mode_1","Mode 1"),
				"2"=> $this->pi_getLL("list_mode_2","Mode 2"),
				"3"=> $this->pi_getLL("list_mode_3","Mode 3"),
			); */
			if (!isset($this->piVars["pointer"]))	$this->piVars["pointer"]=0;
			if (!isset($this->piVars["mode"]))	$this->piVars["mode"]=1;

			//		$fullTable="";	// Clear var;




				// Initializing the query parameters:
			list($this->internal["orderBy"],$this->internal["descFlag"]) = explode(":",$this->piVars["sort"]);
			$this->internal["results_at_a_time"]=t3lib_div::intInRange($lConf["results_at_a_time"],0,1000,4);		// Number of results to show in a listing.
			$this->internal["maxPages"]=t3lib_div::intInRange($lConf["maxPages"],0,1000,2);;		// The maximum number of "pages" in the browse-box: "Page 1", "Page 2", etc.
			$this->internal["searchFieldList"]="title,k_value,comment,update_period,update_seq";
			$this->internal["orderByList"]="sorting,title,k_value";

				// Get number of records:
			$query = $this->pi_list_query("tx_vm19keynumbers_numbers",1);
			$res = mysql(TYPO3_db,$query);
			if (mysql_error())	debug(array(mysql_error(),$query));


			list($this->internal["res_count"]) = mysql_fetch_row($res);

				// Make listing query, pass query to MySQL:
			//$query = $this->pi_list_query("tx_vm19news_news",0,"","","","ORDER BY tstamp DESC");
			$this->internal["orderBy"]="sorting";
			$query = $this->pi_list_query("tx_vm19keynumbers_numbers");
			$res = mysql(TYPO3_db,$query);
			if (mysql_error())	debug(array(mysql_error(),$query));
			$this->internal["currentTable"] = "tx_vm19keynumbers_numbers";

				// Put the whole list together:

		#	$fullTable.=t3lib_div::view_array($this->piVars);	// DEBUG: Output the content of $this->piVars for debug purposes. REMEMBER to comment out the IP-lock in the debug() function in t3lib/config_default.php if nothing happens when you un-comment this line!

			$content.='<H2><img src="'.$this->conf["extCurDir"].'picto_chiffres.gif" align="middle">&nbsp;&nbsp;Chiffres clés</H2>';
			$fullTable=$content;	// Clear var;

			// Adds the mode selector.
			// COMMENT VM$fullTable.=$this->pi_list_modeSelector($items);

			// Adds the whole list table
			$fullTable.=$this->pi_list_makelist($res);

				// Adds the search box:
			// COMMENT VM$fullTable.=$this->pi_list_searchBox();

			// Adds the result browser, seulement si nbre de réponses > nbre de reponses max par page
			//$fullTable.=$this->pi_list_browseresults();
			if ($this->internal["res_count"]>$this->internal["results_at_a_time"]) $fullTable.=$this->pi_list_browseresults();


				// Returns the content from the plugin.
			return $fullTable;
		}
	}
	/**
	 * [Put your description here]
	 */
	function pi_list_row($c)	{
		$editPanel = $this->pi_getEditPanel();
		if ($editPanel)	$editPanel="<TD>".$editPanel."</TD>";

		/* ORGINAL: return '<tr'.($c%2 ? $this->pi_classParam("listrow-odd") : "").'>
				<td><P>'.$this->getFieldContent("uid").'</P></td>
				<td valign="top"><P>'.$this->getFieldContent("title").'</P></td>
				<td valign="top"><P>'.$this->getFieldContent("k_value").'</P></td>
				<td valign="top"><P>'.$this->getFieldContent("unity").'</P></td>
				<td valign="top"><P>'.$this->getFieldContent("update_type").'</P></td>
				<td valign="top"><P>'.$this->getFieldContent("update_period").'</P></td>
				'.$editPanel.'
			</tr>'; */
		$unity_uid=$this->getFieldContent("unity");
		$unity_icon=txRecupLib("tx_vm19keynumbers_unities","uid","icon",$unity_uid);
								if (file_exists($this->upload_doc_folder.'/'.$this->getFieldContent($fN))) {
						$valret= '<img src="'.$this->upload_doc_folder.'/'.$this->getFieldContent($fN).'" align="left" border="0">';
					}

		if ($unity_icon!=false && $unity_icon!="" && file_exists($this->upload_doc_folder.'/'.$unity_icon)) {
			$unity_icon='<img src="'.$this->upload_doc_folder.'/'.$unity_icon.'">';
			}
		else $unity_icon="&nbsp;";

		$u_code=txRecupLib("tx_vm19keynumbers_unities","uid","unity_code",$unity_uid);
		//debug($u_code);
		$k_value=$this->getFieldContent("k_value");
		/*
		Description du format
		unite|nb_dec|dec_sep|mil_sep
		*/
		if (strstr($u_code,"|")) { // existe une séparation format#unité
			$f=explode("|",$u_code);
			$unite=$f[0];
			$nbdec=$f[1];
			$dec_sep=($f[2]!='' ? $f[2] : ',');
			$mil_sep=($f[3]!='' ? $f[3] : '¤');
		}
		else {
			$unite=$u_code;
			$nbdec=1;
			$dec_sep=",";
			$mil_sep="¤";
			}

		$k_value=number_format($k_value,$nbdec,$dec_sep,$mil_sep)."&nbsp;".$unite;
		$k_value=str_replace("¤","&nbsp;",$k_value);
		$line_comment= $this->getFieldContent("comment")!="" ? '<tr><td>&nbsp;</td><td colspan="2" class="comment">'.$this->getFieldContent("comment").'</td></tr>' : ""; 
		
		return '<tr'.($c%2 ? $this->pi_classParam("listrow-odd") : "").'>
			<td width="25">'.$unity_icon.'</td>
			<td colspan="2"><span class="k_value">'.$k_value.'
			<span class="title">'.$this->getFieldContent("title").'</td>'
			.$line_comment			
			.$editPanel.'
			</tr>';
	}
	/**
	 * [ENTETE DU TABLEAU]
	 En fait rien
	 */
	function pi_list_header()	{
	/* PLUS DE CLASSEMENT */
	/*
	return '<tr'.$this->pi_classParam("listrow-header").'>
			<td><P>'.$this->getFieldHeader_sortLink("title").'</P></td>
			<td><P>'.$this->getFieldHeader_sortLink("k_value").'</P></td>
			<td><P>'.$this->getFieldHeader_sortLink("comment").'</P></td>
			</tr>'; */
		return ("");
	}
	/**
	 * [Put your description here]
	 */
	function getFieldContent($fN)	{
		switch($fN) {
			case "uid":
				return $this->pi_list_linkSingle($this->internal["currentRow"][$fN],$this->internal["currentRow"]["uid"],1);	// The "1" means that the display of single items is CACHED! Set to zero to disable caching.
			break;
			/*case "title":
					// This will wrap the title in a link.
				return $this->pi_list_linkSingle($this->internal["currentRow"]["title"],$this->internal["currentRow"]["uid"],1);
			break;*/
			default:
				return $this->internal["currentRow"][$fN];
			break;
		}
	}
	/**
	 * [Put your description here]
	 */
	function getFieldHeader($fN)	{
		switch($fN) {
			case "title":
				return $this->pi_getLL("listFieldHeader_title","<em>title</em>");
			break;
			default:
				return $this->pi_getLL("listFieldHeader_".$fN,"[".$fN."]");
			break;
		}
	}
	
	/**
	 * [Put your description here]
	 */
	function getFieldHeader_sortLink($fN)	{
		return $this->pi_linkTP_keepPIvars($this->getFieldHeader($fN),array("sort"=>$fN.":".($this->internal["descFlag"]?0:1)));
	}




	/**
	 * [PAS UTILISE]
	 */
	function singleView($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

			// This sets the title of the page for use in indexed search results:
		if ($this->internal["currentRow"]["title"])	$GLOBALS["TSFE"]->indexedDocTitle=$this->internal["currentRow"]["title"];

		$content.='<DIV'.$this->pi_classParam("singleView").'>
			<H2>Record "'.$this->internal["currentRow"]["uid"].'" from table "'.$this->internal["currentTable"].'":</H2>
			<table>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><P>'.$this->getFieldHeader("title").'</P></td>
					<td valign="top"><P>'.$this->getFieldContent("title").'</P></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><P>'.$this->getFieldHeader("k_value").'</P></td>
					<td valign="top"><P>'.$this->getFieldContent("k_value").'</P></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><P>'.$this->getFieldHeader("unity").'</P></td>
					<td valign="top"><P>'.$this->getFieldContent("unity").'</P></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><P>'.$this->getFieldHeader("comment").'</P></td>
					<td valign="top"><P>'.$this->getFieldContent("comment").'</P></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><P>'.$this->getFieldHeader("update_type").'</P></td>
					<td valign="top"><P>'.$this->getFieldContent("update_type").'</P></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><P>'.$this->getFieldHeader("update_period").'</P></td>
					<td valign="top"><P>'.$this->getFieldContent("update_period").'</P></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><P>'.$this->getFieldHeader("update_seq").'</P></td>
					<td valign="top"><P>'.$this->getFieldContent("update_seq").'</P></td>
				</tr>
				<tr>
					<td nowrap'.$this->pi_classParam("singleView-HCell").'><P>Last updated:</P></td>
					<td valign="top"><P>'.date("d-m-Y H:i",$this->internal["currentRow"]["tstamp"]).'</P></td>
				</tr>
				<tr>
					<td nowrap'.$this->pi_classParam("singleView-HCell").'><P>Created:</P></td>
					<td valign="top"><P>'.date("d-m-Y H:i",$this->internal["currentRow"]["crdate"]).'</P></td>
				</tr>
			</table>
		<P>'.$this->pi_list_linkSingle("Back",0).'</P></DIV>'.
		$this->pi_getEditPanel();

		return $content;
	}

} // fin classe


if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/vm19_key_numbers/pi1/class.tx_vm19keynumbers_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/vm19_key_numbers/pi1/class.tx_vm19keynumbers_pi1.php"]);
}

?>
