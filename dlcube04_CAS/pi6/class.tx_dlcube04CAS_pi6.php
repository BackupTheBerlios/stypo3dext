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
***************************************************************/
/**
 * Plugin 'FORMULAIRE_AUTH' for the 'dlcube04_CAS' extension.
 * Plugin j'ai perdu mon mot de passe
 * @author	Guillaume Tessier <gui.tessier@wanadoo.fr>
 */


require_once(PATH_tslib."class.tslib_pibase.php");
include_once("typo3conf/ext/dlcube_hn_01/class.WebservicesCompte.php");

class tx_dlcube04CAS_pi6 extends tslib_pibase {
	var $prefixId = "tx_dlcube04CAS_pi6";		// Same as class name
	var $scriptRelPath = "pi6/class.tx_dlcube04CAS_pi6.php";	// Path to this script relative to the extension dir.
	var $extKey = "dlcube04_CAS";	// The extension key.
	var $typeExecution = "prod"; /**dev|dev_ext|prod*/

	/**
	 * Plug-in pour modification du mot de passe pour CAS
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$this->pi_loadLL();

		$content = "";

		if(!isset($this->piVars["action"])){
			$content = $this->getFormulaireVide();
		}
		else if (isset($this->piVars["action"]) && $this->piVars["action"] == "calcul"){
			if($this->piVars["login"] == ""){
				$content='<h3>'.htmlspecialchars($this->pi_getLL("error_missing_field")).'<br/></h3>';
				$content .=$this->getFormulaireVide();
			}
			if(strlen($this->piVars["login"]) < 5){
				$content='<h3>'.htmlspecialchars($this->pi_getLL("error_min_field")).'<br/></h3>';
				$content .=$this->getFormulaireVide();
			}
			else{
				$param = array(
				"in0"=>$this->piVars["login"],
				"in1"=>null);
				$ws = new WebservicesCompte($this->typeExecution);
				if(!$ws->connectIdent()){
					$content="ERROR:".$ws->getErrorMessage();
					return $content;
				}

				$personne = $ws->getPersonneByLogin($param)->out;
				if($personne == "" || $personne == null){
					$content = $this->errorLoginExiste();
				}
				else{
					if($personne->coordonnees->email == ""){
						$content = $this->errorMailExiste();
					}
					else{
						$pwd = $this->calculPassword();
						$md5 = "{MD5}".base64_encode(mhash(MHASH_MD5,$pwd));
						/**
						 * Mise a jour du compte
						 */
						$paramUpdate = array(
						"in0"=>$personne->login,
						"in1"=>$md5,
						"in2"=> null);

						$wsUpdate = new WebservicesCompte($this->typeExecution);

						if(!$wsUpdate->connectServices()){
							$content="ERROR:".$ws->getErrorMessage();
						}
						else{
							$result = $wsUpdate->updateCompte($paramUpdate)->out;
							if($result == "0"){
								$this->sendMail($personne->coordonnees->email,$personne,$pwd);
								$content = $this->success();
							}
							else if($result == "1947"){
								$content = $this->errorLoginExiste();
							}
							else if($result == "1946"){
								$content = "ERREUR SERVEUR<br/>";
								$content.="login:".$personne->login."<br/>";
								$content.="--------TRACE-------<br/>";
								$content.="passwd:".$pwd."<br/>";
								$content.="resultat du WS:".$result;
							}
						}
					}
				}
			}

		}
		return $this->pi_wrapInBaseClass($content);
	}

	function calculPassword(){
		$tableauDico = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","0","1","2","3","4","5","6","7","8","9");
		$pass="";
		$i=1;
		while($i < 8){
			$pass .= $tableauDico[rand(0, count($tableauDico))];
			$i++;
		}
		return $pass;
	}

	/**
	 * Fonction dd'envoie de mail
	 * @return void
	 */
	function sendMail($mail, $personne, $password){
		$to      = $mail;
		$subject = 'Votre demande de regeneration de mot de passe';
		$txt = "Bonjour ".$personne->titre." ". $personne->prenom ." ".$personne->nom."<br/>";
		$txt .="Veuillez trouver ci-joint votre nouveau mot de passe d'acc&egrave;s &agrave; votre espace priv&eacute; sur <a href='http://www.haras-nationaux.fr'>www.haras-nationaux.fr</a><br/><br/>";
		$txt .="Voici votre nouveau mot de passe : <br/>";
		$txt .="<b>".$password."</b><br/><br/>";
		$txt .="Sinc&egrave;rement<br/>";
		$txt .="Le webmaster de haras-nationaux.fr";

     $message = '
     <html>
      <head>
       <title>Votre demande de reg&eacute;n&eacute;ration de mot de passe</title>
      </head>
      <body>'.$txt.'

      </body>
     </html>
     ';

     // Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
     $headers  = 'MIME-Version: 1.0' . "\r\n";
     $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

     // En-têtes additionnels
     //$headers .= 'To: '.$to. " \r\n";
     $headers .= 'From: noreply@haras-nationaux.fr' . "\r\n";

     // Envoi
     mail($to, $subject, $message, $headers);

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

	/**
	 * Inscrit un message de félicitation
	 * @return void
	 */
	function success(){
		$content='
			<div><h3>'.htmlspecialchars($this->pi_getLL("txt_success_update")).'<br/></h3><hr/><a href="'.$this->pi_getPageLink("2822","",array("no_cache"=>"1")).'" id="lienFonction">Retour</a></div>';

		return $content;
	}

	/**
	 * Ecrit un message d'erreur
	 * @return void
	 */
	function errorMailExiste(){
		$content='
			<div><h3>'.htmlspecialchars($this->pi_getLL("txt_error_mail")).'<br/></h3><hr/></div>';
		$content .=$this->getFormulaireVide();
		return $content;
	}

	/**
	 * Retourne le formulaire de création de compte
	 * @return void
	 */
	function getFormulaireVide(){
		$content='
			<div>'.htmlspecialchars($this->pi_getLL("desc_formulaire")).'<br/></div>
			<hr/>
			<form action="'.$this->pi_getPageLink($GLOBALS["TSFE"]->id).'" method="POST">
				<input type="hidden" name="no_cache" value="1">
				<input type="hidden" name="'.$this->prefixId.'[action]" value="calcul">
			<table width="500px">
				<tr>
				<td><p><strong>'.htmlspecialchars($this->pi_getLL("login")).'</p></strong></td>
				<td>
					<input type="text" name="'.$this->prefixId.'[login]" size="23" maxlength="20">
				</td>
				</tr>
			</table>
				<div style="float:right;">
					<input type="button" class="bouton" onclick="history.back();" value="'.htmlspecialchars($this->pi_getLL("back_button_label")).'" >
				</div>
				<div style="float:right;">
					<input type="submit" class="bouton" name="'.$this->prefixId.'[submit_button]" value="'.htmlspecialchars($this->pi_getLL("submit_button_label")).'">
				</div>
			</form>
		';
		return $content;
	}
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube04_CAS/pi6/class.tx_dlcube04CAS_pi6.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube04_CAS/pi6/class.tx_dlcube04CAS_pi6.php"]);
}

?>