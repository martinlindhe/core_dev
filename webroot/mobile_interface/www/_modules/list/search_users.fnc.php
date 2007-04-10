<?
	/*
		Funktioner för att söka efter användare

		Koden från början skriven av Frans Rosén
		Uppdaterad av Martin Lindhe, 2007-04-10
	*/
	
	//Reads post variables and produces result. $_start & $_end is to be used together with page splitting logic
	//todo: rip out all commented out $paging stuff
	function performSearch($_start = 0, $_end = 0)
	{
		global $sql, $user, $t, $l;

		$result = array();
		$result['lan']		= '0';	//Län
		$result['ort'] 		= '0';	//Ort
		$result['single']	= '0';	//?? exponeras ej i sökgränssnittet
		$result['online'] = '0';	//Sök användare online
		$result['pic']		= '1';	//sök bland folk som har bild
		$result['alias']	= '';		//fritext i användarnamnet
		$result['sex']		= '0';	//0 = alla kön, M = killar, F = tjejer
		$result['level']	= '0';	//söka vip-medlemmar?
		$result['ord']		= '0';	//sorterings-ordning ??? exponeras ej i sökgränssnittet
		$result['age']		= '0';	//ålder?
		$result['birth']	= '0';	//??? används ej ???

		if (!is_numeric($_start) || !is_numeric($_end)) return $result;

		$r = $user->getinfo($l['id_id'], 'random');
		$page = 'user';

		$gotage = false;
		$do = true;
		$str = array();
		$url = array();
		$bpic = false;

		if (!empty($id)) {
			if ($id == '1') {
				$do = 1;
				$result['online'] = '1';
				$result['pic'] = '1';
				$bpic = 1;
			} elseif ($id == 'F') {
				$do = 1;
				$result['online'] = '1';
				$result['pic'] = '1';
				$result['sex'] = 'F';
				$bpic = 3;
			} elseif ($id == 'M') {
				$do = 1;
				$result['online'] = '1';
				$result['pic'] = '1';
				$result['sex'] = 'M';
				$bpic = 3;
			}
		}

		if (!empty($_POST['do'])) $do = '1';
		if ($do) {
			$url[] = 'do=1&';
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
			if (!empty($_POST['l']) && is_numeric($_POST['l'])) {
				if($_POST['l'] > 0 && $_POST['l'] <= 10) {
					$result['level'] = intval($_POST['l']);
				}
			}
			if(!empty($_POST['single'])) $result['single'] = '1';
			if(!empty($_POST['online'])) $result['online'] = '1';
			if(!empty($_POST['birth']) && $isG) $result['birth'] = '1';
			if(!empty($_POST['pic']) || $bpic) $result['pic'] = '1'; elseif($do === '1') $result['pic'] = '0'; 
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
				#$join[] = "INNER JOIN {$t}useronline o ON o.id_id = l.id_id AND o.account_date > '".$user->timeout(UO)."'";
				$url[] = 'online='.$result['online'].'&';
			}
	
	/*
			if(!empty($_POST['age']) && array_key_exists($_POST['age'], $ages)) {
				$age = $ages[$_POST['age']];
				$result['age'] = $_POST['age'];
				if($age[0] != 'X') {
					if($age[1] == 'X') {
						$gotage = true;
						#$join[] = " INNER JOIN {$t}userbirth b ON b.id_id = u.id_id AND (DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(b.level_id))-1,'%Y') + 0) >= ".($age[0]);
						if($result['online'])
							$join[] = " INNER JOIN {$t}userbirth b ON b.id_id = u.id_id AND (YEAR(NOW()) - YEAR(b.level_id)) >= ".($age[0]);
					} else {
						$gotage = true;
						#$join[] = " INNER JOIN {$t}userbirth b ON b.id_id = u.id_id AND (DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(b.level_id))-1,'%Y') + 0) >= ".($age[0])." AND (DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(b.level_id)),'%Y') + 0) <= ".($age[1]);
						if($result['online'])
							$join[] = " INNER JOIN {$t}userbirth b ON b.id_id = u.id_id AND (YEAR(NOW()) - YEAR(b.level_id)) >= ".($age[0])." AND (DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(b.level_id)),'%Y') + 0) <= ".($age[1]);
						#$join[] = " INNER JOIN {$t}userbirth b ON b.id_id = u.id_id AND b.level_id >= '1980-05-08' AND b.level_id <= '1988-05-08'";
					}
				}
			}
	
			if($result['birth']) {
				#if(!$result['online'])
				$join[] = " INNER JOIN {$t}userbirth b2 ON b2.id_id = u.id_id AND b2.level_id LIKE '%-".date("m-d")."'";
			}
	*/
				/*else if($result['age'] && $age[0] != 'X') {
					if($age[1] != 'X')
						$res = "FROM {$t}userbirth b INNER JOIN {$t}userlevel l ON l.id_id = b.id_id LEFT JOIN {$t}user u ON u.id_id = l.id_id ".implode(' ', $join)." WHERE (YEAR(NOW()) - YEAR(b.level_id)) >= ".($age[0])." AND (DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(b.level_id)),'%Y') + 0) <= ".($age[1])." AND MATCH(l.level_id) AGAINST ('".implode(" ", $str)."' IN BOOLEAN MODE)";
					else
						$res = "FROM {$t}userbirth b INNER JOIN {$t}userlevel l ON l.id_id = b.id_id LEFT JOIN {$t}user u ON u.id_id = l.id_id ".implode(' ', $join)." WHERE (YEAR(NOW()) - YEAR(b.level_id)) >= ".($age[0])." AND MATCH(l.level_id) AGAINST ('".implode(" ", $str)."' IN BOOLEAN MODE)";
				}*/ 
			$lim = ($result['pic'])?45:50;
			//$paging = paging(@$_POST['p'], $lim);
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
	/*
			if($gotage) {
				$url[] = 'age='.$result['age'].'&';
			}
	*/
			if ($result['online']) {
				#$result['ord'] = false;
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
			#$paging['co'] = $sql->queryResult("SELECT COUNT(*) as count $res");
			#$res = $sql->query("SELECT u.id_id, u.u_alias, u.u_sex, u.u_birth, u.u_picvalid, u.u_picid, u.u_picd, u.u_pstlan, u.level_id, u.u_pstort, u.account_date, u.lastlog_date, u.lastonl_date $res  LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
			#execSt(1); print '<br>';
			//$paging['co'] = 200;
			
			$q = "SELECT u.id_id, u.u_alias, u.u_sex, u.u_birth, u.u_picvalid, u.u_picid, u.u_picd, u.u_pstlan, u.level_id, u.u_pstort, u.account_date, u.lastlog_date, u.lastonl_date $res";
			if ($_start || $_end) $q .= " LIMIT ".$_start.",".$_end;
			$result['res'] = $sql->query($q, 0, 1);

			#print '<br>'; execSt(1);
			//if(count($res) < $lim) $paging['co'] = (($paging['p']-1) * $lim) + count($res);

			#$paging['co'] = count($res);
		}
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

?>