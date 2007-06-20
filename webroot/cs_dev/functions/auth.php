<?
class user_auth {

	function __construct() {
	}

	function login_data($result) {
		global $db, $user;
		cookieSET("a65", $result['u_alias']);
		$user->counterIncrease('login', $result['id_id']);
		$res = now();

		$hidden = $user->getinfo($result['id_id'], 'hidden_login');
		if (!$hidden) {
			$db->replace('REPLACE INTO s_userlogin SET id_id = '.$result['id_id'].', sess_date = "'.$res.'"');
		}

		$db->insert('INSERT INTO s_usersess SET id_id = '.$result['id_id'].', sess_ip = "'.$db->escape($_SERVER['REMOTE_ADDR']).'", sess_id = "'.$db->escape(gc()).'", sess_date = NOW(), type_inf = "i"');
		$db->update('UPDATE s_user SET lastlog_date = "'.$res.'", lastonl_date = "'.$res.'", account_date = "'.$res.'" WHERE id_id = '.$result['id_id']);
		$db->replace('REPLACE INTO s_useronline SET account_date = "'.$res.'", id_id = '.$result['id_id'].', u_sex = "'.$result['u_sex'].'"');
		$_SESSION['data'] = @array('u_pst' => $result['u_pst'], 'u_pstlan_id' => $result['u_pstlan_id'], 'lastlog_date' => $res, 'lastonl_date' => $res, 'u_regdate' => $result['u_regdate'], 'u_picvalid' => $result['u_picvalid'], 'status_id' => $result['status_id'], 'id_id' => $result['id_id'], 'u_alias' => $result['u_alias'], 'u_sex' => $result['u_sex'], 'u_picid' => $result['u_picid'], 'u_picd' => $result['u_picd'], 'u_birth' => $result['u_birth'], 'level_id' => $result['level_id']);
		$user->counterSet($result['id_id']);
		$_SESSION['data']['account_date'] = $res;
		$_SESSION['data']['cachestr'] = $user->cachestr($result['id_id']);
	}

	function notify_user($id, $msg, $alias = '') {
		global $db;
		if (!is_numeric($id) || DEFAULT_USER == $id) return;

		$msg = sprintf($msg, $alias);
		$db->insert('INSERT INTO s_userchat SET user_id = '.$id.', sender_id = '.DEFAULT_USER.', sent_cmt = "'.$db->escape($msg).'", sent_date = NOW()');
	}

	function login($a, $p, $mobile = false)
	{
		global $db, $user;
		$online = gettxt('stat_online');
		$online = explode(':', $online);
		$online = intval($online[0]);
		$result = $db->getOneRow('SELECT id_id, u_alias, level_id, u_picid, u_picd, status_id, u_sex, u_birth, u_pstlan_id, CONCAT(u_pstort, ", ", u_pstlan) as u_pst, lastlog_date, lastonl_date, u_regdate, u_pass, location_id, u_picvalid FROM s_user WHERE u_alias = "'.$db->escape($a).'" LIMIT 1');
		
		if (!$result) return 'Felaktigt alias eller lösenord.';

		if($online >= MAXIMUM_USERS && $result['level_id'] == '1') return 'Det är över '.MAXIMUM_USERS.' inloggade. Du måste vara VIP Delux för att kunna logga in nu.';
		if($result['status_id'] == '1' || $result['status_id'] == '4') {
			if($result['u_pass'] != $p) {
				$db->insert('INSERT INTO s_usersess SET id_id = '.$result['id_id'].', sess_ip = "'.$db->escape($_SERVER['REMOTE_ADDR']).'", sess_id = "'.$db->escape(gc()).'", sess_date = NOW(), type_inf = "f"');
				if (!$mobile) {
					return 'Felaktigt alias eller lösenord.';
				}
				header('Location: login.php?err=wrong');
				die;
			}

			$this->login_data($result);
			$user->setRelCount($result['id_id']);

			//kolla om användaren har verifierat sin info
			$q = 'SELECT verified,timeAsked FROM tblVerifyUsers WHERE user_id='.$result['id_id'];
			$data = $db->getOneRow($q);
				
			//ask user once
			if (!$data) {
				$q = 'REPLACE INTO tblVerifyUsers SET user_id='.$result['id_id'].',timeAsked=NOW(),verified=0';
				$db->replace($q);

				$msg = 'Hej!<br/>'.
								'Vi ber dig att verifiera att dina personuppgifter och kontaktuppgifter stämmer.<br/>'.
								'<b><a href="/member/settings/verify/">Klicka här</a></b> för att bekräfta uppgifterna.<br/><br/>'.
								'Som tack så får du en veckas VIP-Deluxe när du är färdig.';

				spyPostSend($result['id_id'], 'Validering av uppgifter', $msg);
			}

			if (!$mobile) {
				reloadACT(l('main', 'start'));
			} else {
				header('Location: '.$config['web_root'].'index.php');
				die;
			}
		} elseif($result['status_id'] == '3') {
			if (!$mobile) {
				return 'Du är blockerad.';
			} else {
				header('Location: login.php?err=blocked');
				die;
			}
		} else {
			if (!$mobile) {
				return 'Felaktigt alias eller lösenord.';
			} else {
				header('Location: login.php?err=wrong');
				die;
			}
		}
	}

	function logout($empty = false, $mobile = false) {
		global $db;
		if(!empty($_SESSION['data']['id_id'])) {
			if(!$empty) {
				$db->insert('INSERT INTO s_usersess SET id_id = '.$_SESSION['data']['id_id'].', sess_ip = "'.$db->escape($_SERVER['REMOTE_ADDR']).'", sess_id = "'.$db->escape(gc()).'", sess_date = NOW(), type_inf = "o"');
				$db->update('UPDATE s_user SET lastonl_date = account_date, account_date = "'.date("Y-m-d H:i:s", strtotime("-1 HOUR")).'" WHERE id_id = '.$_SESSION['data']['id_id'].' LIMIT 1');
				$db->update('UPDATE s_useronline SET account_date = "'.date("Y-m-d H:i:s", strtotime("-1 HOUR")).'" WHERE id_id = '.$_SESSION['data']['id_id'].' LIMIT 1');
			}
			$_SESSION['data']['id_id'] = false;
		}
		unset($_SESSION['data']['id_id']);
		unset($_SESSION['data']);
		unset($_SESSION);
		if (!$mobile) {
			if(!$empty)
				reloadACT(l('main', 'index'));
			else
				reloadACT(l('main', 'index', '1'));
		} else {
			header('Location: '.$config['web_root']);
			die;
		}
	}

}
	$user_auth = new user_auth();
?>