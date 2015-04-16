<?php

error_reporting(E_ALL | E_STRICT);
// define constants
define('PROJECT_DIR', realpath('./'));
define('LOCALE_DIR', PROJECT_DIR .'/locale');
define('DEFAULT_LOCALE', 'en_US');

require_once('gettext/gettext.inc');

$supported_locales = array('en_US', 'fr_FR');
$encoding = 'UTF-8';

/*$filename = LOCALE_DIR ;

if (file_exists($filename)) {
    echo "Le fichier $filename existe.";
} else {
    echo "Le fichier $filename n'existe pas.";
}
*/
if(isset($_GET['lang'])){
	$locale = (isset($_GET['lang']))? $_GET['lang'] : DEFAULT_LOCALE;
	$_SESSION["lang"]=$locale;
	setcookie('lang', $locale, time() + (3600 * 24 * 30)); 
}
else if(isset($_SESSION['lang']))
{
$locale = $_SESSION['lang'];
}
else if(isset($_COOKIE['lang']))
{
$locale = $_COOKIE['lang'];
}
else{
	$locale=DEFAULT_LOCALE;
}



// gettext setup
T_setlocale(LC_MESSAGES, $locale);
// Set the text domain as 'messages'
$domain = 'defaut';
T_bindtextdomain($domain, LOCALE_DIR);
T_bind_textdomain_codeset($domain, $encoding);
T_textdomain($domain);

header("Content-type: text/html; charset=$encoding");
?>
<link rel="gettext" type="application/x-po" href="/locale/<?php echo $locale ?>/LC_MESSAGES/defaut.po" />
<script type="text/javascript" src="/js/Gettext.js"></script>