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
 * Plugin 'User links edition' for the 'vm19_userlinks' extension.
 *
 * @author	Administrateur TYPO3.7.1 <webtech@haras-nationaux.fr>
 */


require_once("typo3conf/ext/vm19_userlinks/pi1/class.tx_vm19userlinks_pi1.php");
//require_once("typo3conf/ext/vm19_toolbox/functions.php"); // ma boite �outil

class user_liens extends tx_vm19userlinks_pi1 {

	function display ($content,$conf) {
	   $this->pi_loadLL();
	   $this->conf=$conf;
	   $this->pi_setPiVarDefaults();
	   //debug($conf);
	
		$content = '
			<div id="menuOutils">
			     <p id="agenda"><img src="fileadmin/templates/images/icones/agenda.gif" align="middle"/><a href="../webcalendar/login.php?typo_l='.$GLOBALS["TSFE"]->fe_user->user["username"].'&typo_p='.base64_encode($GLOBALS["TSFE"]->fe_user->user["password"]).'">Agenda</a></p>
			     <p id="liens"><img src="fileadmin/templates/images/icones/liens.gif" align="middle"/><a href="?id=2733">Liens</a></p>
			     <p id="sig"><img src="fileadmin/templates/images/icones/sig.gif" align="middle"/><a href="#">SIG</a></p>
			     <p id="annuaire"><img src="fileadmin/templates/images/icones/annuaire.gif" align="middle"/><a href="?id=2706">Annuaire</a></p>
			</div>
			<div id="pratique">';




	   $uid_util=$GLOBALS["TSFE"]->fe_user->user["uid"];

            if ($uid_util) {

		//$this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$content.='<p>';
		$content.=$this->DispFav($this->conf['fixedLinksCats'] ? "category IN (".$this->conf['fixedLinksCats'].")" : " 1 ");
		$content.='</p>';
		
/*			$content.='			
				<tr height="10">
				<td align="center" valign="middle" height="10"><img src="./fileadmin/templates/IMAGES/filet_appli.gif" alt="" height="2" width="176" border="0"></td>
				</tr>'; */
			$tb_links=RecupLib("tx_vm19userlinks_ulinks","user_id","link_ids",$uid_util," AND deleted!=1");
			if ($tb_links!="") {
				$content.='</table>';
				$content.=str_replace("###TITRE###",$this->pi_getLL("liensu","Liens ut"),$this->entet_table);
				$content.=$this->DispFav("uid IN (".$tb_links.")");
				$content.="</td></tr>";
				}
			//$content.=$GLOBALS["TSFE"]->fe_user->user["username"];
		
		$content.="</table>";
//		$content.=time();
		return ($content);
            }   
	}
	
	function DispFav($whtbl) { // argument=
		$query = "SELECT * from tt_links where $whtbl AND deleted!=1 AND hidden!=1 order by category";
		$res = mysql(TYPO3_db,$query) or die ("req invalide : $query");
		if (mysql_num_rows($res)>0) {
			while ($row = mysql_fetch_array($res)) {
				if ($catc && ($catc!=$row['category'])) 
					$ret.='<tr height="10">
						<td align="center" valign="middle" height="10"><img src="./fileadmin/templates/IMAGES/filet_appli.gif" alt="" height="2" width="176" border="0"></td>
						</tr><tr><td>';
				$catc=$row['category'];
				$ret.='<a href="'.$row['url'].'" title="'.$row['title']." ".$row['note'].'">';
				$ret.=$this->srcimg($row['image'],$row['uid'],$row['title']).'</a>'; // pour avoir des retours �la ligne auto
				//if ($nb%3==0) $ret.="<br/>";
				//$ret.=($row['image'] ? '<img border="0" src="'.$this->pics_path.$row['image'].'">' : "->CLICK<-")."</a>&nbsp";
			}
		}
		return($ret);
	}

}

?>
