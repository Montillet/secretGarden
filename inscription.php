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

function random($car) {
	$string = "";
	$chaine = "abcdefghijklmnpqrstuvwxy";
	srand((double)microtime()*1000000);
	for($i=0; $i<$car; $i++) {
	$string .= $chaine[rand()%strlen($chaine)];
	}
	return $string;
}

session_start();
if (isset($_SESSION['captcha']))//On vérifie que le variable existe.
{
        $captcha=$_SESSION['captcha'];
}
else
{
        $connect=0;//Si $_SESSION['connect'] n'existe pas, on donne la valeur "0".
}
?>
<!DOCTYPE html>

<html>
	<head>
		<title><?php print T_("Inscription");?></title>
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
	$verou = 0;
	$mp=0;
	$mp2=1;
	$code = 0;
	$ok = 0 ;
	try
			{
				$bdd = new PDO('mysql:host=sql9;dbname=', '', '');
			}
	catch (Exception $e)
		{
			die('Erreur : ' . $e->getMessage());
		} 
		
	if( isset($_POST['pseudo']) && isset($_POST['mp']) && isset($_POST['mp']) && isset($_POST['code']) && isset($_POST['email'])  ){
		if( $_POST['mp'] != "" && $_POST['mp2'] != "" ){
			if (filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)){
				$pseudo=htmlspecialchars($_POST['pseudo']);
				$email=htmlspecialchars($_POST['email']);
				$twitter=htmlspecialchars($_POST['twitter']);
				$mp=htmlspecialchars($_POST['mp']);
				$mp2=htmlspecialchars($_POST['mp2']);
				$code=htmlspecialchars($_POST['code']);
				$code=md5($code);
				$chaine = random(20);
				$cle=md5($chaine);
				$verou=1;
			}
			else{
				$verou=0;
			}
		}
	}
	if( ($mp == $mp2) && ($verou == 1) && ($code == $captcha) ) {
		$mp=hash('sha256',$mp2);
		$reponse = $bdd->prepare("SELECT id FROM `votresecretgard`.`table_utilisateur` WHERE `table_utilisateur`.`user` = :login ");
		$reponse->execute(array(':login' => $pseudo));
		$count = $reponse->rowCount();
		if($count == 0){
			$reponse = $bdd->prepare("SELECT id FROM `votresecretgard`.`table_utilisateur` WHERE `table_utilisateur`.`email` = :email ");
			$reponse->execute(array(':email' => $email));
			$count2 = $reponse->rowCount();
			if($count2 == 0){
				$ok = 1 ;
				if($_COOKIE['lang'] == "fr_FR"){
					$message = "Bonjour,<br/>\nVous venez de créer un compte sur le site votre-Secret-Garden.fr<br/>\nnous tenons tout d'abord à vous remercier pour l'intérêt \nque vous portez à Secret Garden.\nAfin de confirmer votre inscription,<br/>\nnous vous demandons de bien vouloir cliquer sur le lien suivant :<br/>\n<a href = 'http://votre-secret-garden.fr/verification.php?p=".$pseudo."&&c=".$cle."'>confirmez votre inscription</a><br/>\nNous vous souhaitons la bienvenue au sein de notre communauté\n noté bien vos identifiant qui sont :<br/>\nPseudo : <strong>".$pseudo."</strong> <br/>\n Mot de passe : <strong>".$mp2."</strong> <br/>\n Clé arduino : <strong>".$cle."</strong> <br/>\n nous restons à votre disposition pour toutes questions ou suggestions.<br/>\nSecret Garden.<br/>\nNous suivre sur Twitter : <a href='http://twitter.com/SecretGarden_fr'>http://twitter.com/SecretGarden_fr</a>";
				}
				else if($_COOKIE['lang'] == "en_US"){
					$message = "Hi,<br/>\nyou just create an account on the website votre-Secret-Garden.fr<br/>\nwe would first want to thank you for your interest in Secret Garden.\nTo confirm your registration,<br/>\nwe ask you to please click on the following link:<br/>\n<a href = 'http://votre-secret-garden.fr/verification.php?p=".$pseudo."&&c=".$cle."'>confirm your registration</a><br/>\nWe welcome you to our community\n please note your id :<br/>\nPseudo : <strong>".$pseudo."</strong> <br/>\n Password : <strong>".$mp2."</strong> <br/>\n Arduino key : <strong>".$cle."</strong> <br/>\n we remain at your disposal for any questions or suggestions.<br/>\nSecret Garden.<br/>\nfollow us on Twitter : <a href='http://twitter.com/SecretGarden_fr'>http://twitter.com/SecretGarden_fr</a>";
				}
				else{
					$message = "Bonjour,<br/>\nVous venez de créer un compte sur le site votre-Secret-Garden.fr<br/>\nnous tenons tout d'abord à vous remercier pour l'intérêt \nque vous portez à Secret Garden.\nAfin de confirmer votre inscription,<br/>\nnous vous demandons de bien vouloir cliquer sur le lien suivant :<br/>\n<a href = 'http://votre-secret-garden.fr/verification.php?p=".$pseudo."&&c=".$cle."'>confirmez votre inscription</a><br/>\nNous vous souhaitons la bienvenue au sein de notre communauté\n noté bien vos identifiant qui sont :<br/>\nPseudo : <strong>".$pseudo."</strong> <br/>\n Mot de passe : <strong>".$mp2."</strong> <br/>\n Clé arduino : <strong>".$cle."</strong> <br/>\n nous restons à votre disposition pour toutes questions ou suggestions.<br/>\nSecret Garden.<br/>\nNous suivre sur Twitter : <a href='http://twitter.com/SecretGarden_fr'>http://twitter.com/SecretGarden_fr</a>";
				}
				$message2=T_("Inscription finie, vous allez recevoir un mail avec vos identifiants ainsi qu'un lien de validation. Veuillez vérifier vos mails afin de pouvoir authentifier votre compte merci.");
				$to = $email;
				$subject = T_('Secret Garden - Email de confirmation');
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'From: "Votre-Secret-Garden" <contact@votre-secret-garden.fr>' . "\r\n";
				$mail = mail($to, $subject, $message, $headers);	
				$reponse = $bdd->prepare("INSERT INTO `votresecretgard`.`table_utilisateur` (user,pass,cle,email,twitter)  VALUES(:pseudo, :pass, :cle, :email, :twitter) ");
				$reponse->execute(array(':pseudo' => $pseudo,':pass' => $mp,':cle' => $cle,':email' => $email,':twitter' => $twitter,));
			}
			else{
				$ok = 0; 
				$message = T_("email déja utilisé !");
			}
		}
		else{
			$ok = 0; 
			$message=T_("pseudo déja utilisé !");
		} 
	}
	else if (($mp == $mp2) && ($verou == 1)){
		$ok = 0; 
		$message=T_("le captcha n'est pas bon");
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
				alert("<?php print T_("Votre compte Twitter doit commencez par @");?>");
				surligne(champ,true);
				return false;
			}
		}
		function verif(champ)
		{
		   if(champ.value.length < 2 || champ.value.length > 25)
		   {
			  alert("<?php print T_("Ce champ doit avoir entre 3 et 24 caractères");?>");
			  surligne(champ, true);
			  return false;
		   }
		   else
		   {
			  surligne(champ, false);
			  return true;
		   }
		}
		function validForm(form)
		{	
			var a = valideEmail(form.email);
			var c = valideTwitter(form.twitter);
			var b = document.forms["form"]["pseudo"].value;
			var d = document.forms["form"]["mp"].value;
			var e = document.forms["form"]["mp2"].value;
			var f = document.forms["form"]["code"].value;
			if( !(b==null || b=="") && !(d==null || d=="") && !(e==null || e=="") && !(f==null || f=="") ){
				if(a || (a && c) ){
					surligne(form.pseudo,false);
					surligne(form.mp,false);
					surligne(form.mp2,false);
					surligne(form.code,false);
					return true;
				}
				else{
					return false;
				}
			}
			else{
				surligne(form.pseudo,true);
				surligne(form.mp,true);
				surligne(form.mp2,true);
				surligne(form.code,true);
				alert("<?php print T_("Veuillez remplir tous les champs requis");?>");
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
															<h2><?php print T_("Créez votre compte Secret Garden gratuitement.");?></h2>
															<span class="byline"><?php print T_("et dès à présent contrôlez votre jardin.");?></span>
														</header>
														<section>
															<header>
																<h3><?php print T_("Informations personnelles :");?></h3>
															</header>
																<?php if ($ok == 1){ echo $message2; } else{ ?>
																<form name="form" action="inscription.php" method="post" onsubmit="validForm(this)" >
																	<p><strong>Pseudo* : </strong><input type="text" name="pseudo"  placeholder="pseudo" onblur="verif(this);"> </p>
																	<p><strong>Email* : </strong><input type="text" name="email"  placeholder="Email" onblur="valideEmail(this);"></p>
																	<p><strong>Twitter : </strong><input type="text" name="twitter"  placeholder="@...." onblur="valideTwitter(this);"></p>
																	<p><strong><?php print T_("mot de passe* :");?> </strong><input type="password" name="mp" onblur="verif(this);"></p>
																	<p><strong><?php print T_("Confirmation de mot de passe* :");?> </strong><input type="password" name="mp2" onblur="verif(this);"></p>
																	<p><img src="captcha.php" alt="Captcha" id="captcha" /> <img src="/images/reload.png" alt="Recharger l'image" title="Recharger l'image" style="cursor:pointer;position:relative;top:-7px;" onclick="document.images.captcha.src='captcha.php?id='+Math.round(Math.random(0)*1000)" /></p>
																	<p><strong><?php print T_("Code de sécurité (captcha)* :");?> </strong><input type="text" name="code"></p>
																	<p><?php print T_("* champ obligatoire");?></p>
																	<p id="titre"><?php if(!empty($message)){echo($message);} else{echo("");}?></p>
																	<input TYPE="submit" class="button alt" VALUE="<?php print T_("Inscription");?>" >
																</form>
																<?php }?>
															
															
														</section>
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
											<h3>Adresse</h3>
											<p>
												Secret Garden<br />
												chemin des gardis<br />
												13490, Jouques, France
											</p>
										</li>
										<li>
											<h3>Mail</h3>
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
											<li>Conception: Baptiste Montillet </li>
											<li><a href="mention_legal.php" rel="nofollow"><?php print T_("Mentions Légales");?></a></li>
											<li>Design: <a href="http://twitter.com/n33co"> n33co </a></li>
										</ul>
									</div>

							</div>
						</div>
					</section>
				
			</div>

	</body>
</html>
