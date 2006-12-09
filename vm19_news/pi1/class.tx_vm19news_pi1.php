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
 * Plugin 'vm_news' for the 'vm19_news' extension.
 *
 * @author	Vincent (admin) <vmaury@localhost>
 */


require_once(PATH_tslib."class.tslib_pibase.php");
require_once("typo3conf/ext/vm19_toolbox/functions.php");
include_once("fonctions.php");
session_start();

class tx_vm19news_pi1 extends tslib_pibase {
	var $prefixId = "tx_vm19news_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_vm19news_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "vm19_news";	// The extension key.
	var $upload_img_folder="uploads/tx_vm19news";
	var $upload_doc_folder="uploads/tx_vm19news/docs";
	var $ChemImg="fileadmin/templates/images/elts_reccurents/";


	/**
	/**
	 * le plugin a ��modifi�de fa�n �permettre d'�re ins��plusieurs fois dans une m�e page et que le passage en singleview de l'un n'influence pas les autres
	 */
	function main($content,$conf)	{
		$this->uid = substr(strstr($GLOBALS[GLOBALS][TSFE]->currentRecord,":"),1);
		if (strstr($this->cObj->currentRecord,"tt_content"))	{
			$conf["pidList"] = $this->cObj->data["pages"];
			$conf["recursive"] = $this->cObj->data["recursive"];
		}
	
		$this->conf=$conf;		// Setting the TypoScript passed to this function in $this->conf
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();		// Loading the LOCAL_LANG values
		//$this->pi_moreParams="&pointeruid[".$this->uid."]=".$this->uid;

		/* !-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-
		  modif: on ne se met et en mode singleView que si showUid!=" ET SURTOUT que si l'uid tt_content pass�correspond
		// au courant
		// voir aussi utilisation du dernier argument optionel de la la fonction
		pi_list_linkSingle qui permet de passer un tableau de hachage contenant autant d'arguments suppl�entaires que l'on veut
 		!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-
		*/
/*		if ($this->piVars["showUid"] && $this->piVars["ttc_uid"]==$this->uid)	{	// If a single element should be displayed:
Maintenant la loupe se fait directement dans la liste */

		// Affichage Titre verrue Elodie : si nom du dossier syst�e contenant les actus contient "_noinfo", pas de Logo actu ni titre, ni auteur en mode normal
		if (!strstr(txRecupLib("pages","uid","title",$this->conf['pidList']),"_noinfo")) { 
			$content.='<H2>'.$this->pi_getLL("titre_fe","[titre_fe]").'</H2>'; // <img src="'.$this->conf["extCurDir"].'picto_actu.gif" class="picto">&nbsp;&nbsp; cette bourrique de Diane veut plus du picto
		}
		// si changement de page, on efface le contexte
		if ($_SESSION['datafvmnews']['pidcour']!=$this->cObj->data['pid']) {
			unset($_SESSION['datafvmnews']);
		}
		
		// gestion mode archive
		if (isset($this->piVars['arch_mode'])) {
			$_SESSION['vmnews_arch_mode']=$this->piVars['arch_mode'];
		}
		$this->arch_mode=($this->cObj->data['layout']>=1 || $_SESSION['vmnews_arch_mode']==1);
		
/*		debug($this->cObj->data['layout'],"this->cObj->data['layout']");
		debug($this->piVars,"this->piVars:");*/
		//debug ($_SESSION,"sessione");
		
		if ($this->arch_mode) { // mode archive
			//$GLOBALS["TSFE"]->set_no_cache(); // desactive la cache en mode archive sinon formulairee merde...
			$content.='<a name="debform"></a>';
			$content.="<H3>".$this->pi_getLL("rech_arch","Rech arhives")."</H3>";
			$content.=$this->buttdisphiddarch();
			$content.="<H4>".$this->pi_getLL("crit_arch","Crit arhives")."</H4>";
			
			//debug($this->conf);
			
			//debug($_SESSION['datafvmnews'],"_SESSION['datafvmnews']");
			
			// si formulaire envoyé, on met en session sa valeur; sinon quand on suit un lien vers le détail d'une actu, comme il n'y a pas de submit, on perd les critères courant
			
			if (isset ($this->piVars['DATA']['test_f_sent'])) {
				if ($this->piVars['DATA']['datedebp']!="" ) $this->piVars['DATA']['datedebp']=rmfDateF($this->piVars['DATA']['datedebp']);
				if ($this->piVars['DATA']['datefinp']!="" ) $this->piVars['DATA']['datefinp']=rmfDateF($this->piVars['DATA']['datefinp']);
				$tsdtdebp=DateF2tstamp($this->piVars['DATA']['datedebp']);
				$tsdtfinp=DateF2tstamp($this->piVars['DATA']['datefinp']);
				if ($tsdtdebp>0 && $tsdtfinp>0 && $tsdtdebp>$tsdtfinp) {
					$errorcd=$this->pi_getLL("err_coher_dates","erreur de cohérences sur les dates");
					$this->piVars['DATA']['datefinp']="";
					unset($_SESSION ['datafvmnews']['datefinp']);
					}
				$_SESSION ['datafvmnews']=$this->piVars['DATA'];
				}
			
			if (!isset($_SESSION['datafvmnews']['emplact'])) {
				$this->piVars['DATA']['emplact'][0]="'".$this->cObj->data['pid']."'"; // si pas de sélection precedente, prend l'uid de la page courante
				$_SESSION['datafvmnews']['emplact'][0]="'".$this->cObj->data['pid']."'"; 
			}	
			$_SESSION['datafvmnews']['pidcour']=$this->cObj->data['pid'];
			
			//debug(in_array($this->cObj->data['pid'],$_SESSION['datafvmnews']['emplact']));
			//debug($this->piVars['DATA'],"this->piVars['DATA']:");
			//debug($_SESSION['datafvmnews'],"_SESSION['datafvmnews']");
			
			$reqroot="SELECT pid FROM sys_template WHERE hidden = 0 AND root = 1 AND deleted = 0";
			$resroot=mysql(TYPO3_db,$reqroot);
			if (mysql_num_rows($resroot)==0) die ("This site seems to contain no root page");
			while ($rep=mysql_fetch_row($resroot)) $tbpidroot[]=$rep[0];
// debug root pid tables
			foreach ($tbpidroot as $pidroot) {
				$this->retTLDarbo($pidroot);
			}
			$this->tbemplact=Array("%" => $this->pi_getLL("Indifferent","Indifferent")) + $this->tbemplact;
			$LDEmplact=DispLD($this->tbemplact,$this->prefixId.'[DATA][emplact]',"yes","",false); // liste d�oulante multi
			// avant DispLD n'etait pas compatible XHTML
			//$LDEmplact=str_replace("SELECTED",' selected="selected" ',$LDEmplact);
			
			$comment_fdate=" <small> ex. : 15/10/2005 </small>";
			$cHash = md5(serialize($this->piVars['DATA']));
			$content.='<FORM action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'?cHash='.$cHash.'#debform" name="'.$this->prefixId.'fname" method="POST">';
			$content.='
				<INPUT TYPE="hidden" value="coucou" name="'.$this->prefixId.'[DATA][test_f_sent]" />
                                <TABLE><TR><TD width="120><label for="keywords">'.$this->pi_getLL("keywords","Mots clés").'</label></TD><TD><INPUT id="keywords" type="text" size="60" value="'.$_SESSION['datafvmnews']['keywords'].'" name="'.$this->prefixId.'[DATA][keywords]" />
                               </TD></TR><TR><TD>
                                <label for="and_or">'.$this->pi_getLL("and_or","Et/ou").'</label></TD><TD><INPUT type="radio" id="and_or"  '.($_SESSION['datafvmnews']["and_or"]!="OR" ? ' checked="checked" ' : '').' name="'.$this->prefixId.'[DATA][and_or]" value="AND">'.$this->pi_getLL("AND","ET").'&nbsp;&nbsp;&nbsp;<INPUT type="radio" name="'.$this->prefixId.'[DATA][and_or]" value="OR" '.($_SESSION['datafvmnews']["and_or"]=="OR" ? ' checked="checked" ' : '').'>'.$this->pi_getLL("OR","OU").'</TD></TR><TR>
                                <TD>
                                <label for="datedebp">'.$this->pi_getLL("datedebp","Datedebp").'</label></TD><TD><INPUT id="datedebp" type="text" size="12" value="'.$_SESSION['datafvmnews']['datedebp'].'" name="'.$this->prefixId.'[DATA][datedebp]" />'.$comment_fdate.'
                                </TD</TR><TR><TD>
                                <label for="datefinp">'.$this->pi_getLL("datefinp","Datefinp").'</label></TD><TD><INPUT id="datefinp" type="text" size="12" value="'.$_SESSION['datafvmnews']['datefinp'].'" name="'.$this->prefixId.'[DATA][datefinp]" /> '.$comment_fdate.
                                ($errorcd ? '<BR><span style="color:red">'.$errorcd.'<span>' : "").'</TD></TR><TR><TD>
                                <label for="emplact">'.$this->pi_getLL("Emplact","emplact").'</label></TD><TD>'.$LDEmplact.'
                                </TD></TR><TR><TD>
                                <INPUT '.$this->pi_classParam("submit-button").' type="button" name="'.$this->prefixId.'[reset_button]" value="'.$this->pi_getLL("Reset","Annuler").'" onclick="getElementById(\'keywords\').value=\'\'; getElementById(\'datedebp\').value=\'\'; getElementById(\'datefinp\').value=\'\';">
                                </TD><TD>
                                <INPUT '.$this->pi_classParam("submit-button").' type="submit" name="'.$this->prefixId.'[submit_button]" value="'.$this->pi_getLL("rechercher","Rechercher").'">

                                </TD></TR></TABLE>
                                <HR/>';
			$content.='</FORM>';
		}
		
		//debug($this->cObj->data['pid']); uidd e la page courante
		//debug($this->uid);
		//debug($this->piVars);
		if (!isset($this->piVars["pointer"]))	$this->piVars["pointer"]=0;
		if (!isset($this->piVars["mode"]))	$this->piVars["mode"]=1;

			// Initializing the query parameters:
		list($this->internal["orderBy"],$this->internal["descFlag"]) = explode(":",$this->piVars["sort"]);

		//debug($this->piVars);
		$this->internal["results_at_a_time"]=t3lib_div::intInRange($conf["results_at_a_time"],0,1000,3);		// Number of results to show in a listing.
		$this->internal["maxPages"]=t3lib_div::intInRange($conf["maxPages"],0,1000,2);		// The maximum number of "pages" in the browse-box: "Page 1", "Page 2", etc.
		$this->internal["searchFieldList"]="title,abstract,bodytext";
		$this->internal["orderByList"]="tstamp,uid,title";
		
		if ($this->arch_mode) {
			
			// envoie la requete spéciale
			if (isset($_SESSION['datafvmnews']['keywords'])) {
				$_SESSION['datafvmnews']['keywords']=addslashes($_SESSION['datafvmnews']['keywords']);
				$tbkeyw=explode(" ",$_SESSION['datafvmnews']['keywords']);
				foreach($tbkeyw as $keyw) { $whk.=" __nom_chp__ LIKE '%$keyw%' __op__ "; }
				$whk=vdc($whk,7);
				$whk="(".str_replace("__op__",$_SESSION['datafvmnews']["and_or"],$whk).")";
				$whk="(".str_replace("__nom_chp__","title",$whk)." OR ".str_replace("__nom_chp__","abstract",$whk)." OR ".str_replace("__nom_chp__","bodytext",$whk).")";
				
			}
			
			if ($_SESSION['datafvmnews']['datedebp']!="") { 
				$whdp="( (starttime> ".DateF2tstamp($_SESSION['datafvmnews']['datedebp']).") OR ( starttime=0 AND tstamp > ".DateF2tstamp($_SESSION['datafvmnews']['datedebp']).") )";
			}
			if ($_SESSION['datafvmnews']['datefinp']!="") {
				$whfp="( (endtime>0 AND endtime < ".DateF2tstamp($_SESSION['datafvmnews']['datefinp']).") OR ( endtime=0 AND tstamp < ".DateF2tstamp($_SESSION['datafvmnews']['datefinp']).") )";
		}
			$whg= "from tx_vm19news_news WHERE deleted=0 AND hidden=0 ";
			$whg.=($whk ? " AND " : "").$whk;
			$whg.=($whdp ? " AND " : "").$whdp;
			$whg.=($whfp ? " AND " : "").$whfp;
			
			if ($_SESSION['datafvmnews']['emplact'][0] != "%") $whg.=" AND pid IN (".implode(",",$_SESSION['datafvmnews']['emplact']).")";
			
			//debug ($whg,"where avant :");
			$query=$this->pi_list_query("tx_vm19news_news",1,'','','','',$whg); // le 1 sert a compter seulement
			//debug ($query,"query apres :");
			
		//pi_list_query($table,$count=0,$addWhere='',$mm_cat='',$groupBy='',$orderBy='',$query='',$returnQueryArray=FALSE)
		} else {
			// anciennee methode "normale", ... mais on a rajouté l'affichage sur d'autres pages pr 1 meme actu...
			//$query = $this->pi_list_query("tx_vm19news_news",1);
			$whg= "from tx_vm19news_news WHERE deleted=0 AND hidden=0 ";
			$whg.="AND ( starttime< ".time().' OR  starttime=0  )';
			$whg.=" AND ( endtime> ".time().' OR  endtime=0  )';
			$pidc=$this->cObj->data['pid'];
			$whg.=" AND (pid=$pidc OR otherpages LIKE '$pidc,%' OR otherpages LIKE '%,$pidc,%' OR otherpages LIKE '%,$pidc')";
			$query=$this->pi_list_query("tx_vm19news_news",1,'','','','',$whg); // le 1 sert a compter seulement
			//debug($query);
			}
		$res = mysql(TYPO3_db,$query);
		//debug($query);
		if (mysql_error())	debug(array(mysql_error(),$query));
		
		list($this->internal["res_count"]) = mysql_fetch_row($res);

	#	$content.=t3lib_div::view_array($this->piVars);	// DEBUG: Output the content of $this->piVars for debug purposes. REMEMBER to comment out the IP-lock in the debug() function in t3lib/config_default.php if nothing happens when you un-comment this line!
		if ($this->internal["res_count"]>0) {	
				// Make listing query, pass query to MySQL:
			//pi_list_query($table, $count=0, $addWhere="", $mm_cat="", $groupBy="", $orderBy="", $query="")
			if ($this->arch_mode) {
				$content.='<div class="nbrep_r">'.$this->pi_getLL("nb_news_r","[nb_news]")." : <b>".$this->internal["res_count"]."</b></div>";
				//$query="SELECT * ".$whg." ORDER BY sorting,tstamp DESC";
				// le fait de passer par pi_list_query permet d'utiliser la pagination native
				$query=$this->pi_list_query("tx_vm19news_news",0,'','','',"ORDER BY sorting,tstamp DESC",$whg);
	
			} else {
				// anciennee methode "normale", ... mais on a rajouté l'affichage sur d'autres pages pr 1 meme actu...
				//$query = $this->pi_list_query("tx_vm19news_news",0,"","","","ORDER BY sorting,tstamp DESC");
				$query=$this->pi_list_query("tx_vm19news_news",0,'','','',"ORDER BY sorting,tstamp DESC",$whg);
				}
			$res = mysql(TYPO3_db,$query);
			if (mysql_error())	debug(array(mysql_error(),$query));
			$this->internal["currentTable"] = "tx_vm19news_news";
				// Adds the mode selector.
			//$content.=$this->pi_list_modeSelector($items);	
			
				// Adds the whole list table
			$content.=$this->makelist($res);
			
				// Adds the search box:
			//$content.=$this->pi_list_searchBox();

				// Adds the result browser:
				// Adds the result browser, seulement s'il y a assez de r�ultats
				if ($this->internal["res_count"]>$this->internal["results_at_a_time"]) $content.=$this->pi_list_browseresults();
			} // if no news to display
		else $content.=$this->pi_getLL("no_news".($this->arch_mode ? "1" : ""),"[no_news]");
			
		$content.="<BR/>".$this->buttdisphiddarch();
		//$content.=$this->pi_list_browseresults();
		
			// Returns the content from the plugin.
		return $this->pi_wrapInBaseClass($content);
	}
	/**
	 * [Put your description here]
	 */
	function makelist($res)	{
		$items=Array();
		// si lien direct vers actu (venant accroche page accueil ou newsletter
		if (!empty($_REQUEST["vm19news_dirlink_uid"])) {
				$this->internal["currentTable"] = "tx_vm19news_news";
				
				$this->internal["currentRow"] = $this->pi_getRecord("tx_vm19news_news",$_REQUEST["vm19news_dirlink_uid"]);
	
				$items[] = $this->singleView();
		} else {
				// Make list table rows
			while($this->internal["currentRow"] = mysql_fetch_assoc($res))	{
				$this->NoResult++;
				if ($this->piVars["showUid"]==$this->internal["currentRow"]["uid"] && $this->piVars["ttc_uid"]==$this->uid) {
					// mode unique
					$items[] = $this->singleView();
				}
				else {
					$items[]=$this->makeListItem();
				} // fin si single view OK
			} // fin boucle sur actus
		} // fin si pas lien direct vers une actu
	
		$out = implode(chr(10),$items);
		return $out;
	}	
	
