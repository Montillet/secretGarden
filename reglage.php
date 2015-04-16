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
		$cle=$_SESSION["cle"];
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
		<title><?php print T_("Réglage");?></title>
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
		<link rel="stylesheet" href="css/hopscotch.css"></link>
		<!--[if lte IE 8]><script src="js/html5shiv.js"></script><link rel="stylesheet" href="css/ie8.css" /><![endif]-->
<?php 
		if(isset($_POST['cycle'])){
			$cycle=$_POST['cycle'];
			$heure_lumi=$_POST['heure_lumi'];
			$minute_lumi=$_POST['minute_lumi'];
			$heure_arrosage=$_POST['heure_arrosage'];
			$minute_arrosage=$_POST['minute_arrosage'];
			$heure_eau=$_POST['heure_eau'];
			$minute_eau=$_POST['minute_eau'];
			
			if($cycle == "18"){
				$cycle_e="18h/6h";
				$temp=intval($heure_lumi);
				$fin=$temp+18;
				if($fin > "23"){
					$fin=$fin-24;
				}
				$heure_fin=(string)$fin;
			}
			else if ($cycle == "12"){
				$cycle_e="12h/12h";
				$temp=intval($heure_lumi);
				$fin=$temp+12;
				if($fin > "23"){
					$fin=$fin-24;
				}
				$heure_fin=(string)$fin;
			}
			$temp=intval($heure_eau);
			$temp1=intval($heure_arrosage);
			$temp2=intval($minute_eau);
			$temp3=intval($minute_arrosage);
			$heure=$temp+$temp1;
			$minute=$temp2+$temp3;
			if($minute > 59){
				$minute = $minute-60;
				$heure += 1;
			}
			if($heure > 23){
				$heure=$heure-24;
			}
			$fin_h=(string)$heure;
			$fin_m=(string)$minute;
			
			$xml = new DOMDocument('1.0', 'utf-8');
			$xml->formatOutput = true;
				
			$debut = $xml->createElement("actions");
			$lumi = $xml->createElement('lumiere');
			
			$cicle=$xml->createElement('cycle');
			$cicle->appendChild($xml->createTextNode($cycle));
			$lumi->appendChild($cicle);
			
			$heure_l=$xml->createElement('heure_l');
			$heure_l->appendChild($xml->createTextNode($heure_lumi));
			$lumi->appendChild($heure_l);
			
			$min_l=$xml->createElement('min_l');
			$min_l->appendChild($xml->createTextNode($minute_lumi));
			$lumi->appendChild($min_l);
			
			$h_fin=$xml->createElement('heure_fin');
			$h_fin->appendChild($xml->createTextNode($heure_fin));
			$lumi->appendChild($h_fin);
			
			$min_fin=$xml->createElement('minute_fin');
			$min_fin->appendChild($xml->createTextNode($minute_lumi));
			$lumi->appendChild($min_fin);
			
			$debut->appendChild($lumi);
			
			$eau = $xml->createElement('eau');
			
			$arrosage_h = $xml->createElement('arrosage_h');
			$arrosage_h->appendChild($xml->createTextNode($heure_arrosage));
			$eau->appendChild($arrosage_h);
			
			$arrosage_m = $xml->createElement('arrosage_m');
			$arrosage_m->appendChild($xml->createTextNode($minute_arrosage));
			$eau->appendChild($arrosage_m);
			
			$heure_e = $xml->createElement('heure_e');
			$heure_e->appendChild($xml->createTextNode($heure_eau));
			$eau->appendChild($heure_e);
			
			$minute_e = $xml->createElement('minute_e');
			$minute_e->appendChild($xml->createTextNode($minute_eau));
			$eau->appendChild($minute_e);
			
			$h_f=$xml->createElement('fin_h');
			$h_f->appendChild($xml->createTextNode($fin_h));
			$eau->appendChild($h_f);
			
			$m_f=$xml->createElement('fin_m');
			$m_f->appendChild($xml->createTextNode($fin_m));
			$eau->appendChild($m_f);
			
			$debut->appendChild($eau);
			
			$secu = $xml->createElement('securite');
			$secu->appendChild($xml->createTextNode("1"));
			$debut->appendChild($secu);
			
			$tweet = $xml->createElement('twitter');
			$tweet->appendChild($xml->createTextNode("0"));
			$debut->appendChild($tweet);
			
			$xml->appendChild($debut);	
			
			$contexte = stream_context_create(
			array(
				'ftp' => array('overwrite' => TRUE)
				)
			);
			libxml_set_streams_context($contexte);
			// DOM
			$xml->save('ftp:///www/xml/action_'.$cle.'.xml');
			//echo "sauvegarder";
		}
		if(isset($_POST['securite'])){
			$securite = $_POST['securite'] ;
			$dom = new DomDocument();
			$dom->load('ftp://YOURSERVER/www/xml/action_'.$cle.'.xml');
			$nodelist = $dom->getElementsByTagName('securite');
			foreach ($nodelist as $node) {
				 $node->firstChild->nodeValue = $securite;
			} 
			$contexte = stream_context_create(
			array(
				'ftp' => array('overwrite' => TRUE)
				)
			);
			libxml_set_streams_context($contexte);
			$dom->save('ftp://YOURSERVER/www/xml/action_'.$cle.'.xml');
		}
	?>
		<script type="text/javascript" >	
