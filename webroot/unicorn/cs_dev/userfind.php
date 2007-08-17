<?
	require_once('config.php');

	if(!empty($_POST['a'])) {
		$src = $_POST['a'];
	}
	if(!empty($src)) {
		$res = $db->getOneRow('SELECT id_id, u_alias FROM s_user WHERE u_alias = "'.$db->escape($src).'" AND status_id = "1" LIMIT 1');
		if(empty($res) || !count($res)) {
			$res = $db->getOneRow('SELECT id_id, u_alias FROM s_user WHERE u_alias LIKE "%'.$db->escape($src).'%" AND status_id = "1" LIMIT 1');
		}

		if (!empty($res) && count($res)) {
			header('Location: user_view.php?id='.$res['id_id']);
			die;
		} else {
			header('Location: list_users.php');
			die;
		}
	} elseif(isset($_GET['id'])) {
		$r = $user->getinfo($user->id, 'random');
		if(!$r) {
			$sexs = array('M' => 'F', 'F' => 'M', '' => 'F');
			$sexs = $sexs[$_SESSION['data']['u_sex']];
		} else {
			if($r == 'B') $sexs = false;
			else $sexs = $r;
		}
		if($sexs) {
			$c = $db->getOneItem('SELECT COUNT(*) FROM s_userlevel WHERE MATCH(level_id) AGAINST("+VALID +SEX'.$sexs.'" IN BOOLEAN MODE)');
			$c = mt_rand(0, $c);
			$res = $db->getOneItem('SELECT id_id FROM s_userlevel WHERE MATCH(level_id) AGAINST("+VALID +SEX'.$sexs.'" IN BOOLEAN MODE) LIMIT $c, 1');
		} else {
			$c = $db->getOneItem('SELECT COUNT(*) FROM s_userlevel WHERE MATCH(level_id) AGAINST("+VALID" IN BOOLEAN MODE)');
			$c = mt_rand(0, $c);
			$res = $db->getOneItem('SELECT id_id FROM s_userlevel WHERE MATCH(level_id) AGAINST("+VALID" IN BOOLEAN MODE) LIMIT $c, 1');
		}
		if(!empty($res)) {
			if($res == $l['id_id']) {
				$res = $db->getOneItem('SELECT id_id FROM s_uservalid WHERE id_id != "'.$user->id.'" AND status_id = "'.$sexs.'" ORDER BY RAND() LIMIT 1');
			}
			header('Location: user_view.php?id='.$res);
			die;
		} else {
			$res = $db->getOneItem('SELECT id_id FROM s_user WHERE id_id != "'.$user->id.'" AND status_id = "1" AND u_sex = "'.$sexs.'" ORDER BY RAND() LIMIT 1');
			header('Location: user_view.php?id='.$res);
			die;
		}
	} else {
		header('Location: list_users.php');
		die;
	}
?>
