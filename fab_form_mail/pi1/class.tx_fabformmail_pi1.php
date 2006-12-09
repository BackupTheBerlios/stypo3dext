<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003 Your Name (your@email.com)
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
 * Plugin 'fab test' for the 'fab_form_mail' extension.
 *
 * @author	Your Name <your@email.com>
 */


require_once(PATH_tslib."class.tslib_pibase.php");
require_once("class.tx_fabformmail_pagetree_pi1.php");
require_once("typo3conf/ext/vm19_toolbox/functions.php");

class tx_fabformmail_pi1 extends tslib_pibase {
	var $prefixId = "tx_fabformmail_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_fabformmail_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "fab_form_mail";	// The extension key.
	var $newabonne;
	var $fabvar;
	var $MgL="25px"; //marge gauche du contenu normal


	/**
	 * fonction principale
	 */
	function main($content,$conf) {
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		global $TYPO3_CONF_VARS;
        $this->fabvar = unserialize($TYPO3_CONF_VARS["EXT"]["extConf"]["fab_form_mail"]);

//		debug ($this->piVars[DATA]);

//		if( (isset($this->piVars['submit_button'])) && ( (strlen($this->piVars['DATA']['email'])>0) || (strlen($this->piVars['email'])>0) ) )
// 		vire la premi�e condition: si on appuie sur entr�, $this->piVars['submit_button']="" donc � d�onne...

		if(  (strlen($this->piVars['DATA']['email'])>0) || (strlen($this->piVars['email'])>0)  )
		{
			//t3lib_div::debug($this->piVars); //Mode DEBUG

			if(strlen($this->piVars['DATA']['email'])>0)
			{
				// si formulaire soumis et email saisi
				//test de la presence de l'email dans la table
				$uid=txRecupLib("tx_fabformmail_abonne","email","uid",$this->piVars['DATA']['email']," AND deleted!=1");
				if($uid)
				{
					//utilisateur present dans la table == modif de l'abonnement
					$content = '<h2>'.$this->pi_getLL("subs_modif","Subscription Modification").'</h2>';
				}
				else
				{
					//utilisateur non present == inscription
					$content.='<h2>'.$this->pi_getLL("subs_new","New subscription").'</h2>';
					$this->newabonne=true; // 
				}
				//formulaire
				$content .= $this->affichageFormulaire($uid, $this->piVars['DATA']['email']);

			}
			elseif(strlen($this->piVars['email'])>0) // on vient du formulaire de selection des infos d'abonnement
			{
				if($this->piVars['formulaire']=="abonne") // on a soumis l'abonnement
				{
					// enl�e les pages redondantes (si une page parente est deja select., on vire)
					$this->piVars['DATA']['uidPages']=$this->CleanPageArbo($this->piVars['DATA']['uidPages']);

					// on met un formulaire, pour pouvoir revenir en arri�e
					// le retour en arri�e valide le formulaire avec un javascript
					$content= '<FORM action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'" name="'.$this->prefixId.'fname" method="POST">
	               <INPUT type="hidden" name="'.$this->prefixId.'[DATA][email]" value="'.$this->piVars['email'].'">';

					$content .= "<h2>".$this->pi_getLL("Subscribtion","Subscription")."</h2>";
					//test validit�et recapitulation des infos choisies
					$content .= $this->affichageChoix($this->piVars['uid'], $this->piVars['email'], $this->piVars['DATA']['uidPages'], $this->piVars['DATA']['typeFrequence'], $this->piVars['DATA']['typeEtat'], $this->piVars['DATA']['typeExtension'],$this->piVars['DATA']['hidden'],$this->piVars['DATA']['newsletter']);
					//enregistrement
					$content .= $this->enregistrementAbonnement($this->piVars['uid'], $this->piVars['email'], $this->piVars['DATA']['uidPages'], $this->piVars['DATA']['typeFrequence'], $this->piVars['DATA']['typeEtat'], $this->piVars['DATA']['typeExtension'],$this->piVars['DATA']['hidden'],$this->piVars['DATA']['newsletter']);
					$content.="</form>";


				}
				elseif($this->piVars['formulaire']=="desabonne") // on a choisi de se d�abonner
				{
					$content.= $this->suppressionAbonnement($this->piVars['uid']);
				}
			}
		}
		else // on est arriv�sur la page pour la premi�e fois: on demande la saisie du mail
		{
			//Affichage de l'invite de connexion par
			//saisie de l'email
//			$content = $this->insertJavaScript();
			$content .= $this->EnterMailForm();
		}

		return $this->pi_wrapInBaseClass($content);
	}


