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

	//echo $erreur;

	// Enregistrement de l'erreur dans un fichier txt

	
	if (preg_match("/magic_quotes_gpc/i",$message)) {
			
		}
	else{
		$log = @fopen("/YOURSERVER/www/log/erreur.txt","a");
		@fwrite($log, $erreur . "\n");
		@fclose($log);
	}

	// Envoi par mail

	// ...

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

function random($car) {
	$string = "";
	$chaine = "abcdefghijklmnpqrstuvwxy";
	srand((double)microtime()*1000000);
	for($i=0; $i<$car; $i++) {
	$string .= $chaine[rand()%strlen($chaine)];
	}
	return $string;
}

?>
<!DOCTYPE HTML>

<html>
	<head>
		<title><?php print T_("vous avez oublier votre mot de passe ?");?></title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<link rel="icon" href="images/favicon.ico" />
		<link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,900,300italic" rel="stylesheet" />
		<script src="js/jquery.min.js"></script>
		<script src="js/jquery.dropotron.min.js"></script>
		<script src="js/config.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-panels.min.js"></script>
		<script type="text/javascript" src="http://d3js.org/d3.v3.js"></script>
		<script type="text/javascript" src="js/script.js"></script>
		<noscript>
			<link rel="stylesheet" href="css/skel-noscript.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-desktop.css" />
		</noscript>
		<!--[if lte IE 8]><script src="js/html5shiv.js"></script><link rel="stylesheet" href="css/ie8.css" /><![endif]-->
	</head>
	<?php 
	try
			{
				$bdd = new PDO('mysql:host=sql9;dbname=', '', '');
			}
	catch (Exception $e){
			die('Erreur : ' . $e->getMessage());
	} 
	$message2="";	
	$verou=0;
	$pb=0;
	if( isset($_POST['email']) && isset($_POST['pseudo']) ){
		if (filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)){
			$email=$_POST['email'];
			$pseudo=htmlspecialchars($_POST['pseudo']);
			$verou = 1 ;
		}
		else{
			$verou = 0 ;
		}
			
	}
	if($verou == 1 ){
		$request=$bdd->prepare('SELECT `id` FROM `table_utilisateur` WHERE `user` = :pseudo AND `email` = :email ' );
		$request->execute(array(':pseudo' => $pseudo ,':email' => $email ));
		$res = $request->fetchAll();
		if (count($res) == 1)
		{
			$pb=0;
			$mp=random(8);
			if($_COOKIE['lang'] == "fr_FR"){
				$message = "Bonjour,<br/>\nvous venez de faire une demande de nouveau mot de passe sur votre-Secret-Garden.fr<br/>\n suite à la déclaration de perte de votre mot de passe \nvoici vos identifiant : <br/>\n Pseudo : <strong>".$pseudo."</strong> <br/>\n Mot de passe : <strong>".$mp."</strong> <br/>\n nous restons à votre disposition pour toutes questions ou suggestions.<br/>\nSecret Garden.<br/>\nNous suivre sur Twitter : <a href='http://twitter.com/SecretGarden_fr'>http://twitter.com/SecretGarden_fr</a>";
			}
			else if ($_COOKIE['lang'] == "en_US"){
				$message = "hi,<br/>\nyou just request a new password on votre-secret-garden.fr<br/>\n Following the declaration of loss of password \nhere is your identifiant : <br/>\n Pseudo : <strong>".$pseudo."</strong> <br/>\n password : <strong>".$mp."</strong> <br/>\n we remain at your disposal for any questions or suggestions.<br/>\nSecret Garden.<br/>\nfollow us on Twitter : <a href='http://twitter.com/SecretGarden_fr'>http://twitter.com/SecretGarden_fr</a>";
			}
			else{
				$message = "Bonjour,<br/>\nvous venez de faire une demande de nouveau mot de passe sur votre-Secret-Garden.fr<br/>\n suite à la déclaration de perte de votre mot de passe \nvoici vos identifiant : <br/>\n Pseudo : <strong>".$pseudo."</strong> <br/>\n Mot de passe : <strong>".$mp."</strong> <br/>\n nous restons à votre disposition pour toutes questions ou suggestions.<br/>\nSecret Garden.<br/>\nNous suivre sur Twitter : <a href='http://twitter.com/SecretGarden_fr'>http://twitter.com/SecretGarden_fr</a>";
			}
			$message2=T_("E-mail et pseudo vérifier, nous allons vous envoyé par mail votre nouveau mot de passe");
			$to = $email;
			$subject = T_('Secret Garden - Nouveau mot de passe');
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'From: "Votre-Secret-Garden" <contact@votre-secret-garden.fr>' . "\r\n";
			$mail = mail($to, $subject, $message, $headers);
		}
		else {
			$pb=1;
			$message2= T_("ce pseudo/email n'existe pas.");
		}
	}
	