function xmlAction()
		{
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  var request=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  var request=new ActiveXObject("Microsoft.XMLHTTP");
		  }
			request.onreadystatechange = function()
			{
				if (this.readyState == 4) {
					if (this.status == 200) {
						if (this.responseXML != null) {
							// cycle
							var cycle=this.responseXML.getElementsByTagName('cycle');
							var securite=this.responseXML.getElementsByTagName('securite');
							if (securite[0].childNodes[0].nodeValue === "2") {
								document.getElementById("securite").innerHTML = " <?php print T_("maximal");?>";
							}
							else if (securite[0].childNodes[0].nodeValue === "1") {
								document.getElementById("securite").innerHTML = "<?php print T_("recommandé");?>" ;
							}
							else{
								document.getElementById("securite").innerHTML = " <?php print T_("desactivé");?>" ;
		
							}
							if (cycle[0].childNodes[0].nodeValue === "18") {
								document.getElementById("cycle").innerHTML = "18h/6h";
							}
							else {
								document.getElementById("cycle").innerHTML = "12h/12h" ;
							} 
							document.getElementById("depart_LH").innerHTML=this.responseXML.getElementsByTagName('heure_l')[0].childNodes[0].nodeValue;
							document.getElementById("depart_LM").innerHTML=this.responseXML.getElementsByTagName('min_l')[0].childNodes[0].nodeValue;
							document.getElementById("fin_LH").innerHTML=this.responseXML.getElementsByTagName('heure_fin')[0].childNodes[0].nodeValue;
							document.getElementById("fin_LM").innerHTML=this.responseXML.getElementsByTagName('minute_fin')[0].childNodes[0].nodeValue;
							document.getElementById("duree_H").innerHTML=this.responseXML.getElementsByTagName('arrosage_h')[0].childNodes[0].nodeValue;
							document.getElementById("duree_M").innerHTML=this.responseXML.getElementsByTagName('arrosage_m')[0].childNodes[0].nodeValue;
							document.getElementById("depart_EH").innerHTML=this.responseXML.getElementsByTagName('heure_e')[0].childNodes[0].nodeValue;
							document.getElementById("depart_EM").innerHTML=this.responseXML.getElementsByTagName('minute_e')[0].childNodes[0].nodeValue;
							document.getElementById("fin_EH").innerHTML=this.responseXML.getElementsByTagName('fin_h')[0].childNodes[0].nodeValue;
							document.getElementById("fin_EM").innerHTML=this.responseXML.getElementsByTagName('fin_m')[0].childNodes[0].nodeValue;
						}
					}
				}
			}
			request.open("GET", "/xml/action_<?php echo $cle; ?>.xml?nocache=" + Math.random(), true); 
			request.send(null);
		}
