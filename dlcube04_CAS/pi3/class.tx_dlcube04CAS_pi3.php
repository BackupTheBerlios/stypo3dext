<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2004 Vincent (admin, celui à la pioche) (webtech@haras-nationaux.fr)
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
***********************************************         	****************/
/** 
 * Plugin 'creation de compte' for the 'dlcube04_CAS' extension.
 * Plugin de creationde compte CAS
 *
 * @author	Guillaume Tessier <gui.tessier@wanadoo.fr>
 */


require_once(PATH_tslib."class.tslib_pibase.php");
include_once("typo3conf/ext/dlcube_hn_01/class.WebservicesCompte.php");

class tx_dlcube04CAS_pi3 extends tslib_pibase {
	var $prefixId = "tx_dlcube04CAS_pi3";		// Same as class name
	var $scriptRelPath = "pi3/class.tx_dlcube04CAS_pi3.php";	// Path to this script relative to the extension dir.
	var $extKey = "dlcube04_CAS";	// The extension key.
	
	/**
	 * Plug-in pour création de comptes CAS
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$this->pi_loadLL();
		session_start();
		
		$content='';
		if(!isset($this->piVars["action"])){
			$content = $this->getFormulaireVide();
		}
		else if (isset($this->piVars["action"]) && $this->piVars["action"] == "insert"){
			if(!$this->checkLogin($this->piVars["login"])){
				$content='<h3>'.nl2br(htmlspecialchars($this->pi_getLL("error_login_format"))).'<br/></h3>';
				$content .=$this->getFormulaireVide();
				return $this->pi_wrapInBaseClass($content);
			}
			
			if($this->piVars["action"] == "" || $this->piVars["login"] == "" || $this->piVars["passwd1"] == "" || $this->piVars["passwd2"] == "" ){
				$content='<h3>'.htmlspecialchars($this->pi_getLL("error_missing_field")).'<br/></h3>';
				$content .=$this->getFormulaireVide();
			}
			else if(!$this->checkLogin($this->piVars["login"])){
				$content='<h3>'.nl2br(htmlspecialchars($this->pi_getLL("error_login_format"))).'<br/></h3>';
				$content .=$this->getFormulaireVide();
			}
			else if(strlen($this->piVars["passwd1"]) < 6 || strlen($this->piVars["login"]) < 6){
				$content='<h3>'.htmlspecialchars($this->pi_getLL("error_min_field")).'<br/></h3>';
				$content .=$this->getFormulaireVide();
			}
			else if($this->piVars["passwd1"] != $this->piVars["passwd2"]){
				$content='<h3>'.htmlspecialchars($this->pi_getLL("error_passwd_not_match")).'<br/></h3>';
				$content .=$this->getFormulaireVide();
			}
			else{
				$ws = new WebservicesCompte();
				if(!$ws->connectServices()){
					$content="ERROR:".$ws->getErrorMessage();
				}
				else{
					
					$md5 = "{MD5}".base64_encode(mhash(MHASH_MD5,$this->piVars["passwd1"]));
					$param[] = array(
					"login"=> $this->piVars["login"],
					"password"=> $md5,
					"ctx"=> null);
					$result = $ws->createCompte($param);
					if($result["createCompteCasForPortailReturn"] == "0"){
						$content = $this->success();
					}
					else if($result["createCompteCasForPortailReturn"] == "1945"){
						$content = $this->errorLoginExiste();
					}
					else if($result["createCompteCasForPortailReturn"] == "1944"){
						$content = "ERREUR SERVEUR LOGIN ET OU MOT DE PASSE VIDENT<br/>";
						/*$content.="--------TRACE-------<br/>";
						$content.="login:".$this->piVars["login"]."<br/>";
						$content.="passwd:".$this->piVars["passwd1"]."<br/>";
						$content.="passwd_md5:".$md5."<br/>";
						$content.="resultat du WS:".$result["createCompteCasForPortailReturn"];*/
					}
					else{
						$content = "ERREUR INCONNUE<br/>";
						/*$content.="--------TRACE-------<br/>";
						$content.="login:".$this->piVars["login"]."<br/>";
						$content.="passwd:".$this->piVars["passwd1"]."<br/>";
						$content.="passwd_md5:".$md5."<br/>";
						$content.="resultat du WS:".$result["createCompteCasForPortailReturn"];*/
					}
				}
			}
		}
		else
			$content = "<h1>error</h1>";
		return $this->pi_wrapInBaseClass($content);
	}
	
	/**
	 * Ecrit un message d'erreur	
	 * @return void
	 */
	function errorLoginExiste(){
		$content='
			<div><h3>'.htmlspecialchars($this->pi_getLL("txt_error_login")).'<br/></h3><hr/></div>';
		$content .=$this->getFormulaireVide();
		return $content;
	}
	
	function checkLogin($login){
		for($i=0; $i < strlen($login);$i++){
			if(!ereg("[A-Za-z]|[0-9]|\.|\-|_]",$login[$i])){
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Inscrit un message de félicitation	
	 * @return void
	 */
	function success(){
		$content='
			<div><h3>'.$this->pi_getLL("txt_success_create").' <a href="'.$this->pi_getPageLink("3677").'">'.htmlspecialchars($this->pi_getLL("label_clic_ici")).'</a><br/></h3><hr/></div>';
		return $content;
	}
	
	/**
	 * ecrit un nouveau formulaire	
	 * @return void
	 */
	function getFormulaireVide(){
		$content='
			<script>
				function check(){
					var monLogin = document.formCrea["'.$this->prefixId.'[login]'.'"].value;
					
					var reg1 = /[A-Z]|[0-9]|-|_|\./;
					
					for(i=0;i < monLogin.length;i++){
						alert(reg1.test(monLogin[i]));
						if (!reg1.test(monLogin[i])){
							alert("SEUL LES CARACTERES SUIVANTS SONT AUTORISES POUR LE CHAMP IDENTIFIANT:\n0 à 9\n A à Z\n . , _ , -");
							return false;
						}
					}
					
					return true;
				}
				
				function checkSub(){
					document.formCrea.submit();
				}
			</script>
			<div>'.nl2br(htmlspecialchars($this->pi_getLL("desc_formulaire"))).'<br/></div>
			<hr/>
			<form action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id,'',array("no_cache"=>1)).'" method="POST" name="formCrea">
				<input type="hidden" name="no_cache" value="1">
				<input type="hidden" name="'.$this->prefixId.'[action]" value="insert">
			<table width="400px">
				<tr>
				<td><p><strong>'.htmlspecialchars($this->pi_getLL("login")).'</p></strong></td>
				<td>
					<input type="text" name="'.$this->prefixId.'[login]" size="23" maxlength="20">
				</td>
				</tr>
				<tr>
				<td><p><strong>'.htmlspecialchars($this->pi_getLL("passwd")).'</p></strong></td>
				<td>
					<input type="password" name="'.$this->prefixId.'[passwd1]" size="23">
				</td>
				</tr>
				<tr>
				<td><p><strong>'.htmlspecialchars($this->pi_getLL("passwd2")).'</p></strong></td>
				<td>
					<input type="password" name="'.$this->prefixId.'[passwd2]" size="23">
				</td>
				</tr>
			</table>
			<div style="float:right;width:200px;">
				<div style="float:right;">
					<a href="javascript:history.back();" id="lienFonctionPetit" style="color:white;text-decoration:none" onclick="history.back();">'.htmlspecialchars($this->pi_getLL("back_button_label")).'</a>
				</div>
				<div style="float:left;">
					<a href="javascript:checkSub();" id="lienFonctionPetit" style="color:white;text-decoration:none" onclick="checkSub();">'.htmlspecialchars($this->pi_getLL("submit_button_label")).'</a>
				</div>
			</div>
			</form>
		';
		return $content;
	}
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube04_CAS/pi3/class.tx_dlcube04CAS_pi3.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube04_CAS/pi3/class.tx_dlcube04CAS_pi3.php"]);
}

?>