?>
<script type="text/javascript">
	function surligne(champ, erreur)
	{
	   if(erreur)
		  champ.style.backgroundColor = "#fba";
	   else
		  champ.style.backgroundColor = "";
	}

	function valideEmail(champ)
	{
	var x=document.forms["form"]["email"].value;
	var atpos=x.indexOf("@");
	var dotpos=x.lastIndexOf(".");
	if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length){
		alert("<?php print T_("L'adresse mail fournit n'est pas valide.");?>");
		surligne(champ,true);
		return false;
		}
	else{
		surligne(champ,false);
		return true;
		}
	}
	function validForm(form)
	{	
		var a = valideEmail(form.email);
		var d = document.forms["form"]["pseudo"].value;
		if( d != null && d!=""){
			if( a ){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	function erreur(){
			var x = document.getElementById('titre').innerHTML;
			if( x != ""){
				document.getElementById('titre').style.color="red";
			}
		}
</script>
	<body class="right-sidebar" onload="erreur();" >

		<!-- Header Wrapper -->
			<div id="header-wrapper">
				<div class="container">
					<div class="row">
						<div class="12u">
							
							<!-- Header -->
								<section id="header">
									<a class="drapeau_EnUs" href="?lang=en_US"></a>
									<a class="drapeau_Fr" href="?lang=fr_FR"></a>
									<!-- Logo -->
										<h1><a href="index.php">Secret Garden</a></h1>
									
									<!-- Nav -->
									<nav id="nav">
											<ul>
												<li><a href="index.php"><span class="fa fa-home"></span><?php print T_("Accueil");?></a></li>
											</ul>
										</nav>
								</section>

						</div>
					</div>
				</div>
			</div>
		
		<!-- Main Wrapper -->
			<div id="main-wrapper">
				<div class="container">
					<div class="row">
						<div class="12u">
							
							<!-- Portfolio -->
								<section>
									<div>
										<div class="row">
											<div class="8u skel-cell-important">
												
												<!-- Content -->
													<article class="box is-post">
														
														<header>
															<h2><?php print T_("Vous avez oublié votre mot de passe ?");?></h2>
															<span class="byline"><?php print T_("pas de panique nous allons vous en attribuer un nouveau.");?></span>
														</header>
														<p><?php print T_("Voici comment remédier à cette situation.");?></p>
														<p><?php print T_("Saisissez votre adresse e-mail ainsi que votre pseudo et nous vous attribuerons un nouveau mot de passe qui vous sera envoyé par mail.");?></p>
														<form name="form" action="oublier.php" method="post" onsubmit="validForm(this)" >
															<p><strong><?php print T_("Pseudo");?> : </strong><input type="text" name="pseduo"></p>
															<p><strong><?php print T_("Email ");?>: </strong><input type="text" name="email" onblur="valideEmail(this);"></p>
															<input TYPE="submit" class="button alt" VALUE="<?php print T_("Continuer");?>" >
														</form>
														<?php if($pb == 1){echo ('<p id="titre">' .$message2. '</p>');} ?>
													</article>
											</div>
										</div>
									</div>
								</section>

						</div>
					</div>
				</div>
			</div>

		<!-- Footer Wrapper -->
			<div id="footer-wrapper">
				
				<!-- Footer -->
					<section id="footer" class="container">
						<div class="row">
							<div class="8u">

								<section>
									<header>
										<h2><?php print T_("Quoi de neuf ?");?></h2>
									</header>
									<ul class="dates">
										<li>
											<span class="date">Fev <strong>19</strong></span>
											<h3><a href="#"><?php print T_("Secret Garden fait peau neuve !");?></a></h3>
											<p><?php print T_("Vous avez sûrement remarqué que Secret Garden a évolué. Il est maintenant compatible avec touts les smartphones et les tablettes. ");?></p>
										</li>
										<li>
											<span class="date">Oct <strong>1</strong></span>
											<h3><a href="#"><?php print T_("Lancement de Secret Garden");?> </a></h3>
											<p><?php print T_("Nous sommes très fièrs aujourd'hui de vous présenter ce nouveau site qui est à mi-chemin entre un Data-loggeur et un jardinier ;-).");?></p>
										</li>
									</ul>
								</section>
							
							</div>
						</div>
						<div class="row">
							
							<div class="4u">
							
								<section>
									<header>
										<h2><?php print T_("A propos");?></h2>
									</header>
									<a href="" class="image image-full"><img src="images/pic10.jpg" alt="" /></a>
									<p>
										<?php print T_("Secret Garden et né du cerveau d'une personne qui voulait allier ses deux passions : les nouvelles technologies et la culture des plantes. Secret Garden est donc le fruit du mélange des techniques de culture des plantes (gestion de l'éclairage, de l'arrosage etc...) et de la connaissance en programmation Web et C/C++.");?>   
									</p>
									
								</section>
							
							</div>
							<div class="4u">
							
								<section>
									<header>
										<h2><?php print T_("Suivez-nous");?></h2>
									</header>
									<ul class="social">
										<li><a class="fa fa-twitter solo" href="https://twitter.com/SecretGarden_fr"><span>Twitter</span></a></li>
										<li><a class="fa fa-linkedin solo" href="http://www.linkedin.com/pub/baptiste-montillet/91/705/277"><span>LinkedIn</span></a></li>	
									</ul>
									<ul class="contact">
										<li>
											<h3><?php print T_("Adresse");?></h3>
											<p>
												Secret Garden<br />
												chemin des gardis<br />
												13490, Jouques, France
											</p>
										</li>
										<li>
											<h3><?php print T_("Mail");?></h3>
											<p><a href="mailto:contact@votre-secret-garden.fr">contact@votre-secret-garden.fr</a></p>
										</li>
									</ul>
								</section>
							
							</div>
						</div>
						<div class="row">
							<div class="12u">
							
								<!-- Copyright -->
									<div id="copyright">
										<ul class="links">
											<li>&copy; Secret Garden </li>
											<li><?php print T_("Conception");?>: Baptiste Montillet </li>
											<li><a href="mention_legal.php" rel="nofollow"><?php print T_("Mentions Légales");?></a></li>
											<li><?php print T_("Design");?>: <a href="http://twitter.com/n33co"> n33co </a></li>
										</ul>
									</div>

							</div>
						</div>
					</section>
				
			</div>

	</body>
</html>