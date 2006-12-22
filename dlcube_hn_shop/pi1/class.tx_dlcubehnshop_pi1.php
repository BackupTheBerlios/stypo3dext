<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2004 Vincent (admin, celui ? la pioche) (webtech@haras-nationaux.fr)
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
 * Plugin BOUTIQUE
 *
 * @author	Vincent MAURY <vmauy@dlcube.com>
 */
require_once(PATH_tslib."class.tslib_pibase.php");

class tx_dlcubehnshop_pi1 extends tslib_pibase {
	var $prefixId = "tx_dlcubehnshop_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_dlcubehnshop_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "dlcube_hn_shop";	// The extension key.
	var $upload_doc_folder="uploads/tx_dlcubehnshop";
	
	//var $searchConfig="WSDatastore"; 
	
	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
		$content="Salut ducon";
		
		$this->uid = substr(strstr($GLOBALS[GLOBALS][TSFE]->currentRecord,":"),1);

		switch((string)$conf["CMD"])	{
			case "singleView":
				list($t) = explode(":",$this->cObj->currentRecord);
				$this->internal["currentTable"]=$t;
				$this->internal["currentRow"]=$this->cObj->data;
				return $this->pi_wrapInBaseClass($this->singleView($content));
			break;
			default:
				if (strstr($this->cObj->currentRecord,"tt_content"))	{
					$conf["pidList"] = $this->cObj->data["pages"];
					$conf["recursive"] = $this->cObj->data["recursive"];
					//debug($this->cObj->data);
				}
				return $this->pi_wrapInBaseClass($this->listView($content));
			break;
			}
	} // fin methode main
	

	/**
	 * [Put your description here]
	 */
	function listView($content)	{
		
		$this->uid = substr(strstr($GLOBALS[GLOBALS][TSFE]->currentRecord,":"),1);

		//debug($this->uid);
		// $this->uid correspond a l'uid du tt_content contenant le plugin

		$lConf = $this->conf["listView."];	// Local settings for the listView function
		//print_r($lConf);
		/* !-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-
		  modif: on ne se met et en mode singleView que si showUid!=" ET SURTOUT que si l'uid tt_content pass� correspond
		// au courant
		// voir aussi utilisation du dernier argument optionel de la la fonction
		pi_list_linkSingle qui permet de passer un tableau de hachage contenant autant d'arguments suppl�entaires que l'on veut
 		!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-
		*/
		if ($this->piVars["showUid"] && $this->piVars["ttc_uid"]==$this->uid)	{	// If a single element should be displayed:
			$this->internal["currentTable"] = "tx_dlcubehnshop_articles";
			$this->internal["currentRow"] = $this->pi_getRecord("tx_dlcubehnshop_articles",$this->piVars["showUid"]);

			$content = $this->singleView($content,$conf);
			return $content;
		} else  {
			
			if (!isset($this->piVars["pointer"]))	$this->piVars["pointer"]=0;
			if (!isset($this->piVars["mode"]))	$this->piVars["mode"]=1;

				// Initializing the query parameters:
			list($this->internal["orderBy"],$this->internal["descFlag"]) = explode(":",$this->piVars["sort"]);
			// si aucun classement specifie, par d�aut on classe par date d�roissante
			if ($this->internal["orderBy"]=="") {
				$this->internal["orderBy"]="title";
				//$this->internal["descFlag"]=($this->mod_vignette ? "" : "1");
				}
			//debug ($this->piVars["sort"]);
			$this->internal["results_at_a_time"]=t3lib_div::intInRange( $lConf["results_at_a_time"],0,1000,3);		// Number of results to show in a listing.
			$this->internal["maxPages"]=t3lib_div::intInRange($lConf["maxPages"],0,1000,3);		// The maximum number of "pages" in the browse-box: "Page 1", "Page 2", etc.
			$this->internal["orderByList"]="title,crdate";


			$query = $this->pi_list_query("tx_dlcubehnshop_articles",1);

			$res = mysql(TYPO3_db,$query);
			if (mysql_error())	debug(array(mysql_error(),$query));
			
			list($this->internal["res_count"]) = mysql_fetch_row($res);
			if ($this->internal["res_count"]>0) {
				// Make listing query, pass query to MySQL:
				//$query = $this->pi_list_query("tx_dlcubehnshop_articles",0,"","","","ORDER BY crdate DESC, title ASC");
				$query = $this->pi_list_query("tx_dlcubehnshop_articles");
				$res = mysql(TYPO3_db,$query);
				if (mysql_error())	debug(array(mysql_error(),$query));
				$this->internal["currentTable"] = "tx_dlcubehnshop_articles";
				$fullTable.=$this->pi_list_makelist($res);

				// Adds the result browser, seulement s'il y a assez de r�ultats
				if ($this->internal["res_count"]>$this->internal["results_at_a_time"]) $fullTable.=$this->pi_list_browseresults();
			} // fin si il y a des r�ultats
			else $fullTable.=$this->pi_getLL("no_articles","[no_docs]");


				// Returns the content from the plugin.
			return $fullTable;
		}
	}
	/**
	 * mode vue unique (1 seul doc)
	 */
	function singleView($content)	{

			// This sets the title of the page for use in indexed search results:
		if ($this->internal["currentRow"]["title"])	$GLOBALS["TSFE"]->indexedDocTitle=$this->internal["currentRow"]["title"];

		$content.='<A name="Anc'.$this->getFieldContent("uid").'></A>';
		$content.='<DIV class="txvm19docs_single">';
		$content.=$this->RetEntete($this->internal["currentRow"]["title"]);
		//	<H2><img src="'.$this->conf["extCurDir"].'picto_documents.gif" align="middle">&nbsp;&nbsp;'.$this->internal["currentRow"]["title"].'</H2>
		$content.=$this->getFieldLine("imagette").
				$this->getFieldLine("internal_code").
				$this->getFieldLine("crdate").
				$this->getFieldLine("endtime").
				$this->getFieldLine("author").
				$this->getFieldLine("lang").
				$this->getFieldLine("support").
				$this->getFieldLine("isbn").
				$this->getFieldLine("topics").
				$this->getFieldLine("keywords").
				$this->getFieldLine("document").
				$this->getFieldLine("abstract").
				'<br/><br/>';

				//<P'.$this->pi_classParam("singleViewField-document").'><strong>'.$this->getFieldHeader("document").':</strong> '.$this->getFieldContent("document").'</P>';

		$content.='<p align="center"><span class="picto">'.str_replace('"><img',"#Anc".$this->getFieldContent("uid").'"><img',$this->pi_list_linkSingle($this->pi_getLL("go_back","[go_back]"),0)).'</span></p></DIV>'.
		$this->pi_getEditPanel();
// // la magouille avec le str_replace ('"><img', xx sert à rajouter au cul de l'url un lien vers l'ancre (#Anc) pour qu'au retour on retombe au même endroit dans la page
	

		return $content;
	}