	/**
	 * affichage de l'invite de connexion permettant de saisir son mail
	 */
	function EnterMailForm() {
			$content=
                                '<h2>'.$this->pi_getLL("identification","Identification").'</h2>
		                        <P>'.$this->pi_getLL("ident_text","preferences").'</P>
                                <FORM action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'" name="'.$this->prefixId.'fname" method="POST">
								'.$this->pi_getLL("email","Email").'
                                <INPUT type="text" name="'.$this->prefixId.'[DATA][email]">&nbsp;&nbsp;&nbsp;&nbsp;
                                <INPUT '.$this->pi_classParam("submit-button").' type="submit" name="'.$this->prefixId.'[submit_button]" value="'.$this->pi_getLL("editprefs","Edit preferences").'">
                                </FORM>
                                ';
			return $content;
	}

	/**
	 * fonction permettant de retourner l'arborescence du site
	 *
	 *------------------------------------------------------------
	 *  $TYPO3_CONF_VARS["EXT"]["extConf"]["fab_form_mail"][racine]
	 *  	\determine la racine de l'arbo (0 par defaut)
	 *
         *  $TYPO3_CONF_VARS["EXT"]["extConf"]["fab_form_mail"][profondeur]
         *      \determine la profondeur d'affichage (999 par defaut)
	 *
	 **/
	function arbo($selection) {
		//recuperation des vars 'profondeur' et 'racine' pour construction arbo
                                                                                                                                                             
                //recuperation de l'architecture du site
                $arboObj = new t3lib_pageTree();
                $arbo = $arboObj->getTree($this->fabvar[racine],$this->fabvar[profondeur],"","");
                                                                                                                                                             

                                /****
                                **
                                **
                                ** architecture du tableau d'arborecence
                                **
                                **      $tab => array(
                                **                      row => array(           //infos de la base 'pages'
                                **                              uid,
                                **                              title,
                                **                              doktype,
                                **                              php_tree_stop
                                **                              ),
                                **                      HTML,                   //image
                                **                      invertedDepth,          //niveau de profondeur
                                **                      blanLineCode            //code pour afficher l'arbo
                                **                                              //(,blank,line...) avant l'affichage de l'icone
                                **              );
                                **
                                **
                                ****/

		$arboSTR = "";

		$tabselection = explode(",",$selection);
		$checked = "";
		
		$arboSTR.='<style type="text/css">
				INPUT {padding-bottom:0px; padding-top:0px; margin-bottom:0px; margin-top:0px}
				TD {padding-bottom:0px; padding-top:0px; margin-bottom:0px; margin-top:0px}
			  </style>';
		
		$arboSTR .= '<table border="0" cellspacing="0" cellpadding="0" style="margin-left:15px;">';
		// on rajoute un case �cocher en haut pour s�ectionner tout le site en un seul click
		// si nouvel abonnement
		if (in_array($this->fabvar[racine],$tabselection) || $this->newabonne) $checked = "checked";
		$arboSTR .= '<tr valign="top"><td valign="top" nowrap>';
		$arboSTR .= '<input type="checkbox" name="'.$this->prefixId.'[DATA][uidPages][]" value="'.$this->fabvar[racine].'" '.$checked.'>';
        $arboSTR .= '<img src="typo3/gfx/i/pages_mountpoint.gif">'.'&nbsp;<b>'.$this->pi_getLL("root","Root")."</B><br><small>".$this->pi_getLL("rootrmk","Rootrmk")."</small></td></tr>\n";

            for($i=0; $i<count($arbo); $i++)
                {
			//on check si l'utilisateur possede deja ses preferences
			if($arbo[$i][row][doktype] == 1) { //254 = repertoire sysme; 2 raccourci
				$checked = "";
			/* On remplace tout �...
				for($j=0; $j<count($tabselection); $j++)
	                                if($tabselection[$j]==$arbo[$i][row][uid])
        		       		{
                	                        $checked = "checked";
                        	                break;
	                                }
			par � :  */
				if (in_array($arbo[$i][row][uid],$tabselection)) $checked = "checked";

				$arboSTR .= '<tr><td nowrap align="top">';
				
				$arboSTR .= '<input type="checkbox" name="'.$this->prefixId.'[DATA][uidPages][]" value="'.$arbo[$i][row][uid].'" '.$checked.'>';
                $arboSTR .= $arbo[$i][HTML].'&nbsp;'.$arbo[$i][row][title]."</td></tr>\n";
			}
                }
		$arboSTR .= "</table><P>";
	return $arboSTR;
	}

