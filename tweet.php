<?php 
require("localisation.php");
require('tmhOAuth/tmhOAuth.php');

$date=date("d-m-Y");
$list_t=array();
$list_h=array();
$listt=array();
$listh=array();

function tweetMe($tweet){
	if($tweet == "" || $tweet == null)
		throw new Exception(T_("La chaine à tweeter est vide."));

	$params = array(
		'status'  => $tweet
		);

	$config = array(
				'consumer_key'    => '', //to have these keys you need to use the Tweeter API and have an tweeter Dev Account
				'consumer_secret' => '',
				'token'           => '',
				'secret'          => '',
				'user_agent'      => ''
				);

	$tmhOAuthEngine = new tmhOAuth($config);

	$response = $tmhOAuthEngine->user_request(array(
				'method' => 'POST',
				'url' => $tmhOAuthEngine->url("1.1/statuses/update"),
				'params' => $params,
				'multipart' => true
			  ));

	return $response;
}

try
		{
			$bdd = new PDO('mysql:host=sql9;dbname=', '', '');
		}
		catch (Exception $e)
		{
				die('Erreur : ' . $e->getMessage());
		}

if(isset($_GET['clé'])){
		$cle=$_GET['clé'];
		$request=$bdd->prepare('SELECT `user`,`twitter` FROM `table_utilisateur` WHERE `cle` =\''.$cle.'\' ' );
		$request->execute();
		$res = $request->fetchAll();
		if (count($res) == 0)
		{
			$verou=0;
		}
		else{
			$verou=1;
			foreach ($res as $ligne) {
				$pseudo = $ligne['user'];
				$tweet = $ligne['twitter'];
			}
		}
	}	
if($verou == 1){
	$dom = new DomDocument();
	$dom->load('ftp://YOURSERVER/www/xml/action_'.$pseudo.'.xml');
	$nodelist = $dom->getElementsByTagName('heure_l');
	foreach ($nodelist as $node) {
		$heure_depart=$node->nodeValue;
	} 
	$nodelist = $dom->getElementsByTagName('heure_fin');
	foreach ($nodelist as $node) {
		$heure_fin=$node->nodeValue;
	}	
		
	$request=$bdd->query("SELECT `temperature`,`humidite` FROM `".$pseudo."` WHERE TO_DAYS(NOW()) - TO_DAYS(`timestamp`) = 1 AND HOUR(`timestamp`) > ".$heure_depart." AND HOUR(`timestamp`) < ".$heure_fin." ORDER BY `".$pseudo."`.`id` DESC ");


	while($row = $request->fetch()) {
			array_push($list_t,$row['temperature']);
			array_push($list_h,$row['humidite']);
	}
	$request->closeCursor();

	$temperature_jour=max($list_t);
	$humidite_jour=max($list_h);

	$request=$bdd->query("SELECT `temperature`,`humidite` FROM `".$pseudo."` WHERE TO_DAYS(NOW()) - TO_DAYS(`timestamp`) = 1 AND HOUR(`timestamp`) < ".$heure_depart." OR HOUR(`timestamp`) > ".$heure_fin." ORDER BY `".$pseudo."`.`id` DESC ");

	while($row = $request->fetch()) {
			array_push($listt,$row['temperature']);
			array_push($listh,$row['humidite']);
	}
	$request->closeCursor();

	$temperature_nuit=min($listt);
	$humidite_nuit=min($listh);


	$message=T_("Rapport du " . $date . " : Tmax en journee : " . $temperature_jour . "°C RH journee : " . $humidite_jour . "% Tmin la nuit : " . $temperature_nuit . "°C RH nuit : " . $humidite_nuit . "% ".$tweet." bit.ly/1efBx1K ");


	if(empty($temperature_jour)){
		print T_("pas de rapport pour cette date ");
	}
	else {
		$result = tweetMe($message);
		if($result == 200)
			echo T_('Le tweet a bien été envoyé !');
		else
			echo T_('Une erreur s\'est produite.');
	}
}
else{
	print T_("Cette clé n'est liée à aucun compte  ");
}

?>