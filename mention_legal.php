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

?>
<!DOCTYPE HTML>

<html>
	<head>
		<title><?php print T_("Mentions Légales");?></title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="robots" content="noindex" />
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
	<body class="right-sidebar" >

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
															<h2><?php print T_("Mentions Légales :");?></h2>
														</header>
														<h2>Informations légales</h2>
														<h3>1. Présentation du site.</h3>
														<p>En vertu de l'article 6 de la loi n° 2004-575 du 21 juin 2004 pour la confiance dans l'économie numérique, il est précisé aux utilisateurs du site <a href="http://votre-secret-garden.fr/" title="Baptiste Montillet - votre-secret-garden.fr">votre-secret-garden.fr</a> l'identité des différents intervenants dans le cadre de sa réalisation et de son suivi :</p>
														<p><strong>Propriétaire</strong> : Baptiste Montillet - chemin des gardis 13490 Jouques FRANCE<br />
														<strong>Créateur</strong>  : <a href="votre-secret-garden.fr">Baptiste Montillet</a><br />
														<strong>Responsable publication</strong> : Baptiste Montillet – contact@votre-secret-garden.fr<br />
														Le responsable publication est une personne physique ou une personne morale.<br />
														<strong>Webmaster</strong> : Baptiste Montillet – contact@votre-secret-garden.fr<br />
														<strong>Hébergeur</strong> : NUXIT – NUXIT - 400 Avenue Roumanille BP 60177 - 06903 Sophia Antipolis Cedex FRANCE<br />
														
														<h3>2. Conditions générales d’utilisation du site et des services proposés.</h3>
														<p>L’utilisation du site <a href="http://votre-secret-garden.fr/" title="Baptiste Montillet - votre-secret-garden.fr">votre-secret-garden.fr</a> implique l’acceptation pleine et entière des conditions générales d’utilisation ci-après décrites. Ces conditions d’utilisation sont susceptibles d’être modifiées ou complétées à tout moment, les utilisateurs du site <a href="http://votre-secret-garden.fr/" title="Baptiste Montillet - votre-secret-garden.fr">votre-secret-garden.fr</a> sont donc invités à les consulter de manière régulière.</p>
														<p>Ce site est normalement accessible à tout moment aux utilisateurs. Une interruption pour raison de maintenance technique peut être toutefois décidée par Baptiste Montillet, qui s’efforcera alors de communiquer préalablement aux utilisateurs les dates et heures de l’intervention.</p>
														<p>Le site <a href="http://votre-secret-garden.fr/" title="Baptiste Montillet - votre-secret-garden.fr">votre-secret-garden.fr</a> est mi à jour régulièrement par Baptiste Montillet. De la même façon, les mentions légales peuvent être modifiées à tout moment : elles s’imposent néanmoins à l’utilisateur qui est invité à s’y référer le plus souvent possible afin d’en prendre connaissance.</p>
														<p>Baptiste Montillet ne pourra être tenue responsable des omissions, des inexactitudes et des carences dans la mise à jour, qu’elles soient de son fait ou du fait des tiers partenaires qui lui fournissent ces informations.</p>
														<p>Tous les informations indiquées sur le site <a href="http://votre-secret-garden.fr/" title="Baptiste Montillet - votre-secret-garden.fr">votre-secret-garden.fr</a> sont données à titre indicatif, et sont susceptibles d’évoluer. Par ailleurs, les renseignements figurant sur le site <a href="http://votre-secret-garden.fr/" title="Baptiste Montillet - votre-secret-garden.fr">votre-secret-garden.fr</a> ne sont pas exhaustifs. Ils sont donnés sous réserve de modifications ayant été apportées depuis leur mise en ligne.</p>											
														<p>Toutefois, ces informations et/ou documents peuvent être susceptibles de contenir des inexactitudes techniques et des erreurs typographiques.</p>
														
														<h3>3. Limitations contractuelles sur les données techniques.</h3>
														<p>Le site utilise entre autres la technologie JavaScript.</p>
														<p>Le site Internet ne pourra être tenu responsable de dommages matériels liés à l’utilisation du site. De plus, l’utilisateur du site s’engage à accéder au site en utilisant un matériel récent, ne contenant pas de virus et avec un navigateur de dernière génération mi-à-jour</p>
														
														<h3>4. Propriété intellectuelle et contrefaçons.</h3>
														<p>Baptiste Montillet est propriétaire des droits de propriété intellectuelle ou détient les droits d’usage sur tous les éléments accessibles sur le site, notamment les textes, images, graphismes, logo, icônes, algorithmes.</p>
														<p>Toute reproduction, représentation, modification, publication, adaptation de tout ou partie des éléments du site, quel que soit le moyen ou le procédé utilisé, est interdite, sauf autorisation écrite préalable de : Baptiste Montillet.</p>
														<p>Toute exploitation non autorisée du site ou de l’un quelconque des éléments qu’il contient sera considérée comme constitutive d’une contrefaçon et poursuivie conformément aux dispositions des articles L.335-2 et suivants du Code de Propriété Intellectuelle.</p>
														
														<h3>5. Limitations de responsabilité.</h3>
														<p>Baptiste Montillet ne pourra être tenue responsable des dommages directs et indirects causés au matériel de l’utilisateur, lors de l’accès au site votre-secret-garden.fr, et résultant soit de l’utilisation d’un matériel ne répondant pas aux spécifications indiquées au point 4, soit de l’apparition d’un bug ou d’une incompatibilité.</p>
														<p>Baptiste Montillet ne pourra également être tenue responsable des dommages indirects (tels par exemple qu’une perte de marché ou perte d’une chance) consécutifs à l’utilisation du site <a href="http://votre-secret-garden.fr/" title="Baptiste Montillet - votre-secret-garden.fr">votre-secret-garden.fr</a>.</p>
														<p>Des espaces interactifs (possibilité de poser des questions dans l’espace contact) sont à la disposition des utilisateurs. Baptiste Montillet se réserve le droit de supprimer, sans mise en demeure préalable, tout contenu déposé dans cet espace qui contreviendrait à la législation applicable en France, en particulier aux dispositions relatives à la protection des données. Le cas échéant, Baptiste Montillet se réserve également la possibilité de mettre en cause la responsabilité civile et/ou pénale de l’utilisateur, notamment en cas de message à caractère raciste, injurieux, diffamant, ou pornographique, quel que soit le support utilisé (texte, photographie…).</p>
														<p>L’utilisation des informations et/ou documents disponibles sur ce site se fait sous l’entière et seule responsabilité de l’utilisateur, qui assume la totalité des conséquences pouvant en découler, sans que Baptiste Montillet puisse être recherché à ce titre, et sans recours contre ce dernier.</p>
														<p>Baptiste Montillet ne pourra en aucun cas être tenu responsable de tout dommage de quelque nature qu’il soit résultant de l’interprétation ou de l’utilisation des informations et/ou documents disponibles sur ce site.</p>
														
														<h3>6. Gestion des données personnelles.</h3>
														<p>En France, les données personnelles sont notamment protégées par la loi n° 78-87 du 6 janvier 1978, la loi n° 2004-801 du 6 août 2004, l'article L. 226-13 du Code pénal et la Directive Européenne du 24 octobre 1995.</p>
														<p>A l'occasion de l'utilisation du site <a href="http://votre-secret-garden.fr/" title="Baptiste Montillet - votre-secret-garden.fr">votre-secret-garden.fr</a>, peuvent êtres recueillies : l'URL des liens par l'intermédiaire desquels l'utilisateur a accédé au site <a href="http://votre-secret-garden.fr/" title="Baptiste Montillet - votre-secret-garden.fr">votre-secret-garden.fr</a>, le fournisseur d'accès de l'utilisateur, l'adresse de protocole Internet (IP) de l'utilisateur.</p>
														<p> En tout état de cause Baptiste Montillet ne collecte des informations personnelles relatives à l'utilisateur que pour le besoin de certains services proposés par le site <a href="http://votre-secret-garden.fr/" title="Baptiste Montillet - votre-secret-garden.fr">votre-secret-garden.fr</a>. L'utilisateur fournit ces informations en toute connaissance de cause, notamment lorsqu'il procède par lui-même à leur saisie.</p>
														<p>Conformément aux dispositions des articles 38 et suivants de la loi 78-17 du 6 janvier 1978 relative à l’informatique, aux fichiers et aux libertés, tout utilisateur dispose d’un droit d’accès, de rectification et d’opposition aux données personnelles le concernant, en effectuant sa demande écrite et signée, accompagnée d’une copie du titre d’identité avec signature du titulaire de la pièce, en précisant l’adresse à laquelle la réponse doit être envoyée.</p>
														<p>Aucune information personnelle de l'utilisateur du site <a href="http://votre-secret-garden.fr/" title="Baptiste Montillet - votre-secret-garden.fr">votre-secret-garden.fr</a> n'est publiée à l'insu de l'utilisateur, échangée, transférée, cédée ou vendue sur un support quelconque à des tiers. Seule l'hypothèse du rachat de Baptiste Montillet et de ses droits permettrait la transmission des dites informations à l'éventuel acquéreur qui serait à son tour tenu de la même obligation de conservation et de modification des données vis à vis de l'utilisateur du site <a href="http://votre-secret-garden.fr/" title="Baptiste Montillet - votre-secret-garden.fr">votre-secret-garden.fr</a>.</p>
														<p>Les bases de données sont protégées par les dispositions de la loi du 1er juillet 1998 transposant la directive 96/9 du 11 mars 1996 relative à la protection juridique des bases de données.</p>
														
														<h3>7. Liens hypertextes et cookies.</h3>
														<p>Le site <a href="http://votre-secret-garden.fr/" title="Baptiste Montillet - votre-secret-garden.fr">votre-secret-garden.fr</a> contient un certain nombre de liens hypertextes vers d’autres sites, mi en place avec l’autorisation de Baptiste Montillet. Cependant, Baptiste Montillet n’a pas la possibilité de vérifier le contenu des sites ainsi visités, et n’assumera en conséquence aucune responsabilité de ce fait.</p>
														<p>La navigation sur le site <a href="http://votre-secret-garden.fr/" title="Baptiste Montillet - votre-secret-garden.fr">votre-secret-garden.fr</a> est susceptible de provoquer l’installation de cookie(s) sur l’ordinateur de l’utilisateur. Un cookie est un fichier de petite taille, qui ne permet pas l’identification de l’utilisateur, mais qui enregistre des informations relatives à la navigation d’un ordinateur sur un site. Les données ainsi obtenues visent à faciliter la navigation ultérieure sur le site, et ont également vocation à permettre diverses mesures de fréquentation.</p>
														<p>Le refus d’installation d’un cookie peut entraîner l’impossibilité d’accéder à certains services. L’utilisateur peut toutefois configurer son ordinateur de la manière suivante, pour refuser l’installation des cookies :</p>
														<p>Sous Internet Explorer : onglet outil (pictogramme en forme de rouage en haut a droite) / options internet. Cliquez sur Confidentialité et choisissez Bloquer tous les cookies. Validez sur Ok.</p>
														<p>Sous Firefox : en haut de la fenêtre du navigateur, cliquez sur le bouton Firefox, puis aller dans l'onglet Options. Cliquer sur l'onglet Vie privée.
														Paramétrez les Règles de conservation sur :  utiliser les paramètres personnalisés pour l'historique. Enfin décochez-la pour  désactiver les cookies.</p>
														<p>Sous Safari : Cliquez en haut à droite du navigateur sur le pictogramme de menu (symbolisé par un rouage). Sélectionnez Paramètres. Cliquez sur Afficher les paramètres avancés. Dans la section "Confidentialité", cliquez sur Paramètres de contenu. Dans la section "Cookies", vous pouvez bloquer les cookies.</p>
														<p>Sous Chrome : Cliquez en haut à droite du navigateur sur le pictogramme de menu (symbolisé par trois lignes horizontales). Sélectionnez Paramètres. Cliquez sur Afficher les paramètres avancés. Dans la section "Confidentialité", cliquez sur préférences.  Dans l'onglet "Confidentialité", vous pouvez bloquer les cookies.</p>

														<h3>8. Droit applicable et attribution de juridiction.</h3>
														<p>Tout litige en relation avec l’utilisation du site <a href="http://votre-secret-garden.fr/" title="Baptiste Montillet - votre-secret-garden.fr">votre-secret-garden.fr</a> est soumis au droit français. Il est fait attribution exclusive de juridiction aux tribunaux compétents de Paris.</p>
														
														<h3>9. Les principales lois concernées.</h3>
														<p>Loi n° 78-87 du 6 janvier 1978, notamment modifiée par la loi n° 2004-801 du 6 août 2004 relative à l'informatique, aux fichiers et aux libertés.</p>
														<p> Loi n° 2004-575 du 21 juin 2004 pour la confiance dans l'économie numérique.</p>
														
														<h3>10. Lexique.</h3>
														<p>Utilisateur : Internaute se connectant, utilisant le site susnommé.</p>
														<p>Informations personnelles : « les informations qui permettent, sous quelque forme que ce soit, directement ou non, l'identification des personnes physiques auxquelles elles s'appliquent » (article 4 de la loi n° 78-17 du 6 janvier 1978).</p>
														Crédits : les mentions légales ont étés générées et offertes par Subdelirium <a target="_blank" href="http://www.subdelirium.com/competences/creation-de-sites-web/" alt="creation site web">création de site web</a></p>

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