	/**
	 * permet de proposer les types d'extensions pour l'abonnement
	 **/
	function choixExtension($extension,$dispOnly=false) {
		
		$typeExtension['tt_content'] = $this->pi_getLL("tt_content","content");
		$typeExtension['vm19docsbase'] = $this->pi_getLL("vm19docsbase","documents");
		//$typeExtension['vm19keynumbers'] = $this->pi_getLL("vm19keynumbers","key_numbers");
		$typeExtension['vm19news'] = $this->pi_getLL("vm19news","19news");
		$typeExtension['vm19_hnlinks'] = $this->pi_getLL("vm19_hnlinks","vm19_hnlinks");
		$typeExtension['vm19_hn_reglementation'] = $this->pi_getLL("vm19_hn_reglementation","vm19_hn_reglementation");

		$typeExtensionSTR = '<H3>'.$this->pi_getLL("conttype_news_title","content type").'</H3>';
		$typeExtensionSTR .='<div style="margin-left:'.$this->MgL.'">';

		$tabextension = explode(",",$extension);

		foreach ($typeExtension as $key=>$name)
		{
		  $checked = "";
			if (in_array($key,$tabextension) || $this->newabonne) $checked = "checked"; // si extension s�ectionn� ou que nouvel abonnement
			if (!$dispOnly) {
				$typeExtensionSTR .= '<input type="checkbox" name="'.$this->prefixId.'[DATA][typeExtension][]" value="'.$key.'" '.$checked.'>'.$name.'<br>';
			}
			else {
				if ($checked) $typeExtensionSTR .="- ".$name.'<br>'; 
			}
		}
		$typeExtensionSTR .="</div>";
		return $typeExtensionSTR;
	}

        /**
         * permet de proposer le choix entre les nouveautes ou les infos modifiee
         **/
        function choixEtat($etat,$dispOnly=false) {
                $typeEtat[0] = array($this->pi_getLL("newsonly","news_only"),0);
                $typeEtat[1] = array($this->pi_getLL("modif","modif"),1);

                $typeEtatSTR = 	'<H3>'.$this->pi_getLL("contmodif_news_title","Modification type selection").'</H3>';
		$typeEtatSTR .='<div style="margin-left:'.$this->MgL.'">';

		$checked="checked";
                for($i=0; $i<count($typeEtat); $i++)
		{
			//on check si l'utilisateur possede deja ses preferences
                        if($typeEtat[$i][1] == $etat)
                                $checked = "checked";
                        else
                                $checked = "";

                        if (!$dispOnly) {
						$typeEtatSTR .= '<input type="radio" name="'.$this->prefixId.'[DATA][typeEtat]" value="'.$typeEtat[$i][1].'" '.$checked.'>'.$typeEtat[$i][0].'<br>';
						} else { 
							if ($checked=="checked") $typeEtatSTR .= '- '.$typeEtat[$i][0].'<br>'; 
						}
			$checked="";
		}
		$typeEtatSTR .="</div>";
        return $typeEtatSTR;
        }

