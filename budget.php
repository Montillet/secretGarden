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
		<title><?php print T_("Budget");?></title>
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
		<link rel="stylesheet" href="css/hopscotch.css"></link>
		<!--[if lte IE 8]><script src="js/html5shiv.js"></script><link rel="stylesheet" href="css/ie8.css" /><![endif]-->
		<?php 
		$cout=0;
		$duree=0;
		$heureC=18;
		$heureF=12;
		if(isset($_POST['W'])){
			$puissance=$_POST['W'];
			$prix=$_POST['E'];
			$dureeC=$_POST['croissance'];
			$dureeF=$_POST['flo'];
			$type=$_POST['type'];
			$dureeT=$_POST['total'];
		}
		if(isset($_POST['flo']) || isset($_POST['total'])){	
			if($dureeT > 0){
				$dureeC=$dureeT/2;
				$dureeF=$dureeC;
			}
			$cout = ((($puissance/1000)*$heureC*$dureeC)+(($puissance/1000)*$heureF*$dureeF))*$prix;
			$cout = number_format($cout,2, ',', ' ');
			$duree = $dureeC+$dureeF;
		}
		if(isset($_POST['type'])){
			if($type == "00"){
				$puissance = ($puissance)/2;
				$coutE = ((($puissance/1000)*$heureC*$dureeC)+(($puissance/1000)*$heureF*$dureeF))*$prix;
				$economie = number_format($cout-$coutE,2, ',', ' ');
			}
			else if($type == "01"){
				$puissance = ($puissance*3)/4;
				$coutE = ((($puissance/1000)*$heureC*$dureeC)+(($puissance/1000)*$heureF*$dureeF))*$prix;
				$economie = number_format($cout-$coutE,2, ',', ' ');
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

			function verifForm(champ)
			{
				var a = champ.value;
				if (a==null || a=="")
				{
					surligne(champ, true);
					return false;
				}
				if (isNaN(a))
				{
					alert("<?php print T_("Caractère non valide, veuillez entrer un nombre");?>");
					surligne(champ, true);
					return false;
				}
				else{
					surligne(champ, false);
					return true;
				}
			}

			function validForm(form)
			{	
				var a = document.forms["form"]["croissance"].value;
				var b = document.forms["form"]["flo"].value;
				var c = document.forms["form"]["total"].value;
				var e = verifForm(form.W);
				var f = verifForm(form.E);
				if(	e && f){
					if ((a != "") && (b != "") && (c==null || c=="") ){
						var g = verifForm(form.croissance);
						var h = verifForm(form.flo);
						if ( g && h){
							document.getElementById("id_div_1").style.display="";
							return true ;
						}
						else{
							return false;
						}
						
					}
					else if ((c != "") && (a==null || a=="") && (b==null || b=="")){
						var i = verifForm(form.total);
						if ( i ){
							document.getElementById("id_div_1").style.display="";
							return true ;
						}
						else{
							return false;
						}
					}
					else {
						if( (a==null || a=="") && (b==null || b=="") && (c==null || c=="") ){
							surligne(form.croissance,true);
							surligne(form.flo,true);
							surligne(form.total,true);
							alert("<?php print T_("Veuillez remplir le nombre de jours d'éclairage que vous prévoyez");?> ");
							document.getElementById("ou").style.fontSize="x-large";
							document.getElementById("ou").style.textDecoration="underline";
							return false;
						}
						else if( a != "" && (c==null || c=="") ){
							surligne(form.flo,true);
							alert("<?php print T_("Ne vous arrêtez pas en si bon chemin, veuillez remplir le nombre de jours de floraison que vous prévoyez ");?>");
							return false ;
						}
						else if( b != "" && (c==null || c=="") ){
							alert("<?php print T_("Ne vous arrêtez pas en si bon chemin, veuillez remplir le nombre de jours de croissance que vous prévoyez ");?>");
							surligne(form.croissance,true);
							return false;
						}
					}
				}
				else {
					alert("<?php print T_("Il manque des informations essentielles.");?>");
					return false;
				}
			} 
		</script>
	</head>
	<body class="right-sidebar" >

		<!-- Header Wrapper -->
			<div id="header-wrapper">
				<div class="container">
					<div class="row">
						<div class="12u">
							<script src="js/hopscotch.js"></script>
							<script src="js/visite.js"></script>
							<!-- Header -->
								<section id="header">
									<a class="drapeau_EnUs" href="?lang=en_US"></a>
									<a class="drapeau_Fr" href="?lang=fr_FR"></a>
									<!-- Logo -->
										<h1 id='fin'><a href="index.php">Secret Garden</a></h1>
									
									<!-- Nav -->
										<nav id="nav">
											<ul>
												<li><a href="index.php"><span class="fa fa-home"></span><?php print T_(" Accueil");?></a></li>
												<li><a href="votrecompte.php"><span class="fa fa-user"></span><?php print T_(" Mon compte");?></a></li>
												<li><a href="moniteur.php"><span class="fa fa-tachometer"></span><?php print T_(" Moniteur");?></a></li>
												<li><a href="reglage.php"><span class="fa fa-cogs"></span><?php print T_(" Réglage");?></a></li>
												<li><a href="statistique.php"><span class="fa fa-bar-chart-o"></span><?php print T_(" Statistiques");?></a></li>
												<li class="current_page_item"><a href="budget.php"><span class="fa fa-money"></span><?php print T_(" Budget");?></a></li>
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
															<h2 id ='budget'><?php print T_("Votre budget");?></h2>
															<span class="byline"><?php print T_("Estimer/Calculer");?></span>
														</header>
														
														<section>
															<header>
																<h3><?php print T_("Votre installation");?></h3>
															</header>
															<form name="form" action="budget.php" method="post" onsubmit="return validForm(this)" >
															<label><strong> <?php print T_("Type de Lampe :");?> </strong></label>
															<select name="type" >
																<option value="00">MH/HPS</option>
																<option value="01">Fluo Compact (néons)</option>
																<option value="02">LED</option>
															</select> 
															<br/>
															<br/>
															<p><strong><?php print T_("Puissance de votre lampe :");?> </strong><input type="text" name="W" placeholder="400" onblur="verifForm(this)">&nbsp; W</p>
															<p><span class="hotspot" onmouseover="tooltip.show('<?php print T_("Où trouver cette information : vous trouverez cette information sur votre dernière facture EDF.");?>');" onmouseout="tooltip.hide();"><strong> <?php print T_("Prix du kWh");?> : </strong></span>&nbsp;<input type="text" name="E" placeholder="0.1428" onblur="verifForm(this)">&nbsp; €/kWh</p>
															<p><strong><?php print T_("Prévision du nombre de jours de croissance : ");?></strong><input type="text" name="croissance" placeholder="31" >&nbsp; <?php print T_("Jours");?></p>
															<p><strong><?php print T_("Prévision du nombre de jours de floraison : ");?></strong><input type="text" name="flo" placeholder="31" >&nbsp; <?php print T_("Jours");?></p>
															<p id="ou"><?php print T_("ou");?> </p>
															<p><strong><?php print T_("Prévision du nombre de jours totaux");?> </strong> <input type="text" name="total" placeholder="62" >&nbsp; <?php print T_("Jours");?></p>
															<div id="id_div_1" style="<?php if(isset($_POST['W'])){ echo "" ; } else { echo "display:none;"; } ?> " ><p>&nbsp; <?php print T_("votre plantation vous coûtera en électricité environ :");?> <?php echo $cout;?> €</p>
															<p><?php print T_("sur une durée de ");?><?php echo $duree;?> <?php print T_("Jours");?></p>
															<?php 
															if(isset($_POST['type'])){
																if($type == "00" || $type == "01"){
																	print T_("&nbsp; Pour la même puissance lumineuse si vous prenez une lampe LED de $puissance Watt vous economiseriez environ $economie € </br> &nbsp; sur votre facture d'electricité. ");
																} 
																} ?>
															</div>
															<br/>
															<input TYPE="submit" class="button alt" VALUE="<?php print T_("Lancer une estimation");?>" >
															</form>
														</section>
													</article>
											</div>
											<div class="4u">
											
												<!-- Sidebar -->
													<section class="box">
														<a class="image image-full"><img src="images/plante_menthe.jpg" alt="" /></a>
														<header>
															<h3><?php print T_("Budget");?></h3>
														</header>
														<p><?php print T_("Sur cette page, vous pouvez faire une simulation de votre consommation électrique pour estimer combien cela vous coûtera en euro. Si vous ne connaissez pas certaines informations requises, il est possible que vous les trouviez dans la FAQ");?></p>
														<a href="faq.php" class="button alt"><?php print T_("FAQ");?></a>
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