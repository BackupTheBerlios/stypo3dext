<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2004 Administrateur TYPO3.7.1 (webtech@haras-nationaux.fr)
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
 *
 * @author	Administrateur TYPO3.7.1 <webtech@haras-nationaux.fr>
 */


require_once("typo3conf/ext/vm19_news/pi1/class.tx_vm19news_pi1.php");

class user_actuspa extends tx_vm19news_pi1 {

	function display ($content,$conf) {
	   	$this->pi_loadLL();
	   	$this->conf=$conf;
	   	$this->pi_setPiVarDefaults();

		//$urlactus=($conf['pid_actus_pa']>0 ? "?id=".$conf['pid_actus_pa'] : "#") ;
		$urlactus=($conf['pid_actus_pa']>0 ? t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $this->pi_getPageLink($conf['pid_actus_pa']) : "#") ;
		
		$content .= '
		 <a href="'.$urlactus.'" class="boutonActus"></a><ul>';
	
		$limit=($conf['nb_accros_aff']>0 ? $conf['nb_accros_aff'] : 5) ;

 		$query = "SELECT * from tx_vm19news_news where paccdisp=1 AND deleted!=1 AND hidden!=1 AND (endtime=0 OR endtime > ".time().") AND (starttime=0 OR starttime < ".time().") order by tstamp DESC,sorting LIMIT $limit";
		$res = mysql(TYPO3_db,$query) or die ("req invalide : $query");
/*		$content.="linkVars:".$GLOBALS["TSFE"]->linkVars;
		$GLOBALS["TSFE"]->linkVars='vm19news_dirlink_uid';*/
		if (mysql_num_rows($res)>0) {
			while ($row = mysql_fetch_array($res)) {
				//$tbparams=Array('vm19news_dirlink_uid'=>trim($row['uid']),'cHash'=>md5($row['uid'])); // parametres supplémentaires à passer à l'url
				// le cHash est faux, donc ce ne sera pas mis en cache ni indexé mais on s'en tamponne
				// titre a enlacer par le lien
				$thetitle='<b>'.$row['title'].'</b><br/>'.t3tronqstrww($row['abstract'],$conf['nb_words']);
				// on rajoute l'ancre...
				$content.='<li>'.str_replace('"><b>','?vm19news_dirlink_uid='.trim($row['uid']).'&cHash='.md5($row['uid']).'#Anc'.$row['uid'].'"><b>', $this->pi_linkToPage($thetitle, $row['pid'],'',$tbparams)).'</li>';
				//$content.=t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $this->pi_getPageLink($GLOBALS['TSFE']->id,$params);
				
				// ancienne méthode sans utiliser le pi_geypagelink
				//$content.='<li><a href="?id='.$row['pid'].'&vm19news_dirlink_uid='.trim($row['uid']).'#Anc'.$row['uid'].'"><b>'.$row['title'].'</b><br/>'.t3tronqstrww($row['abstract'],$conf['nb_words']).'</a>'.'</li>';
			}
		}
	
		/*
		$content .= '
      			<li>la premi�e accroche <a href="#">Ut vin, 
        		oemata reddit, scire velim, chartis pretium quotus...</a> </li>
      			<li>Hos ediscit et hos arto stipata theatro spectat <a href="#">Ut vin, 
        		poemata reddit, scire velim, chartis pretium quotus...</a> </li>
		*/
    		
  		//ancienne méthode
  		$content.='<br/><li><a href="'.$this->pi_getPageLink($conf['pid_archives_actus']).'#debform"><b>'.$this->pi_getLL("reach_archives","consult archives").'</b></a></li>';
  		//$content.='<br/><li><a href="index.php?id='.$conf['pid_archives_actus'].'#debform"></a></li>';
  		$content .= '</ul>
  		';

	return ($content);
	}
	
}

?>