        /**
         * permet de proposer la frequence d'envoi
         **/
        function choixFrequence($frequence,$dispOnly=false) {
        	$typeFrequence[0] = array($this->pi_getLL("evday","every_day"),1);
			$typeFrequence[1] = array($this->pi_getLL("evweek","every_week"),7);
			$typeFrequence[2] = array($this->pi_getLL("ev2week","every_2_weeks"),14);
			$typeFrequence[3] = array($this->pi_getLL("evmonth","every_month"),28);
                                                                                                                                                             
	        $typeFrequenceSTR = '<H3>'.$this->pi_getLL("periodicity_title","Periocity").'</H3>';
			$typeFrequenceSTR .='<div style="margin-left:'.$this->MgL.'">';

            for($i=0; $i<count($typeFrequence); $i++)
				{
			//on check si l'utilisateur possede deja ses preferences
			if($typeFrequence[$i][1] == $frequence)
				$checked = "checked";
			else $checked = "";
		
            if (!$dispOnly) {
				$typeFrequenceSTR .= '<input type="radio" name="'.$this->prefixId.'[DATA][typeFrequence]" value="'.$typeFrequence[$i][1].'" '.$checked.'>'.$typeFrequence[$i][0].'<br>';
			} else
				{if ($checked == "checked") $typeFrequenceSTR .= '- '.$typeFrequence[$i][0].'<br>';
			}
		}
			$typeFrequenceSTR .="</div>";
	        return $typeFrequenceSTR;
        }


        /**
         * permet de proposer un d�abonnement temporaire
         **/
        function choixDesTmp($DesTmp,$dispOnly=false) {
                                                                          
	        $typeFrequenceSTR = '<H3>'.$this->pi_getLL("unsustmp","Unsuscribe temporarly").'</H3>';

			//on check si l'utilisateur possede deja ses preferences
			if($DesTmp==1)
				$checked = "checked";
			else $checked = "";
		
            if (!$dispOnly) {
				$typeFrequenceSTR .= '<input type="checkbox" name="'.$this->prefixId.'[DATA][hidden]" value="1" '.$checked.'>'.$this->pi_getLL("unsustmpdsk","Unsuscribe temporarly desc").'<br>';
			} else
				{if ($checked == "checked") {
					return $typeFrequenceSTR = '<H3>'.$this->pi_getLL("unsustmp","Unsuscribe temporarly").$this->pi_getLL("activ","active").'</H3><FONT COLOR="RED"><DIV  style="margin-left:'.$this->MgL.'">'.$this->pi_getLL("unsustmpdsk2","desactivated").'</FONT></DIV>';;
					} else return "";
			}
	        return $typeFrequenceSTR;
        }

