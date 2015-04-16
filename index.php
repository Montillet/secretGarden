<?php
require("localisation.php");
setcookie('zbat', 0 , time() + (3600 * 24 ));
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

	// Enregistrement de l'erreur dans un fichier txt

	/*
	$log = fopen('Erreurs.txt', 'a');
	fwrite($log, $erreur . "\n");
	fclose($log);
	*/

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

function getRelativeTime($date)
{
	$date_a_comparer = new DateTime($date);
	$date_actuelle = new DateTime("now");

	$intervalle = $date_a_comparer->diff($date_actuelle);

	if ($date_a_comparer > $date_actuelle)
	{
		$prefixe = T_('dans ');
	}
	else
	{
		$prefixe = T_('il y a ');
	}

	$ans = $intervalle->format('%y');
	$mois = $intervalle->format('%m');
	$jours = $intervalle->format('%d');
	$heures = $intervalle->format('%h');
	$minutes = $intervalle->format('%i');
	$secondes = $intervalle->format('%s');

	if ($ans != 0)
	{
		$relative_date = $prefixe . $ans . T_(' an') . (($ans > 1) ? 's' : '');
		if ($mois >= 6) $relative_date .= T_(' et demi');
	}
	elseif ($mois != 0)
	{
		$relative_date = $prefixe . $mois . T_(' mois');
		if ($jours >= 15) $relative_date .= T_(' et demi');
	}
	elseif ($jours != 0)
	{
		$relative_date = $prefixe . $jours . T_(' jour') . (($jours > 1) ? 's' : '');
	}
	elseif ($heures != 0)
	{
		$relative_date = $prefixe . $heures . T_(' heure') . (($heures > 1) ? 's' : '');
	}
	elseif ($minutes != 0)
	{
		$relative_date = $prefixe . $minutes . T_(' minute') . (($minutes > 1) ? 's' : '');
	}
	else
	{
		$relative_date = $prefixe . T_(' quelques secondes');
	}

	return $relative_date;
}

session_start();

