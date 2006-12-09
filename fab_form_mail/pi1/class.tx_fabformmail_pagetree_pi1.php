<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 1999-2003 Kasper Skårhøj (kasper@typo3.com)
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
*  A copy is found in the textfile GPL.txt and important notices to the license 
*  from the author is found in LICENSE.txt distributed with these scripts.
*
* 
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/** 
 * generate a page-tree. OBS: remember $clause
 *
 * @author	Kasper Skårhøj <kasper@typo3.com>
 */

class t3lib_pageTree {
	var $makeHTML=1;

	var $clause=" AND NOT deleted AND NOT hidden AND (doktype=1 OR doktype=2) ";
	var $backPath;		
	var $db;
	var $fieldArray = Array("uid","title","doktype","php_tree_stop");
	var $defaultList = "uid,pid,tstamp,sorting,deleted,perms_userid,perms_groupid,perms_user,perms_group,perms_everybody,crdate,cruser_id";
	var $setRecs = 0;
	
		// internal
	var $tree = Array();
	var $recs = Array();
	var $ids = Array();
	var $ids_hierarchy = array();
	
	var $specUIDmap=array();

	function init($clause)	{
		if (t3lib_extMgm::isLoaded("cms"))	{
			$this->fieldArray=array_merge($this->fieldArray,array("hidden","starttime","endtime","fe_group","module","extendToSubpages"));
		}

		$this->backPath = $GLOBALS["BACK_PATH"];
		if ($clause)	{
			$this->clause.=" ".$clause;
		}
	}
	function reset()	{
			// internal
		$this->tree = Array();
		$this->recs = Array();
		$this->ids = Array();
		$this->ids_hierarchy = array();
	}
	function getTree($pid, $depth=999, $depthData="",$blankLineCode="")	{
		// Generates a list of Page-uid's that corresponds to the tables in the tree. This list should ideally include all records in the pages-table.
		$this->buffer_idH=array();
		$query = "SELECT ".implode($this->fieldArray,",")." FROM pages WHERE pid = '$pid'".$this->clause." ORDER BY sorting";
		$res = mysql(TYPO3_db, $query);

		$depth=intval($depth);
		$HTML="";
		$a=0;
		$c=mysql_num_rows($res);
		if (mysql_error())	{
			echo mysql_error();
			debug($query);
		}
		while ($row = mysql_fetch_assoc($res))	{
//echo $row["title"]."<br>";
			$a++;
			$newID =$row["uid"];
			$this->tree[]=array();		// Reserve space.
			end($this->tree);
			$treeKey = key($this->tree);	// Get the key for this space
			$LN = ($a==$c)?"blank":"line";

			if ($this->setRecs)	{
				$this->recs[$row["uid"]] = $row;
			}
			$this->ids[]=$idH[$row["uid"]]["uid"]=$row["uid"];
			$this->ids_hierarchy[$depth][]=$row["uid"];

			if ($depth>1 && $this->expandNext($newID) && !$row["php_tree_stop"])	{
				$nextCount=$this->getTree($newID, $depth-1, $this->makeHTML?$depthData.'<IMG src="'.$this->backPath.'t3lib/gfx/ol/'.$LN.'.gif" border="0" width="18" height="16" "align="top">':'', $blankLineCode.",".$LN);
				if (count($this->buffer_idH))	$idH[$row["uid"]]["subrow"]=$this->buffer_idH;
				$exp=1;
			} else {
				$nextCount=$this->getCount($newID);
				$exp=0;
			}

				// Set HTML-icons, if any:
			if ($this->makeHTML)	{	
				$HTML=$depthData.$this->PMicon($row,$a,$c,$nextCount,$exp);
				$HTML.=$this->wrapStop($this->wrapIcon('<IMG src="typo3/gfx/i/pages.gif" border="0" width="18" height="16" "align="top">',$row),$row);
			}
			$this->tree[$treeKey] = Array("row"=>$row, "HTML"=>$HTML, "invertedDepth"=>$depth, "blankLineCode"=>$blankLineCode);
		}
		$this->buffer_idH=$idH;
		return $this->tree;
		//return $c;
	}
	function wrapStop($str,$row)	{
		if ($row["php_tree_stop"])	{
			$str.='<font color=red>+ </font>';
		}
		return $str;
	}
	function getFolderTree($files_path, $depth=999, $depthData="")	{
			// This generates the directory tree
		$dirs = t3lib_div::get_dirs($files_path);
//		debug($dirs);
		$c=0;
		if (is_array($dirs))	{
			$depth=intval($depth);
			$HTML="";
			$a=0;
			$c=count($dirs);
			sort($dirs);

			while (list($key,$val)= each($dirs))	{
				$a++;
				$this->tree[]=array();		// Reserve space.
				end($this->tree);
				$treeKey = key($this->tree);	// Get the key for this space
				$LN = ($a==$c)?"blank":"line";

				$val = ereg_replace("^\./","",$val);
				$title = $val;
				$path = $files_path.$val."/";
				$webpath=t3lib_BEfunc::getPathType_web_nonweb($path);

				$md5_uid = md5($path);
				$specUID=hexdec(substr($md5_uid,0,6));
				$this->specUIDmap[$specUID]=$path;
				$row=array();
				$row["path"]=$path;
				$row["uid"]=$specUID;
				$row["title"]=$title;

				if ($depth>1 && $this->expandNext($specUID))	{
					$nextCount=$this->getFolderTree($path, $depth-1, $this->makeHTML?$depthData.'<IMG src="'.$this->backPath.'t3lib/gfx/ol/'.$LN.'.gif" border="0" width="18" height="16" "align="top">':'');
					$exp=1;
				} else {
					$nextCount=$this->getFolderCount($path);
					$exp=0;
				}
	
					// Set HTML-icons, if any:
				if ($this->makeHTML)	{	
					$HTML=$depthData.$this->PMicon($row,$a,$c,$nextCount,$exp);

					$icon = 'gfx/i/_icon_'.$webpath.'folders.gif';
					if ($val=="_temp_")	{
						$icon = 'gfx/i/sysf.gif';
						$row["title"]="<b>TEMP</b>";
					}
					if ($val=="_recycler_")	{
						$icon = 'gfx/i/recycler.gif';
						$row["title"]="<b>RECYCLER</b>";
					}
					$HTML.=$this->wrapIcon('<IMG src="'.$this->backPath.$icon.'">',$row);
				}
				$this->tree[$treeKey] = Array("row"=>$row, "HTML"=>$HTML);
			}
		}
		return $c;
	}
	function getFolderCount($files_path)	{
			// This generates the directory tree
		$dirs = t3lib_div::get_dirs($files_path);
		$c=0;
		if (is_array($dirs))	{
			$c=count($dirs);
		}
		return $c;
	}
	function expandNext($id)	{
		return 1;
	}
	function getCount($pid)	{
		$query = "SELECT count(*) FROM pages WHERE pid = '$pid'".$this->clause;
		$res = mysql(TYPO3_db, $query);
		$row=mysql_fetch_row($res);
		return $row[0];
	}
	function wrapIcon($icon,$row)	{
		return $icon;
	}
	function PMicon($row,$a,$c,$nextCount,$exp)	{
		$PM = "join";
		$BTM = ($a==$c)?"bottom":"";
		$icon = '<IMG src="'.$this->backPath.'t3lib/gfx/ol/'.$PM.$BTM.'.gif" border="0" width="18" height="16" "align="top">';
		return $icon;
	}
	function addField($field,$noCheck=0)	{
		global $TCA;
		if ($noCheck || is_array($TCA["pages"]["columns"][$field]) || t3lib_div::inList($this->defaultList,$field))	{
			$this->fieldArray[]=$field;
		}
	}
}


if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["t3lib/class.t3lib_pagetree.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["t3lib/class.t3lib_pagetree.php"]);
}

?>