	/**
	 * affichage du formulaire d'abonnement
	 **/
	function affichageFormulaire($uid, $email) {
		
		//initialisation des variables servant a remplir le form en cas de maj
		$typefrequence = 28;
		$typeetat = 0;
		$typeextension = "";
		$uidpages = "";
		//on va recuperer les infos si l'uid existe
		if($uid)
		{
			$infos = $this->reqInfos($uid);
			/* $infos[0] = typefrequence
			 * $infos[1] = typeetat
			 * $infos[2] = typeextension
			 * $infos[3] = uidpages
			 * $infos[4] = hidden , ie si deactiv�
			 * $infos[5] = abonn��la newslstter
			 */
	        	$typefrequence = $infos[0];
			$typeetat = $infos[1];
			$typeextension = $infos[2];
			$uidpages = $infos[3];
			$desTmp = $infos[4];
			$newsLOK=($infos[5]==1 ? "checked" : "");
		}

		$content = '<FORM action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'" method="POST">';

		$content .= '
			<input type="hidden" name="'.$this->prefixId.'[formulaire]" value="abonne">
			<input type="hidden" name="'.$this->prefixId.'[email]" value="'.$email.'">
			<input type="hidden" name="'.$this->prefixId.'[uid]" value="'.$uid.'">
			';
		$content.= '<P>'.$this->pi_getLL("email","email").'<b>'.$email.'</b></P>';
		$content.= '<P><BR>'.$this->pi_getLL("click","click").'<b><a href="#PG">'.$this->pi_getLL("here","HERE")."</a></b>".$this->pi_getLL("prefgen","to access general preferences").'</P>';

		//Abonnement �la newletter hn
		$content.='<H3>'.$this->pi_getLL("newsletter_abonn_title","Newsletter").'</H3>';
		$content.= '<input type="checkbox" name="'.$this->prefixId.'[DATA][newsletter]" value="1" '.$newsLOK.'>'.$this->pi_getLL("newsletterA","Abonnmt newsletter").'<br>';
		
		//affichage de l'arbo (le tableau 'uidPages' est retourne contenant les pages selectionnees)
		$content.='<H3>'.$this->pi_getLL("arbo_news_title","Tree selection").'</H3>';
		$content.='
		<P>'.$this->pi_getLL("arbo_news_text","select your sections").':<br><br></P>';

		$content .= $this->arbo($uidpages);
		$content.='<a name="PG">';

		//affichage du choix des contenus
		$content.='<P>'.$this->choixExtension($typeextension).'</P>';

		//affichage du type de nouveaut�(modification ou nouveau)
		$content.= '<P>'.$this->choixEtat($typeetat).'</P>';

		//affichage de la periodicite de reception
		$content.= '<P>'.$this->choixFrequence($typefrequence).'</P>';
		
		$content.= '<P>'.$this->choixDesTmp($desTmp).'</P>';

		$content.= '<BR/><BR/>
		<input '.$this->pi_classParam("submit-button").' type="reset" name="'.$this->prefixId.'[reset_button]" value="'.$this->pi_getLL("cancel","cancel").'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input '.$this->pi_classParam("submit-button").' type="submit" name="'.$this->prefixId.'[submit_button]" value="'.$this->pi_getLL("subscribe","subscribe").'">
		</FORM>';

		//demande de desabonnement
		if($uid)
		{
			$content.= '<FORM action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'" method="POST">
			<input type="hidden" name="'.$this->prefixId.'[formulaire]" value="desabonne">
			<input type="hidden" name="'.$this->prefixId.'[email]" value="'.$email.'">
			<input type="hidden" name="'.$this->prefixId.'[uid]" value="'.$uid.'">
			<input '.$this->pi_classParam("submit-button").' type="submit" value="'.$this->pi_getLL("Unsubscribe","Unsubscribe").'">
			</form>';
		}

		return $content;
	}

	/**
	 * Nettoyage de l'arbo: envoie un tableau contenant les uid de pages
	 * si l'une d'elle est fille (ou petite fille) d'une page deja dans le tableau, on la vire du tableau
	 * on renvoie ce meme tableau nettoy�
	 **/
	function CleanPageArbo($tab) {
//		$TabTemp = array();
		for($i=0; $i<count($tab); $i++)
		{
			if($this->PageParenteDejaSel($tab[$i], $tab))
				$TabTemp[]=$tab[$i];
			//	array_push($TabTemp, $tab[$i]);
		}
		return $TabTemp;
	}

