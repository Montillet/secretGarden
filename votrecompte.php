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
	try
			{
				$bdd = new PDO('mysql:host=sql9;dbname=', '', '');
			}
	catch (Exception $e)
		{
			die('Erreur : ' . $e->getMessage());
		} 
		$request=$bdd->prepare('SELECT `id`,`user`,`cle`,`email`,`twitter`,`prowl` FROM `table_utilisateur` WHERE `user` =:pseudo ');
		$request->execute(array(':pseudo' => $pseudo));
		while($row = $request->fetch()){
			$id=$row['id'];
			$pseudo=$row['user'];
			$cle=$row['cle'];
			$email=$row['email'];
			$twitter=$row['twitter'];
			$prowl=$row['prowl'];
		}
// On affiche la page cachée.
?>
<!DOCTYPE HTML>

<html>
	<head>
		<title><?php print T_("Mon compte");?></title>
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
		<script type="text/javascript" src="js/script.js"></script>
		<noscript>
			<link rel="stylesheet" href="css/skel-noscript.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-desktop.css" />
		</noscript>
		<!--[if lte IE 8]><script src="js/html5shiv.js"></script><link rel="stylesheet" href="css/ie8.css" /><![endif]-->
		<?php 
		$verou = 0;
		$mp=0;
		$mp2=1;
		$imail=0;
		$tweet=0;
		$modif=0;
		$prowl2=0;
		$message="";
		$filename='http://votre-secret-garden.fr/xml/action_'.$cle.'.xml';
		
		$file_headers = @get_headers($filename);
		if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
			$check = 5 ;
		}
		else {
			$dom = new DomDocument();
			$dom->load('ftp://YOURSERVER/www/xml/action_'.$cle.'.xml');
			$nodelist = $dom->getElementsByTagName('twitter');
			foreach ($nodelist as $node) {
				 $check = $node->firstChild->nodeValue;
			}
		}
		
		if( isset($_POST['set_tweet'])){
			$securite=$_POST['set_tweet'];
			if (file_exists($filename)) {
				$dom = new DomDocument();
				$dom->load('ftp://YOURSERVER/www/xml/action_'.$cle.'.xml');
				foreach ($nodelist as $node) {
					 $node->firstChild->nodeValue = $securite;
				} 
				$check=$_POST['set_tweet'];
				$contexte = stream_context_create(
				array(
					'ftp' => array('overwrite' => TRUE)
					)
				);
				libxml_set_streams_context($contexte);
				$dom->save('ftp://YOURSERVER/www/xml/action_'.$cle.'.xml');
			}
			else{
				$message=T_("Veuillez d'abord configurer les cycles lumineux et d'arrosage dans la page réglage.");
			}
		}
		if( (isset($_POST['mp']) && isset($_POST['mp']) ) || (isset($_POST['email']) && isset($_POST['twitter'])) ){
			if( $_POST['mp'] != "" && $_POST['mp2'] != "" && isset($_POST['email']) && isset($_POST['twitter']) ){
				if (filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)){
					$mp=htmlspecialchars($_POST['mp']);
					$mp=htmlspecialchars($_POST['mp2']);
					$imail=$_POST['email'];
					$tweet=htmlspecialchars($_POST['twitter']);
					$verou = 1 ;
				}
				else{
					$verou =0;
				}
				
			}
			else if( isset($_POST['email']) && isset($_POST['twitter']) ){
				if (filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)){
					$imail=$_POST['email'];
					$tweet=htmlspecialchars($_POST['twitter']);
					$verou = 2 ;
				}
				else{
					$verou =0;
				}
				
			}
		}
		
		if( isset($_POST['prowl'])){
			$prowl2=htmlspecialchars($_POST['prowl']);
			$verou = 2 ;
		}
		if( ($tweet != $twitter) && $verou == 2){
			$request=$bdd->prepare('UPDATE `votresecretgard`.`table_utilisateur` SET `twitter` = :tweet WHERE `table_utilisateur`.`id` = :id ');
			$request->execute(array(':tweet' => $tweet ,':id' => $id ));
			$modif = 1 ;
		}
		if(($imail != $email) && $verou == 2 ){
			$request=$bdd->prepare('UPDATE `votresecretgard`.`table_utilisateur` SET `email`= :email WHERE `table_utilisateur`.`id` = :id ');
			$request->execute(array(':email' => $imail ,':id' => $id ));
			$modif = 1 ;
		}
		if(($prowl2 != $prowl) && $verou == 2 ){
			$request=$bdd->prepare('UPDATE `votresecretgard`.`table_utilisateur` SET `prowl`= :prowl WHERE `table_utilisateur`.`id` = :id ');
			$request->execute(array(':prowl' => $prowl2 ,':id' => $id ));
			$modif = 1 ;
		}
		if( ($verou == 1) && ($mp == $mp2) ){
			$mp1=hash('sha256',$mp);
			$bdd->prepare('UPDATE `votresecretgard`.`table_utilisateur` SET `pass` = :password WHERE `table_utilisateur`.`id` = :id');
			$request->execute(array(':password' => $mp1 ,':id' => $id ));
			$modif = 1 ;
		}
		if($modif == 1){
			$request=$bdd->prepare('SELECT `email`,`twitter`,`prowl` FROM `table_utilisateur` WHERE `user` = :pseudo ' );
			$request->execute(array(':pseudo' => $pseudo));
			while($row = $request->fetch()){
				$email=$row['email'];
				$twitter=$row['twitter'];
				$prowl=$row['prowl'];
			}
			$modif = 0 ;
		}
