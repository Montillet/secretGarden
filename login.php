<?php
require("localisation.php");
	function gestionDesErreurs($type, $message, $fichier, $ligne)
	{
		switch ($type)
		{
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_CORE_WARNING:
			case E_COMPILE_ERROR:
			case E_COMPILE_WARNING:
			case E_USER_ERROR:
				$type_erreur = "Erreur fatale";
				break;

			case E_WARNING:
			case E_USER_WARNING:
				$type_erreur = "Avertissement";
				break;

			case E_NOTICE:
			case E_USER_NOTICE:
				$type_erreur = "Remarque";
				break;

			case E_STRICT:
				$type_erreur = "Syntaxe Obsolète";
				break;

			default:
				$type_erreur = "Erreur inconnue";
		}

		$erreur = date("d.m.Y H:i:s") . ' - <b>' . $type_erreur.'</b> : <b>' . $message . '</b> ligne ' . $ligne . ' (' . $fichier . ')';

		if (preg_match("/magic_quotes_gpc/i",$message)) {
			
		}
		else{
			$log = @fopen("/YOURSERVER/www/log/erreur.txt","a");
			@fwrite($log, $erreur . "\n");
			@fclose($log);
		}

	}

	function gestionDesExceptions($exception)  
	{
		gestionDesErreurs (E_USER_ERROR, $exception->getMessage(), $exception->getFile(), $exception->getLine());  
	}

	function gestionDesErreursFatales()
	{
		if (is_array($e = error_get_last()))
		{
			$type = isset($e['type']) ? $e['type'] : 0;
			$message = isset($e['message']) ? $e['message'] : '';
			$fichier = isset($e['file']) ? $e['file'] : '';
			$ligne = isset($e['line']) ? $e['line'] : '';

			if ($type > 0) gestionDesErreurs($type, $message, $fichier, $ligne);
		}
	}

	error_reporting(0);

	set_error_handler('gestionDesErreurs');
	set_exception_handler("gestionDesExceptions");
	register_shutdown_function('gestionDesErreursFatales');
	
	session_start();// À placer obligatoirement avant tout code HTML.
	 
	$_SESSION['connect']=0; //Initialise la variable 'connect'.
	setcookie('zbat', 0 , time() + (3600 * 24 ));
	$login = null;
	$motdepasse = null;
	$message = null;
	$MAX_essai=3;
	$message2 = T_("Vous avez atteint le nombre de tentative maximale revenez demain");
	$message3 = T_("mot de passe oublié ?") ;
	
	if(isset($_COOKIE['zbat'])){
		$nbr_essai = $_COOKIE['zbat'] ;
	}
	else{
		setcookie('zbat', 0 , time() + (3600 * 24 ));
		$nbr_essai = 0 ;
	}
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		if(!empty($_POST["login"]) && !empty($_POST["password"])) {
			$login = $_POST["login"];
			$motdepasse = $_POST["password"];
			$mp=hash('sha256',$motdepasse);
			
			require('config.php'); // On réclame le fichier
			
			$sql = "SELECT * FROM table_utilisateur WHERE user='".mysql_real_escape_string($login)."'";

			// On vérifie si ce login existe
			$requete_1 = mysql_query($sql) or die ( mysql_error() );

			if(mysql_num_rows($requete_1)==0)
			{
				$message = T_("Ce login/mot de passe n'existe pas");
				$nbr_essai = $_COOKIE['zbat'] ;
				
				if($nbr_essai < $MAX_essai){
					$nbr_essai++;
					setcookie("zbat", $nbr_essai, time() + (3600 * 24 ));
				}
			}
			else
			{
				// On vérifie si le login et le mot de passe correspondent au compte utilisateur
				$requete_2 = mysql_query($sql." AND pass='".mysql_real_escape_string($mp)."'")
				or die ( mysql_error() );

				if(mysql_num_rows($requete_2)==0)
				{
					// On va récupérer les résultats
					$result = mysql_fetch_array($requete_1, MYSQL_ASSOC);

					// On va récupérer la date de la dernière connexion
					$lastconnection = explode(' ', $result["dates"]);
					$lastjour = explode('-', $lastconnection[0]);

					// On va récupérer le nombre de tentative et l'affecter
					$nbr_essai = $result["nbr_connect"];

					if($lastjour[2]==date("d") && $nbr_essai==$MAX_essai)
					{
						setcookie("zbat", 3, time() + (3600 * 24 ));
					}
					else
					{
						$nbr_essai++;
						$update = "UPDATE table_utilisateur SET nbr_connect='".$nbr_essai."', dates=NOW() WHERE id='".$result["id"]."'";
						mysql_query($update) or die ( mysql_error() );
						setcookie("zbat", $nbr_essai, time() + (3600 * 24 ));
						$message = T_("Ce login/mot de passe n'existe pas");
					}
				}
				else
				{
					// On va récupérer les résultats
					$result = mysql_fetch_array($requete_2, MYSQL_ASSOC);

					$nbr_essai = 0;
					$update = "UPDATE table_utilisateur SET nbr_connect='".$nbr_essai."', dates=NOW()
					WHERE id='".$result["id"]."'";

					mysql_query($update) or die ( mysql_error() );
					
					$sql = "SELECT verif FROM table_utilisateur WHERE user='".mysql_real_escape_string($login)."'";
					$requete = mysql_query($sql) or die ( mysql_error() );
					
					while ($row = mysql_fetch_assoc($requete)) {
						$verif=$row['verif'];
					}
					if($verif == 1){
						$sql = "SELECT cle FROM table_utilisateur WHERE user='".mysql_real_escape_string($login)."'";
						// on requpere la clé arduino
						$requete = mysql_query($sql) or die ( mysql_error() );
						
						while ($row = mysql_fetch_assoc($requete)) {
							$cle=$row['cle'];
						}
						// On redirige vers la partie membre
						//session_start();
						$_SESSION["connect"] = 1;
						$_SESSION["login"] = $login;
						$_SESSION["cle"] = $cle;
						header('Location: moniteur.php');
					}
					else{
						$message = T_("Vous n'avez pas encore confirmé votre compte allez voir dans vos mails.");
					}
				}
			}
		}
	} 
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
		<meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <title><?php print T_("Connexion SG");?></title>
        <meta name="author" content="baptiste montillet" />
        <link rel="icon" href="images/favicon.ico" />
        <link rel="stylesheet" type="text/css" href="css/login.css" />
		<script src="js/modernizr.custom.63321.js"></script>
		<!--[if lte IE 7]><style>.main{display:none;} .support-note .note-ie{display:block;}</style><![endif]-->
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Raleway:400,700">
		<style>	
			body {
				background: #7f9b4e url(images/bg2.jpg) no-repeat center top;
				-webkit-background-size: cover;
				-moz-background-size: cover;
				background-size: cover;
			}
			.container > header h1,
			.container > header h2 {
				color: #fff;
				text-shadow: 0 1px 1px rgba(0,0,0,0.7);
			}
		</style>
		<script type="text/javascript">
		function surligne(champ, erreur)
			{
			   if(erreur)
				  champ.style.backgroundColor = "#fba";
			   else
				  champ.style.backgroundColor = "";
			}
		function validForm(form)
		{	
			var a = document.forms["form"]["login"].value;
			var b = document.forms["form"]["password"].value;
			if((a==null || a=="") ){
				surligne(form.login,true);
				return false ;
			}
			else if((b==null || b=="")){
				surligne(form.password,true);
			}
			else{
				surligne(form.password,false);
				surligne(form.login,false);
				return true;
			}
		} 
		function erreur(){
			var x = document.getElementById('titre').innerHTML;
			if( x != ""){
				document.getElementById('titre').style.color="red";
			}
		}
		</script>
    </head>
    <body onload="erreur();">
        <div class="container">
					
			<header>
				
				<h1><?php print T_("Connectez vous à votre");?> <strong>Secret Garden</strong>.</h1>
								
				<div class="support-note">
					<span class="note-ie"><?php print T_("Désolé, seulement pour les tout derniers navigateurs");?></span>
				</div>
				
			</header>
			
			<section class="main">
				<form class="form-4" name="form" action="login.php" method="post" onsubmit= "return verifForm(this)" >
					<?php 
					if (isset($_COOKIE['zbat']) && $_COOKIE['zbat'] == 3 ){
								echo('<p id="titre">' .$message2. '</p>');
								echo('<a href="oublier.php" rel="nofollow" >' .$message3. '</a>');
					}
					else{
					?>
					<h1><?php print T_("Connectez-vous");?></h1>
					<p>
				        <label><?php print T_("Nom d'utilisateur ou email");?></label>
				        <input type="text" name="login" placeholder="Username or email" required>
				    </p>
				    <p>
				        <label ><?php print T_("Mot de passe");?></label>
				        <input type="password" name='password' placeholder="Password" required> 
				    </p>
					<p id="titre"><?php if(!empty($message)){echo($message);} else{echo("");}?></p>
				    <p>
				        <input type="submit" name="submit" value="Continue">
						<a href="inscription.php"><?php print T_("vous n'avez pas encore de compte ? Enregistrez-vous ici.");?> </a>
						<br/>
						<a href="oublier.php" rel="nofollow" ><?php print T_("mot de passe oublié ?");?> </a>
				    </p> 
					<?php } ?>
				</form>
			</section>
			
        </div>
    </body>
</html>