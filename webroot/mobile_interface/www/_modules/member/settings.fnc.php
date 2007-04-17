<?
	function makeSelection($name, $sel)
	{
		$array = getset('', $name, 'mo', 'text_cmt ASC');

		$ret = '<select style="width: 185px;" name="det_'.$name.'"><option value="">-- Välj --</option>';
		foreach($array as $arr) $ret .= '<option value="'.$arr[1].'"'.($sel == $arr[1]?' selected':'').'>'.$arr[1].'</option>';
		return $ret.'</select>';
	}

	function storeFacts()
	{
		global $user, $l;

		if($l['status_id'] == '1') {
			if (isset($_POST['det_civil'])) {
				$id = $user->setinfo($l['id_id'], 'det_civil', "'".secureINS($_POST['det_civil'])."'");
				if ($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
			}
			if (isset($_POST['det_attitude'])) {
				$id = $user->setinfo($l['id_id'], 'det_attitude', "'".secureINS($_POST['det_attitude'])."'");
				if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
			}
			if (isset($_POST['det_children'])) {
				$id = $user->setinfo($l['id_id'], 'det_children', "'".secureINS($_POST['det_children'])."'");
				if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
			}
			if (isset($_POST['det_alcohol'])) {
				$id = $user->setinfo($l['id_id'], 'det_alcohol', "'".secureINS($_POST['det_alcohol'])."'");
				if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
			}
			if (isset($_POST['det_tobacco'])) {
				$id = $user->setinfo($l['id_id'], 'det_tobacco', "'".secureINS($_POST['det_tobacco'])."'");
				if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
			}
			if (isset($_POST['det_sex'])) {
				$id = $user->setinfo($l['id_id'], 'det_sex', "'".secureINS($_POST['det_sex'])."'");
				if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
			}
			if (isset($_POST['det_music'])) {
				$id = $user->setinfo($l['id_id'], 'det_music', "'".secureINS($_POST['det_music'])."'");
				if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
			}
			if (isset($_POST['det_length'])) {
				$id = $user->setinfo($l['id_id'], 'det_length', "'".secureINS($_POST['det_length'])."'");
				if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
			}
			if (isset($_POST['det_wants'])) {
				$id = $user->setinfo($l['id_id'], 'det_wants', "'".secureINS($_POST['det_wants'])."'");
				if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
			}
		}
	}

	//Byter lösenord. returnerar sträng med felkod vid failure eller boolean TRUE vid success.
	function setNewPassword($_old_pwd, $_new_pwd, $_new_pwd_confirm)
	{
		global $sql, $user, $t, $l;

		if (empty($_new_pwd) || empty($_new_pwd_confirm) || ($_new_pwd != $_new_pwd_confirm)) {
			return 'Lösenordet matchar inte.';
		}

		$exists = $sql->queryLine("SELECT u_pass FROM {$t}user WHERE id_id = ".$l['id_id']." LIMIT 1");
		if (empty($exists) || !count($exists)) {
			return 'Felaktigt lösenord.';
		}

		if ($exists[0] != $_old_pwd) {
			return 'Felaktigt lösenord.';
		}

		if (strlen($_new_pwd) > 15 || strlen($_new_pwd) < 5) {
			return 'Felaktigt lösenord. Minst 5, max 15 tecken.';
		}

		$sql->logADD($l['id_id'], $_old_pwd.'->'.$_new_pwd, 'NEW_PASS');
		$sql->queryUpdate("UPDATE {$t}user SET u_pass = '".secureINS($_new_pwd)."' WHERE id_id = ".$l['id_id']);

		if ($user->level($l['level_id'], 7)) {
			$sql->queryUpdate("UPDATE {$t}admin SET user_pass = '".secureINS($_new_pwd)."' WHERE main_id = '".$l['id_id']."' LIMIT 1");
		}
		return true;
	}

	/* Updates mms key */
	function updateMMSKey()
	{
		global $sql, $user, $l, $t;

		if (!$l['id_id'] || empty($_POST['ins_mmskey'])) return;
		
		$blocked_keys = array(123, 321, 1234, 12345, 1111, 4321, 54321);
		
		//if non-allowed code, return error
		if (in_array($_POST['ins_mmskey'], $blocked_keys)) return 'Otillåten MMS-nyckel';
		
		$q = 'SELECT owner_id FROM s_obj WHERE content_type="mmskey" AND content="'.secureINS($_POST['ins_mmskey']).'" AND owner_id != '.$l['id_id'].' LIMIT 1';
		$check = $sql->queryResult($q);
		if ($check) return 'MMS nyckel upptagen!';
			
		$id = $user->setinfo($l['id_id'], 'mmskey', "'".$_POST['ins_mmskey']."'");
		if ($id[0]) $user->setrel($id[1], 'user_settings', $l['id_id']);
	}

?>