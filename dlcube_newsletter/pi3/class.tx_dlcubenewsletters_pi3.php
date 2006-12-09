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
 * Plugin 'Newsletters - Press releases' for the 'dlcube_newsletters' extension.
 *
 * @author	DLCube <bnorrin@dlcube.com>
 */

require_once("typo3conf/ext/vm19_toolbox/functions.php");
require_once(PATH_tslib.'class.tslib_pibase.php');

class tx_dlcubenewsletters_pi3 extends tslib_pibase {
	var $prefixId = 'tx_dlcubenewsletters_pi3';		// Same as class name
	var $scriptRelPath = 'pi3/class.tx_dlcubenewsletters_pi3.php';	// Path to this script relative to the extension dir.
	var $extKey = 'dlcube_newsletters';	// The extension key.
	var $upload_img_folder="uploads/tx_vm19news/";
	
	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		switch((string)$conf['CMD'])	{
			case 'singleView':
				list($t) = explode(':',$this->cObj->currentRecord);
				$this->internal['currentTable']=$t;
				$this->internal['currentRow']=$this->cObj->data;
				return $this->pi_wrapInBaseClass($this->singleView($content,$conf));
			break;
			default:
				if (strstr($this->cObj->currentRecord,'tt_content'))	{
					$conf['pidList'] = $this->cObj->data['pages'];
					$conf['recursive'] = $this->cObj->data['recursive'];
				}
				return $this->pi_wrapInBaseClass($this->listView($content,$conf));
			break;
		}
	}
	
	/**
	 * [Put your description here]
	 */
	function listView($content,$conf)	{
		$this->conf=$conf;		// Setting the TypoScript passed to this function in $this->conf
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();		// Loading the LOCAL_LANG values
		$this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$lConf = $this->conf['listView.'];	// Local settings for the listView function
	
		if ($this->piVars['showUid'])	{	// If a single element should be displayed:
			$this->internal['currentTable'] = 'tx_dlcubenewsletters_presse_ids';
			$this->internal['currentRow'] = $this->pi_getRecord('tx_dlcubenewsletters_presse_ids',$this->piVars['showUid']);
	
			$content = $this->singleView($content,$conf);
			return $content;
		} else {
			$items=array(
				'1'=> $this->pi_getLL('list_mode_1','Mode 1'),
				'2'=> $this->pi_getLL('list_mode_2','Mode 2'),
				'3'=> $this->pi_getLL('list_mode_3','Mode 3'),
			);
			if (!isset($this->piVars['pointer']))	$this->piVars['pointer']=0;
			if (!isset($this->piVars['mode']))	$this->piVars['mode']=1;
	
				// Initializing the query parameters:
			list($this->internal['orderBy'],$this->internal['descFlag']) = explode(':',$this->piVars['sort']);
			$this->internal['results_at_a_time']=t3lib_div::intInRange($lConf['results_at_a_time'],0,1000,3);		// Number of results to show in a listing.
			$this->internal['maxPages']=t3lib_div::intInRange($lConf['maxPages'],0,1000,2);;		// The maximum number of "pages" in the browse-box: "Page 1", "Page 2", etc.
			$this->internal['searchFieldList']='title';
			$this->internal['orderByList']='uid,title';
	
				// Get number of records:
			$res = $this->pi_exec_query('tx_dlcubenewsletters_presse_ids',1);
			list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
	
				// Make listing query, pass query to SQL database:
			$res = $this->pi_exec_query('tx_dlcubenewsletters_presse_ids');
			$this->internal['currentTable'] = 'tx_dlcubenewsletters_presse_ids';
	
				// Put the whole list together:
			$fullTable='';	// Clear var;
		#	$fullTable.=t3lib_div::view_array($this->piVars);	// DEBUG: Output the content of $this->piVars for debug purposes. REMEMBER to comment out the IP-lock in the debug() function in t3lib/config_default.php if nothing happens when you un-comment this line!
	
				// Adds the mode selector.
			//$fullTable.=$this->pi_list_modeSelector($items);
	
				// Adds the whole list table
			$fullTable.=$this->makelist($res);
	
				// Adds the search box:
			//$fullTable.=$this->pi_list_searchBox();
	
				// Adds the result browser:
			//$fullTable.=$this->pi_list_browseresults();
	
				// Returns the content from the plugin.
			return $fullTable;
		}
	}
	/**
	 * [Put your description here]
	 */
	function makelist($res)	{
		$items=Array();
			// Make list table rows
		while($this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$items[]=$this->makeListItem();
		}
	
		$out = '<div'.$this->pi_classParam('listrow').'>
			'.implode(chr(10),$items).'
			</div>';
		return $out;
	}
	
	/**
	 * [Put your description here]
	 */
	function makeListItem()	{
		$i = 0;
		$j = 0;

		// est-ce qu'il y a bien des valeurs ?
 		if($this->getFieldContent('news_uid')) {

			$NewsUids = explode(",", $this->getFieldContent('news_uid') );
			
			// Construction du Where
			$AddWhere = '`uid` IN(';
			for($i=0;$i<count($NewsUids);$i++) 
			{
				if($i > O) $AddWhere .= ',';
				$AddWhere .= $NewsUids[$i];
			}
			$AddWhere .= ')';

			// requ�e
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_vm19docsbase_docs',$AddWhere,'','`uid` DESC',"");
			if (mysql_error())	debug(array(mysql_error()));
			$this->internal["currentTable"] = "tx_vm19docsbase_docs";

			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	
			{

				// mise en forme diff�ente pour la premiere news
				if($j == 0) {
					
					$img = 'uploads/tx_vm19docsbase/'.$row['imagette'];

					$out .= '<tr>';
					$out .= '<td align="left" valign="top" style="padding: 5px;border-bottom:solid 1px #97C0E6;">';
					if($row['imagette']) $out .= '  <span id="images"><img src="'.$img.'" width="70"></span>';
					$out .= '  <img src="fileadmin/templates/newsletters/images/puce_bleu.gif" align="absmiddle"><a href="uploads/tx_vm19docsbase/'.$row['document'].'" id="communique">'.$row['title'].'; '.substr($row['abstract'],0,110).'...</a>';
					$out .= '    </td>';
					$out .= '  </tr>'."\n";
				} else {
					$out .= '<tr>';
					$out .= '<td align="left" valign="top" style="padding: 5px;">';
					$out .= ' <img src="fileadmin/templates/newsletters/images/puce_bleu.gif" align="absmiddle"><a href="uploads/tx_vm19docsbase/'.$row['document'].'" id="communique">'.$row['title'].'; '.substr($row['abstract'],0,180).'...</a>';
					$out .= '    </td>';
					$out .= '  </tr>'."\n";
				}
				$j++;
			}
		}
		else
		{
			$out = 'Aucunes news sp&aecute;ifi&aecute; !';
		}

		return $out;
	}
	/**
	 * [Put your description here]
	 */
	function singleView($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
	
			// This sets the title of the page for use in indexed search results:
		if ($this->internal['currentRow']['title'])	$GLOBALS['TSFE']->indexedDocTitle=$this->internal['currentRow']['title'];
	
		$content='<div'.$this->pi_classParam('singleView').'>
			<H2>Record "'.$this->internal['currentRow']['uid'].'" from table "'.$this->internal['currentTable'].'":</H2>
				<p'.$this->pi_classParam("singleViewField-title").'><strong>'.$this->getFieldHeader('title').':</strong> '.$this->getFieldContent('title').'</p>
				<p'.$this->pi_classParam("singleViewField-news-uid").'><strong>'.$this->getFieldHeader('news_uid').':</strong> '.$this->getFieldContent('news_uid').'</p>
		<p>'.$this->pi_list_linkSingle($this->pi_getLL('back','Back'),0).'</p></div>'.
		$this->pi_getEditPanel();
	
		return $content;
	}
	/**
	 * [Put your description here]
	 */
	function getFieldContent($fN)	{
		switch($fN) {
			case 'uid':
				return $this->pi_list_linkSingle($this->internal['currentRow'][$fN],$this->internal['currentRow']['uid'],1);	// The "1" means that the display of single items is CACHED! Set to zero to disable caching.
			break;
			case "title":
					// This will wrap the title in a link.
				return $this->pi_list_linkSingle($this->internal['currentRow']['title'],$this->internal['currentRow']['uid'],1);
			break;
			default:
				return $this->internal['currentRow'][$fN];
			break;
		}
	}
	/**
	 * [Put your description here]
	 */
	function getFieldHeader($fN)	{
		switch($fN) {
			case "title":
				return $this->pi_getLL('listFieldHeader_title','<em>title</em>');
			break;
			default:
				return $this->pi_getLL('listFieldHeader_'.$fN,'['.$fN.']');
			break;
		}
	}
	
	/**
	 * [Put your description here]
	 */
	function getFieldHeader_sortLink($fN)	{
		return $this->pi_linkTP_keepPIvars($this->getFieldHeader($fN),array('sort'=>$fN.':'.($this->internal['descFlag']?0:1)));
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dlcube_newsletters/pi3/class.tx_dlcubenewsletters_pi3.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dlcube_newsletters/pi3/class.tx_dlcubenewsletters_pi3.php']);
}

?>
