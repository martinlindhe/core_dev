<?
class user_auth {

	function __construct() {
	}

	function login_data($result) {
		global $db, $user;

		$user->counterIncrease('login', $result['id_id']);
		$res = now();

		$hidden = $user->getinfo($result['id_id'], 'hidden_login');
		if (!$hidden) {
			$db->replace('REPLACE INTO s_userlogin SET id_id = "'.$db->escape($result['id_id']).'", sess_date = "'.$res.'"');
		}

		$db->insert('INSERT INTO s_usersess SET id_id = "'.$db->escape($result['id_id']).'", sess_ip = "'.$db->escape($_SERVER['REMOTE_ADDR']).'", sess_date = NOW(), type_inf = "i"');
		$db->update('UPDATE s_user SET lastlog_date = "'.$res.'", lastonl_date = "'.$res.'", account_date = "'.$res.'" WHERE id_id = "'.$db->escape($result['id_id']).'"');
		$db->replace('REPLACE INTO s_useronline SET account_date = "'.$res.'", id_id = "'.$db->escape($result['id_id']).'", u_sex = "'.$result['u_sex'].'"');

		$user->counterSet($result['id_id']);

		$_SESSION['data'] = $result;
		$_SESSION['data']['account_date'] = $res;
		$_SESSION['data']['cachestr'] = $user->cachestr($result['id_id']);
	}

	function notify_user($id, $msg, $alias = '') {
		if(DEFAULT_USER == $id) return;
		$msg = sprintf($msg, $alias);
		$this->sql->queryInsert("INSERT INTO s_userchat SET user_id = '".$id."', sender_id = '".DEFAULT_USER."', sent_cmt = '".secureINS($msg)."', sent_date = NOW()");
	}

	function login($a, $p, $mobile = false)
	{
		global $db, $user, $config;

		$online = gettxt('stat_online');
		$online = explode(':', $online);
		$online = intval($online[0]);
		
		if ($mobile) {
			//non case sensitive login!
			$q = 'SELECT id_id, u_alias, level_id, u_picid, u_picd, status_id, u_sex, u_birth, u_pstlan_id, CONCAT(u_pstort, ", ", u_pstlan) as u_pst, lastlog_date, lastonl_date, u_regdate, u_pass, location_id, u_picvalid FROM s_user WHERE LCASE(u_alias) = LCASE("'.$db->escape($a).'") LIMIT 1';
		} else {		
			$q = 'SELECT id_id, u_alias, level_id, u_picid, u_picd, status_id, u_sex, u_birth, u_pstlan_id, CONCAT(u_pstort, ", ", u_pstlan) as u_pst, lastlog_date, lastonl_date, u_regdate, u_pass, location_id, u_picvalid FROM s_user WHERE u_alias = "'.$db->escape($a).'" LIMIT 1';
		}
		$result = $db->getOneRow($q);

		if (!$result) return 'Felaktigt alias eller lösenord.';

		//non case sensitive password for mobile login
		if ( (!$mobile && $result['u_pass'] != $p) || ($mobile && strtolower($result['u_pass']) != strtolower($p)) ) {
			$db->insert('INSERT INTO s_usersess SET id_id = '.$result['id_id'].', sess_ip = "'.$db->escape($_SERVER['REMOTE_ADDR']).'", sess_date = NOW(), type_inf = "f"');
			if (!$mobile) {
				return 'Felaktigt alias eller lösenord.';
			} else {
				header('Location: login.php?err=wrong'); die;
			}
		}

		if($online >= MAXIMUM_USERS && $result['level_id'] == '1') return 'Det är över '.MAXIMUM_USERS.' inloggade. Du måste vara VIP Delux för att kunna logga in nu.';

		if($result['status_id'] == '1' || $result['status_id'] == '4') {

			$this->login_data($result);
			$user->setRelCount($result['id_id']);

			/*
			//kolla om användaren har verifierat sin info
			$q = 'SELECT verified,timeAsked FROM tblVerifyUsers WHERE user_id='.$result[0];
			$data = $this->sql->queryLine($q, 0, 1);
				
			//ask user once
			if (!$data) {
				$q = 'REPLACE INTO tblVerifyUsers SET user_id='.$result[0].',timeAsked=NOW(),verified=0';
				$this->sql->queryInsert($q);

				$msg = 'Hej!<br/>'.
								'Vi ber dig att verifiera att dina personuppgifter och kontaktuppgifter stämmer.<br/>'.
								'<b><a href="/member/settings/verify/">Klicka här</a></b> för att bekräfta uppgifterna.<br/><br/>'.
								'Som tack så får du en veckas VIP-Deluxe när du är färdig.';

				spyPostSend($result[0], 'Validering av uppgifter', $msg);
			}
			*/

			/*
			if(!empty($_POST['redir'])) {
				$this->notify_user($result[0], gettxt('moved_login'), $result[1]);
				echo '<script type="text/javascript">window.setTimeout(\'document.location.href = "'.l('main', 'start').'"\', 6000);</script>';
				die();
			}
			*/
			header('Location: '.$config['start_page']);
			die;

		} elseif($result['status_id'] == '3') {
			if (!$mobile) {
				return 'Du är blockerad.';
			} else {
				header('Location: login.php?err=blocked'); die;
			}
		} else {
			if (!$mobile) {
				return 'Felaktigt alias eller lösenord.';
			} else {
				header('Location: login.php?err=wrong'); die;
			}
		}
	}

	function logout()
	{
		global $db;

		if (!empty($_SESSION['data']['id_id'])) {
			$db->insert('INSERT INTO s_usersess SET id_id = '.$_SESSION['data']['id_id'].', sess_ip = "'.$db->escape($_SERVER['REMOTE_ADDR']).'", sess_date = NOW(), type_inf = "o"');
			$db->update('UPDATE s_user SET lastonl_date = account_date, account_date = "'.date("Y-m-d H:i:s", strtotime("-1 HOUR")).'" WHERE id_id = '.$_SESSION['data']['id_id'].' LIMIT 1');
			$db->update('UPDATE s_useronline SET account_date = "'.date("Y-m-d H:i:s", strtotime("-1 HOUR")).'" WHERE id_id = '.$_SESSION['data']['id_id'].' LIMIT 1');

			$_SESSION['data']['id_id'] = false;
		}
		unset($_SESSION['data']['id_id']);
		unset($_SESSION['data']);
		unset($_SESSION);
	}

}
	$user_auth = new user_auth();
?>