</script>
	</head>
	<body class="right-sidebar" onload="xmlAction()">

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
												<li class="current_page_item"><a href="reglage.php"><span class="fa fa-cogs"></span><?php print T_(" Réglage");?></a></li>
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
														<script src="js/hopscotch.js"></script>
														<script src="js/visite.js"></script>
														<header>
															<h2 id = 'hopscotch'><?php print T_("Programmation des cycles de mise en route :");?></h2>
															<span class="byline"><?php print T_("eau/lumière");?></span>
														</header>
														
														<section>
															<header>
																<h3><?php print T_("Configuration actuelle");?></h3>
															</header>
															<form name="form" action="reglage.php" method="post">
																<p><h3><?php print T_("Lumière :");?> </h3></p>
																<input type="radio" name="cycle" value="18" id="18/6" checked="checked" />
																<label for="18/6"><?php print T_("6h/18h");?></label>
																<input type="radio" name="cycle" value="12" id="12/12" />
																<label for="12/12"><?php print T_("12h/12h");?></label>
																<p><strong><?php print T_("Cycle jour/nuit :");?> </strong><span id="cycle" >--</span></p>
																<select name="heure_lumi" >
																	<option value="00" selected="selected" >00</option>
																	<option value="01">01</option>
																	<option value="02">02</option>
																	<option value="03">03</option>
																	<option value="04">04</option>
																	<option value="05">05</option>
																	<option value="06">06</option>
																	<option value="07">07</option>
																	<option value="08">08</option>
																	<option value="09">09</option>
																	<option value="10">10</option>
																	<option value="11">11</option>
																	<option value="12">12</option>
																	<option value="13">13</option>
																	<option value="14">14</option>
																	<option value="15">15</option>
																	<option value="16">16</option>
																	<option value="17">17</option>
																	<option value="18">18</option>
																	<option value="19">19</option>
																	<option value="20">20</option>
																	<option value="21">21</option>
																	<option value="22">22</option>
																	<option value="23">23</option>
																</select> h 
																<select name="minute_lumi" >
																	<option value="00" selected="selected" >00</option>
																	<option value="05" >05</option>
																	<option value="10" >10</option>
																	<option value="15" >15</option>
																	<option value="20" >20</option>
																	<option value="25" >25</option>
																	<option value="30" >30</option>
																	<option value="35" >35</option>
																	<option value="40" >40</option>
																	<option value="45" >45</option>
																	<option value="50" >50</option>
																	<option value="55" >55</option>
																</select>
																<p><strong><?php print T_("Départ cycle :");?> </strong><span id="depart_LH">--</span>h<span id="depart_LM">--</span></p>
																<p id ='light'><strong><?php print T_("Fin cycle :");?> </strong><span id="fin_LH">--</span>h<span id="fin_LM">--</span></p>
																<p><h3><?php print T_("Eau :");?></h3></p>
																<select name="heure_arrosage" >
																	<option value="00" selected="selected" >00</option>
																	<option value="01">01</option>
																	<option value="02">02</option>
																	<option value="03">03</option>
																	<option value="04">04</option>
																	</select> h 
																	<select name="minute_arrosage" >
																	<option value="00" selected="selected" >00</option>
																	<option value="05" >05</option>
																	<option value="10" >10</option>
																	<option value="15" >15</option>
																	<option value="20" >20</option>
																	<option value="25" >25</option>
																	<option value="30" >30</option>
																	<option value="35" >35</option>
																	<option value="40" >40</option>
																	<option value="45" >45</option>
																	<option value="50" >50</option>
																	<option value="55" >55</option>
																	</select>
																<p><strong><?php print T_("Durée de l'arrosage :");?> </strong> <span id="duree_H">--</span>h<span id="duree_M">--</span></p>
																<select name="heure_eau" >
																	<option value="00">00</option>
																	<option value="01">01</option>
																	<option value="02">02</option>
																	<option value="03">03</option>
																	<option value="04">04</option>
																	<option value="05">05</option>
																	<option value="06">06</option>
																	<option value="07">07</option>
																	<option value="08">08</option>
																	<option value="09">09</option>
																	<option value="10">10</option>
																	<option value="11">11</option>
																	<option value="12">12</option>
																	<option value="13">13</option>
																	<option value="14">14</option>
																	<option value="15">15</option>
																	<option value="16">16</option>
																	<option value="17">17</option>
																	<option value="18">18</option>
																	<option value="19">19</option>
																	<option value="20">20</option>
																	<option value="21">21</option>
																	<option value="22">22</option>
																	<option value="23">23</option>
																</select> h 
																<select name="minute_eau" >
																	<option value="00" >00</option>
																	<option value="05" >05</option>
																	<option value="10" >10</option>
																	<option value="15" >15</option>
																	<option value="20" >20</option>
																	<option value="25" >25</option>
																	<option value="30" >30</option>
																	<option value="35" >35</option>
																	<option value="40" >40</option>
																	<option value="45" >45</option>
																	<option value="50" >50</option>
																	<option value="55" >55</option>
																</select>
															<p><strong><?php print T_("Départ cycle :");?> </strong><span id="depart_EH">--</span>h<span id="depart_EM">--</span></p>
															<p id ='water'><strong><?php print T_("Fin cycle :");?> </strong><span id="fin_EH">--</span>h<span id="fin_EM">--</span></p>
															<p id ='security'><h3><?php print T_("Sécurité :");?></h3></p>
															<select name="securite" >
																	<option value="0"><?php print T_("desactivé");?></option>
																	<option value="1" selected="selected" ><?php print T_("recommandé");?></option>
																	<option value="2" ><?php print T_("maximal");?></option>
															</select>
															<p ><strong><?php print T_("niveau de sécurité :");?> </strong><span id="securite" >--</span></p>
															<input TYPE="submit" class="button alt" VALUE="<?php print T_("modifier");?>" >
															</form>
														</section>
													</article>

											</div>
											<div class="4u">
											
												<!-- Sidebar -->
													<section class="box">
														<a  class="image image-full"><img src="images/carotte.jpg" alt="" /></a>
														<header>
															<h3><?php print T_("Panneau de contrôle");?></h3>
														</header>
														<p><?php print T_("Voici votre panneau de contrôle ! C'est depuis ce menu que vous pouvez vérifier et modifier les cycles lumineux et l'arrosage. Vous pouvez aussi configurer le niveau de sécurité. Pour plus d'infos n'hésitez pas à aller sur la FAQ.");?> </p>
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