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
 * Plugin 'document' for the 'vm19_docs_base' extension.
 *
 * @author	Vincent (admin) <artec.vm@nerim.net>
 */


require_once(PATH_tslib."class.tslib_pibase.php");

require_once("typo3conf/ext/vm19_toolbox/functions.php");

class tx_vm19docsbase_pi1 extends tslib_pibase {
	var $prefixId = "tx_vm19docsbase_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_vm19docsbase_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "vm19_docs_base";	// The extension key.
	var $upload_doc_folder="uploads/tx_vm19docsbase";
	
	/**
	 * le plugin a �t� modifi� de fa�on � permettre d'�tre ins�r� plusieurs fois dans une m�me page et que le passage en singleview de l'un n'influence pas les autres

	 */
	function main($content,$conf)	{

       //global $TYPO3_CONF_VARS;
        //$GLOBALS["TSFE"]->set_no_cache();
		$this->uid = substr(strstr($GLOBALS[GLOBALS][TSFE]->currentRecord,":"),1);
		$this->conf=$conf;		// Setting the TypoScript passed to this function in $this->conf
		$this->pi_setPiVarDefaults();

        // *************************************
		// *** doing the things...:
		// *************************************
        if (t3lib_div::GPvar("file")) {
            if (t3lib_div::GPvar("uid") == $this->uid) { $content .= $this->getDownloadFile(t3lib_div::GPvar("file")); }
            return $this->pi_wrapInBaseClass($content);
        } else {

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
	}

	/**
	 * [Put your description here]
	 */
	function listView($content,$conf)	{

		$this->uid = substr(strstr($GLOBALS[GLOBALS][TSFE]->currentRecord,":"),1);

		//debug($this->uid);
		// $this->uid correspond � l'uid du tt_content contenant le plugin

		$this->conf=$conf;		// Setting the TypoScript passed to this function in $this->conf
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();		// Loading the LOCAL_LANG values
		$lConf = $this->conf["listView."];	// Local settings for the listView function

		/* !-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-
		  modif: on ne se met et en mode singleView que si showUid!=" ET SURTOUT que si l'uid tt_content pass� correspond
		// au courant
		// voir aussi utilisation du dernier argument optionel de la la fonction
		pi_list_linkSingle qui permet de passer un tableau de hachage contenant autant d'arguments suppl�mentaires que l'on veut
 		!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-
		*/
		if ($this->piVars["showUid"] && $this->piVars["ttc_uid"]==$this->uid)	{	// If a single element should be displayed:
			$this->internal["currentTable"] = "tx_vm19docsbase_docs";
			$this->internal["currentRow"] = $this->pi_getRecord("tx_vm19docsbase_docs",$this->piVars["showUid"]);

			$content = $this->singleView($content,$conf);
			return $content;
		} else  {
			/* Sert pour le sélecteur de mode, on s'en tappe
			  $items=array(
				"1"=> $this->pi_getLL("list_mode_1","Mode 1"),
				"2"=> $this->pi_getLL("list_mode_2","Mode 2"),
				"3"=> $this->pi_getLL("list_mode_3","Mode 3"),
			); */
			if (!isset($this->piVars["pointer"]))	$this->piVars["pointer"]=0;
			if (!isset($this->piVars["mode"]))	$this->piVars["mode"]=1;

				// Initializing the query parameters:
			list($this->internal["orderBy"],$this->internal["descFlag"]) = explode(":",$this->piVars["sort"]);
			// si aucun classement specifie, par d�faut on classe par date d�croissante
			if ($this->internal["orderBy"]=="") {
				$this->internal["orderBy"]="tstamp";
				$this->internal["descFlag"]=1;
				}
			//debug ($this->piVars["sort"]);
			$this->internal["results_at_a_time"]=t3lib_div::intInRange($lConf["results_at_a_time"],0,1000,3);		// Number of results to show in a listing.
			$this->internal["maxPages"]=t3lib_div::intInRange($lConf["maxPages"],0,1000,2);;		// The maximum number of "pages" in the browse-box: "Page 1", "Page 2", etc.
			$this->internal["searchFieldList"]="internal_code,title,ext_author,isbn,keywords,abstract,workflow_state,document";
			$this->internal["orderByList"]="title,tstamp";


			$query = $this->pi_list_query("tx_vm19docsbase_docs",1);

			$res = mysql(TYPO3_db,$query);
			if (mysql_error())	debug(array(mysql_error(),$query));


			$fullTable=$this->RetEntete();
			list($this->internal["res_count"]) = mysql_fetch_row($res);
			if ($this->internal["res_count"]>0) {
					// Make listing query, pass query to MySQL:
				//$query = $this->pi_list_query("tx_vm19docsbase_docs",0,"","","","ORDER BY tstamp DESC, title ASC");
				$query = $this->pi_list_query("tx_vm19docsbase_docs");
				$res = mysql(TYPO3_db,$query);
				if (mysql_error())	debug(array(mysql_error(),$query));
				$this->internal["currentTable"] = "tx_vm19docsbase_docs";



			#	$fullTable.=t3lib_div::view_array($this->piVars);	// DEBUG: Output the content of $this->piVars for debug purposes. REMEMBER to comment out the IP-lock in the debug() function in t3lib/config_default.php if nothing happens when you un-comment this line!

					// Adds the mode selector: on le vire
				//$fullTable.=$this->pi_list_modeSelector($items);

					// Adds the whole list table
				$fullTable.=$this->pi_list_makelist($res);

					// Adds the search box:
					// Elle est Moche alors on la vire
				//$fullTable.=$this->pi_list_searchBox();

					// Adds the result browser, seulement s'il y a assez de r�sultats
				if ($this->internal["res_count"]>$this->internal["results_at_a_time"]) $fullTable.=$this->pi_list_browseresults();
			} // fin si il y a des r�sultats
			else $fullTable.=$this->pi_getLL("no_docs","[no_docs]");


				// Returns the content from the plugin.
			return $fullTable;
		}
	}
	/**
	 * [Put your description here]
	 */
	function singleView($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

			// This sets the title of the page for use in indexed search results:
		if ($this->internal["currentRow"]["title"])	$GLOBALS["TSFE"]->indexedDocTitle=$this->internal["currentRow"]["title"];




// exemple d'appel de style sp�cifique � chaque champ:
// allourdit inultilement...
//<P'.$this->pi_classParam("singleViewField-internal-code").'><strong>'.$this->getFieldHeader("internal_code").':</strong> '.$this->getFieldContent("internal_code").'</P>

		$content.='<DIV'.$this->pi_classParam("singleView").'>';
		$content.=$this->RetEntete($this->internal["currentRow"]["title"]);
		//	<H2><img src="'.$this->conf["extCurDir"].'picto_documents.gif" align="middle">&nbsp;&nbsp;'.$this->internal["currentRow"]["title"].'</H2>
		$content.=$this->getFieldLine("imagette").
				$this->getFieldLine("internal_code").
				$this->getFieldLine("tstamp").
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

		$content.='<P>'.$this->pi_list_linkSingle($this->pi_getLL("go_back","[go_back]"),0).'</P></DIV>'.
		$this->pi_getEditPanel();

		return $content;
	}
// fonction qui retourne une ligne compl�te, si le champ n'est pas vide, avec titre du champ et valeur avec traitemenst �ventuels
	function getFieldLine($fN) {
		// n'affiche que si le champ est non vide et <>0
		$valret="";

		if ($fN=="author" || ($this->internal["currentRow"][$fN]!="" && $this->internal["currentRow"][$fN]!="0")) { //&& $this->getFieldContent($fN)!="0"
			switch ($fN) {
				case "imagette":
					if (file_exists($this->upload_doc_folder.'/'.$this->getFieldContent($fN))) {
						//$size=getimagesize($this->upload_doc_folder.'/'.$this->getFieldContent($fN));
						$ConfI["file"] = $this->upload_doc_folder.'/'.$this->getFieldContent($fN);
						$ConfI["file."]["maxW"] = $this->conf["ImgMaxWidth"];
						$valret= str_replace(">",' align="left">',$this->cObj->IMAGE($ConfI));
						//$valret= '<img src="'.$this->upload_doc_folder.'/'.$this->getFieldContent($fN).'" align="left" border="0">';
					}
				break;

				case "lang":
					if ($this->conf["langCodeND"]!=$this->internal["currentRow"][$fN]) $valret= '<P><b>'.$this->getFieldHeader($fN).':</b> '.$this->getFieldContent($fN).'</P>';
					break;

				case "document":
					if (file_exists($this->upload_doc_folder.'/'.$this->getFieldContent($fN))) {
						$valret= '<P><b>'.$this->getFieldHeader($fN).':</b> '.$this->getFieldContent($fN).'&nbsp;<a href="'.$this->upload_doc_folder.'/'.$this->getFieldContent($fN).'" target="_blank"><img src="'.$this->conf["extCurDir"].'b_oeil.gif" border="0" title="'.$this->pi_getLL("download","[download]").'"></a>&nbsp;'.DFSIL($this->upload_doc_folder.'/'.$this->getFieldContent($fN)).'</P>';
					}
				break;
				
				case "author": // renvoie l'auteur et son mail avec une petite enveloppe derri�re
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
		la fonction pi_list_linkSingle poss�de u dernier argument aoptionel qui permet de passer un tableau de hachage contenant autant d'arguments suppl�mentaires que l'on veut !
		ici on y passe l'uid du tt_content contenant le plugin de fa�on � les diff�rencier
 		!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-
		*/

		$Margt['ttc_uid']=$this->uid;
		// petites corrections: on passe aussi le pointer, qui indique la position dans les enregistrements, sinon il est perdu
		$Margt['pointer']=$this->piVars["pointer"];
		// ainsi que le classement qui n'�tait pas m�moris� si on passait en singleview
		$Margt['sort']=$this->piVars["sort"];
		
		$loupe=$this->pi_list_linkSingle($loupe,$this->internal["currentRow"]["uid"],1,$Margt).'&nbsp;';	// The "1" means that the display of single items is CACHED! Set to zero to disable caching.

		// la puce par d�faut, c'est tux
		$puce='<img src="'.$this->conf["extCurDir"].'tux.gif" border="0" title="'.$this->pi_getLL("other_doc","[other_doc]").'">';
		$file_name=$this->getFieldContent("document");
		$CfN=$this->upload_doc_folder.'/'.$file_name;
		// test existance document
		if (file_exists($CfN) && $file_name!="" ) {
				//$CDocN=$this->getFileIconHTML($file_name,$file_name).' <a href="'.$CfN.'" target="_blank">'.$file_name.'</a>'.DFSIL($CfN);}
				// Plus que l'icone
				$puce=$this->getFileIconHTML($file_name,$file_name);
				$CDocN='&nbsp;<a href="'.$CfN.'" target="_blank"><img src="'.$this->conf["extCurDir"].'b_oeil.gif" border="0" title="'.$this->pi_getLL("download","[download]").'"></a>&nbsp;'.DFSIL($CfN);
				}

		return '<tr"'.($c%2 ? ' class="rowodd"' : "").'>
				<td align="center">'.$puce.'</td>
				<td valign="center">'.$this->getFieldContent("title").'</td>
				<td valign="center">'.$this->getFieldContent("tstamp").'</td>
				<td valign="center">'.$loupe.$CDocN.'</td>
				'.$editPanel.'
			</tr>';
	}
	/**
	 * [Put your description here]

	 */
	function pi_list_header()	{
		return '<thead'.$this->pi_classParam("listrow-header").'>
		 		<th width="50">&nbsp;</th>
				<th width="200" align="left">'.$this->getFieldHeader_sortLink("title").'</th>
				<th align="left">'.$this->getFieldHeader_sortLink("tstamp").'</th>
				<th>&nbsp;</th>
			</thead>';
	}
	/**
	 * [Put your description here]
	 */
	function getFieldContent($fN,$mode="L")	{
		switch($fN) {
			case "uid":
				return $this->pi_list_linkSingle($this->internal["currentRow"][$fN],$this->internal["currentRow"]["uid"],1);	// The "1" means that the display of single items is CACHED! Set to zero to disable caching.
			break;
			// plus de lien vers la fiche d�taill�e sur le nom: il est fait sur la loupe
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
			case "endtime":
				return getDateF($this->internal["currentRow"][$fN]);
			break;
			
			case "abstract":
			//return $this->pi_RTEcssText($this->internal["currentRow"][$fN]);
				/*
				modifications apport�es au traitements de la m�thode getFieldContent($fN)
				la ligne d'origine ci-dessus est g�n�r�e automatiquement, mais dans ce cas, les liens (entres autres) ins�r�s avec le RTE
				ne sont pas affich�s dans le FE correctement bien qu'ils soient enregistr�s.
				Pour que le changement ci-dessous fonctionne, il faut l'associer � la directive plac�e dans le gabarit principal suivante:
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
	    // on n'affiche plus le nom du champ en lien mais une ic�ne de classement
		// pour les tris, attention que le champ de tri soit list� dans la prorpri�i� d�clar�e ci-dessus
		//			$this->internal["orderByList"]="title,tstamp";

		//return $this->pi_linkTP_keepPIvars($this->getFieldHeader($fN),array("sort"=>$fN.":".($this->internal["descFlag"]?0:1)));
		return $this->getFieldHeader($fN).'&nbsp;&nbsp;'.$this->pi_linkTP_keepPIvars('<img src="'.$this->conf["extCurDir"].'b_updown.gif" align="middle" border="0" title="'.$this->pi_getLL("order_records","[order_records]").$this->getFieldHeader($fN).'">',array("sort"=>$fN.":".($this->internal["descFlag"]?0:1)));
	}


    /**
	 * Returns the fileicon
	 */
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

		// on r�cupere le nom du dossier systeme$
		// s'il contient market ou beneficia ou prestati, met l'autre ic�ne
	function RetEntete($title="") {
		$DStitle=txRecupLib("pages","uid","title",$this->conf['pidList']);

		if ($this->marketTitle($DStitle)) {
			$entete='<H2><img src="'.$this->conf["extCurDir"].'picto_fiches.gif" align="middle">&nbsp;&nbsp;'.($title=="" ? $DStitle : $title).'</H2>';
			}
		else {
   			$entete='<H2><img src="'.$this->conf["extCurDir"].'picto_documents.gif" align="middle">&nbsp;&nbsp;'.($title=="" ? $DStitle : $title).'</H2>';
			}
		return($entete);
	}

// fonction qui renvoie true si l'argument contient un des mots separ�s par des virgules
// situ�s dans la var de conf keyWordMarket situ� dans le fichier ext_typoscript_setup.txt
	function marketTitle($arg) {
		$mtr=false;
		$tval=explode(',',$this->conf["keyWordMarket"]);
		foreach($tval as $kw) {
			if (stristr($arg,$kw)) $mtr=true;}
		return($mtr);
 	}
} // fin def de classe

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/vm19_docs_base/pi1/class.tx_vm19docsbase_pi1.php"])	{
include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/vm19_docs_base/pi1/class.tx_vm19docsbase_pi1.php"]);
	}


?>
