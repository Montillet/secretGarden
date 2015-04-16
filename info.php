<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
	<META NAME="Author" LANG="fr" CONTENT="Baptiste MONTILLET"> 
	<link rel="icon" href="images/favicon.ico" />
	<?php require("localisation.php"); ?>
    <title><?php print T_("info");?></title>
	
</head>
 
<body>
	<a class="drapeau_EnUs" href="?lang=en_US"></a>
	<a class="drapeau_Fr" href="?lang=fr_FR"></a>
	<h3><?php print T_("Vous ne savez ce que représente l'humidité de la plante ou moisture ?");?></h3>
	
    <p><?php print T_("L'humidité de la plante représente en fait l'humidité dans le pot de l'une de vos plantes. <br />\n
Ce chiffre évolue selon une échelle sans unité 0 à 1000 et la barre ci-dessous vous permettra de vous y retrouver.");?></p>
	
	<img src="<?php print T_("images/echelle.png");?>" alt="Echelle du taux d'humidité">
     
</body>
</html>



