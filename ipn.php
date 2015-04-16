<?php
//permet de traiter le retour ipn de paypal
$email_account = "bn@votre-secret-garden.fr";
$req = 'cmd=_notify-validate';
 
foreach ($_POST as $key => $value) {
$value = urlencode(stripslashes($value));
$req .= "&$key=$value";
}
 
try
		{
			$bdd = new PDO('mysql:host=sql9;dbname=', '', '');
		}
		catch (Exception $e)
		{
				die('Erreur : ' . $e->getMessage());
		}
 
$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Host: www.sandbox.paypal.com\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$address_street = $_POST['address_street'];
$address_city = $_POST['address_city'];
$address_state = $_POST['address_state']; 
$address_zip = $_POST['address_zip']; 
$address_country = $_POST['address_country'];
$name= $first_name." ".$last_name;
$address=$address_street." ".$address_city." ".$address_state." ".$address_zip." ".$address_country;
parse_str($_POST['custom'],$custom);
$date=date("Y-m-d H:i:s"); 

if (!$fp) {
 
} 
else {
	fputs ($fp, $header . $req);
	while (!feof($fp)) {
		$res = fgets ($fp, 1024);
		if (strcmp ($res, "VERIFIED") == 0) {
			// vérifier que payment_status a la valeur Completed
			if ( $payment_status == "Completed") {
				if ( $email_account == $receiver_email) {
					/**
					* C'EST LA QUE TOUT SE PASSE
					
					*/
					$datas=serialize($_POST);
					$request=$bdd->prepare("INSERT INTO donation (transaction_id, email, somme, monnaie, noms, adresse, request, date) VALUES (:id, :email, :somme, :monnaie, :noms, :adresse, :request, :date)");
					$request->execute(array(':id' => $txn_id, ':email' => $payer_email, ':somme' => $payment_amount, ':monnaie' => $payment_currency,':noms' => $name,':adresse' => $address, ':request' => $datas, ':date' => $date,));
					 
					/**
					* FIN CODE
					*/
				}
			}
			else {
			// Statut de paiement: Echec
				header("Location: erreur.php");
				exit();
			}
			exit();
		}
		else if (strcmp ($res, "INVALID") == 0) {
			header("Location: erreur.php");
			exit();
		// Transaction invalide
		}
	}
	fclose ($fp);
}	