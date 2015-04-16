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

	// Affichage de l'erreur

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

session_start();
if (isset($_SESSION['connect']))//On vérifie que le variable existe.
{
		$connect=$_SESSION['connect'];//On récupère la valeur de la variable de session.
		$pseudo=$_SESSION["login"];
}
else
{
		$connect=0;//Si $_SESSION['connect'] n'existe pas, on donne la valeur "0".
}
try
	{
		$bdd = new PDO('mysql:host=sql9;dbname=', '', '');
	}
catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	} 
if($connect == 1){
	$bdd->exec("DELETE FROM ".$pseudo);
?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Secret Garden</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
</head>
<body>
	<a class="drapeau_EnUs" href="?lang=en_US"></a>
	<a class="drapeau_Fr" href="?lang=fr_FR"></a>
	<p><?php print T_("RAZ ok");?></p>
	<a href="index.php"><?php print T_("Accueil");?></a>
	<?php header('Location: statistique.php'); ?>
</body>
</html>
<?php
}
 
else // Le mot de passe n'est pas bon.
{
	header('Location: index.php');
}
// On affiche la zone de texte pour rentrer le mot de passe.
?>