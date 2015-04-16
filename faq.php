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
       
if ($connect == "1") // Si le visiteur s'est identifié.
{
// On affiche la page cachée.
?>
<!DOCTYPE HTML>

<html>
	<head>
		<title><?php print T_("FAQ");?></title>
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
		<script type="text/javascript">
		
		function affichage_pardefaut() {
			document.getElementById('r0').className = 'off';
			document.getElementById('r1').className = 'off';
			document.getElementById('r2').className = 'off';
			document.getElementById('r3').className = 'off';
			document.getElementById('r4').className = 'off';
			document.getElementById('r5').className = 'off';
			document.getElementById('r6').className = 'off';
			document.getElementById('r7').className = 'off';
			document.getElementById('r8').className = 'off';
		}
		function affiche(id){
			var x = document.getElementById(id).className;
			if (x == 'on') {
				document.getElementById(id) .className = 'off';
			} 
			else
			{
				document.getElementById(id) .className = 'on';
			}
		}
		function ouvre_popup(page) {
			window.open(page,"nom_popup","menubar=no, status=no, scrollbars=no, menubar=no, width=520, height=670");
		}
		</script>
	</head>
	<body class="right-sidebar" onload="affichage_pardefaut();">

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
												<li><a href="index.php"><span class="fa fa-home"></span><?php print T_(" Accueil");?></a></li>
												<li><a href="votrecompte.php"><span class="fa fa-user"></span><?php print T_(" Mon compte");?></a></li>
												<li><a href="moniteur.php"><span class="fa fa-tachometer"></span><?php print T_(" Moniteur");?></a></li>
												<li><a href="reglage.php"><span class="fa fa-cogs"></span><?php print T_(" Réglage");?></a></li>
												<li><a href="statistique.php"><span class="fa fa-bar-chart-o"></span><?php print T_(" Statistiques");?></a></li>
												<li><a href="budget.php"><span class="fa fa-money"></span><?php print T_(" Budget");?></a></li>
												<li class="current_page_item"><a href="faq.php"><span class="fa fa-question"></span><?php print T_(" FAQ");?></a></li>
												<li><a href="logout.php"><span class="fa fa-power-off"></span><?php print T_(" Déconnexion");?></a></li>
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
															<h2><?php print T_("Foire aux questions");?> </h2>
															<span class="byline"><?php print T_("Vous avez une Question ? il y a sûrement la réponse ici.");?>  </span>
														</header>
														<section>
															<header>
																<h3><?php print T_("Sommaire d'aide.");?></h3>
															</header>
															<section>
																<a onclick="affiche('r0');"><?php print T_("Qu'est-ce que la clé Prowl, à quoi sert-elle et où puis-je la trouver ?");?></a>
																<span id="r0"><?php print T_("La clé prowl est la clé qui permet de lier votre Smartphone à Secret Garden grâce à l'application prowl. Comment l'obtenir : tout d'abord vous aurez besoin de télécharger l'application (prowl: growl Client) sur votre Smartphone. Ensuite vous devrez vous enregistrer sur <a href='http://www.prowlapp.com'>prowlapp.com</a>. Une fois enregistré vous devrez vous rendre sur la page <a href='http://www.prowlapp.com/api_settings.php'>API keys</a> pour finalement générer votre clé.");?></span>
															</section>
															<section>
																<a onclick="affiche('r1');"><?php print T_("Que se passera-t-il si la carte de pilotage n'est plus connectée à internet ?");?></a>
																<span id="r1"><?php print T_("Secret Garden continuera à fonctionner normalement et dès que la carte sera reconnectée à internet, elle recommencera à envoyer les données.");?> </span>
															</section>
															<section>
																<a onclick="affiche('r2');"><?php print T_("Certaines jauges sont à '0', pourquoi ?");?></a>
																<span id="r2"><?php print T_("Certaines fonctionnalités de Secret Garden sont optionelles donc il est normal quelles restent à 0 si vous n'avez pas le capteur qui active cette fonction.");?>  </span>
															</section>
															<section>
																<a onclick="affiche('r3');"><?php print T_("Comment savoir de quel capteur je dispose ?");?></a>
																<span id="r3" ><?php print T_("Si vous avez suivi le tutoriel sur le blog <a href='http://blog.votre-secret-garden.fr/fr/article/diy-tutoriel-dinstallation-et-de-configuration-de-la-carte-de-pilotage'>Tutoriel d'installation et de configuration de la carte de pilotage</a> vous devez normalement avoir un capteur de température/humidité ainsi qu'un capteur de moisture (humidité de la plante). Il n'est pas interdit de rajouter une sonde pH ou un captteur de luminosité pour plus de fonctionnalités.");?></span>
															</section>
															<section>
																<a onclick="affiche('r4');"><?php print T_("Comment être sûr que la carte envoie bien les données à Secret Garden");?></a>
																<span id="r4" ><?php print T_("Il y a plusieurs façons de le savoir, la plus simple et d'aller sur la page Moniteur et de regarder la ligne 'dernière synchronisation', ou tout simplement regarder dans 'problème'. Si l'affichage montre 'carte arrêtée' cela signifie que votre carte n'a pas envoyé de donnée depuis plus d'une heure. La deuxième technique est plus compliquée mais donne plus de renseignements, elle consiste à déconnecter la carte et à la reconnecter à votre PC/Mac. Après avoir lancé le logiciel Arduino vous pouvez regarder alors les informations dans le moniteur série.");?></span>
															</section>
															<section>
																<a onclick="affiche('r5');"><?php print T_("À quoi sert la sécurité et que représente-t-elle ?");?></a>
																<span id="r5" ><?php print T_("Il existe 3 niveaux de sécurité, le premier 'Recommandé' sert à déconnecter tous les appareil électrique de l'installation en cas de température anormale (supérieure à 40°C). la carte vous préviendra par notification sur votre smartphone que votre jardin a été mi hors circuit. Il se réenclenchera dès que la température sera revenue à un niveau acceptable. Si le problème persiste cela peut signifier que votre installation est défectuese. Le niveau 'Maximal' prend en compte toutes les fonctions du niveau 'Recommander' mais en plus il gère les arrosages d'urgence. Si par exemple vous avez prevu des cycles d'arrosage trop courts, il se permettra de lancer un cycle d'arrosage supplémentaire pour pallier à ce manque d'eau. Il vous previendra qu'il à dù arroser les plantes en dehors des cycles prévus par notification sur votre smartphone. Enfin dans le mode 'désactivé' aucun niveau de sécurité n'est pris en compte. ");?></span>
															</section>
															<section>
																<a onclick="affiche('r6');"><?php print T_("Je ne comprends pas comment configurer les cycles lumineux");?></a>
																<span id="r6" ><?php print T_("Les cycles de lumière sont configurés de la manière suivante : deux paramètres doivent être configurés, le premier permet de fixer le cycle jour / nuit (D / N). Pour celui-ci, vous avez deux possibilités : le cycle de jour court (D / N : 6h/18h) est généralement utilisé pour favoriser la croissance végétative des plantes (le processus de croissance ne concerne que les feuilles). En revanche, le cycle D / N : 12h/12h favorise généralement la floraison et la fructification. Enfin, le second paramètre que vous pouvez choisir est l'heure du début de la période de la journée.");?> </span>
															</section>
															<section>
																<a onclick="affiche('r7');"><?php print T_("Je ne comprends pas à quoi sert la jauge (moisture) humidité de la plante");?></a>
																<span id="r7" ><a onclick="ouvre_popup('info.php')"><?php print T_("Cliquez ici");?></a> <?php print T_("pour voir la documentation détaillée.");?> </span>
															</section>
															<section>
																<a onclick="affiche('r8');"><?php print T_("Vous avez une erreur Err_temperature_ 1,2,3");?></a>
																<span id="r8" ><a onclick="ouvre_popup('pb.php')"><?php print T_("Cliquez ici");?></a> <?php print T_("pour voir la documentation détaillée.");?></span>
															</section>
															<a href="<?php print T_("http://blog.votre-secret-garden.fr/fr/forum");?>"><?php print T_("Vous n'avez pas trouvé la réponse à votre question allez voir sur le forum");?></a>
															
														</section>
													</article>
											</div>
											<div class="4u">
											
												<!-- Sidebar -->
													<section class="box">
														<a class="image image-full"><img src="images/tomate.jpg" alt="" /></a>
														<header>
															<h3><?php print T_("FAQ");?> </h3>
														</header>
														<p><?php print T_("FAQ ou foire aux questions est la page d'aide de Secret Garden vous y trouverais un ensemble de questions réponse en rapport avec toutes les fonctionnalités que le secret garden a. Si n'avez pas trouvez la réponse à votre question n'hésitez pas à m'envoyer un mail.");?></p>
													</section>
													<section class="box">
														<header>
															<h3><?php print T_("Contribuer");?> </h3>
														</header>
														<p><?php print T_("Vous voulez plus de fonctionnalités sur Secret Garden ou tout simplement vous voulez nous soutenir et permettre à Secret Garden de rester un service gratuit.");?></p>
														<a href="donate.php" class="button alt"><?php print T_("Soutenez-nous");?></a>
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
											<li><a href="mention_legal.php"><?php print T_("Mentions Légales");?></a></li>
											<li><?php print T_("Design");?>: <a href="http://twitter.com/n33co"> n33co </a></li>
										</ul>
									</div>

							</div>
						</div>
					</section>
				
			</div>

	</body>
</html>
<?php
}
 
else // Le mot de passe n'est pas bon.
{
	header('Location: login.php');
}
// On affiche la zone de texte pour rentrer le mot de passe.
?>