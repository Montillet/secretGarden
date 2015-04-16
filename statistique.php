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
		<title>Statistiques</title>
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
	try
		{
			$pdo = new PDO('mysql:host=sql9;dbname=', '', '');
		}
		catch (Exception $e)
		{
				die('Erreur : ' . $e->getMessage());
		}
	$reqT = $pdo->prepare("SELECT `timestamp`,`temperature` FROM `".$pseudo."` ORDER BY `timestamp` ASC ");
	$reqT->execute();
	$reqH = $pdo->prepare("SELECT `timestamp`,`humidite` FROM `".$pseudo."` ORDER BY `timestamp` ASC ");
	$reqH->execute();
	$reqM = $pdo->prepare("SELECT `timestamp`,`moisture` FROM `".$pseudo."` ORDER BY `timestamp` ASC ");
	$reqM->execute();
	$reqPh = $pdo->prepare("SELECT `timestamp`,`ph` FROM `".$pseudo."` ORDER BY `timestamp` ASC ");
	$reqPh->execute();
	$reqPpfd = $pdo->prepare("SELECT `timestamp`,`ppfd` FROM `".$pseudo."` ORDER BY `timestamp` ASC ");
	$reqPpfd->execute();
	
	function saveCSV($stream,$fichier){
		$tableau=explode("z",$stream);
		$taille=count($tableau);
		$file = fopen("/YOURSERVER/www/csv/" . $fichier . ".csv","w");
		for ($i=0; $i<$taille; $i++) {
			fwrite($file,$tableau[$i]);
			fwrite($file,"\r\n");
		}
		fclose($file);
	}
	function makeCSV($req){
		$csv_terminated = "z";
		$csv_separator = ",";
		$csv_enclosed = '';
		$csv_escaped = "\\";
	 
		//recupération nom de colonne
		foreach(range(0, $req->columnCount() - 1) as $column_index){
			$meta = $req->getColumnMeta($column_index);
			$nameCol[] = $meta['name'];
		}
	 
		//création ligne d'en tete
		array_walk($nameCol, 'format', $csv_enclosed);
		$out = implode(',', $nameCol).$csv_terminated;
	 
		//création ligne d'enregistrement
		while ($row = $req->fetch(PDO::FETCH_ASSOC)) {
			array_walk($row, 'format', $csv_enclosed);
			$out .= implode(',', $row).$csv_terminated;   
		}
		return $out;
	}
	//fonction de formatage des cellules
	function format(&$item,$key, $escaped){
		$item = $escaped.addcslashes($item,$escaped).$escaped;
	}
	
	//creation du csv
	$outT = makeCSV($reqT);
	saveCSV($outT,'temp_'.$cle);
	$outH = makeCSV($reqH);
	saveCSV($outH,'humi_'.$cle);
	$outM = makeCSV($reqM);
	saveCSV($outM,'moist_'.$cle);
	$outPh = makeCSV($reqPh);
	saveCSV($outPh,'ph_'.$cle);
	$outPpfd = makeCSV($reqPpfd);
	saveCSV($outPpfd,'ppfd_'.$cle);
	
?>	
<script type="text/javascript">
function delete_table(){
		var x = confirm("<?php print T_("êtes vous vraiment sûr de vouloir supprimer toutes vos données");?>");
		if (x==true) {
			document.location.href="delete.php"; 
		}
		else {
			
		}
	}
