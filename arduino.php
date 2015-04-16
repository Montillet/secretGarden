<?php
//require("localisation.php");
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
			continue;
		}
	else{
		$log = @fopen("/YOURPATH/www/log/erreur.txt","a");
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

// on se connecte à MySQL 
try
{
	$bdd = new PDO('mysql:host=sql9;dbname=YOURSERVER', 'YOURID', 'YOURPASS');
}
catch (Exception $e)
{
		die('Erreur : ' . $e->getMessage());
}
$verou=9;
$temp=0;
$ph=0;
$ppfd=0;
$humi=0;
$moist=0;
$pb=0;

if(isset($_POST['moist'])){
	$temp=htmlspecialchars($_POST['temp']);
	$humi=htmlspecialchars($_POST['humi']);
	$moist=htmlspecialchars($_POST['moist']);
	$pb=htmlspecialchars($_POST['pb']);
	$today = date("Y-m-d H:i:s");
}
else{
	echo("erreur capteur");
}
if(isset($_POST['ph'])){
	$ph=htmlspecialchars($_POST['ph']);
}
if(isset($_POST['ppfd'])){
	$ppfd=htmlspecialchars($_POST['ppfd']);
}

if(isset($_POST['cle'])){
	$cle=htmlspecialchars($_POST['cle']);
	//echo($cle);
	$request=$bdd->prepare("SELECT `user` FROM `table_utilisateur` WHERE `cle` = :cle" );
	$request->execute(array( ':cle' => $cle ));
	$res = $request->fetchAll();
	if (count($res) == 0)
	{
		$verou=0;
		echo "the arduino key are not valid !";
	}
	else{
		echo "data upload OK" ;
		$verou=1;
		foreach ($res as $ligne) {
			$pseudo = $ligne['user'];
		}
	}
}
if($temp != 0 && $verou == 1){
	$request=$bdd->prepare('INSERT INTO '.$pseudo.' (temperature,humidite,moisture,ph,ppfd,pb,timestamp)  VALUES (:temperature, :humidite, :moisture, :ph, :ppfd, :pb, :heure)');	
	$request->execute(array(':temperature' => $temp ,':humidite' => $humi ,':moisture' => $moist ,':ph' => $ph ,':ppfd' => $ppfd ,':pb' => $pb ,':heure' => $today));
}
if($verou == 1){
	$request=$bdd->query("SELECT `temperature`,`humidite`,`moisture`,`ph`,`ppfd`,`pb`,`timestamp` FROM `".$pseudo."` ORDER BY `id` DESC LIMIT 1 ");
		$xml = new DOMDocument('1.0', 'utf-8');
		$xml->formatOutput = true;
		while($row = $request->fetch()) {
			
			$debut = $xml->createElement("inputs");
			
			$temp = $xml->createElement('temperature');
			$temp->appendChild($xml->createTextNode($row['temperature']));
			$debut->appendChild($temp);
			
			$humi = $xml->createElement('humidite');
			$humi->appendChild($xml->createTextNode($row['humidite']));
			$debut->appendChild($humi);
			
			$moist = $xml->createElement('moisture');
			$moist->appendChild($xml->createTextNode($row['moisture']));
			$debut->appendChild($moist);
			
			$ph = $xml->createElement('ph');
			$ph->appendChild($xml->createTextNode($row['ph']));
			$debut->appendChild($ph);
			
			$ppfd = $xml->createElement('ppfd');
			$ppfd->appendChild($xml->createTextNode($row['ppfd']));
			$debut->appendChild($ppfd);
			
			$lastsync = $xml->createElement('lastsync');
			$lastsync->appendChild($xml->createTextNode($lastmodified));
			$debut->appendChild($lastsync);
			
			$pb = $xml->createElement('PB');
			$pb->appendChild($xml->createTextNode($row['pb']));
			$debut->appendChild($pb);
			
			$xml->appendChild($debut);
			
		}
		$request->closeCursor();	
		
		$contexte = stream_context_create(
		array(
			'ftp' => array('overwrite' => TRUE)
			)
		);
		libxml_set_streams_context($contexte);
		// DOM
		$xml->save('ftp://YOURSERVER/www/xml/'.$cle.'.xml');
}