	/**
	 * [Put your description here]
	 */
	function makeListItem()	{
		$Margt['ttc_uid']=$this->uid;
		$Margt['pointer']=$this->piVars["pointer"];
		//$Margt['#,/toto']="#titi";

 		/*
		la fonction pi_list_linkSingle poss�e u dernier argument aoptionel qui permet de passer un tableau de hachage contenant autant d'arguments suppl�entaires que l'on veut !
		ici on y passe l'uid du tt_content contenant le plugin de fa�n �les diff�encier
 		!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-!-
		*/
		
		$mylink=$this->pi_list_linkSingle("salut_blaireau",$this->internal["currentRow"]["uid"],1,$Margt);
		$mylink=str_replace('">salut_blaireau',"#Anc".$this->internal["currentRow"]["uid"].'">salut_blaireau',$mylink);
		// la magouille avec le str_replace ('">salut_blaireau', xx sert �rajouter au cul de l'url un lien vers l'ancre (#Anc) pour qu'au retour on retombe au m�e endroit
		// nouvelles specs: si pas de d�ail, n'affiche pas le "en savoir plus"
		$link_single=false;
		if ($this->internal["currentRow"]["bodytext"]!="") $link_single=true;
		
		$out.='<div class="actuList"><a name="Anc'.$this->internal["currentRow"]["uid"].'"> </a>
		<h3>'.($link_single ?  str_replace('salut_blaireau',$this->mefField("","small_img",""),$mylink) : $this->mefField("","small_img","")).
		$this->mefField("","title","").'</h3>';
		// l'ancre sert a ce qu'au retour d'une loupe on puisse repointer au m�e endroit..
		if ($this->arch_mode) {
			$out.='<DIV class="datarchactus">'.$this->pi_getLL("Emplact","emplact")." : ".$this->tbtxtpath[$this->internal["currentRow"]['pid']]."<BR/>";			
			$out.=$this->mefField("".$this->pi_getLL("date_parut","du"),"starttime","");
			if ($this->internal["currentRow"]['endtime']>0) $out.=$this->mefField(", ".$this->pi_getLL("parue_jusk"," au "),"endtime","");
			$out.="</DIV>";
		}
		$out.=$this->mefField('',"abstract",'').
		'<DIV align="right">'.($link_single ? str_replace('salut_blaireau','<img src="'.$this->conf["extCurDir"].'loupe_plus.gif" border="0" title="'.$this->pi_getLL("details","[details]").'">',$mylink) : "").'</div>
		</DIV>';
		//$out.=DispHRs(50);
		$out.=($this->NoResult!=$this->internal["res_count"] ? DispHRs(50) : "");
		return $out;
	}
	/**
	 * [Put your description here] 
	 */
	function singleView()	{
		//debug($this->piVars);
		//debug($this->pi_moreParams);
		//debug($this->conf);
		//debug($this->internal["currentRow"],"currentrow");
		
		// This sets the title of the page for use in indexed search results:
		if ($this->internal["currentRow"]["title"])	$GLOBALS["TSFE"]->indexedDocTitle=$this->internal["currentRow"]["title"];
		// picto que Diane ne neut plus <img src="'.$this->conf["extCurDir"].'picto_actu.gif" class="picto">&nbsp;&nbsp;
/*	Version en tabelau 2 colonnes
		$content='<div id="actuSingle"><a name="Anc'.$this->internal["currentRow"]["uid"].'"> </a><h3>'.$this->mefField("","title","").'</h3>'.
		$this->mefField('<DIV align="right" style="font-size:10px"> du ',"starttime","<BR/></DIV>").
		'<table border="0"><tr><td valign="top">'.
		$this->mefField('',"big_img","").
		$this->mefField("<br/><small>","bimg_legend","&nbsp;&nbsp;&nbsp;").
		$this->mefField("<i>","bimg_credit","</i></small>").
		'</td><td>'.
		$this->mefField("<p>","bodytext","</p>").
		'</td></tr></table>'.
		$this->mefField('<p>'.$this->pi_getLL("doc_joint","[doc_joint]"),"document",'</P>').
		'<DIV class="actInfosComp">'.
		$this->mefField('',"author",'').
		//$this->mefField('&nbsp;&nbsp;<a href="mailto:',"email",'"><img src="'.$this->ChemImg.'mailto.gif" class="picto" alt="bouton mail" /></A>').
		//$this->mefField('<BR/>fin de parution le ',"endtime","").
		'</DIV>'.
		'<P>'.str_replace('"><img',"#Anc".$this->internal["currentRow"]["uid"].'"><img',$this->pi_list_linkSingle($this->pi_getLL("go_back","[go_back]"),0)).'</P></div>'.
		$this->pi_getEditPanel();*/
		// la magouille avec le str_replace ('"><img', xx sert �rajouter au cul de l'url un lien vers l'ancre (#Anc) pour qu'au retour on retombe au m�e endroit
		$content='<div id="actuSingle"><a name="Anc'.$this->internal["currentRow"]["uid"].'"> </a><h3>'.$this->mefField("","title","").'</h3>'.
		$this->mefField('<DIV align="right" style="font-size:10px"> du ',"starttime","</DIV>").
		$this->mefField('<p><div style="float:left">',"big_img","</div>").
		$this->mefField("<small><b>","bimg_legend","</b><br/>").
		$this->mefField('<span class="credit_phot">',"bimg_credit","</span></small><br/><br/>").
		$this->mefField("","bodytext","</p>").
		
		$this->mefField('<p>'.$this->pi_getLL("doc_joint","[doc_joint]"),"document",'</P>').
		'<DIV class="actInfosComp">'.
		$this->mefField('',"author",'').
		//$this->mefField('&nbsp;&nbsp;<a href="mailto:',"email",'"><img src="'.$this->ChemImg.'mailto.gif" class="picto" alt="bouton mail" /></A>').
		//$this->mefField('<BR/>fin de parution le ',"endtime","").
		'</DIV>'.
		'<P>'.str_replace('"><img',"#Anc".$this->internal["currentRow"]["uid"].'"><img',$this->pi_list_linkSingle($this->pi_getLL("go_back","[go_back]"),0)).'</P></div>'.
		$this->pi_getEditPanel();
	
		return $content;
	}	
	
