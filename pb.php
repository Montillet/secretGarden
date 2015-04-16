<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
	<META NAME="Author" LANG="fr" CONTENT="Baptiste MONTILLET">  
	<link rel="icon" href="images/favicon.ico" />
	<?php require("localisation.php"); ?>
    <title><?php print T_("pb");?></title>

	<style type="text/css">
		body {
			background-color: #FAFFFA 
			}
  </style>
</head>
 
<body>
	<a class="drapeau_EnUs" href="?lang=en_US"></a>
	<a class="drapeau_Fr" href="?lang=fr_FR"></a>
	<h2><?php print T_("Problème Capteur ?");?></h2>
 
    <p><?php print T_("Lorsque vous voyez une erreur du type 'err_temperature_x' <br /> Cela veut dire que votre capteur de température est en panne. <br /> Il y a plusieurs types de panne. <br />
On peut identifier la panne grâce par son numéro 1,2 ou 3.<br />
Si vous obtenez : <br /> DHTLIB_ERROR_CHECKSUM <br /> Cela veut dire que les données envoyées par votre capteur sont incompréhensibles, vérifiez le câblage. <br /> Vous l'avez peut-être câblé à l'envers. <br />
DHTLIB_ERROR_TIMEOUT <br /> Cela veut dire que le laps de temps entre le moment où votre carte demande la température et le moment ou le capteur lui a répondu (ou pas.. visiblement ;-) ) est trop long. <br />
Ce n'est pas trop grave si cette erreur ne demeure pas. <br /> Par contre si l'erreur persiste vérifiez la connexion entre le capteur et la carte. <br />
DHTLIB_ERROR_UNKNOWN <br /> Ce code erreur englobe tous les autres types d'erreurs qui ne sont pas recensés dans les deux premières.<br />
C'est généralement très mauvais signe pour votre capteur. RIP ");?>
	</p>
</body>
</html>