?>
		<script type="text/javascript">
	function checkbox_Value(){
		var tweet = <?php echo $check ; ?> ;
		if( tweet == 1 ){
			document.forms["form"]["tweeton"].checked = true ;
			document.forms["form"]["tweetoff"].checked = false ;
		}
		else if(tweet == 0) {
			document.forms["form"]["tweeton"].checked = false ;
			document.forms["form"]["tweetoff"].checked = true ;
		}
		else {
			document.forms["form"]["tweeton"].checked = false ;
			document.forms["form"]["tweetoff"].checked = false ;
			document.getElementById('titre').style.color="red";
		}
	}
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
		alert("<?php print T_("l'adresse mail fournis n'est pas valide.");?>");
		surligne(champ,true);
		return false;
		}
	else{
		surligne(champ,false);
		return true;
		}
	}

	function valideMp(champ1,champ2){
		var d = document.forms["form"]["mp"].value;
		var e = document.forms["form"]["mp2"].value;
		if((d == e) && !(d==null || d=="") && !(e==null || e=="")){
			surligne(champ1,false);
			surligne(champ2,false);
			return true;
		}
		else{
			alert("<?php print T_("Les deux mots de passe ne sont pas identiques.");?>");
			surligne(champ1,true);
			surligne(champ2,true);
			return false;
		}
	}

	function valideTwitter(champ){
		var c = document.forms["form"]["twitter"].value;
		if( !(c==null || c=="") && (c.indexOf("@") == 0)){
			surligne(champ,false);
			return true;
		}
		else{
			alert("<?php print T_("votre compte Twitter doit commencez par @");?>");
			surligne(champ,true);
			return false;
		}
	}
	function validForm(form)
	{	
		var a = valideEmail(form.email);
		var c = valideTwitter(form.twitter);
		var d = document.forms["form"]["mp"].value;
		var e = document.forms["form"]["mp2"].value;
		if( (d==null || d=="") && (e==null || e=="") ){
			if( a && c){
				return true;
			}
			else {
				return false;
			}
		}
		else {
			var b = valideMp(form.mp,form.mp2);
			if( a && b && c){
				return true;
			}
			else {
				return false;
			}
		}
	}
		</script>
	</head>
	<body class="right-sidebar" onload="checkbox_Value();">

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
												<li class="current_page_item"><a href="votrecompte.php"><span class="fa fa-user"></span><?php print T_(" Mon compte");?></a></li>
												<li><a href="moniteur.php"><span class="fa fa-tachometer"></span><?php print T_(" Moniteur");?></a></li>
												<li><a href="reglage.php"><span class="fa fa-cogs"></span><?php print T_(" Réglage");?></a></li>
												<li><a href="statistique.php"><span class="fa fa-bar-chart-o"></span><?php print T_(" Statistiques");?></a></li>
												<li><a href="budget.php"><span class="fa fa-money"></span><?php print T_(" Budget");?></a></li>
												<li><a href="faq.php"><span class="fa fa-question"></span><?php print T_(" FAQ");?></a></li>
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
															<h2><?php print T_("Mon compte");?></h2>
															<span class="byline"><?php print T_("Mes informations");?></span>
														</header>
														<section>
															<header>
																<h3><?php print T_("Informations personnelles");?></h3>
																<p id="titre"><?php if(!empty($message)){echo($message);} else{echo("");}?></p>
																<form name="form" action="votrecompte.php" method="post" onsubmit="validForm(this)" >
																	<p><strong><?php print T_("Pseudo ");?>: </strong> &nbsp; &nbsp; <font style="text-transform: uppercase;"><?php echo $pseudo; ?></font> </p>
																	<p><strong><?php print T_("Clé arduino ");?>: </strong> &nbsp; &nbsp; <?php echo $cle; ?> </p>
																	<p><strong><?php print T_("Clé prowl");?> : </strong><input type="text" name="prowl" size="47" value="<?php echo $prowl; ?>"></p>
																	<p><strong><?php print T_("Email ");?>: </strong><input type="text" name="email" value="<?php echo $email; ?>" onblur="valideEmail(this);"></p>
																	<p><strong><?php print T_("Twitter ");?>: </strong><input type="text" name="twitter" value="<?php echo $twitter; ?>" onblur="valideTwitter(this);"></p>
																	<p><input type="radio" name="set_tweet" value="0" id="tweetoff" />
																	<label for="tweetoff"><?php print T_("Rapport twitter desactivé");?></label>
																	<input type="radio" name="set_tweet" value="1" id="tweeton"  />
																	<label for="tweeton"><?php print T_("Rapport twitter activé");?></label></p>
																	<p><strong><?php print T_("Nouveau mot de passe");?> : </strong><input type="password" name="mp"></p>
																	<p><strong><?php print T_("Confirmation de mot de passe ");?>: </strong><input type="password" name="mp2"></p>
																	<input TYPE="submit" class="button alt" VALUE="<?php print T_("Mettre à jour votre profil");?>" >
																</form>
															</header>
															
														</section>
													</article>
											</div>
											<div class="4u">
											
												<!-- Sidebar -->
													<section class="box">
														<a class="image image-full"><img src="images/framboise.jpg" alt="" /></a>
														<header>
															<h3><?php print T_("Mon compte");?> </h3>
														</header>
														<p><?php print T_("Je pense que tout est dit dans le titre, vous pouvez trouver ici toutes les informations relatives à votre compte.");?> </p>
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