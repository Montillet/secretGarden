<?php
error_reporting(0);

$DB_serveur = 'sql9'; // Nom du serveur
$DB_utilisateur = ''; // Nom de l'utilisateur de la base
$DB_motdepasse = ''; // Mot de passe pour accèder à la base
$DB_base = ''; // Nom de la base

Define('_MAX_TENTATIVE', 3) ; 

$connection = mysql_connect($DB_serveur, $DB_utilisateur, $DB_motdepasse) // On se connecte au serveur
or die (mysql_error().' sur la ligne '.__LINE__);

mysql_select_db($DB_base, $connection) // On se connecte à la BDD
or die (mysql_error().' sur la ligne '.__LINE__);
?>