<?php
/**
 * Copyright [2011] [Mario Mueller]
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

/**
 * Example File
 * @author Mario Mueller <mario.mueller.work@gmail.com>
 * @version 1.0.0
 */
require("localisation.php");


require_once dirname(__FILE__) . '/bootstrap.php';
$cle="";
if(isset($_GET['message']) && isset($_GET['id']) ){
	if(filter_var($_GET['message'], FILTER_VALIDATE_INT) !== false){
		$id_message=$_GET['message'];
	}
	$cle=htmlspecialchars($_GET['id']);
}
else{
	$message=T_("problème connection Arduino"); 
}

switch ($id_message) {
	case 0:
		$message=T_("à très chaud Mode Sécurité activé lampe(s) et pompe OFF !");
		break;
	case 1:
		$message=T_("est trempée dans l'eau c'est mauvais signe..");
		break;
	case 2:
		$message=T_("a soif");
		break;
	}
}	

// Use \Prowl\SecureConnector to make cUrl use SSL
$oProwl = new \Prowl\Connector();

$oMsg = new \Prowl\Message();

// If you have one:
// $oProwl->setProviderKey('MY_PROVIDER_KEY');

try {

	// You can choose to pass a callback
	$oProwl->setFilterCallback(function($sText) {
		return $sText;
	});

	// or set a filter instance:
	// $oFilter = new \Prowl\Security\PassthroughFilterImpl();
	// $oProwl->setFilter($oFilter);

	/*
	 * Both, the closure and the instance, can be passed to the connector
	 * or to each message. Setting it at the connector passes the closure or the instance down
	 * to each message on push() execution - but only if the message has neither of them set.
	 */

	$oProwl->setIsPostRequest(true);
	$oMsg->setPriority(0);

	// You can ADD up to 5 api keys
	// This is a Test Key, please use your own.
	$oMsg->addApiKey($cle);
	$oMsg->setEvent(_('Ta Plante :'));

	// These are optional:
	$oMsg->setDescription($message);
	$oMsg->setApplication('Secret Garden');

	$oResponse = $oProwl->push($oMsg);

	if ($oResponse->isError()) {
		print $oResponse->getErrorAsString();
	} else {
		print "Message sent." . PHP_EOL;
		print "You have " . $oResponse->getRemaining() . " Messages left." . PHP_EOL;
		print "Your counter will be resetted on " . date('Y-m-d H:i:s', $oResponse->getResetDate()) . PHP_EOL;
	}
} catch (\InvalidArgumentException $oIAE) {
	print $oIAE->getMessage();
} catch (\OutOfRangeException $oOORE) {
	print $oOORE->getMessage();
}