function affichage_pardefaut()
	{	
		
		document.getElementById('graphique1').className = 'off';
		document.getElementById('graphique2').className = 'off';
		document.getElementById('graphique3').className = 'off';
		document.getElementById('graphique4').className = 'off';
		document.getElementById('graphique5').className = 'off';
		document.getElementById('graphique6').className = 'on';
		document.getElementById('graphique7').className = 'on';
		document.getElementById('graphique8').className = 'on';
		document.getElementById('graphique9').className = 'off';
		document.getElementById('graphique10').className = 'off';
	
	}
	function verif ()
        {
			var etatGraph = document.getElementById('graph').value;
            var etat = document.getElementById('gt').checked;
			var etat2 = document.getElementById('gh').checked;
			var etat3 = document.getElementById('gm').checked;
			var etat4 = document.getElementById('gph').checked;
			var etat5 = document.getElementById('gppfd').checked;
			
			if(etatGraph == "area"){
				document.getElementById('graphique1').className = 'off';
				document.getElementById('graphique2').className = 'off';
				document.getElementById('graphique3').className = 'off';
				document.getElementById('graphique4').className = 'off';
				document.getElementById('graphique5').className = 'off';
				document.getElementById('graphique6').className = 'on';
				document.getElementById('graphique7').className = 'on';
				document.getElementById('graphique8').className = 'on';
				document.getElementById('graphique9').className = 'on';
				document.getElementById('graphique10').className = 'on';
				
				if(etat)
				{
					document.getElementById('graphique6').className = 'on';
				}
				else if(!etat)
				{
					document.getElementById('graphique6').className = 'off';
				}
				if(etat2)
				{
					document.getElementById('graphique7').className = 'on';
				}
				else if(!etat2)
				{
					document.getElementById('graphique7').className = 'off';
				}
				if(etat3)
				{
					document.getElementById('graphique8').className = 'on';
				}
				else if(!etat3)
				{
					document.getElementById('graphique8').className = 'off';
				}
				if(etat4)
				{
					document.getElementById('graphique9').className = 'on';
				}
				else if(!etat4)
				{
					document.getElementById('graphique9').className = 'off';
				}
				if(etat5)
				{
					document.getElementById('graphique10').className = 'on';
				}
				else if(!etat5)
				{
					document.getElementById('graphique10').className = 'off';
				}
			}
			else if(etatGraph == "ligne"){
				
				document.getElementById('graphique1').className = 'on';
				document.getElementById('graphique2').className = 'on';
				document.getElementById('graphique3').className = 'on';
				document.getElementById('graphique4').className = 'on';
				document.getElementById('graphique5').className = 'on';
				document.getElementById('graphique6').className = 'off';
				document.getElementById('graphique7').className = 'off';
				document.getElementById('graphique8').className = 'off';
				document.getElementById('graphique9').className = 'off';
				document.getElementById('graphique10').className = 'off';
				if(etat)
				{
					document.getElementById('graphique1').className = 'on';
				}
				else if(!etat)
				{
					document.getElementById('graphique1').className = 'off';
				}
				if(etat2)
				{
					document.getElementById('graphique2').className = 'on';
				}
				else if(!etat2)
				{
					document.getElementById('graphique2').className = 'off';
				}
				if(etat3)
				{
					document.getElementById('graphique3').className = 'on';
				}
				else if(!etat3)
				{
					document.getElementById('graphique3').className = 'off';
				}
				if(etat4)
				{
					document.getElementById('graphique4').className = 'on';
				}
				else if(!etat4)
				{
					document.getElementById('graphique4').className = 'off';
				}
				if(etat5)
				{
					document.getElementById('graphique5').className = 'on';
				}
				else if(!etat5)
				{
					document.getElementById('graphique5').className = 'off';
				}
			}
			else {
				document.getElementById('graphique1').className = 'off';
				document.getElementById('graphique2').className = 'off';
				document.getElementById('graphique3').className = 'off';
				document.getElementById('graphique4').className = 'off';
				document.getElementById('graphique5').className = 'off';
				document.getElementById('graphique6').className = 'on';
				document.getElementById('graphique7').className = 'on';
				document.getElementById('graphique8').className = 'on';
				document.getElementById('graphique9').className = 'off';
				document.getElementById('graphique10').className = 'off';
			}
        }
