<?
	/*
		Funktioner för att söka efter användare

		Koden från början skriven av Frans Rosén
		Uppdaterad av Martin Lindhe, 2007-04-10
	*/

	//Reads post variables and produces result. $_start & $_end is to be used together with page splitting logic
	function performSearch($_start = 0, $_end = 0)
	{
		global $sql, $user, $t, $l;

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

		$r = $user->getinfo($l['id_id'], 'random');
		$page = 'user';

		$gotage = false;
		$str = array();
		$url = array();
		$bpic = false;

		if (!empty($id)) {
			if ($id == '1') {
				$result['online'] = '1';
				$result['pic'] = '1';
				$bpic = 1;
			} elseif ($id == 'F') {
				$result['online'] = '1';
				$result['pic'] = '1';
				$result['sex'] = 'F';
				$bpic = 3;
			} elseif ($id == 'M') {
				$result['online'] = '1';
				$result['pic'] = '1';
				$result['sex'] = 'M';
				$bpic = 3;
			}
		}

		$url[] = '';
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
			$ort_check = $sql->queryResult("SELECT st_lan FROM {$t}pstort WHERE st_ort = '".secureINS($result['ort'])."' LIMIT 1");
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
		if(!empty($_POST['alias'])) $result['alias'] = secureINS($_POST['alias']);
		if(!empty($_POST['age']) && is_numeric($_POST['age']) && $_POST['age'] > 0 && $_POST['age'] < 10) $result['age'] = $_POST['age'];
		if ($result['lan']) {
			$str[] = '+LÄN'.str_replace('-', '', str_replace(' ', '', $result['lan']));
			$url[] = 'lan='.$result['lan'].'&';
		}
		if ($result['ort']) {
			$str[] = '+ORT'.str_replace('-', '', str_replace(' ', '', $result['ort']));
			$url[] = 'ort='.$result['ort'].'&';
		}
		if ($result['single']) {
			$str[] = '+SINGLEYES';
			$url[] = 'single='.$result['single'].'&';
		}
		if ($result['level']) {
			$str[] = '+LEVEL'.$result['level'];
			$url[] = 'l='.$result['level'].'&';
		}
		if ($result['pic']) {
			$str[] = '+VALID';
			$url[] = 'pic='.$result['pic'].'&';
		}
		if ($result['sex'] && ($result['sex'] == 'M' || $result['sex'] == 'F')) {
			$str[] = '+SEX'.$result['sex'];
			$url[] = 'sex='.$result['sex'].'&';
		}
		if ($result['alias']) {
			$url[] = 'alias='.secureOUT($result['alias']).'&';
		}
		if ($result['age']) {
			$str[] = '+AGEOF'.$result['age'];
			$url[] = 'age='.$result['age'].'&';
		}
		$join = array();
		if ($result['online']) {
			$url[] = 'online='.$result['online'].'&';
		}
	
		$lim = ($result['pic'])?45:50;
		$result['paging'] = paging(@$_POST['p'], $lim);
		if (count($str) > 1) {
			if($result['online'])
				$res = "FROM {$t}useronline o INNER JOIN {$t}userlevel l ON l.id_id = o.id_id LEFT JOIN {$t}user u ON u.id_id = l.id_id ".implode(' ', $join)." WHERE o.account_date > '".$user->timeout(UO)."' AND MATCH(l.level_id) AGAINST ('".implode(" ", $str)."' IN BOOLEAN MODE)";
			else
				$res = "FROM {$t}userlevel l LEFT JOIN {$t}user u ON u.id_id = l.id_id ".implode(' ', $join)." WHERE MATCH(l.level_id) AGAINST ('".implode(" ", $str)."' IN BOOLEAN MODE)";
		} else {
			if($result['online'])
				$res = "FROM {$t}useronline o LEFT JOIN {$t}user u ON u.id_id = o.id_id ".implode(' ', $join)." WHERE o.account_date > '".$user->timeout(UO)."'";
			else
				$res = "FROM {$t}user u ".implode(' ', $join)." WHERE u.status_id = '1'";
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
		if ($result['ord']) $url[] = 'ord='.secureOUT($result['ord']).'&';
		$result['paging']['co'] = 200;
			
		$q = "SELECT u.id_id, u.u_alias, u.u_sex, u.u_birth, u.u_picvalid, u.u_picid, u.u_picd, u.u_pstlan, u.level_id, u.u_pstort, u.account_date, u.lastlog_date, u.lastonl_date $res";
		if ($_start || $_end) $q .= " LIMIT ".$_start.",".$_end;
		else $q .= " LIMIT {$result['paging']['slimit']}, {$result['paging']['limit']}";
			
		//echo $q;
			
		$result['res'] = $sql->query($q, 0, 1);

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
		global $sql, $t;

		$list = $sql->query("SELECT st_lan FROM {$t}pstlan ORDER BY main_id ASC");

		foreach ($list as $res) {
			echo '<option value="'.$res[0].'"'.($_selected===$res[0] ? ' selected':'').'>'.secureOUT($res[0]).'</option>';
		}
	}
	
	/* echos out <option> values for all "ort" from database, to use with search windows */
	function optionOrt($_lan, $_selected = 0)
	{
		global $sql, $t;

		if (!$_lan) return false;

		$list = $sql->query("SELECT st_ort FROM {$t}pstort WHERE st_lan = '".secureINS($_lan)."' ORDER BY st_ort");

		foreach ($list as $res) {
			echo '<option value="'.$res[0].'"'.($_selected===$res[0]?' selected':'').'>'.$res[0].'</option>';
		}
	}

?>