	/* Mise en forme des champs, si non vides, non=0 ou non faux
	*/
	function mefField($balD,$fN,$balF="") {
		$fieldVT=$this->getFieldContent($fN);
		if ($fieldVT !="" || $fieldVT!=0) return $balD.$fieldVT.$balF;
		}
	/**
	 * [R�up�ation et traitement des champs]
	 */
	function getFieldContent($fN)	{
	  $fieldValue=$this->internal["currentRow"][$fN];
//		debug($fN);
		switch($fN) {
			case "uid":
				return $this->pi_list_linkSingle($fieldValue,$this->internal["currentRow"]["uid"],1);	// The "1" means that the display of single items is CACHED! Set to zero to disable caching.
			break;
			
			case "small_img":
			case "big_img":
						if (file_exists($this->upload_img_folder.'/'.$fieldValue) && $fieldValue!="") {
							 // contruction du tableau du conf de l'image
							 // car utilisation de la m�hode cObj->IMAGE($conf) pour redimensionnement auto
							 // les param�res addistionnels (Align et maxW notamment, sont plac�s dans le ext_typoscript_setup.txt) 
							$ConfI["file"] = $this->upload_img_folder.'/'.$fieldValue;
							$ConfI["file."]["maxW"] = $this->conf[$fN."MaxWidth"];
							
							return str_replace(">",' class="actu'.$fN.'">',$this->cObj->IMAGE($ConfI));
							//return '<img src="'.$this->upload_img_folder.'/'.$fieldValue.'" align="left" border="0">';
							// voir http://typo3api.ueckermann.de/classtslib__cObj.html#a69 pour plus d'info sue les param
						}
						elseif ($fN=="small_img") 
						{
							//return '<img src="'.$this->conf["extCurDir"].'fleche_puce.gif" class="picto" />';
							// Diabne veut plus du picto
							return "";
						}
						else return "";
						
			break;
			
			case "abstract":
			case "bodytext":
				//return $this->pi_RTEcssText($fieldValue);
				/*
				modifications apport�s au traitements de la m�hode getFieldContent($fN)
				la ligne d'origine ci-dessus est g��� automatiquement, mais dans ce cas, les liens (entres autres) ins�� avec le RTE
				ne sont pas affich� dans le FE correctement bien qu'ils soient enregistr�.
				Pour que le changement ci-dessous fonctionne, il faut l'associer �la directive plac� dans le gabarit principal suivante:
				plugin.tx_macledextensionsansunderscores_pi1.RTEcontent_stdWrap.parseFunc < tt_content.text.20.parseFunc */
				return $this->cObj->stdWrap($this->pi_RTEcssText($fieldValue), $this->conf['RTEcontent_stdWrap.']);
			break;
			
			case "starttime":
				if ($fieldValue=="0") $fieldValue=$this->internal["currentRow"]["tstamp"];
			case "tstamp":
			case "endtime":
				if ($fieldValue!="0") return (getDateF($fieldValue));
			break;

			case "author":
				$fieldValue= txRecupLib("fe_users","uid","name",$fieldValue);
				if (!$fieldValue) { // si pas d'auteur interne saisit, renvoie le be_user cr�teur
					$fieldValue=txRecupLib("be_users","uid","realName",$this->internal["currentRow"]["cruser_id"]);
					}
				return ($fieldValue ? "Auteur : ".$fieldValue:"");
			break;
			
			case "email":
				$fieldValue= txRecupLib("fe_users","uid","email",$this->internal["currentRow"]["author"]);
				// si pas de mail et que l'auteur interne n'existe pas non plus, on renvoe le mail du be_user createur	
				if (!$fieldValue && !txRecupLib("fe_users","uid","name",$this->internal["currentRow"]["author"])) {
					$fieldValue=txRecupLib("be_users","uid","email",$this->internal["currentRow"]["cruser_id"]);
					}
				return ($fieldValue ? $fieldValue:"");
			break;
			
			case "document":
				if ($fieldValue!="") {
					$CfN=$this->upload_doc_folder.'/'.$fieldValue;
					
					$fieldValue.='<span class="actdoc">';
					$fieldValue.=DFSIL($CfN);
					$fieldValue.='&nbsp;<a href="'.$CfN.'" target="_blank"><img src="'.$this->ChemImg.'telecharger.gif" class="picto" title="'.$this->pi_getLL("download","[download]").'"></a></span>';
				}
				return $fieldValue;
			break;
				
			case "bimg_credit":
				if ($fieldValue!="") {
					$fieldValue=$this->pi_getLL("CredPhot","[(C) Photo]").$fieldValue;
				}
				return $fieldValue;
			break;
				
			default:
				return $fieldValue;
			break;
		}
	}
	