</script>
	</head>
	<body class="right-sidebar" onload="affichage_pardefaut();" >

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
												<li class="current_page_item"><a href="statistique.php"><span class="fa fa-bar-chart-o"></span><?php print T_(" Statistiques");?></a></li>
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
															<h2 id='graph'><?php print T_("Vue d'ensemble");?></h2>
															<span class="byline"><?php print T_("Graphiques");?></span>
														</header>
															<span id="graphique6"></span>
															<span id="graphique7"></span>
															<span id="graphique8"></span>
															<span id="graphique9"></span>
															<span id="graphique10"></span>
															<span id="graphique1"></span>
															<span id="graphique2"></span>
															<span id="graphique3"></span>
															<span id="graphique4"></span>
															<span id="graphique5"></span>
															<input type="button" onclick="delete_table();" class="button alt" VALUE="<?php print T_("Effacer les données");?>" >
													</article>
													<script type="text/javascript">
															var largeur = screen.width;
															if (largeur < 1000 ){
																var margin = {top: 20, right: 20, bottom: 30, left: 30},
																width = 300 - margin.left - margin.right,
																height = 300 - margin.top - margin.bottom,
																margin3 = {top: 10, right: 10, bottom: 100, left: 40},
																margin2 = {top: 230, right: 10, bottom: 20, left: 40},
																width2 = 300 - margin3.left - margin3.right,
																height3 = 300 - margin3.top - margin3.bottom,
																height2 = 300 - margin2.top - margin2.bottom;
															}
															else{
																var margin = {top: 20, right: 20, bottom: 30, left: 50},
																width = 600 - margin.left - margin.right,
																height = 500 - margin.top - margin.bottom,
																margin3 = {top: 10, right: 10, bottom: 100, left: 40},
																margin2 = {top: 430, right: 10, bottom: 20, left: 40},
																width2 = 600 - margin3.left - margin3.right,
																height3 = 500 - margin3.top - margin3.bottom,
																height2 = 500 - margin2.top - margin2.bottom;
															}
															var parseDate = d3.time.format("%Y-%m-%d %H:%M:%S").parse;

															var x = d3.time.scale().range([0, width2]),
															x2 = d3.time.scale().range([0, width2]),
															x3 = d3.time.scale().range([0, width2]),
															x4 = d3.time.scale().range([0, width2]),
															x5 = d3.time.scale().range([0, width2]),
															x6 = d3.time.scale().range([0, width2]),
															x7 = d3.time.scale().range([0, width2]),
															x8 = d3.time.scale().range([0, width2]),
															x9 = d3.time.scale().range([0, width2]),
															x10 = d3.time.scale().range([0, width2]),	
															x11 = d3.time.scale().range([0, width]),
															x12 = d3.time.scale().range([0, width]),
															x13 = d3.time.scale().range([0, width]),
															x14 = d3.time.scale().range([0, width]),
															x15 = d3.time.scale().range([0, width]);

															var y = d3.scale.linear().range([height3, 0]),
															y2 = d3.scale.linear().range([height2, 0]),
															y3 = d3.scale.linear().range([height3, 0]),
															y4 = d3.scale.linear().range([height2, 0]),
															y5 = d3.scale.linear().range([height3, 0]),
															y6 = d3.scale.linear().range([height2, 0]),
															y7 = d3.scale.linear().range([height3, 0]),
															y8 = d3.scale.linear().range([height2, 0]),
															y9 = d3.scale.linear().range([height3, 0]),
															y10 = d3.scale.linear().range([height2, 0]),
															y11 = d3.scale.linear().range([height, 0]),
															y12 = d3.scale.linear().range([height, 0]),
															y13 = d3.scale.linear().range([height, 0]),
															y14 = d3.scale.linear().range([height, 0]),
															y15 = d3.scale.linear().range([height, 0]);
															
															var xAxis = d3.svg.axis().scale(x).orient("bottom"),
																xAxis2 = d3.svg.axis().scale(x2).orient("bottom"),
																yAxis = d3.svg.axis().scale(y).orient("left"),
																
																xAxis3 = d3.svg.axis().scale(x3).orient("bottom"),
																xAxis4 = d3.svg.axis().scale(x4).orient("bottom"),
																yAxis2 = d3.svg.axis().scale(y3).orient("left");
																
																xAxis5 = d3.svg.axis().scale(x5).orient("bottom"),
																xAxis6 = d3.svg.axis().scale(x6).orient("bottom"),
																yAxis3 = d3.svg.axis().scale(y5).orient("left");
																
																xAxis7 = d3.svg.axis().scale(x7).orient("bottom"),
																xAxis8 = d3.svg.axis().scale(x8).orient("bottom"),
																yAxis4 = d3.svg.axis().scale(y7).orient("left");
																
																xAxis9 = d3.svg.axis().scale(x9).orient("bottom"),
																xAxis10 = d3.svg.axis().scale(x10).orient("bottom"),
																yAxis5 = d3.svg.axis().scale(y9).orient("left");
															
															var xAxis11 = d3.svg.axis().scale(x11).orient("bottom"),
																xAxis12 = d3.svg.axis().scale(x12).orient("bottom"),
																xAxis13 = d3.svg.axis().scale(x13).orient("bottom"),
																xAxis14 = d3.svg.axis().scale(x14).orient("bottom"),
																xAxis15 = d3.svg.axis().scale(x15).orient("bottom");
																
															var yAxis11 = d3.svg.axis().scale(y11).orient("left"),
																yAxis12 = d3.svg.axis().scale(y12).orient("left"),
																yAxis13 = d3.svg.axis().scale(y13).orient("left"),
																yAxis14 = d3.svg.axis().scale(y14).orient("left"),
																yAxis15 = d3.svg.axis().scale(y15).orient("left");
																
															var line = d3.svg.line()
																.x(function(d) { return x11(d.timestamp); })
																.y(function(d) { return y11(d.temperature); });

															var line2 = d3.svg.line()
																.x(function(d) { return x12(d.timestamp); })
																.y(function(d) { return y12(d.humidite); });

															var line3 = d3.svg.line()
																.x(function(d) { return x13(d.timestamp); })
																.y(function(d) { return y13(d.moisture); });
															
															var line4 = d3.svg.line()
																.x(function(d) { return x14(d.timestamp); })
																.y(function(d) { return y14(d.ph); });
															
															var line5 = d3.svg.line()
																.x(function(d) { return x15(d.timestamp); })
																.y(function(d) { return y15(d.ppfd); });
															
															var brush = d3.svg.brush()
																.x(x2)
																.on("brush", brushed);

															var brush2 = d3.svg.brush()
																.x(x4)
																.on("brush", brushed2);	

															var brush3 = d3.svg.brush()
																.x(x6)
																.on("brush", brushed3);	
															
															var brush4 = d3.svg.brush()
																.x(x8)
																.on("brush", brushed4);	

															var brush5 = d3.svg.brush()
																.x(x10)
																.on("brush", brushed5);	

															var area = d3.svg.area()
																.interpolate("monotone")
																.x(function(d) { return x(d.timestamp); })
																.y0(height3)
																.y1(function(d) { return y(d.temperature); });

															var area3 = d3.svg.area()
																.interpolate("monotone")
																.x(function(d) { return x3(d.timestamp); })
																.y0(height3)
																.y1(function(d) { return y3(d.humidite); });	

															var area5 = d3.svg.area()
																.interpolate("monotone")
																.x(function(d) { return x5(d.timestamp); })
																.y0(height3)
																.y1(function(d) { return y5(d.moisture); });
															
															var area7 = d3.svg.area()
																.interpolate("monotone")
																.x(function(d) { return x7(d.timestamp); })
																.y0(height3)
																.y1(function(d) { return y7(d.ph); });	

															var area9 = d3.svg.area()
																.interpolate("monotone")
																.x(function(d) { return x9(d.timestamp); })
																.y0(height3)
																.y1(function(d) { return y9(d.ppfd); });
																
															var area2 = d3.svg.area()
																.interpolate("monotone")
																.x(function(d) { return x2(d.timestamp); })
																.y0(height3)
																.y1(function(d) { return y2(d.temperature); });

															var area4 = d3.svg.area()
																.interpolate("monotone")
																.x(function(d) { return x4(d.timestamp); })
																.y0(height2)
																.y1(function(d) { return y4(d.humidite); });	

															var area6 = d3.svg.area()
																.interpolate("monotone")
																.x(function(d) { return x6(d.timestamp); })
																.y0(height2)
																.y1(function(d) { return y6(d.moisture); });
															
															var area8 = d3.svg.area()
																.interpolate("monotone")
																.x(function(d) { return x8(d.timestamp); })
																.y0(height2)
																.y1(function(d) { return y8(d.ph); });	

															var area10 = d3.svg.area()
																.interpolate("monotone")
																.x(function(d) { return x10(d.timestamp); })
																.y0(height2)
																.y1(function(d) { return y10(d.ppfd); });	

															var svg1 = d3.select("#graphique1")
																.append("svg:svg")
																.attr("width", width + margin.left + margin.right)
																.attr("height", height + margin.top + margin.bottom);

															var svg2 = d3.select("#graphique2")
																.append("svg:svg")
																.attr("width", width + margin.left + margin.right)
																.attr("height", height + margin.top + margin.bottom);

															var svg3 = d3.select("#graphique3")
																.append("svg:svg")
																.attr("width", width + margin.left + margin.right)
																.attr("height", height + margin.top + margin.bottom);
															
															var svg4 = d3.select("#graphique4")
																.append("svg:svg")
																.attr("width", width + margin.left + margin.right)
																.attr("height", height + margin.top + margin.bottom);

															var svg5 = d3.select("#graphique5")
																.append("svg:svg")
																.attr("width", width + margin.left + margin.right)
																.attr("height", height + margin.top + margin.bottom);
															
															var graph = svg1.append("g")
																.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
															var graph2 = svg2.append("g")
																.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
															var graph3 = svg3.append("g")
																.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
															var graph4 = svg4.append("g")
																.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
															var graph5 = svg5.append("g")
																.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
																
															var svg6 = d3.select("span#graphique6").append("svg")
																.attr("width", width2 + margin3.left + margin3.right)
																.attr("height", height3 + margin3.top + margin3.bottom);
																
															svg6.append("defs").append("clipPath")
																.attr("id", "clip")
															  .append("rect")
																.attr("width", width2)
																.attr("height", height3);
																
															var svg7 = d3.select("span#graphique7").append("svg")
																.attr("width", width2 + margin3.left + margin3.right)
																.attr("height", height3 + margin3.top + margin3.bottom);
																
															svg7.append("defs").append("clipPath")
																.attr("id", "clip")
															  .append("rect")
																.attr("width", width2)
																.attr("height", height3);
															
															var svg8 = d3.select("span#graphique8").append("svg")
																.attr("width", width2 + margin3.left + margin3.right)
																.attr("height", height3 + margin3.top + margin3.bottom);
																
															svg8.append("defs").append("clipPath")
																.attr("id", "clip")
															  .append("rect")
																.attr("width", width2)
																.attr("height", height3);
															
															var svg9 = d3.select("span#graphique9").append("svg")
																.attr("width", width2 + margin3.left + margin3.right)
																.attr("height", height3 + margin3.top + margin3.bottom);
																
															svg9.append("defs").append("clipPath")
																.attr("id", "clip")
															  .append("rect")
																.attr("width", width2)
																.attr("height", height3);
															
															var svg10 = d3.select("span#graphique10").append("svg")
																.attr("width", width2 + margin3.left + margin3.right)
																.attr("height", height3 + margin3.top + margin3.bottom);
																
															svg10.append("defs").append("clipPath")
																.attr("id", "clip")
															  .append("rect")
																.attr("width", width2)
																.attr("height", height3);
																
															var focus = svg6.append("g")
																.attr("transform", "translate(" + margin3.left + "," + margin3.top + ")");

															var context = svg6.append("g")
																.attr("transform", "translate(" + margin2.left + "," + margin2.top + ")");

															var focus2 = svg7.append("g")
																.attr("transform", "translate(" + margin3.left + "," + margin3.top + ")");

															var context2 = svg7.append("g")
																.attr("transform", "translate(" + margin2.left + "," + margin2.top + ")");

															var focus3 = svg8.append("g")
																.attr("transform", "translate(" + margin3.left + "," + margin3.top + ")");

															var context3 = svg8.append("g")
																.attr("transform", "translate(" + margin2.left + "," + margin2.top + ")");
															
															var focus4 = svg9.append("g")
																.attr("transform", "translate(" + margin3.left + "," + margin3.top + ")");

															var context4 = svg9.append("g")
																.attr("transform", "translate(" + margin2.left + "," + margin2.top + ")");		
															
															var focus5 = svg10.append("g")
																.attr("transform", "translate(" + margin3.left + "," + margin3.top + ")");

															var context5 = svg10.append("g")
																.attr("transform", "translate(" + margin2.left + "," + margin2.top + ")");
																
															d3.csv("/csv/temp_<?php echo $cle; ?>.csv", function(error, data) {
															  data.forEach(function(d) {
																d.timestamp = parseDate(d.timestamp);
																d.temperature = +d.temperature;
															  });
															  
															  var bisectDate = d3.bisector(function(d) { return d.timestamp; }).left,
																  formatValue = d3.format(",.2f"),
																  formatCurrency = function(d) { return formatValue(d) + "°C"; };
																
															  x11.domain(d3.extent(data, function(d) { return d.timestamp; }));
															  y11.domain(d3.extent(data, function(d) { return d.temperature; }));

															  graph.append("g")
																  .attr("class", "x axis")
																  .attr("transform", "translate(0," + height + ")")
																  .call(xAxis11);

															  graph.append("g")
																  .attr("class", "y axis")
																  .call(yAxis11)
																.append("text")
																  .attr("transform", "rotate(-90)")
																  .attr("y", 6)
																  .attr("dy", ".71em")
																  .style("text-anchor", "end")
																  .text("<?php print T_("Température ambiante (°C)");?>");

															  graph.append("path")
																  .datum(data)
																  .attr("class", "line")
																  .attr("d", line);
															  
															  data.sort(function(a, b) {
																return a.timestamp - b.timestamp;
															  });
															  
															  var focus = svg1.append("g")
																  .attr("class", "focus")
																  .style("display", "none");

															  focus.append("circle")
																  .attr("r", 4.5);

															  focus.append("text")
																  .attr("x", 9)
																  .attr("dy", ".35em");
															  
															  graph.append("rect")
																  .attr("class", "overlay")
																  .attr("width", width)
																  .attr("height", height)
																  .on("mouseover", function() { focus.style("display", null); })
																  .on("mouseout", function() { focus.style("display", "none"); })
																  .on("mousemove", mousemove);

															  function mousemove() {
																var x0 = x11.invert(d3.mouse(this)[0]),
																	i = bisectDate(data, x0, 1),
																	d0 = data[i - 1],
																	d1 = data[i],
																	d = x0 - d0.timestamp > d1.timestamp - x0 ? d1 : d0,
																	l = x11(d.timestamp) + margin.left ,
																	m = y11(d.temperature) + margin.top ;
																focus.attr("transform", "translate(" + l + "," + m + ")");
																focus.select("text").text(formatCurrency(d.temperature));
															  }
															});
															d3.csv("/csv/humi_<?php echo $cle; ?>.csv", function(error, data) {
															  data.forEach(function(d) {
																d.timestamp = parseDate(d.timestamp);
																d.humidite = +d.humidite;
															  });
															  
															  var bisectDate = d3.bisector(function(d) { return d.timestamp; }).left,
																  formatValue = d3.format(",.2f"),
																  formatCurrency = function(d) { return formatValue(d) + "%"; };
															  
															  x12.domain(d3.extent(data, function(d) { return d.timestamp; }));
															  y12.domain(d3.extent(data, function(d) { return d.humidite; }));

															  graph2.append("g")
																  .attr("class", "x axis")
																  .attr("transform", "translate(0," + height + ")")
																  .call(xAxis12);

															  graph2.append("g")
																  .attr("class", "y axis")
																  .call(yAxis12)	  
																.append("text")
																  .attr("transform", "rotate(-90)")
																  .attr("y", 6)
																  .attr("dy", ".71em")
																  .style("text-anchor", "end")
																  .text("<?php print T_("humidité ambiante (%)");?>");

															  graph2.append("path")
																  .datum(data)
																  .attr("class", "line")
																  .attr("d", line2);
															  
															  data.sort(function(a, b) {
																return a.timestamp - b.timestamp;
															  });
															  
															  var focus2 = svg2.append("g")
																  .attr("class", "focus")
																  .style("display", "none");

															  focus2.append("circle")
																  .attr("r", 4.5);

															  focus2.append("text")
																  .attr("x", 9)
																  .attr("dy", ".35em");
															  
															  graph2.append("rect")
																  .attr("class", "overlay")
																  .attr("width", width)
																  .attr("height", height)
																  .on("mouseover", function() { focus2.style("display", null); })
																  .on("mouseout", function() { focus2.style("display", "none"); })
																  .on("mousemove", mousemove);

															  function mousemove() {
																var x0 = x12.invert(d3.mouse(this)[0]),
																	i = bisectDate(data, x0, 1),
																	d0 = data[i - 1],
																	d1 = data[i],
																	d = x0 - d0.timestamp > d1.timestamp - x0 ? d1 : d0,
																	l = x12(d.timestamp) + margin.left ,
																	m = y12(d.humidite) + margin.top;
																focus2.attr("transform", "translate(" + l + "," + m + ")");
																focus2.select("text").text(formatCurrency(d.humidite));
															  }
															  
															});	  
															d3.csv("/csv/moist_<?php echo $cle; ?>.csv", function(error, data) {
															  data.forEach(function(d) {
																d.timestamp = parseDate(d.timestamp);
																d.moisture = +d.moisture;
															  });
															  
															  var bisectDate = d3.bisector(function(d) { return d.timestamp; }).left,
																  formatValue = d3.format(",.2f"),
																  formatCurrency = function(d) { return formatValue(d) ; };
															  
															  x13.domain(d3.extent(data, function(d) { return d.timestamp; }));
															  y13.domain(d3.extent(data, function(d) { return d.moisture; }));

															  graph3.append("g")
																  .attr("class", "x axis")
																  .attr("transform", "translate(0," + height + ")")
																  .call(xAxis13);

															  graph3.append("g")
																  .attr("class", "y axis")
																  .call(yAxis13)
																  
																.append("text")
																  .attr("transform", "rotate(-90)")
																  .attr("y", 6)
																  .attr("dy", ".71em")
																  .style("text-anchor", "end")
																  .text("<?php print T_("moisture ()");?>");

															  graph3.append("path")
																  .datum(data)
																  .attr("class", "line")
																  .attr("d", line3);

															  data.sort(function(a, b) {
																return a.timestamp - b.timestamp;
															  });
															  
															  var focus3 = svg3.append("g")
																  .attr("class", "focus")
																  .style("display", "none");

															  focus3.append("circle")
																  .attr("r", 4.5);

															  focus3.append("text")
																  .attr("x", 9)
																  .attr("dy", ".35em");
															  
															  graph3.append("rect")
																  .attr("class", "overlay")
																  .attr("width", width)
																  .attr("height", height)
																  .on("mouseover", function() { focus3.style("display", null); })
																  .on("mouseout", function() { focus3.style("display", "none"); })
																  .on("mousemove", mousemove);

															  function mousemove() {
																var x0 = x13.invert(d3.mouse(this)[0]),
																	i = bisectDate(data, x0, 1),
																	d0 = data[i - 1],
																	d1 = data[i],
																	d = x0 - d0.timestamp > d1.timestamp - x0 ? d1 : d0,
																	l = x13(d.timestamp) + margin.left ,
																	m = y13(d.moisture) + margin.top ;
																focus3.attr("transform", "translate(" + l + "," + m + ")");
																focus3.select("text").text(formatCurrency(d.moisture));
															  }
															});
															d3.csv("/csv/ph_<?php echo $cle; ?>.csv", function(error, data) {
																data.forEach(function(d) {
																d.timestamp = parseDate(d.timestamp);
																d.ph = +d.ph;
															  });
															  
															  var bisectDate = d3.bisector(function(d) { return d.timestamp; }).left,
																  formatValue = d3.format(",.2f"),
																  formatCurrency = function(d) { return formatValue(d) ; };
															  
															  x14.domain(d3.extent(data, function(d) { return d.timestamp; }));
															  y14.domain(d3.extent(data, function(d) { return d.ph; }));

															  graph4.append("g")
																  .attr("class", "x axis")
																  .attr("transform", "translate(0," + height + ")")
																  .call(xAxis14);

															  graph4.append("g")
																  .attr("class", "y axis")
																  .call(yAxis14)
																  
																.append("text")
																  .attr("transform", "rotate(-90)")
																  .attr("y", 6)
																  .attr("dy", ".71em")
																  .style("text-anchor", "end")
																  .text("ph ()");

															  graph4.append("path")
																  .datum(data)
																  .attr("class", "line")
																  .attr("d", line4);

															  data.sort(function(a, b) {
																return a.timestamp - b.timestamp;
															  });
															  
															  var focus4 = svg4.append("g")
																  .attr("class", "focus")
																  .style("display", "none");

															  focus4.append("circle")
																  .attr("r", 4.5);

															  focus4.append("text")
																  .attr("x", 9)
																  .attr("dy", ".35em");
															  
															  graph4.append("rect")
																  .attr("class", "overlay")
																  .attr("width", width)
																  .attr("height", height)
																  .on("mouseover", function() { focus4.style("display", null); })
																  .on("mouseout", function() { focus4.style("display", "none"); })
																  .on("mousemove", mousemove);

															  function mousemove() {
																var x0 = x14.invert(d3.mouse(this)[0]),
																	i = bisectDate(data, x0, 1),
																	d0 = data[i - 1],
																	d1 = data[i],
																	d = x0 - d0.timestamp > d1.timestamp - x0 ? d1 : d0,
																	l = x14(d.timestamp) + margin.left ,
																	m = y14(d.ph) + margin.top ;
																focus4.attr("transform", "translate(" + l + "," + m + ")");
																focus4.select("text").text(formatCurrency(d.ph));
															  }
															});
															d3.csv("/csv/ppfd_<?php echo $cle; ?>.csv", function(error, data) {
															  data.forEach(function(d) {
																d.timestamp = parseDate(d.timestamp);
																d.ppfd = +d.ppfd;
															  });
															  
															  var bisectDate = d3.bisector(function(d) { return d.timestamp; }).left,
																  formatValue = d3.format(",.2f"),
																  formatCurrency = function(d) { return formatValue(d) + "µmol/m².s"; };
																
															  x15.domain(d3.extent(data, function(d) { return d.timestamp; }));
															  y15.domain(d3.extent(data, function(d) { return d.ppfd; }));

															  graph5.append("g")
																  .attr("class", "x axis")
																  .attr("transform", "translate(0," + height + ")")
																  .call(xAxis15);

															  graph5.append("g")
																  .attr("class", "y axis")
																  .call(yAxis15)
																.append("text")
																  .attr("transform", "rotate(-90)")
																  .attr("y", 6)
																  .attr("dy", ".71em")
																  .style("text-anchor", "end")
																  .text("<?php print T_("Luminosité 'ppfd' (µmol/m².s)");?>");

															  graph5.append("path")
																  .datum(data)
																  .attr("class", "line")
																  .attr("d", line5);
															  
															  data.sort(function(a, b) {
																return a.timestamp - b.timestamp;
															  });
															  
															  var focus5 = svg5.append("g")
																  .attr("class", "focus")
																  .style("display", "none");

															  focus5.append("circle")
																  .attr("r", 4.5);

															  focus5.append("text")
																  .attr("x", 9)
																  .attr("dy", ".35em");
															  
															  graph5.append("rect")
																  .attr("class", "overlay")
																  .attr("width", width)
																  .attr("height", height)
																  .on("mouseover", function() { focus5.style("display", null); })
																  .on("mouseout", function() { focus5.style("display", "none"); })
																  .on("mousemove", mousemove);

															  function mousemove() {
																var x0 = x15.invert(d3.mouse(this)[0]),
																	i = bisectDate(data, x0, 1),
																	d0 = data[i - 1],
																	d1 = data[i],
																	d = x0 - d0.timestamp > d1.timestamp - x0 ? d1 : d0,
																	l = x15(d.timestamp) + margin.left ,
																	m = y15(d.ppfd) + margin.top ;
																focus5.attr("transform", "translate(" + l + "," + m + ")");
																focus5.select("text").text(formatCurrency(d.ppfd));
															  }
															});
															
															d3.csv("/csv/temp_<?php echo $cle; ?>.csv", function(error, data) {
															  data.forEach(function(d) {
																d.timestamp = parseDate(d.timestamp);
																d.temperature = +d.temperature;
															  });

															  x.domain(d3.extent(data.map(function(d) { return d.timestamp; })));
															  y.domain(d3.extent(data, function(d) { return d.temperature; }));
															  x2.domain(x.domain());
															  y2.domain(y.domain());

															  focus.append("path")
																  .datum(data)
																  .attr("clip-path", "url(#clip)")
																  .attr("d", area);

															  focus.append("g")
																  .attr("class", "x axis")
																  .attr("transform", "translate(0," + height3 + ")")
																  .call(xAxis);

															  focus.append("g")
																  .attr("class", "y axis")
																  .call(yAxis);

															  context.append("path")
																  .datum(data)
																  .attr("d", area2);

															  context.append("g")
																  .attr("class", "x axis")
																  .attr("transform", "translate(0," + height2 + ")")
																  .call(xAxis2);
															  focus.append("text")
																  .attr("transform", "rotate(-90)")
																  .attr("y", 6)
																  .attr("dy", ".71em")
																  .style("text-anchor", "end")
																  .text("<?php print T_("Température (ºC)");?>");

															  context.append("g")
																  .attr("class", "x brush")
																  .call(brush)
																.selectAll("rect")
																  .attr("y", -6)
																  .attr("height", height2 + 7);
															});

															d3.csv("/csv/humi_<?php echo $cle; ?>.csv", function(error, data) {

															  data.forEach(function(d) {
																d.timestamp = parseDate(d.timestamp);
																d.humidite = +d.humidite;
															  });

															  x3.domain(d3.extent(data.map(function(d) { return d.timestamp; })));
															  y3.domain(d3.extent(data.map(function(d) { return d.humidite; })));
															  x4.domain(x3.domain());
															  y4.domain(y3.domain());

															  focus2.append("path")
																  .datum(data)
																  .attr("clip-path", "url(#clip)")
																  .attr("d", area3);

															  focus2.append("g")
																  .attr("class", "x axis")
																  .attr("transform", "translate(0," + height3 + ")")
																  .call(xAxis3);

															  focus2.append("g")
																  .attr("class", "y axis")
																  .call(yAxis2);

															  context2.append("path")
																  .datum(data)
																  .attr("d", area4);

															  context2.append("g")
																  .attr("class", "x axis")
																  .attr("transform", "translate(0," + height2 + ")")
																  .call(xAxis4);
															  focus2.append("text")
																  .attr("transform", "rotate(-90)")
																  .attr("y", 6)
																  .attr("dy", ".71em")
																  .style("text-anchor", "end")
																  .text("<?php print T_("Humidité (%)");?> ");

															  context2.append("g")
																  .attr("class", "x brush")
																  .call(brush2)
																.selectAll("rect")
																  .attr("y", -6)
																  .attr("height", height2 + 7);
															});

															d3.csv("/csv/moist_<?php echo $cle; ?>.csv", function(error, data) {
															  data.forEach(function(d) {
																d.timestamp = parseDate(d.timestamp);
																d.moisture = +d.moisture;
															  });

															  x5.domain(d3.extent(data.map(function(d) { return d.timestamp; })));
															  y5.domain(d3.extent(data.map(function(d) { return d.moisture; })));
															  x6.domain(x5.domain());
															  y6.domain(y5.domain());

															  focus3.append("path")
																  .datum(data)
																  .attr("clip-path", "url(#clip)")
																  .attr("d", area5);

															  focus3.append("g")
																  .attr("class", "x axis")
																  .attr("transform", "translate(0," + height3 + ")")
																  .call(xAxis5);

															  focus3.append("g")
																  .attr("class", "y axis")
																  .call(yAxis3);

															  context3.append("path")
																  .datum(data)
																  .attr("d", area6);

															  context3.append("g")
																  .attr("class", "x axis")
																  .attr("transform", "translate(0," + height2 + ")")
																  .call(xAxis6);
															  focus3.append("text")
																  .attr("transform", "rotate(-90)")
																  .attr("y", 6)
																  .attr("dy", ".71em")
																  .style("text-anchor", "end")
																  .text("<?php print T_("Moisture (_)");?> ");

															  context3.append("g")
																  .attr("class", "x brush")
																  .call(brush3)
																.selectAll("rect")
																  .attr("y", -6)
																  .attr("height", height2 + 7);
															});
															
															d3.csv("/csv/ph_<?php echo $cle; ?>.csv", function(error, data) {

															  data.forEach(function(d) {
																d.timestamp = parseDate(d.timestamp);
																d.ph = +d.ph;
															  });

															  x7.domain(d3.extent(data.map(function(d) { return d.timestamp; })));
															  y7.domain(d3.extent(data.map(function(d) { return d.ph; })));
															  x8.domain(x7.domain());
															  y8.domain(y7.domain());

															  focus4.append("path")
																  .datum(data)
																  .attr("clip-path", "url(#clip)")
																  .attr("d", area7);

															  focus4.append("g")
																  .attr("class", "x axis")
																  .attr("transform", "translate(0," + height3 + ")")
																  .call(xAxis7);

															  focus4.append("g")
																  .attr("class", "y axis")
																  .call(yAxis4);

															  context4.append("path")
																  .datum(data)
																  .attr("d", area8);

															  context4.append("g")
																  .attr("class", "x axis")
																  .attr("transform", "translate(0," + height2 + ")")
																  .call(xAxis4);
															  focus4.append("text")
																  .attr("transform", "rotate(-90)")
																  .attr("y", 6)
																  .attr("dy", ".71em")
																  .style("text-anchor", "end")
																  .text("<?php print T_("pH (_) ");?>");

															  context4.append("g")
																  .attr("class", "x brush")
																  .call(brush4)
																.selectAll("rect")
																  .attr("y", -6)
																  .attr("height", height2 + 7);
															});
															
															d3.csv("/csv/ppfd_<?php echo $cle; ?>.csv", function(error, data) {

															  data.forEach(function(d) {
																d.timestamp = parseDate(d.timestamp);
																d.ppfd = +d.ppfd;
															  });

															  x9.domain(d3.extent(data.map(function(d) { return d.timestamp; })));
															  y9.domain(d3.extent(data.map(function(d) { return d.ppfd; })));
															  x10.domain(x9.domain());
															  y10.domain(y9.domain());

															  focus5.append("path")
																  .datum(data)
																  .attr("clip-path", "url(#clip)")
																  .attr("d", area9);

															  focus5.append("g")
																  .attr("class", "x axis")
																  .attr("transform", "translate(0," + height3 + ")")
																  .call(xAxis9);

															  focus5.append("g")
																  .attr("class", "y axis")
																  .call(yAxis5);

															  context5.append("path")
																  .datum(data)
																  .attr("d", area10);

															  context5.append("g")
																  .attr("class", "x axis")
																  .attr("transform", "translate(0," + height2 + ")")
																  .call(xAxis10);
															  focus5.append("text")
																  .attr("transform", "rotate(-90)")
																  .attr("y", 6)
																  .attr("dy", ".71em")
																  .style("text-anchor", "end")
																  .text("<?php print T_("Luminosité 'ppfd' (µmol/m².s)");?>");

															  context5.append("g")
																  .attr("class", "x brush")
																  .call(brush5)
																.selectAll("rect")
																  .attr("y", -6)
																  .attr("height", height2 + 7);
															});
															
															function brushed() {
															  x.domain(brush.empty() ? x2.domain() : brush.extent());
															  focus.select("path").attr("d", area);
															  focus.select(".x.axis").call(xAxis);
															}
															function brushed2() {
															  x3.domain(brush2.empty() ? x4.domain() : brush2.extent());
															  focus2.select("path").attr("d", area3);
															  focus2.select(".x.axis").call(xAxis3);
															}
															function brushed3() {
															  x5.domain(brush3.empty() ? x6.domain() : brush3.extent());
															  focus3.select("path").attr("d", area5);
															  focus3.select(".x.axis").call(xAxis5);
															}
															function brushed4() {
															  x7.domain(brush4.empty() ? x8.domain() : brush4.extent());
															  focus4.select("path").attr("d", area7);
															  focus4.select(".x.axis").call(xAxis7);
															}
															function brushed5() {
															  x9.domain(brush5.empty() ? x10.domain() : brush5.extent());
															  focus5.select("path").attr("d", area9);
															  focus5.select(".x.axis").call(xAxis9);
															}
													</script>
													

											</div>
											<div class="4u">
											
												<!-- Sidebar -->
													<section class="box">
														<a  class="image image-full"><img src="images/croissance.jpg" alt="" /></a>
														<header>
															<h3><?php print T_("Graphiques");?></h3>
														</header>
														<p><?php print T_("C'est ici que vous pouvez visualiser toutes vos données recueillies depuis que vous avez connecté votre jardin. Pour plus d'infos ... Dans la FAQ aller, il te faudra ;-) .");?>  </p>
														<a href="faq.php" class="button alt"><?php print T_("FAQ");?></a>
													</section>
													<section class="box">
														<header>
															<h3 id='aide_graph'><?php print T_("Configuration des graphiques");?></h3>
														</header>
														<p><?php print T_("Dans ce menu, vous pouvez sélectionner les graphiques que vous voulez voir ou ne pas voir.");?></p>
														<ul class="divided">
															<li><select name="graph" id="graph" onChange="verif();">
																	<option value="area" selected="selected" ><?php print T_("aire");?></option>
																	<option value="ligne"><?php print T_("ligne");?></option>
																</select>
															</li>
															<li><input type="checkbox" checked="checked" name="gt" id="gt" onChange="verif();" /><?php print T_("graphique de température");?></li>
															<li><input type="checkbox" checked="checked" name="gh" id="gh" onChange="verif();"/><?php print T_("graphique d' humidité");?></li>
															<li><input type="checkbox" checked="checked" name="gm" id="gm" onChange="verif();"/><span class="hotspot" onmouseover="tooltip.show('<?php print T_("Humidité de la plante ou moisture : <br/> Représente le taux d'humidité de la terre dans laquelle poussent vos plantes. <br/> Pour plus d'info consultez la FAQ");?>);" onmouseout="tooltip.hide();"><?php print T_("graphique de moisture");?></span></li>
															<li><input type="checkbox" name="gph" id="gph" onChange="verif();"/><?php print T_("graphique pH");?></li>
															<li><input type="checkbox" name="gco2" id="gppfd" onChange="verif();"/><span class="hotspot" onmouseover="tooltip.show('<?php print T_("Photosynthetic Photon Flux Density : <br/> représente la quantité de lumière (le nombre de photons) qui est utile pour la photosynthèse.<br/> Pour plus d'info consultez la FAQ");?>');" onmouseout="tooltip.hide();"><?php print T_("graphique PPFD");?></span></li>
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