if (isset($_SESSION['connect']))//On vérifie que le variable existe.
{
        $connect=$_SESSION['connect'];//On récupère la valeur de la variable de session.
}
else
{
        $connect=0;//Si $_SESSION['connect'] n'existe pas, on donne la valeur "0".
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Secret Garden</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="google-site-verification" content="E1TAJaUhxCl1hiOWc8qAK3I8TmSpeV3qGPrdzG8Pa_8" />
		<meta name="description" content="Secret Garden is a datalogeur based on Arduino board and it helps you to have a beautiful garden. " />
		<meta name="keywords" content="gardening, dataloggeur, free, cloud" />
		<meta content="Secret Garden is a datalogeur based on Arduino board and it helps you to have a beautiful garden." itemprop="description"></meta>
		<link rel="icon" href="images/favicon.ico" />
		<link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,900,300italic" rel="stylesheet" />
		<script src="js/jquery.min.js"></script>
		<script src="js/jquery.dropotron.min.js"></script>
		<script src="js/config.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-panels.min.js"></script>
		<noscript>
			<link rel="stylesheet" href="css/skel-noscript.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-desktop.css" />
		</noscript>
		<!--[if lte IE 8]><script src="js/html5shiv.js"></script><link rel="stylesheet" href="css/ie8.css" /><![endif]-->
		<?php
		$nbr_img=0;
		$handle=@opendir("images/slider");

		/* This is the correct way to loop over the directory. */
		while (false !== ($entry = readdir($handle))) {
			if(preg_match('/\.(png|jpe?g|gif)$/i', $entry)){
				$nbr_img++;
			}
		}		
		?>
		<script>
		var x =0;
		var lens = <?php echo $nbr_img; ?> ;
		function slideshow(){
			if( x < lens ){
				x=x+1;
				document.getElementById("Accueil").src="images/slider/"+x+".jpg";
			}
			else{
				x=1;
			}
			setTimeout('slideshow()',60000);
		}
	  </script>
	</head>
	<body class="homepage" onload="slideshow();"><div itemscope itemtype="http://schema.org/SoftwareApplication" id="skel-panels-pageWrapper" style="position: relative; left: 0px; right: 0px; top: 0px; -webkit-backface-visibility: hidden; -webkit-perspective: 500; -webkit-transition: -webkit-transform 0.25s ease-in-out; transition: -webkit-transform 0.25s ease-in-out;">

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
										<h1><a href="http://votre-secret-garden.fr"><span itemprop="name">Secret Garden</a></h1>
									<!-- Nav -->
										<nav id="nav">
											<ul>
												<li class="current_page_item"><a itemprop="url" href="index.php"><span class="fa fa-home"></span>&nbsp;<?php print T_("Accueil");?></a></li>
												<li><a href="inscription.php"><span class="fa fa-sign-in"></span>&nbsp;<?php print T_("Créer un compte");?></a></li>
												<?php if($connect ==1){
													print T_("<li><a href='moniteur.php'><span class='fa fa-leaf'></span> Votre jardin</a></li>"); ;
												}
												else{
													print T_("<li><a href='login.php'><span class='fa fa-user'></span> Se connecter</a></li>");
												}?>
												<li><a href="donate.php"><span class="fa fa-heart"></span>&nbsp;<?php print T_("Soutenez-nous");?></a></li>
											</ul>
											
										</nav>

								</section>

						</div>
					</div>
					<div class="row">
						<div class="12u">

							<!-- Banner -->
								<section id="banner">
									<a href="#">
										<span class="image image-full">
											<img itemprop="image" id="Accueil" src="images/slider/1.jpg" alt="jardin"/>
										</span>
										<header>
											<h2><?php print T_("Bienvenue sur Secret Garden !");?></h2>
											<span class="byline"><?php print T_("Le Web-moniteur de votre jardin.");?> </span>
										</header>
									</a>
								</section>

						</div>
					</div>
					<div class="row">
						<div class="12u">
								
							<!-- Intro -->
								<section id="intro">
								
									<div>
										<div class="row">
											<div class="4u">
												<section class="first">
													<span class="pennant"><span class="fa fa-cog"></span></span>
													<header>
														<h2><?php print T_("C'est quoi ?");?></h2>
													</header>
													<p><?php print T_("Secret Garden vous permet de collecter et d'afficher toutes les données liées à votre jardin. Il vous permet aussi de le piloter grâce à une simple interface.");?></p>
												</section>
											</div>
											<div class="4u">
												<section class="middle">
													<span class="pennant pennant-alt"><span class="fa fa-flash"></span></span>
													<header>
														<h2><?php print T_("Rapide");?></h2>
													</header>
													<p><?php print T_("Vous n'avez qu'à connecter la carte à internet et elle se chargera de tout le reste.");?></p>
												</section>
											</div>
											<div class="4u">
												<section class="last">
													<span class="pennant pennant-alt2"><span class="fa fa-star"></span></span>
													<header>
														<h2><?php print T_("Ouvert");?></h2>
													</header>
													<p><?php print T_("Secret Garden est Open source et Open hardware puisqu'il s'appuie sur des cartes Arduino (Yùn). Vous pouvez donc trouver tous les codes sources sur le blog.");?> </p>
												</section>
											</div>
										</div>
									</div>

									<div class="actions">
										<a href="login.php" class="button big" ><?php print T_("Ca Commence ici");?></a>
										<a href="#savoir" class="button alt big"><?php print T_("en savoir plus");?></a>
									</div>
								
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
									<header class="major">
										<h2 id="savoir"><?php print T_("Que peut il faire ?");?></h2>
									</header>
									<div>
										<div class="row">
											<div class="4u">
												<section class="box">
													<a class="image image-full"><img src="images/vignette/gauges.jpg" alt="jauge de visualisation" /></a>
													<header>
														<h3 ><?php print T_("Visualiser les constantes de votre jardin");?></h3>
													</header>
													<p><?php print T_("Vous pouvez d'un simple coup d'oeil contrôler la température, l'humidité et bien d'autres paramètres grâce à des cadrans instinctifs.");?></p>
													<footer>
														<a href="login.php" class="button alt"><?php print T_("Essayez le");?></a>
													</footer>
												</section>
											</div>
											<div class="4u">
												<section class="box">
													<a class="image image-full"><img src="images/vignette/reglage.jpg" alt="settings" /></a>
													<header>
														<h3><?php print T_("Contrôler et ajuster les cycles lumineux et l'arrosage");?></h3>
													</header>
													<p><?php print T_("Vous pouvez à tout moment modifier ou ajuster les cycles pour que vos plantes grandissent dans les meilleures conditions.");?></p>
													<footer>
														<a href="login.php" class="button alt"><?php print T_("Essayez le");?></a>
													</footer>
												</section>
											</div>
											<div class="4u">
												<section class="box">
													<a class="image image-full"><img src="images/vignette/graphique.jpg" alt="graphique" /></a>
													<header>
														<h3><?php print T_("Visualiser l'ensemble des données sous forme de différents graphiques");?></h3>
													</header>
													<p><?php print T_("Grâce à une palette de graphiques vous pourrez visualiser toutes les données collectées par votre carte.");?> </p>
													<footer>
														<a href="login.php" class="button alt"><?php print T_("Essayez le");?></a>
													</footer>
												</section>
											</div>
										</div>
										<div class="row">
											<div class="4u">
												<section class="box">
													<a class="image image-full"><img src="images/vignette/tweet.jpg" alt="jardin qui tweet" /></a>
													<header>
														<h3><?php print T_("Rapport journalier sur Twitter");?></h3>
													</header>
													<p><?php print T_("Tous les jours, vous serez tenu au courant de l'état de santé de votre jardin.");?></p>
													<footer>
														<a href="login.php" class="button alt"><?php print T_("Essayez le");?></a>
													</footer>
												</section>
											</div>
											<div class="4u">
												<section class="box">
													<a class="image image-full"><img src="images/vignette/jardin_secur.jpg" alt="jardin sécurisé" /></a>
													<header>
														<h3><?php print T_("Un jardin sécurisé");?> </h3>
													</header>
													<p><?php print T_("Grâce à Secret Garden vous serez prévenu(e) en temps réel si un problème survient dans votre jardin lequel basculera alors instantanément en mode sécurisé.");?></p>
													<footer>
														<a href="login.php" class="button alt"><?php print T_("Essayez le");?></a>
													</footer>
												</section>
											</div>
											<div class="4u">
												<section class="box">
													<a class="image image-full"><img src="images/vignette/mobile.jpg" alt="disponible sur mobile" /></a>
													<header>
														<h3><?php print T_("Secret Garden sur Mobile");?></h3>
													</header>
													<p><?php print T_("Gràce à un design adaptatif Secret Garden est également disponible au format mobile et tablette.");?></p>
													<footer>
														<a href="login.php" class="button alt"><?php print T_("Essayez le");?></a>
													</footer>
												</section>
											</div>
										</div>
									</div>
								</section>

						</div>
					</div>
					<div class="row">
						<div class="12u">

							<!-- Blog -->
								<section>
									<header class="major">
										<h2><a href="<?php print T_("http://blog.votre-secret-garden.fr/fr/");?>"><?php print T_("le Blog");?></a></h2>
									</header>
									<div>
										<div class="row">
											<div class="6u">
												<section class="box">
													<a href="<?php print T_("http://blog.votre-secret-garden.fr/fr/article/diy-tutoriel-dinstallation-et-de-configuration-de-la-carte-de-pilotage");?>" class="image image-full"><img src="images/vignette/tuto1.jpg" alt="DIY arduino" /></a>
													<header>
														<h3><a href="<?php print T_("http://blog.votre-secret-garden.fr/fr/article/diy-tutoriel-dinstallation-et-de-configuration-de-la-carte-de-pilotage");?>"><?php print T_("DIY : Tutoriel d'installation et de configuration de la carte de pilotage");?></a></h3>
														<span class="byline"><?php print T_("Mis en ligne ");?> <?php echo getRelativeTime("2014-01-08 19:27:05")?><?php print T_(",");?></span>
													</header>
													<p><?php print T_("Bonjour à tous ! Dans ce tutoriel nous allons voir ensemble comment assembler et programmer de A à Z la carte qui permettra à Secret Garden de piloter votre jardin.");?> </p>
													<footer class="actions">
														<a href="<?php print T_("http://blog.votre-secret-garden.fr/fr/article/diy-tutoriel-dinstallation-et-de-configuration-de-la-carte-de-pilotage");?>" class="button fa fa-file-text"><?php print T_("lire la suite");?></a>
													</footer>
												</section>
											</div>
											<div class="6u">
												<section class="box">
													<a href="<?php print T_("http://blog.votre-secret-garden.fr/fr/article/diy-creer-une-lampe-led-pour-bouture");?>" class="image image-full"><img src="images/img_lampe.jpg" alt="DIY objet connecter" /></a>
													<header>
														<h3><a href="<?php print T_("http://blog.votre-secret-garden.fr/fr/article/diy-creer-une-lampe-led-pour-bouture");?>"><?php print T_("DIY : Créer une lampe LED pour la germination");?></a></h3>
														<span class="byline"><?php print T_("Mis en ligne ");?> <?php echo getRelativeTime("2014-01-08 19:27:05")?><?php print T_(",");?></span>
													</header>
													<p><?php print T_("Bonjour à tous ! Cette fois ci nous allons fabriquer une lampe LED horticole pour votre jardin .");?></p>
													<footer class="actions">
														<a href="<?php print T_("http://blog.votre-secret-garden.fr/fr/article/diy-creer-une-lampe-led-pour-bouture");?>" class="button fa fa-file-text"><?php print T_("lire la suite");?></a>
													</footer>
												</section>
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
											<p><?php print T_("Vous avez sûrement remarqué que Secret Garden a évolué. Il est maintenant compatible avec tous les smartphones et les tablettes.");?> </p>
										</li>
										<li>
											<span class="date">Oct <strong>1</strong></span>
											<h3><a href="#"><?php print T_("Lancement de Secret Garden");?> </a></h3>
											<p><?php print T_("Nous sommes très fièrs aujourd'hui de vous présenter ce nouveau site qui est à mi-chemin entre un Data-loggeur et un jardinier ;-).");?> </p>
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
									<p><?php print T_("Secret Garden est né du cerveau d'une personne qui voulait allier ses deux passions : les <span itemprop='applicationCategory'>nouvelles technologies</span> et la <span itemprop='applicationCategory'>culture des plantes</span>. Secret Garden est donc le fruit du mélange des techniques de culture des plantes (gestion de l'éclairage, de l'arrosage etc...) et de la connaissance en programmation Web et C/C++.");?></p>
									
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
											<li>Conception: <span itemprop="author" itemscope itemtype="http://schema.org/Person"><span itemprop="name">Baptiste Montillet</span> </span> </li>
											<li><a href="mention_legal.php" rel="nofollow"> <?php print T_("Mentions Légales");?> </a></li>
											<li>Design: <a href="http://twitter.com/n33co"> n33co </a></li>
										</ul>
									</div>

							</div>
						</div>
					</section>
				
			</div>
</div><div id="skel-panels-defaultWrapper"></div><div id="skel-panels-fixedWrapper" style="position: relative;"></div>
	</body>
</html>