	function retTLDarbo($pido,$indent=0,$txt_path="") {
		$reple=msq("SELECT tt_content.pid,pages.uid,pages.title from tt_content,pages WHERE pages.uid=$pido AND tt_content.list_type='vm19_news_pi1' AND tt_content.hidden !=1 AND tt_content.deleted !=1 AND tt_content.pid=pages.uid AND pages.hidden !=1 AND pages.deleted !=1 GROUP BY tt_content.pid ORDER BY pages.sorting,pages.title");

		if (mysql_num_rows($reple)>0) {
			$rwple=mysql_fetch_array($reple);
			//$this->tbemplact["'".$rwple[0]."'"]=(in_array("'".$rwple[0]."'",$_SESSION['datafvmnews']['emplact']) ? "#SEL#": "").($indent>2 ?  str_repeat("&nbsp;|&nbsp;&nbsp;",$indent-2) : "")."&nbsp;|--".$rwple[2];
			// sans les guillements
			$this->tbemplact["'".$rwple[0]."'"]=(in_array("'$rwple[0]'",$_SESSION['datafvmnews']['emplact']) ? "#SEL#": "").($indent>2 ?  str_repeat("&nbsp;|&nbsp;&nbsp;",$indent-2) : "")."&nbsp;|--".$rwple[2];
			$txt_path.=($txt_path !="" ? " -> " : "").$rwple[2];
			$this->tbtxtpath[$rwple[0]]=$txt_path;
			}
		$indent++; // pour les enfants
		// chercje les enfants
		$reppe=msq("SELECT pages.uid from pages WHERE pages.pid=$pido AND pages.hidden !=1 AND pages.deleted !=1 ORDER BY pages.sorting,pages.title");
		while ($rwppe=mysql_fetch_array($reppe)) { 
			//if ($this->pi_getPageLink($rwppe[0]))
				$this->retTLDarbo($rwppe[0],$indent,$txt_path);
		}
		
	}	// fin methode
	
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
	
	function buttdisphiddarch() {
		if ($this->cObj->data['layout']!=1) { // on n'est pas en mode archive forcée
			if ($this->arch_mode) {
				$Moargt['arch_mode']=0;
				$label=$this->pi_getLL("arch_hidd","masquer les archives");
			} else {
				$Moargt['arch_mode']="1debform"; // on met pas le #debform sinon il url_encodé
				$label=$this->pi_getLL("arch_disp","aff les archives");
			}
		return('<DIV id="tx-vm19news-pi1_arch">'.str_replace("1debform","1#debform",$this->pi_list_linkSingle($label,1,0,$Moargt)).'</DIV>');
		} // fin si pas archive forcée

	}
}


if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/vm19_news/pi1/class.tx_vm19news_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/vm19_news/pi1/class.tx_vm19news_pi1.php"]);
}

?>
