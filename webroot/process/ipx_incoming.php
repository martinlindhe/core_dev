<?
	/*
		This script is called by IPX for incoming SMS
	*/

	require_once('config.php');

	define('VIP_LEVEL1',	1);	//Normal VIP
	define('VIP_LEVEL2',	2);	//VIP delux

	function addVIP($user_id, $vip_level, $days)
	{
		global $user_db;
		
		if (!is_numeric($user_id) || !is_numeric($vip_level) || !is_numeric($days)) return false;

		$q = 'SELECT userId FROM s_vip WHERE userId='.$user_id.' AND level='.$vip_level;
		
		if ($user_db->getOneItem($q)) {
			$q = 'UPDATE s_vip SET days=days+'.$days.',timeSet=NOW() WHERE userId='.$user_id.' AND level='.$vip_level;
			$user_db->update($q);
		} else {
			$q = 'INSERT INTO s_vip SET userId='.$user_id.',level='.$vip_level.',days='.$days.',timeSet=NOW()';
			$user_db->insert($q);
		}
		$user_db->showProfile();
	}


	$allowed_ip = array(
		'127.0.0.1',
		'213.80.11.162',	//Unicorn kontor oxtorgsgränd 3
		'87.227.76.225',	//Martin glocalnet hem-ip
		'217.151.193.80'	//Ericsson IPX (ipx-pat.ipx.com)
	);

	if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ip)) {
		$session->log('ipx_incoming.php accessed by unlisted IP', LOGLEVEL_ERROR);
		//fixme: ska stoppa här vid okänt ip, gör det ej nu för debuggande
		//die('ip not allowed');
	}

	//All incoming data is set as GET parameters
	$params = '';
	if (!empty($_GET)) $params = $_GET;
	if (!$params) die('nothing to do');

	//Log the incoming SMS
	$q = 'INSERT INTO tblIncomingSMS SET params="'.$db->escape(serialize($params)).'",IP='.$session->ip.',timeReceived=NOW()';
	$db->insert($q);

	//Acknowledgment - Tell IPX that the SMS received
	header('HTTP/1.1 200 OK');
	header('Content-Type: text/plain');
	echo '<DeliveryResponse ack="true"/>';

//	$params['Message'] = 'POG VIP 194712';

	//1. parse sms, format "POG vipnivå userid"
	$in_cmd = explode(' ', strtoupper($params['Message']));

	if (empty($in_cmd[0]) || empty($in_cmd[1]) || empty($in_cmd[2]) || !is_numeric($in_cmd[2])) {
		$session->log('Invalid SMS cmd: '.$params['Message']);
		die;
	}

	$vip_codes = array(
										//days, price i öre, SEK2000 = 20.00 kronor
		'VIP'			=> array(14, 'SEK2000'),
		'VIP-2V'	=> array(14, 'SEK2000')/*,
		'VIP-1M'	=> array(30, 'SEK3000'),
		'VIP-6M'	=> array(180, 'SEK15000')*/
	);
	
	$vip_delux_codes = array(
		'VIPD'			=> array(10, 'SEK2000'),
		'VIDP-10D'	=> array(10, 'SEK2000')/*,
		'VIPD-1M'		=> array(30, 'SEK5000')*/
	);

	if (!array_key_exists($in_cmd[1], $vip_codes) && !array_key_exists($in_cmd[1], $vip_delux_codes)) {
		$session->log('Unknown incoming SMS code "'.$in_cmd[1].'" ('.$params['Message'].')');
		die;
	}
	
	//identifiera användaren
	$config['user_db']['username']	= 'root';
	$config['user_db']['password']	= 'dravelsql';
	$config['user_db']['database']	= 'platform';
	$user_db = new DB_MySQLi($config['user_db']);

	$q = 'SELECT u_alias FROM s_user WHERE id_id='.$in_cmd[2];
	$username = $user_db->getOneItem($q);
	if (!$username) {
		$session->log('Specified user dont exist: '.$in_cmd[2]);
		die;
	}

	if (array_key_exists($in_cmd[1], $vip_codes)) {

		$days = $vip_codes[$in_cmd[1]][0];
		$tariff = $vip_codes[$in_cmd[1]][1];
		$vip_level = VIP_LEVEL1;
		$msg = 'Du debiteras nu '.$tariff.' för '.$days.' dagar VIP till användare '.$username;

		$session->log('Attempting to charge '.$username.' for '.$days.' days VIP ('.$tariff.') (cmd: '.$in_cmd[1].')');	

	} else if (array_key_exists($in_cmd[1], $vip_delux_codes)) {
		$days = $vip_delux_codes[$in_cmd[1]][0];
		$tariff = $vip_delux_codes[$in_cmd[1]][1];
		$vip_level = VIP_LEVEL2;

		$msg = 'Du debiteras nu '.$tariff.' för '.$days.' dagar VIP DELUX till användare '.$username;

		$session->log('Attempting to charge '.$username.' for '.$days.' days VIP DELUX ('.$tariff.') (cmd: '.$in_cmd[1].')');	

	} else {
		$session->log('SMS - impossible codepath!!', LOGLEVEL_ERROR);
		die;
	}

	//2. skicka ett nytt sms till avsändaren, med TARIFF satt samt med messageid från incoming sms satt som "reference id"
	$sms_err = sendSMS($params['OriginatorAddress'], $msg, $tariff, $params['MessageId']);
	if ($sms_err === true) {
		addVIP($in_cmd[2], $vip_level, $days);
		$session->log('Charge to '.$username.' of '.$tariff.' succeeded');
	} else {
		$session->log('Charge to '.$username.' of '.$tariff.' failed with error '.$sms_err, LOGLEVEL_ERROR);
	}

?>