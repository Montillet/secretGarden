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
		<title><?php print T_("Moniteur");?></title>
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
		<script type="text/javascript" src="js/gauge.js"></script>
		<script type="text/javascript" src="http://d3js.org/d3.v3.min.js"></script>
		<noscript>
			<link rel="stylesheet" href="css/skel-noscript.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-desktop.css" />
			<link rel="stylesheet" href="css/style_info_bulle.css" />
		</noscript>
		<link rel="stylesheet" href="css/hopscotch.css"></link>
		<!--[if lte IE 8]><script src="js/html5shiv.js"></script><link rel="stylesheet" href="css/ie8.css" /><![endif]-->
<?php			
		function date_fr($timestamp){
			list($date, $time) = explode(" ", $timestamp);
			list($year, $month, $day) = explode("-", $date);
			list($hour, $min, $sec) = explode(":", $time);
			$months = array("janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre");
			$lastmodified = "le $day ".$months[$month-1]." $year à ${hour}h${min}m${sec}s";
			return $lastmodified;
		}
		// on se connecte à MySQL 
		try
		{
			$bdd = new PDO('mysql:host=sql9;dbname=', '', '');
		}
		catch (Exception $e)
		{
				die('Erreur : ' . $e->getMessage());
		}
		
		$request=$bdd->prepare("SELECT `id` FROM `".$pseudo."`");
		$request->execute();
		$res = $request->fetchAll();
		if (count($res) == 0)
		{
			$tour="tour_on";
		}
		else {
			$tour="tour_off";
		}
		
		$request=$bdd->prepare("SELECT `timestamp` FROM `".$pseudo."` ORDER BY `id` DESC LIMIT 1 ");
		$request->execute();
		$res = $request->fetchAll();
		if(count($res) > 0){
			foreach ($res as $ligne) {
				$timestamp = $ligne['timestamp'];
			}
		}
		else{
			$timestamp="1600-01-01 00:00:00";
		}
		$time=new DateTime($timestamp);
		$diff = $time->diff(new DateTime());
		$annee=$diff->y;
		$mois=$diff->m;
		$jours=$diff->d;
		$heure=$diff->h;
		if(isset($_COOKIE['lang'])){
			if($_COOKIE['lang'] == "fr_FR"){
				$lastmodified=date_fr($timestamp);
			}
			else if($_COOKIE['lang'] == "en_US"){
				$lastmodified=$timestamp;
			}
			else{
			$lastmodified=date_fr($timestamp);
			}
		}
		else{
			$lastmodified=date_fr($timestamp);
		}
		if($annee == 0 && $mois == 0 && $jours == 0 && $heure <2){
			$statut = 1;
		}
		else{
			$statut=0;
		}
		
		$request=$bdd->query("SELECT `temperature`,`humidite`,`moisture`,`ph`,`ppfd`,`pb`,`timestamp` FROM `".$pseudo."` ORDER BY `id` DESC LIMIT 1 ");
		$xml = new DOMDocument('1.0', 'utf-8');
		$xml->formatOutput = true;
		while($row = $request->fetch()) {
			
			$debut = $xml->createElement("inputs");
			
			$temp = $xml->createElement('temperature');
			$temp->appendChild($xml->createTextNode($row['temperature']));
			$debut->appendChild($temp);
			
			$humi = $xml->createElement('humidite');
			$humi->appendChild($xml->createTextNode($row['humidite']));
			$debut->appendChild($humi);
			
			$moist = $xml->createElement('moisture');
			$moist->appendChild($xml->createTextNode($row['moisture']));
			$debut->appendChild($moist);
			
			$ph = $xml->createElement('ph');
			$ph->appendChild($xml->createTextNode($row['ph']));
			$debut->appendChild($ph);
			
			$ppfd = $xml->createElement('ppfd');
			$ppfd->appendChild($xml->createTextNode($row['ppfd']));
			$debut->appendChild($ppfd);
			
			$lastsync = $xml->createElement('lastsync');
			$lastsync->appendChild($xml->createTextNode($lastmodified));
			$debut->appendChild($lastsync);
			
			$pb = $xml->createElement('PB');
			$pb->appendChild($xml->createTextNode($row['pb']));
			$debut->appendChild($pb);
			
			$xml->appendChild($debut);
			
		}
		$request->closeCursor();	
		
		$contexte = stream_context_create(
		array(
			'ftp' => array('overwrite' => TRUE)
			)
		);
		libxml_set_streams_context($contexte);
		// DOM
		$xml->save('ftp://YOURSERVER/www/xml/'.$cle.'.xml');
		
		
	?>
		<script type="text/javascript">
	var gauges = [];
	var temperature = 0 ;
	var humidite = 0 ;
	var moisture = 0;
	var ph = 0 ;
	var ppfd = 0 ;
	function affichage_pardefaut()
	{
		document.getElementById('temperature').className = 'on';
		document.getElementById('moisture').className = 'on';
		document.getElementById('humidite').className = 'on';
		document.getElementById('ph').className = 'off';
		document.getElementById('ppfd').className = 'off';
	}
	function verif ()
        {
            var etat = document.getElementById('gt').checked;
			var etat2 = document.getElementById('gh').checked;
			var etat3 = document.getElementById('gm').checked;
			var etat4 = document.getElementById('gph').checked;
			var etat5 = document.getElementById('gppfd').checked;
             
            if(etat)
            {
                document.getElementById('temperature').className = 'on';
            }
            else if(!etat)
            {
                document.getElementById('temperature').className = 'off';
            }
			if(etat2)
            {
                document.getElementById('humidite').className = 'on';
            }
            else if(!etat2)
            {
                document.getElementById('humidite').className = 'off';
            }
			if(etat3)
            {
                document.getElementById('moisture').className = 'on';
            }
            else if(!etat3)
            {
                document.getElementById('moisture').className = 'off';
            }
			if(etat4)
            {
                document.getElementById('ph').className = 'on';
            }
            else if(!etat4)
            {
                document.getElementById('ph').className = 'off';
            }
			if(etat5)
            {
                document.getElementById('ppfd').className = 'on';
            }
            else if(!etat5)
            {
                document.getElementById('ppfd').className = 'off';
            }
			else {
				document.getElementById('temperature').className = 'on';
				document.getElementById('moisture').className = 'on';
				document.getElementById('humidite').className = 'on';
				document.getElementById('ph').className = 'off';
				document.getElementById('ppfd').className = 'off';
			}
        }
			function createphGauge(name, label, min, max)
			{
				var config = 
				{
					size: 150,
					label: label,
					min: undefined != min ? min : 0,
					max: undefined != max ? max : 14,
					minorTicks: 5
				}
				
				var range = config.max - config.min;
				config.yellowZones = [{ from: config.min , to: config.min + range*0.35 }];
				config.greenZones = [{ from: config.min + range*0.35, to: config.min + range*0.64 }];
				config.redZones = [{ from: config.min + range*0.64, to: config.max }];
				
				gauges[name] = new Gauge(name + "GaugeContainer", config);
				gauges[name].render();
			}
			function createppfdGauge(name, label, min, max)
			{
				var config = 
				{
					size: 150,
					label: label,
					min: undefined != min ? min : 0,
					max: undefined != max ? max : 300,
					minorTicks: 5
				}
				
				var range = config.max - config.min;
				config.greenZones = [{ from: config.min + range*0.23, to: config.min + range*0.667 }];
				config.yellowZones = [{ from: config.min + range*0.667, to: config.max }];
				
				gauges[name] = new Gauge(name + "GaugeContainer", config);
				gauges[name].render();
			}
			function createTGauge(name, label, min, max)
			{
				var config = 
				{
					size: 150,
					label: label,
					min: undefined != min ? min : 0,
					max: undefined != max ? max : 50,
					minorTicks: 5
				}
				
				var range = config.max - config.min;
				config.yellowZones = [{ from: config.min + range*0.75, to: config.min + range*0.9 }];
				config.redZones = [{ from: config.min + range*0.9, to: config.max }];
				
				gauges[name] = new Gauge(name + "GaugeContainer", config);
				gauges[name].render();
			}
			function createHGauge(name, label, min, max)
			{
				var config = 
				{
					size: 150,
					label: label,
					min: undefined != min ? min : 0,
					max: undefined != max ? max : 100,
					minorTicks: 5
				}
				
				var range = config.max - config.min;
				config.yellowZones = [{ from: config.min + range*0.75, to: config.min + range*0.9 }];
				config.redZones = [{ from: config.min + range*0.9, to: config.max }];
				
				gauges[name] = new Gauge(name + "GaugeContainer", config);
				gauges[name].render();
			}
			function createMGauge(name, label, min, max)
			{
				var config = 
				{
					size: 150,
					label: label,
					min: undefined != min ? min : 0,
					max: undefined != max ? max : 800,
					minorTicks: 5
				}
				
				var range = config.max - config.min;
				config.yellowZones = [{ from: config.min , to: config.min + range*0.75 }];
				config.greenZones = [{ from: config.min + range*0.75, to: config.min + range*0.875 }];
				config.redZones = [{ from: config.min + range*0.875, to: config.max }];
				
				gauges[name] = new Gauge(name + "GaugeContainer", config);
				gauges[name].render();
			}
			
			function createGauges()
			{
				createTGauge("temperature", "°C");
				createHGauge("humidite", "%");
				createMGauge("moisture", "moisture");
				createphGauge("ph", "ph");
				createppfdGauge("ppfd", "µmol/m².s");
				//createGauge("test", "Test", -50, 50 );
			}
			
			function updateGauges()
			{
				for (var key in gauges)
				{
					var value = getValue(gauges[key]);
					gauges[key].redraw(value);
				}
			}
			
			function getValue(gauge)
			{	
				if(gauge.config.label == "°C"){
					var a = parseFloat(temperature);
					return a;
				}
				if(gauge.config.label == "%"){
					var b = parseFloat(humidite); 
					return b;
				}
				if(gauge.config.label == "moisture"){
					var c = parseFloat(moisture);
					return c;
				}
				if(gauge.config.label == "ph"){
					var d = parseFloat(ph);
					return d;
				}
				if(gauge.config.label == "µmol/m².s"){
					var e = parseFloat(ppfd);
					return e;
				}
			}			
			function initialize()
			{
				createGauges();
				setInterval(updateGauges, 5000);
			}
		function getArduinoIO()
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
							var pb,sync; 
							var statut = <?php echo $statut; ?> ;
							temperature = this.responseXML.getElementsByTagName('temperature')[0].childNodes[0].nodeValue;	
							humidite = this.responseXML.getElementsByTagName('humidite')[0].childNodes[0].nodeValue;
							moisture = this.responseXML.getElementsByTagName('moisture')[0].childNodes[0].nodeValue;
							ph = this.responseXML.getElementsByTagName('ph')[0].childNodes[0].nodeValue;
							ppfd = this.responseXML.getElementsByTagName('ppfd')[0].childNodes[0].nodeValue;
							// lastsync
							sync=this.responseXML.getElementsByTagName('lastsync')[0].childNodes[0].nodeValue;
							//alert(sync);
							document.getElementById("lastsync").innerHTML = sync ;
							if( statut == 1){
								document.getElementById("statut").innerHTML = "<?php print T_("Carte connectée.");?>" ;
							}
							else{
								document.getElementById("statut").innerHTML = "<?php print T_("Carte déconnectée.");?>" ;
							}
							// PB
							pb=this.responseXML.getElementsByTagName('PB');
							if (pb[0].childNodes[0].nodeValue === "ok") {
								document.getElementById("pb").innerHTML = "<?php print T_("Tout est ok");?>";
								//document.getElementById("pb").style.color = "#00FF00";								
							}
							else {
								document.getElementById("pb").innerHTML = pb[0].childNodes[0].nodeValue + " !" ;
								document.getElementById("pb").style.color="red";
							} 
						}
					}
				}
			}
			request.open("GET", "xml/<?php echo $cle; ?>.xml?nocache=" + Math.random(), true); 
			request.send(null);
			//setTimeout('getArduinoIO()', 14990);
		}
	</script>
	</head>
	<body class="right-sidebar" onload="getArduinoIO(); initialize(); affichage_pardefaut();">

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
										<h1 id ='title' ><a href="index.php">Secret Garden</a></h1>
									
									<!-- Nav -->
										<nav id="nav">
											<ul>
												<li><a href="index.php"><span class="fa fa-home"></span><?php print T_(" Accueil");?></a></li>
												<li><a href="votrecompte.php"><span class="fa fa-user"></span><?php print T_(" Mon compte");?></a></li>
												<li class="current_page_item"><a href="moniteur.php"><span class="fa fa-tachometer"></span><?php print T_(" Moniteur");?></a></li>
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
															<h2><?php print T_("Votre Jardin");?></h2>
															<span class="byline"><?php print T_("en live");?></span>
														</header>
														
														<section>
															<input type="hidden" id='<?php echo $tour ?>'>
															<div id="temperature">
															<header>
																<h3><?php print T_("Température ambiante :");?></h3>
															</header>
															<span id="temperatureGaugeContainer"></span>
															</div>
														</section>
														<section>
															<div id="humidite">
															<header>
																<h3><?php print T_("Humidité ambiante :");?></h3>
															</header>
															<span id="humiditeGaugeContainer"></span>
															</div>
														</section>
														<section>
															<div id="moisture">
															<header>
																<h3><?php print T_("Humidité du terreau de la plante (moisture) :");?></h3>
															</header>
															<span id="moistureGaugeContainer"></span>
															</div>
														</section>
														<section>
															<div id="ph">
															<header>
																<h3><?php print T_("pH de l'eau :");?></h3>
															</header>
															<span id="phGaugeContainer"></span>
															</div>
														</section>
														<section>
															<div id="ppfd">
															<header>
																<h3><span class="hotspot" onmouseover="tooltip.show('<?php print T_("Photosynthetic Photon Flux Density : <br/> représente la quantité de lumière (le nombre de photons) qui est utile pour la photosynthèse.<br/> Pour plus d'info consultez la FAQ");?>');" onmouseout="tooltip.hide();">PPFD :</span></h3>
															</header>
															<span id="ppfdGaugeContainer"></span>
															</div>
														</section>
														<section>
															<header>
																<h3><?php print T_("Dernière synchronisation :");?></h3>
															</header>
																<span id= "lastsync">--</span>
														</section>
														<section>
															<header>
																<h3><?php print T_("Statut de la carte :");?></h3>
															</header>
																<span id= "statut">--</span>
														</section>
														<section>
															<header>
																<h3><?php print T_("Problème ? :");?></h3>
																<span id= "pb">--</span>
															</header>
															<a onclick="getArduinoIO();" class="button alt"><span class="fa fa-undo"></span>&nbsp;<?php print T_("Rafraîchir");?></a>
														</section>
														<script type="text/javascript" src="js/hopscotch.js"></script>
														<script type="text/javascript" src="js/visite.js"></script>
													</article>

											</div>
											<div class="4u"> 
											
												<!-- Sidebar -->
													<section class="box">
														<a  class="image image-full"><img src="images/plante.jpg" alt="" /></a>
														<header>
															<h3><?php print T_("Bienvenue");?> <?php echo $pseudo ; ?> !</h3>
														</header>
														<p><?php print T_("Bienvenue sur la page d'accueil de Votre jardin ! Vous allez pouvoir ici contrôler toutes les constantes de votre jardin. Si vous avez des questions n'hésitez pas à consulter la FAQ.");?></p>
														<a href="faq.php" class="button alt"><?php print T_("FAQ");?></a>
													</section>
													<section class="box">
														<header>
															<h3><?php print T_("Configuration des jauges");?></h3>
														</header>
														<p><?php print T_("Dans ce menu, vous pouvez sélectionner les jauges que vous voulez voir ou ne pas voir.");?></p>
														<ul class="divided">
															<li><input type="checkbox" checked="checked" name="gt" id="gt" onChange="verif();" /><?php print T_("jauge de température");?></li>
															<li><input type="checkbox" checked="checked" name="gh" id="gh" onChange="verif();"/><?php print T_("jauge d' humidité");?></li>
															<li><input type="checkbox" checked="checked" name="gm" id="gm" onChange="verif();"/><?php print T_("jauge de moisture");?></li>
															<li><input type="checkbox" name="gph" id="gph" onChange="verif();"/><?php print T_("jauge pH");?></li>
															<li><input type="checkbox" name="gco2" id="gppfd" onChange="verif();"/><span class="hotspot" onmouseover="tooltip.show('<?php print T_("Photosynthetic Photon Flux Density : <br/> représente la quantité de lumière (le nombre de photons) qui est utile pour la photosynthèse.<br/> Pour plus d'info consultez la FAQ");?>');" onmouseout="tooltip.hide();"><?php print T_("jauge PPFD");?></span></li>
														</ul>
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