// fonction qui retourne une ligne compl�e, si le champ n'est pas vide, avec titre du champ et valeur avec traitemenst �entuels
	function getFieldLine($fN) {
		// n'affiche que si le champ est non vide et <>0
		$valret="";

		if ($fN=="author" || ($this->internal["currentRow"][$fN]!="" && $this->internal["currentRow"][$fN]!="0") || $fN=="imagette") { //&& $this->getFieldContent($fN)!="0"
			switch ($fN) {
				case "imagette":
					$imgsrc=$this->retImagette($this->getFieldContent('imagette'),$this->getFieldContent('document'),false);
					
					if (file_exists($this->upload_doc_folder.'/'.$this->getFieldContent("document"))) {
						$valret.='<a href="'.$this->upload_doc_folder.'/'.$this->getFieldContent("document").'" target="_blank">'.$imgsrc.'</a>';
						}
					else $valret.=$imgsrc;

				break;

				case "lang":
					if ($this->conf["langCodeND"]!=$this->internal["currentRow"][$fN]) $valret= '<P><span class="descDoc">'.$this->getFieldHeader($fN).':</span> '.$this->getFieldContent($fN).'</P>';
					break;

				case "document":
					if (file_exists($this->upload_doc_folder.'/'.$this->getFieldContent($fN))) {
						$valret= '<P><b>'.$this->getFieldHeader($fN).':</b> '.$this->getFieldContent($fN).'&nbsp;<a href="'.$this->upload_doc_folder.'/'.$this->getFieldContent($fN).'" target="_blank"><img src="'.$this->conf["extCurDir"].'telecharger.gif" class="picto"  title="'.$this->pi_getLL("download","[download]").'"></a>&nbsp;'.DFSIL($this->upload_doc_folder.'/'.$this->getFieldContent($fN)).'</P>';
					}
				break;
				
				case "author": // renvoie l'auteur et son mail avec une petite enveloppe derri�e
					if ($this->internal["currentRow"]["int_author"]!="") {
						$fN="int_author";
						$valret='<P><b>'.$this->getFieldHeader($fN).':</b> '.$this->getFieldContent($fN);
						$mail=txRecupLib("fe_users","uid","email",$this->internal["currentRow"]["int_author"]);
						$valret.='&nbsp;&nbsp;'.txMefMail($mail);
						$valret.='</P>';
						}
					elseif ($this->internal["currentRow"]["ext_author"]!="") {
						 $fN="ext_author";
						$valret='<P><b>'.$this->getFieldHeader($fN).':</b> '.$this->getFieldContent($fN).'</P>';
						 }
					else {
						$valret='<P><b>'.$this->getFieldHeader("int_author").':</b> '.txRecupLib("be_users","uid","realName",$this->internal["currentRow"]["cruser_id"]);
						$mail=txRecupLib("be_users","uid","email",$this->internal["currentRow"]["cruser_id"]);
						$valret.='&nbsp;&nbsp;'.txMefMail($mail);
						$valret.='</P>';
						}
				break;
	
				default:
					$valret= '<P><b>'.$this->getFieldHeader($fN).':</b> '.$this->getFieldContent($fN).'</P>';
				break;
			}
		}
		return $valret;
	}

	/**
	 * [FONCTIONS MODE LISTE]
	 */
	function pi_list_row($c)	{
		$editPanel = $this->pi_getEditPanel();
		if ($editPanel)	$editPanel="<TD>".$editPanel."</TD>";

		$lConf = $this->conf["listView."];

		$loupe='<img src="'.$this->conf["extCurDir"].'loupe_plus.gif" border="0" title="'.$this->pi_getLL("details","[details]").'">';

		/*
		la fonction pi_list_linkSingle poss�e u dernier argument aoptionel qui permet de passer un tableau de hachage contenant autant d'arguments suppl�entaires que l'on veut !
		ici on y passe l'uid du tt_content contenant le plugin de fa�n �les diff�encier
 		!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-
		*/

		$Margt['ttc_uid']=$this->uid;
		// petites corrections: on passe aussi le pointer, qui indique la position dans les enregistrements, sinon il est perdu
		$Margt['pointer']=$this->piVars["pointer"];
		// ainsi que le classement qui n'�ait pas m�oris�si on passait en singleview
		$Margt['sort']=$this->piVars["sort"];
		
		// asctuce pour rajouter une ancre �la fin du lien
		$mylink=$this->pi_list_linkSingle("salut_blaireau",$this->internal["currentRow"]["uid"],1,$Margt);
		$mylink=str_replace('">salut_blaireau',"#Anc".$this->internal["currentRow"]["uid"].'">salut_blaireau',$mylink);
		$loupe='&nbsp;&nbsp;'.str_replace("salut_blaireau",$loupe,$mylink).'&nbsp;';
	
		//$loupe=$this->pi_list_linkSingle($loupe,$this->internal["currentRow"]["uid"],1,$Margt).'&nbsp;';	// The "1" means that the display of single items is CACHED! Set to zero to disable caching.

		// la puce par d�aut, c'est tux
		$puce='<img src="'.$this->conf["extCurDir"].'tux.gif" border="0" title="'.$this->pi_getLL("other_doc","[other_doc]").'">';
		$file_name=$this->getFieldContent("document");
		$CfN=$this->upload_doc_folder.'/'.$file_name;
		// test existance document
		if (file_exists($CfN) && $file_name!="" ) {
				//$CDocN=$this->getFileIconHTML($file_name,$file_name).' <a href="'.$CfN.'" target="_blank">'.$file_name.'</a>'.DFSIL($CfN);}
				// Plus que l'icone
				$puce=$this->getFileIconHTML($file_name,$file_name);
				$CDocN='&nbsp;<a href="'.$CfN.'" target="_blank"><img src="'.$this->conf["extCurDir"].'telecharger.gif" border="0" title="'.$this->pi_getLL("download","[download]").'"></a>&nbsp;'.DFSIL($CfN);
				}
	// l'ancre sert a ce qu'au retiur d'une loupe on puisse repointer au même endroit..
		return '<a name="Anc'.$this->getFieldContent("uid").'"></a><tr class="'.($c%2 ? 'doclist_rowodd' : 'doclist_roweven').'">
				<td>'.$puce.'</td>
				<td style="text-align:left">'.$this->getFieldContent("title").'</td>
				<td>'.$this->getFieldContent("crdate").'</td>
				<td style="text-align:left">'.$loupe.$CDocN.'</td>
				'.$editPanel.'
			</tr>';
	}
	/**
	 * [Put your description here]

	 */
	function pi_list_header()	{
		return '<tr class="docTableHead">
		 		<td width="50">&nbsp;</td>
				<td width="200" align="left">'.$this->getFieldHeader_sortLink("title").'</td>
				<td align="left">'.$this->getFieldHeader_sortLink("crdate").'</td>
				<td>&nbsp;</td>
			</tr>';
	}
	/**
	 * [Put your description here]
	 */
	function getFieldContent($fN,$mode="L")	{
		switch($fN) {
			case "uid":
				return $this->internal["currentRow"]["uid"];	// The "1" means that the display of single items is CACHED! Set to zero to disable caching.
			break;
			// plus de lien vers la fiche d�aill� sur le nom: il est fait sur la loupe
			/*
			case "title":
				// This will wrap the title in a link: to Single if in List (default), back

				if($mode!="S") {
					return $this->pi_list_linkSingle($this->internal["currentRow"]["title"],$this->internal["currentRow"]["uid"],1);
					}
				else return $this->internal["currentRow"][$fN]; */
			case "nature":
			case "support":
			case "lang":
			case "topics":
				return txRecupLib("tx_vm19docsbase_".$fN,"uid","title",$this->internal["currentRow"][$fN]);
			break;
			case "int_author":
//				return "toto: ".$this->internal["currentRow"][$fN];
				return txRecupLib("fe_users","uid","name",$this->internal["currentRow"][$fN]);
			break;
			case "tstamp":
			case "crdate":
			case "endtime":
				return getDateF($this->internal["currentRow"][$fN]);
			break;
			
			case "abstract":
			//return $this->pi_RTEcssText($this->internal["currentRow"][$fN]);
				/*
				modifications apport�s au traitements de la m�hode getFieldContent($fN)
				la ligne d'origine ci-dessus est g��� automatiquement, mais dans ce cas, les liens (entres autres) ins�� avec le RTE
				ne sont pas affich� dans le FE correctement bien qu'ils soient enregistr�.
				Pour que le changement ci-dessous fonctionne, il faut l'associer �la directive plac� dans le gabarit principal suivante:
				plugin.tx_macledextensionsansunderscores_pi1.RTEcontent_stdWrap.parseFunc < tt_content.text.20.parseFunc */
				return $this->cObj->stdWrap( $this->pi_RTEcssText($this->internal["currentRow"][$fN]), $this->conf['RTEcontent_stdWrap.']);
			break;

			default:
				return $this->internal["currentRow"][$fN];
			break;
		}
	}
	/**
	 * [Put your description here]
	 */
	function getFieldHeader($fN)	{
				return $this->pi_getLL("listFieldHeader_".$fN,"[".$fN."]");
		}

	/**
	 * [Put your description here]
	 */
	function getFieldHeader_sortLink($fN)	{
	    // on n'affiche plus le nom du champ en lien mais une ic�e de classement
		// pour les tris, attention que le champ de tri soit list�dans la prorpri��d�lar� ci-dessus
		//			$this->internal["orderByList"]="title,tstamp";

		//return $this->pi_linkTP_keepPIvars($this->getFieldHeader($fN),array("sort"=>$fN.":".($this->internal["descFlag"]?0:1)));
		return $this->getFieldHeader($fN).'&nbsp;&nbsp;'.$this->pi_linkTP_keepPIvars('<img src="'.$this->conf["extCurDir"].'b_updown.gif" align="middle" border="0" title="'.$this->pi_getLL("order_records","[order_records]").$this->getFieldHeader($fN).'">',array("sort"=>$fN.":".($this->internal["descFlag"]?0:1)));
	}

