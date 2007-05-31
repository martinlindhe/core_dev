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

	ipxHandleIncoming();
?>