	/**
	 * Test de la presence de parent dans la selection
	 // retourne faux si un parent de la page est d��m�oris�
	 // vrai sinon
	 **/
	function PageParenteDejaSel($page,$tab) {
	
        //recuperation des vars 'profondeur' et 'racine' pour construction arbo
		$pid = $page;
		
		$tabSQL = array();	//liste des pid
		if ($this->fabvar[racine]!=$pid) { // si le pid demand�est dif de la racine
			do{
				$query = 'SELECT pid from pages where uid='.$pid.' AND uid!=pid';
				$res = mysql(TYPO3_db,$query);
				if (mysql_num_rows($res)>0) {
					$row = mysql_fetch_row($res);
					$pid = $row[0];
					array_push($tabSQL, $pid);
					}
				else $pid=$this->fabvar[racine];
			}while($pid!=$this->fabvar[racine]);
			//echo "<hr>";
	
			for($i=0; $i<count($tabSQL); $i++)
			{
				if(in_array($tabSQL[$i],$tab))
					return false;
			}
		} // fin si 
		return true;
	}

	/**
	 * enregistrement des choix
	 uidPages=tableau contenant les uid des pages s�ectionn�s
	 **/
	function enregistrementAbonnement($uid, $email, $uidPages, $typeFrequence, $typeEtat, $typeExtension,$desTmp,$newsletter) {

		$typeExtension = (is_array($typeExtension) ? implode(",",$typeExtension) : "");
	    $uidPages = (is_array($uidPages) ? implode(",",$uidPages): "");
		$desTmp=($desTmp==1 ? 1 : 0);
		
		if ($newsletter!=1) $newsletter=0;

		if($uid)
		{
			//modification
                                $query = "UPDATE tx_fabformmail_abonne SET 
				typefrequence = '$typeFrequence',
				hidden=$desTmp,
				newsletter=$newsletter,
				typeetat = '$typeEtat',
				typeextension = '$typeExtension',
				uidpages = '$uidPages'
                                WHERE uid = $uid";
                                $res = mysql(TYPO3_db, $query);
                                echo mysql_error();
                                #header ('Location: '.t3lib_div::locationHeaderUrl('index.php?id=72'));
		}
		else
		{
			//creation
			//insertion dans la base
				$lastsentdate=time()-($typeFrequence*24*3600);
				$email=addslashes($email);
				$query = "INSERT INTO tx_fabformmail_abonne (email,pid,typefrequence,typeetat,typeextension,uidpages,newsletter,hidden,lastsentdate)
				VALUES(
				'$email',
				'".$GLOBALS['TSFE']->id."',
				'$typeFrequence',
				'$typeEtat',
				'$typeExtension',
				'$uidPages',
				$newsletter,
				$desTmp,$lastsentdate)";
//				echo debug ($query);
				$res = mysql(TYPO3_db, $query);
				echo mysql_error();
				#header ('Location: '.t3lib_div::locationHeaderUrl('index.php?id=72'));
		}
		
				if (!$this->err) { 
					return "<BR><b>".$this->pi_getLL("prefsaved","Preferences saved")."</b>".
					 "<BR>".$this->pi_getLL("click","Click").'<b><a href="javascript:document.'.$this->prefixId.'fname.submit()">'.$this->pi_getLL("here","HERE")."</a></b>".$this->pi_getLL("tomod","to modify them");
					}
	}

        /**
         * suppression d'un abonnement
         **/
        function suppressionAbonnement($uid) {
                                                                                                                                                             
		$query = 'UPDATE tx_fabformmail_abonne SET deleted=1 WHERE uid = '.$uid;
//		$query = 'DELETE FROM tx_fabformmail_abonne WHERE uid = '.$uid;
                $res = mysql(TYPO3_db, $query);
		echo mysql_error();
                #header ('Location: '.t3lib_div::locationHeaderUrl('index.php?id=72'));
                return "<h3>".$this->pi_getLL("unsuslbl1","Unsuscribe confirmed")."</H3>";
	}