/** affiche imagette ou apercu pdf ... ou rien/image pr��inie **/

    	function retImagette($imagette="",$doc="",$disp_imggen=false,$classImg="imgDoc") {
		if ($imagette!="" && file_exists($this->upload_doc_folder.'/'.$imagette)) {
			//$size=getimagesize($this->upload_doc_folder.'/'.$this->getFieldContent($fN));
			$ConfI["file"] = $this->upload_doc_folder.'/'.$imagette;
			$ConfI["file."]["maxW"] = $this->conf["ImgMaxWidth"];
			
			$valret.= str_replace(">",' class="'.$classImg.'">',$this->cObj->IMAGE($ConfI));
			//$valret= '<img src="'.$this->upload_doc_folder.'/'.$this->getFieldContent($fN).'" align="left" border="0">';
		} else {
			if ($doc!="" && file_exists($this->upload_doc_folder.'/'.$doc)) {
				$fI=t3lib_div::split_fileref($this->upload_doc_folder."/".$doc);
				if (strstr("pdf jpeg gif png jpg tiff",strtolower($fI["fileext"]))) { 
					$img = t3lib_div::makeInstance('t3lib_stdGraphic');
					//$img->mayScaleUp = 1;
				
					$img->init(); // renvoie 1... donc OK
					
					// mettre les images à la bonne largeur cad $this->conf["ImgMaxWidth"]
					$imgInfo = $img->imageMagickConvert($this->upload_doc_folder.'/'.$doc,'jpg',$this->conf["ImgMaxWidth"],'',"","",'',1);
					
					//debug ($imgInfo); // vide
					if ($imgInfo[3]) $valret='<img src="'.$imgInfo[3].'" class="'.$classImg.'">';
				} elseif ($disp_imggen) {
					$valret='<img src="'.$this->conf['imggen'].'" class="'.$classImg.'">';
				}
			}	
			
		}
	
		return($valret);	
	}
	
	/*** retourne l'icone d'un fichier **/
	
    function getFileIconHTML($file,$altText) {
//ancienne synatxe: $iconDir = "t3lib/gfx/fileicons"; //
// ces valeurs sont maintenant renseignées dans le fichier ext_typoscript_setup.txt
		$altIconDir=$this->conf["altIconDir"];
		$defaultIcon=$this->conf["defaultIcon"];
		$iconDir = $this->conf["iconDir"];

		$fI=t3lib_div::split_fileref($this->params["path"]."/".$file);
        if (file_exists($iconDir."/".$fI["fileext"].".gif")) {
            $img = $iconDir."/".$fI["fileext"].".gif";
        } else {
            $img = file_exists($altIconDir."/".$fI["fileext"].".gif") ? $altIconDir."/".$fI["fileext"].".gif" : $defaultIcon;
        }
        return '<IMG src="'.$img.'" border="0" title="'.$altText.'">';
    }

	
	function RetEntete($title="Document") {
		
		// SUR INTRANET ON PRENAIT LE NOM DU DOSSIER SYSTEME, Plus sur Internet
		$DStitle=txRecupLib("pages","uid","title",$this->conf['pidList']);
		
		
		$entete='<H2 class="titreDossier"><img src="'.$this->conf["extCurDir"].'picto_documents.gif" class="picto">&nbsp;&nbsp;'.$title.'</H2>';
			
		return($entete);
	}

// fonction qui renvoie true si l'argument contient un des mots separ� par des virgules
// situ� dans la var de conf keyWordMarket situ�dans le fichier ext_typoscript_setup.txt
	function marketTitle($arg) {
		$mtr=false;
		$tval=explode(',',$this->conf["keyWordMarket"]);
		foreach($tval as $kw) {
			if (stristr($arg,$kw)) $mtr=true;}
		return($mtr);
 	}

} // fin def de classe


if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_shop/pi1/class.tx_dlcubehnshop_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_shop/pi1/class.tx_dlcubehnshop_pi1.php"]);
}

?>