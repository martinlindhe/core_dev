<?
	function makeSelection($name, $array, $sel) {
		$ret = '<select style="width: 185px;" name="'.$name.'"><option value="">-- Välj --</option>';
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

?>