	/**
	 * affichage des preferences
	 **/
	function affichageChoix($uid, $email, $uidPages, $typeFrequence, $typeEtat, $typeExtension,$desTmp,$newsletter) {

		// tests de validit�
	    if(count($typeExtension)==0)
			$str.= $this->pi_getLL("err1extmin","1 ext minimum")."<br><br>";
			
        if(count($uidPages)==0)
        	$str.= $this->pi_getLL("err1pagemin","1 page minimum")."<br><br>";
			
		if ($str!="") { // il y a une erreur
		
			$str="<H3>".$this->pi_getLL("Error","Error")."</h3>".$str.'<a href="javascript:document.'.$this->prefixId.'fname.submit()">'.$this->pi_getLL("back","back")."</a>";
	
			$this->err=true;
		
			return $str;
		}
		
		// pas d'erreurs
		
		$str = $this->pi_getLL("PrefSel","Preferences");
		// affiche en premier si d�abonnement temporaire
		$str.= $this->choixDesTmp($desTmp,true);
		$str.='<h3>'.($newsletter==1 ? $this->pi_getLL("AbonnNLOK","GABONNE") :$this->pi_getLL("AbonnNLKO","PAS GABONNE") ) .'</h3>';
		$str .= '<h3>'.$this->pi_getLL("RubSurv","pages changed").'</h3>';
		$str .='<div style="margin-left:'.$this->MgL.'">';
		if (in_array($this->fabvar[racine],$uidPages))
		{
			$str .= "<B>".$this->pi_getLL("RootSelected","All pages !")."</B>"; }
		else {
		
			$req='SELECT title from pages WHERE uid IN ('.implode(",",$uidPages).')';
			
			$res = mysql(TYPO3_db, $req);
			while($row = mysql_fetch_row($res))
			{
				$str.=" - ".$row[0]."<br>";
			}
		} // fin si pas racine s�ectionn�
		$str .= "</DIV>";
		
		$str .= $this->choixExtension(implode(",",$typeExtension),true);
		
		$str .= $this->choixEtat($typeEtat,true);
		
		$str .= $this->choixFrequence($typeFrequence,true);

		$str.="<br><br>".$this->pi_getLL("rmk2","No change, no mail");
		return $str."<br><br>";
	}

	/**
	 * selection des infos sur la table
	 **/
	function reqInfos($uid) {
		$query = 'SELECT typefrequence, typeetat, typeextension, uidpages, hidden,newsletter  FROM tx_fabformmail_abonne
			WHERE uid = '.$uid;
		$res = mysql(TYPO3_db,$query);
		if(mysql_num_rows($res))
			return mysql_fetch_row($res);
		else
			return false;
	}

	/**
	 * fonction de calcul de dimension d'un tableau
	 */
	function comptedim($array) {
		static $dimcount = 1;
		if (is_array(reset($array))) {
			$dimcount++;
			$return = $this->comptedim(reset($array));
		} else {
			$return = $dimcount;
		}
		return $return;
	}

	/**
 * permet d'inserer les fonctions javascripts
 **/
function insertJavaScript() {
	$str = '<script language="javascript">
		<!--
		function verifEmail(email) {
alert();
			// V�ifie si un Email est correct
			var saisie,saisie1,saisie2,saisie3;
			var mescouilles;
			saisie = email.indexOf("@");
			if (saisie!=(-1)) {
				longueur = email.length;
				saisie1 = email.substring(saisie+1,longueur);
				saisie2 = saisie1.indexOf(".");
				longueur = saisie1.length;
				saisie3 = saisie1.substring(saisie2+1,longueur);
					if (saisie2==(-1))
					{
						alert("L\'email est invalide");
						return false;
					}
					else {
						if (saisie3 == "")
						{
							alert("L\'email est invalide.");
							return false;
						}
						else
							return true;
					}
			}
			else
			{
				alert("L\'email est invalide..");
				return false;
			}
		}
		-->
		</script>';
	return $str;
}

} // fin definition de l'objet


if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/fab_form_mail/pi1/class.tx_fabformmail_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/fab_form_mail/pi1/class.tx_fabformmail_pi1.php"]);
	}

?>
