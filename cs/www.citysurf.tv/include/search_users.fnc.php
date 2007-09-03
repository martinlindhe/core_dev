<?
	/*
		Funktioner för att söka efter användare

		Koden från början skriven av Frans Rosén
		Uppdaterad av Martin Lindhe, 2007-04-10
	*/

	//Reads post variables and produces result. $_start & $_end is to be used together with page splitting logic
	//$type = '1' = all, 'F' = female, 'M' = male
	function performSearch($type = '', $_start = 0, $_end = 0, $lim = 100)	//limit of results per page
	{
		global $db, $user;

		$sexs = '';
		$sexu = '';

		$result = array();
		$result['lan']		= '0';	//$_POST['lan']				Län
		$result['ort'] 		= '0';	//$_POST['ort']				Ort
		$result['single']	= '0';	//$_POST['single']		?? exponeras ej i sökgränssnittet
		$result['online'] = '0';	//$_POST['online']		Sök användare online
		$result['pic']		= '1';	//$_POST['pic']				sök bland folk som har bild
		$result['alias']	= '';		//$_POST['alias']			fritext i användarnamnet
		$result['sex']		= '0';	//$_POST['sex']				0 = alla kön, M = killar, F = tjejer
		$result['level']	= '0';	//$_POST['l']					söka vip-medlemmar?
		$result['ord']		= '0';	//$_POST['ord']				sorterings-ordning ??? exponeras ej i sökgränssnittet
		$result['age']		= '0';	//$_POST['age']				ålders-range
		$result['birth']	= '0';	//$_POST['birth']			används ej ???

		if (!is_numeric($_start) || !is_numeric($_end)) return $result;

		$r = $user->getinfo($user->id, 'random');
		$page = 'user';

		$gotage = false;
		$str = array();
		//$url = array();
		$bpic = false;

		if (!empty($type)) {
			if ($type == '1') {
				$result['online'] = '1';
				$result['pic'] = '1';
				$bpic = 1;
			} elseif ($type == 'F') {
				$result['online'] = '1';
				$result['pic'] = '1';
				$result['sex'] = 'F';
				$bpic = 3;
			} elseif ($type == 'M') {
				$result['online'] = '1';
				$result['pic'] = '1';
				$result['sex'] = 'M';
				$bpic = 3;
			}
		}

		//$url[] = '';
		if(!empty($_POST['lan'])) {
			$result['lan'] = $_POST['lan'];
		}
		if(!empty($_POST['sex'])) {
			$result['sex'] = $_POST['sex'];
		}
		if(!empty($_POST['ord']) && ($_POST['ord'] == 'A' || $_POST['ord'] == 'L')) {
			$result['ord'] = $_POST['ord'];
		}
		$str[] = '+ACTIVE';
		if (!empty($_POST['ort']) && $result['lan']) {
			$result['ort'] = $_POST['ort'];
			$ort_check = $db->getOneItem("SELECT st_lan FROM s_pstort WHERE st_ort = '".$db->escape($result['ort'])."' LIMIT 1");
			if (empty($ort_check) || $ort_check != $result['lan']) $result['ort'] = '0';
		}
		if (!empty($_POST['l_6']) && is_numeric($_POST['l_6'])) {
			if($_POST['l_6'] > 0 && $_POST['l_6'] <= 10) {
				$result['level'] = intval($_POST['l_6']);
			}
		}
		if(!empty($_POST['single'])) $result['single'] = '1';
		if(!empty($_POST['online'])) $result['online'] = '1';
		if(!empty($_POST['birth']) && $isG) $result['birth'] = '1';
		if(!empty($_POST['pic']) || $bpic) $result['pic'] = '1'; else $result['pic'] = '0'; 
		if(!empty($_POST['alias'])) $result['alias'] = $db->escape($_POST['alias']);
		if(!empty($_POST['age']) && is_numeric($_POST['age']) && $_POST['age'] > 0 && $_POST['age'] < 10) $result['age'] = $_POST['age'];
		if ($result['lan']) {
			$str[] = '+LÄN'.str_replace('-', '', str_replace(' ', '', $result['lan']));
		}
		if ($result['ort']) {
			$str[] = '+ORT'.str_replace('-', '', str_replace(' ', '', $result['ort']));
		}
		if ($result['single']) {
			$str[] = '+SINGLEYES';
		}
		if ($result['level']) {
			$str[] = '+LEVEL'.$result['level'];
		}
		if ($result['pic']) {
			$str[] = '+VALID';
		}
		if ($result['sex'] && ($result['sex'] == 'M' || $result['sex'] == 'F')) {
			$str[] = '+SEX'.$result['sex'];
		}
		if ($result['age']) {
			$str[] = '+AGEOF'.$result['age'];
		}
		$join = array();
		if ($result['online']) {
		}
	
		$result['paging'] = paging(@$_POST['p'], $lim);
		
		if (count($str) > 1) {
			if($result['online'])
				$res = "FROM s_useronline o INNER JOIN s_userlevel l ON l.id_id = o.id_id LEFT JOIN s_user u ON u.id_id = l.id_id ".implode(' ', $join)." WHERE o.account_date > '".$user->timeout(UO)."' AND MATCH(l.level_id) AGAINST ('".implode(" ", $str)."' IN BOOLEAN MODE)";
			else
				$res = "FROM s_userlevel l LEFT JOIN s_user u ON u.id_id = l.id_id ".implode(' ', $join)." WHERE MATCH(l.level_id) AGAINST ('".implode(" ", $str)."' IN BOOLEAN MODE)";
		} else {
			if($result['online'])
				$res = "FROM s_useronline o LEFT JOIN s_user u ON u.id_id = o.id_id ".implode(' ', $join)." WHERE o.account_date > '".$user->timeout(UO)."'";
			else
				$res = "FROM s_user u ".implode(' ', $join)." WHERE u.status_id = '1'";
		}
		if ($result['alias']) {
			$res .= " AND u.u_alias LIKE '%".$result['alias']."%'";
		}

		if ($result['online']) {
			$page = 'online';
			if($bpic == 3) $page = $result['sex'].'online';
			if(@$_SERVER['QUERY_STRING'] == 'do=1&online=1&pic=1'.$sexu) $page = $sexs.'online';
		} else $page = 'null';
	
		if (!$result['ord'] || $result['ord'] == 'L') {
			$ord = 'ORDER BY u.lastonl_date DESC';
		} else {
			$ord = 'ORDER BY u.u_alias ASC';
		}

		$result['paging']['co'] = $lim;
			
		$q = "SELECT u.* $res $ord";
		
		$q .= " LIMIT {$result['paging']['slimit']}, {$result['paging']['limit']}";
		
		$result['res'] = $db->getArray($q);

		if (count($res) < $lim) $result['paging']['co'] = (($result['paging']['p']-1) * $lim) + count($res);

		$sext = array('F' => 'tjejer ', 'M' => 'killar ');
		if (!$r) {
			$sexs = '';
			$sexu = '';
		} else {
			if ($r == 'B') {
				$sexs = '';
				$sexu = '';
			} else {
				$sexs = $r;
				$sexu = '&sex='.$r;
			}
		}

		return $result;
	}

	/* echos out <option> values for all "län" from database, to use with search windows */
	function optionLan($_selected = 0)
	{
		global $db;

		$list = $db->getArray('SELECT st_lan FROM s_pstlan ORDER BY main_id ASC');

		foreach ($list as $res) {
			echo '<option value="'.$res['st_lan'].'"'.($_selected===$res['st_lan'] ? ' selected':'').'>'.secureOUT(ucwords(strtolower($res['st_lan']))).'</option>';
		}
	}
	
	/* echos out <option> values for all "ort" from database, to use with search windows */
	function optionOrt($_lan, $_selected = 0)
	{
		global $db;

		if (!$_lan) return false;

		$list = $db->getArray("SELECT st_ort FROM s_pstort WHERE st_lan = '".$db->escape($_lan)."' ORDER BY st_ort");

		foreach ($list as $res) {
			echo '<option value="'.$res['st_ort'].'"'.($_selected===$res['st_ort']?' selected':'').'>'.secureOUT(ucwords(strtolower($res['st_ort']))).'</option>';
		}
	}

?>
