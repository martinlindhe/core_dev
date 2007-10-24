<?
	//av martin. används häråvar, mot cs databasen
	function addVIP($user_id, $vip_level, $days)
	{
		global $user_db, $session, $config;
	
		//$session->log('addVIP user='.$user_id.', level: '.$vip_level.',days: '.$days );
		
		if (!is_numeric($user_id) || !is_numeric($vip_level) || !is_numeric($days)) return false;
	
		$q = 'SELECT userId FROM s_vip WHERE userId='.$user_id.' AND level='.$vip_level;
	
		if ($user_db->getOneItem($q)) {
			$q = 'UPDATE s_vip SET days=days+'.$days.',timeSet=NOW() WHERE userId='.$user_id.' AND level='.$vip_level;
			$user_db->update($q);
		} else {
			$q = 'INSERT INTO s_vip SET userId='.$user_id.',level='.$vip_level.',days='.$days.',timeSet=NOW()';
			$user_db->insert($q);
		}
	
		$q = 'SELECT level_id FROM s_user WHERE id_id='.$user_id;
		$old_level = $user_db->getOneItem($q);
		//$session->log( 'old: '.$old_level.', new: '.$vip_level );
		if ($old_level >= $vip_level) return true;
	
		$q = 'UPDATE s_user SET level_id="'.$vip_level.'" WHERE id_id='.$user_id;
		$user_db->update($q);
		//$session->log('updated');
	}

	//MT billing funktion för citysurf
	function prepareIPX_MT_bill_CS($msg)
	{
		global $config, $session;

		//1. parse sms, format "POG vipnivå userid"
		$in_cmd = explode(' ', strtoupper($msg));

		if (empty($in_cmd[0]) || empty($in_cmd[1]) || empty($in_cmd[2]) || !is_numeric($in_cmd[2])) {
			$session->log('Invalid SMS cmd: '.$msg, LOGLEVEL_WARNING);
			die;
		}

		$data['user_id'] = $in_cmd[2];

		$vip_codes = array(
			//days, price i öre, SEK2000 = 20.00 kronor
			'VIP'			=> array(14, '20'),
			'VIP-2V'	=> array(14, '20')/*,
			'VIP-1M'	=> array(30, '30'),
			'VIP-6M'	=> array(180, '150')*/
		);

		$vip_delux_codes = array(
			'VIPD'			=> array(10, '20'),
			'VIDP-10D'	=> array(10, '20')/*,
			'VIPD-1M'		=> array(30, '50')*/
		);

		if (!array_key_exists($in_cmd[1], $vip_codes) && !array_key_exists($in_cmd[1], $vip_delux_codes)) {
			$session->log('Unknown incoming SMS code "'.$in_cmd[1].'" ('.$msg.')', LOGLEVEL_WARNING);
			die;
		}

		//identifiera användaren
		$user_db = new DB_MySQLi($config['user_db']);

		$q = 'SELECT u_alias FROM s_user WHERE id_id='.$data['user_id'];
		$data['username'] = $user_db->getOneItem($q);
		if (!$data['username']) {
			$l = 'IPX incoming SMS: '.$msg.', Specified user dont exist: '.$data['user_id'];
			$session->log($l, LOGLEVEL_WARNING);
			mail('martin@unicorn.tv', '[IPX] Billing error', $l);
			die;
		}

		if (array_key_exists($in_cmd[1], $vip_codes)) {
			$data['days'] = $vip_codes[$in_cmd[1]][0];
			$price = $vip_codes[$in_cmd[1]][1];
			$data['tariff'] = 'SEK'.$price.'00';	//de två nollorna är för ören
			$data['vip_level'] = VIP_LEVEL1;
			$data['msg'] = 'Du debiteras nu '.$price.' kr för '.$data['days'].' dagar VIP till användare '.$data['username'];
			$data['internal_msg'] = 'Ditt konto har uppgraderats med '.$data['days'].' dagar VIP';
		} else if (array_key_exists($in_cmd[1], $vip_delux_codes)) {
			$data['days'] = $vip_delux_codes[$in_cmd[1]][0];
			$price = $vip_delux_codes[$in_cmd[1]][1];
			$data['tariff'] = 'SEK'.$price.'00';	//de två nollorna är för ören
			$data['vip_level'] = VIP_LEVEL2;
			$data['msg'] = 'Du debiteras nu '.$price.' kr för '.$data['days'].' dagar VIP DELUX till användare '.$data['username'];
			$data['internal_msg'] = 'Ditt konto har uppgraderats med '.$data['days'].' dagar VIP DELUX';
		}

		$l = 'Attempting to charge '.$data['username'].' for '.$data['days'].' days VIP level '.$data['vip_level'].' ('.$data['tariff'].') (cmd: '.$in_cmd[1].')';
		$session->log($l);
		mail('martin@unicorn.tv', '[IPX] Attempting MT-billing', $l);
		
		return $data;
	}

?>
