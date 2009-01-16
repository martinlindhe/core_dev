<?php

require_once('/var/www/core_dev/core/functions_core.php');
require_once('/var/www/core_dev/core/input_sip.php');

function sipAuthCallback($username, $realm, $uri, $nonce, $response)
{
	$a1 = $username.':'.$realm.':'."test";	//XXX fetch password from somewhere
	$a2 = "REGISTER".':'.$uri;
	if (md5(md5($a1).':'.$nonce.':'.md5($a2)) == $response) return true;
	return false;
}

$sip = new sip_server('10.10.10.240');
$sip->auth_callback('sipAuthCallback');

$sip->dst_ip = '10.10.10.240';

do {
	$pkt = $sip->listen();

} while ($pkt !== false);

?>
