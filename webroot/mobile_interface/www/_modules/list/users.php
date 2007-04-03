<?
	$r = $user->getinfo($l['id_id'], 'random');
	$page = 'user';
	$thislan = '0';
	$thisort = '0';
	$thissingle = '0';
	$thisonline = '0';
	$thispic = '1';
	$thisalias = '';
	$thissex = '0';
	$thislevel = '0';
	$thisord = '0';
	$thisage = '0';
	$thisbirth = '0';
	$gotage = false;
	$do = true;
	$str = array();
	$url = array();
	$bpic = false;
	if(!empty($id)) {
		if($id == '1') {
			$do = 1;
			$thisonline = '1';
			$thispic = '1';
			$bpic = 1;
		} elseif($id == 'F') {
			$do = 1;
			$thisonline = '1';
			$thispic = '1';
			$thissex = 'F';
			$bpic = 3;
		} elseif($id == 'M') {
			$do = 1;
			$thisonline = '1';
			$thispic = '1';
			$thissex = 'M';
			$bpic = 3;
		}
	}
	$lan_sql = $sql->query("SELECT st_lan FROM {$t}pstlan ORDER BY main_id ASC");
	if(!empty($_POST['do'])) $do = '1';
	if($do) {
		$url[] = 'do=1&';
		if(!empty($_POST['lan'])) {
			$thislan = $_POST['lan'];
		}
		if(!empty($_POST['sex'])) {
			$thissex = $_POST['sex'];
		}
		if(!empty($_POST['ord']) && ($_POST['ord'] == 'A' || $_POST['ord'] == 'L')) {
			$thisord = $_POST['ord'];
		}
		$str[] = '+ACTIVE';
		if($thislan) {
			$ort_sql = $sql->query("SELECT st_ort FROM {$t}pstort WHERE st_lan = '".secureINS($thislan)."' ORDER BY st_ort");
			if(!count($ort_sql)) { $thislan = '0'; }
		}
		if(!empty($_POST['ort']) && $thislan) {
			$thisort = $_POST['ort'];
			$ort_check = $sql->queryResult("SELECT st_lan FROM {$t}pstort WHERE st_ort = '".secureINS($thisort)."' LIMIT 1");
			if(empty($ort_check) || $ort_check != $thislan) $thisort = '0';
		}
		if(!empty($_POST['l']) && is_numeric($_POST['l'])) {
			if($_POST['l'] > 0 && $_POST['l'] <= 10) {
				$thislevel = intval($_POST['l']);
			}
		}
		if(!empty($_POST['single'])) $thissingle = '1';
		if(!empty($_POST['online'])) $thisonline = '1';
		if(!empty($_POST['birth']) && $isG) $thisbirth = '1';
		if(!empty($_POST['pic']) || $bpic) $thispic = '1'; elseif($do === '1') $thispic = '0'; 
		if(!empty($_POST['alias'])) $thisalias = secureINS($_POST['alias']);
		if(!empty($_POST['age']) && is_numeric($_POST['age']) && $_POST['age'] > 0 && $_POST['age'] < 10) $thisage = $_POST['age'];
		if($thislan) {
			$str[] = '+LÄN'.str_replace('-', '', str_replace(' ', '', $thislan));
			$url[] = 'lan='.$thislan.'&';
		}
		if($thisort) {
			$str[] = '+ORT'.str_replace('-', '', str_replace(' ', '', $thisort));
			$url[] = 'ort='.$thisort.'&';
		}
		if($thissingle) {
			$str[] = '+SINGLEYES';
			$url[] = 'single='.$thissingle.'&';
		}
		if($thislevel) {
			$str[] = '+LEVEL'.$thislevel;
			$url[] = 'l='.$thislevel.'&';
		}
		if($thispic) {
			$str[] = '+VALID';
			$url[] = 'pic='.$thispic.'&';
		}
		if($thissex && ($thissex == 'M' || $thissex == 'F')) {
			$str[] = '+SEX'.$thissex;
			$url[] = 'sex='.$thissex.'&';
		}
		if($thisalias) {
			$url[] = 'alias='.secureOUT($thisalias).'&';
		}
		if($thisage) {
			$str[] = '+AGEOF'.$thisage;
			$url[] = 'age='.$thisage.'&';
		}
		$join = array();
		if($thisonline) {
			#$join[] = "INNER JOIN {$t}useronline o ON o.id_id = l.id_id AND o.account_date > '".$user->timeout(UO)."'";
			$url[] = 'online='.$thisonline.'&';
		}

/*
		if(!empty($_POST['age']) && array_key_exists($_POST['age'], $ages)) {
			$age = $ages[$_POST['age']];
			$thisage = $_POST['age'];
			if($age[0] != 'X') {
				if($age[1] == 'X') {
					$gotage = true;
					#$join[] = " INNER JOIN {$t}userbirth b ON b.id_id = u.id_id AND (DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(b.level_id))-1,'%Y') + 0) >= ".($age[0]);
					if($thisonline)
						$join[] = " INNER JOIN {$t}userbirth b ON b.id_id = u.id_id AND (YEAR(NOW()) - YEAR(b.level_id)) >= ".($age[0]);
				} else {
					$gotage = true;
					#$join[] = " INNER JOIN {$t}userbirth b ON b.id_id = u.id_id AND (DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(b.level_id))-1,'%Y') + 0) >= ".($age[0])." AND (DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(b.level_id)),'%Y') + 0) <= ".($age[1]);
					if($thisonline)
						$join[] = " INNER JOIN {$t}userbirth b ON b.id_id = u.id_id AND (YEAR(NOW()) - YEAR(b.level_id)) >= ".($age[0])." AND (DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(b.level_id)),'%Y') + 0) <= ".($age[1]);
					#$join[] = " INNER JOIN {$t}userbirth b ON b.id_id = u.id_id AND b.level_id >= '1980-05-08' AND b.level_id <= '1988-05-08'";
				}
			}
		}

		if($thisbirth) {
			#if(!$thisonline)
			$join[] = " INNER JOIN {$t}userbirth b2 ON b2.id_id = u.id_id AND b2.level_id LIKE '%-".date("m-d")."'";
		}
*/
			/*else if($thisage && $age[0] != 'X') {
				if($age[1] != 'X')
					$res = "FROM {$t}userbirth b INNER JOIN {$t}userlevel l ON l.id_id = b.id_id LEFT JOIN {$t}user u ON u.id_id = l.id_id ".implode(' ', $join)." WHERE (YEAR(NOW()) - YEAR(b.level_id)) >= ".($age[0])." AND (DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(b.level_id)),'%Y') + 0) <= ".($age[1])." AND MATCH(l.level_id) AGAINST ('".implode(" ", $str)."' IN BOOLEAN MODE)";
				else
					$res = "FROM {$t}userbirth b INNER JOIN {$t}userlevel l ON l.id_id = b.id_id LEFT JOIN {$t}user u ON u.id_id = l.id_id ".implode(' ', $join)." WHERE (YEAR(NOW()) - YEAR(b.level_id)) >= ".($age[0])." AND MATCH(l.level_id) AGAINST ('".implode(" ", $str)."' IN BOOLEAN MODE)";
			}*/ 
		$lim = ($thispic)?45:50;
		$paging = paging(@$_POST['p'], $lim);
		if(count($str) > 1) {
			if($thisonline)
				$res = "FROM {$t}useronline o INNER JOIN {$t}userlevel l ON l.id_id = o.id_id LEFT JOIN {$t}user u ON u.id_id = l.id_id ".implode(' ', $join)." WHERE o.account_date > '".$user->timeout(UO)."' AND MATCH(l.level_id) AGAINST ('".implode(" ", $str)."' IN BOOLEAN MODE)";
			else
				$res = "FROM {$t}userlevel l LEFT JOIN {$t}user u ON u.id_id = l.id_id ".implode(' ', $join)." WHERE MATCH(l.level_id) AGAINST ('".implode(" ", $str)."' IN BOOLEAN MODE)";
		} else {
			if($thisonline)
				$res = "FROM {$t}useronline o LEFT JOIN {$t}user u ON u.id_id = o.id_id ".implode(' ', $join)." WHERE o.account_date > '".$user->timeout(UO)."'";
			else
				$res = "FROM {$t}user u ".implode(' ', $join)." WHERE u.status_id = '1'";
		}
		if($thisalias) {
			$res .= " AND u.u_alias LIKE '%".$thisalias."%'";
		}
/*
		if($gotage) {
			$url[] = 'age='.$thisage.'&';
		}
*/
		if($thisonline) {
			#$thisord = false;
			$page = 'online';
			if($bpic == 3) $page = $thissex.'online';
			if(@$_SERVER['QUERY_STRING'] == 'do=1&online=1&pic=1'.$sexu) $page = $sexs.'online';
		} else $page = 'null';

		if($thisord == 'L' || !$thisord) {
			$ord = 'ORDER BY u.lastonl_date DESC';
		} else {
			$ord = 'ORDER BY u.u_alias ASC';
		}
		if($thisord) $url[] = 'ord='.secureOUT($thisord).'&';
		#$paging['co'] = $sql->queryResult("SELECT COUNT(*) as count $res");
//$ord
		#$res = $sql->query("SELECT u.id_id, u.u_alias, u.u_sex, u.u_birth, u.u_picvalid, u.u_picid, u.u_picd, u.u_pstlan, u.level_id, u.u_pstort, u.account_date, u.lastlog_date, u.lastonl_date $res  LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
#execSt(1); print '<br>';
		$paging['co'] = 200;
		$res = $sql->query("SELECT u.id_id, u.u_alias, u.u_sex, u.u_birth, u.u_picvalid, u.u_picid, u.u_picd, u.u_pstlan, u.level_id, u.u_pstort, u.account_date, u.lastlog_date, u.lastonl_date $res LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
#print '<br>'; execSt(1);
		if(count($res) < $lim) {
			$paging['co'] = (($paging['p']-1) * $lim) + count($res);
		}
		#$paging['co'] = count($res);
	}
$sext = array('F' => 'tjejer ', 'M' => 'killar ');
if(!$r) {
	$sexs = '';
	$sexu = '';
} else {
	if($r == 'B') {
		$sexs = '';
		$sexu = '';
	} else {
		$sexs = $r;
		$sexu = '&sex='.$r;
	}
}
#if($sexu)

	$menu = array('user' => array(l('list', 'users'), 'senast inloggade'), 'online' => array(l('list', 'users', '1'), 'visa online'), 'Fonline' => array(l('list', 'users', 'F'), 'tjejer online'), 'Monline' => array(l('list', 'users', 'M'), 'killar online'));
#else
#	$menu = array('user' => array(l('list', 'users'), 'senast inloggade'), 'online' => array(l('list', 'users', '1'), 'visa online'));











	require(DESIGN.'head.php');
?>
<script type="text/javascript">
function changePage(p) {
	document.search.p.value = p;
	document.search.submit();
}
</script>
<form name="search" action="<?=l('list', 'users', '2')?>" method="post">
		<input type="hidden" name="do" value="1" />
		<input type="hidden" name="p" value="0" />
		<div id="bigContent">
			<div class="bigHeader2"><h4>sök - <?=makeMenu($page, $menu)?></h4></div>
			<div class="bigBoxed2">
			<table cellspacing="0" class="mrg">
			<tr>
				<td style="padding-right: 30px;">alias:<br /><input type="text" class="txt" style="width: 170px;" name="alias" value="<?=secureOUT($thisalias)?>" /></td>
				<td style="padding-right: 30px;">bor i:<br /><select class="txt" name="lan" onchange="this.form.submit();" style="width: 170px;"><option value="0">alla län</option>
<?

	foreach($lan_sql as $lan_res) {
		$sel = ($thislan === $lan_res[0])?' selected':'';
		echo '<option value="'.$lan_res[0].'"'.$sel.'>'.secureOUT($lan_res[0]).'</option>';
	}
	#if(!empty($thislan)) {
		echo '</select><br />';
		echo '<select name="ort"'.(empty($thislan)?' disabled':'').' style="width: 170px;" class="txt" onchange="this.form.submit();">
		<option value="0">i alla orter</option>';
		foreach($ort_sql as $ort_res) {
			$sel = ($thisort === $ort_res[0])?' selected':'';
			echo '<option value="'.$ort_res[0].'"'.$sel.'>'.$ort_res[0].'</option>';
		}
	#}

?>
				</select></td>
				<td style="padding-right: 30px;">alternativ:<br />
					<input type="checkbox" class="chk" value="1" name="pic" id="pic1" onclick="this.form.submit();"<?=($thispic)?' checked':'';?>><label for="pic1"> har bild</label><br />
					<input type="checkbox" class="chk" value="1" name="online" id="online1" onclick="this.form.submit();"<?=($thisonline)?' checked':'';?>><label for="online1"> är online</label><br />
					<input type="checkbox" class="chk" name="l" value="6" name="l_6" id="l_6" onclick="this.form.submit();"<?=($thislevel == '6')?' checked':'';?>><label for="l_6"> VIP</label>
				</td>
				<td style="padding-right: 30px;">kön:<br />
					<input type="radio" class="chk" name="sex" value="0" id="s_0" onclick="this.form.submit();"<?=(!$thissex)?' checked':'';?>><label for="s_0"> alla</label><br />
					<input type="radio" class="chk" name="sex" value="M" id="s_m" onclick="this.form.submit();"<?=($thissex == 'M')?' checked':'';?>><label for="s_m"> killar</label><br />
					<input type="radio" class="chk" name="sex" value="F" id="s_f" onclick="this.form.submit();"<?=($thissex == 'F')?' checked':'';?>><label for="s_f"> tjejer</label>
				</td>
				<td>ålder:<br />
					<select name="age" class="txt" onchange="this.form.submit();">
					<option value="0"<?=(!$thisage)?' selected':'';?>>alla åldrar</option>
					<option value="1"<?=($thisage == '1')?' selected':'';?>>mellan 0-20 år</option>
					<option value="2"<?=($thisage == '2')?' selected':'';?>>mellan 21-25 år</option>
					<option value="3"<?=($thisage == '3')?' selected':'';?>>mellan 26-30 år</option>
					<option value="4"<?=($thisage == '4')?' selected':'';?>>mellan 31-35 år</option>
					<option value="5"<?=($thisage == '5')?' selected':'';?>>mellan 36-40 år</option>
					<option value="6"<?=($thisage == '6')?' selected':'';?>>mellan 41-45 år</option>
					<option value="7"<?=($thisage == '7')?' selected':'';?>>mellan 46-50 år</option>
					<option value="8"<?=($thisage == '8')?' selected':'';?>>mellan 51-55 år</option>
					<option value="9"<?=($thisage == '9')?' selected':'';?>>56 år och äldre</option>
					</select>
				</td>
			</tr>
			</table>
			<input type="submit" class="btn2_sml r" value="sök" /><br class="clr" />
			</div>
			<div><?	if($do && count($res)) dopaging($paging, 'javascript:changePage(\'', '\');', 'biggest', STATSTR, 0); ?></div>
			<table cellspacing="0"<?=($thispic)?'':' width="783"';?>>
<?
	if(!empty($res) && count($res)) {
		$i = 0;
		$nl = true;
	if($thispic) {
		foreach($res as $row) {
			if($nl) echo (($i)?'</tr>':'').'<tr>';
			$i++;
			echo '<td style="padding: 0 0 6px '.((!$nl)?'5':'0').'px;">'.$user->getimg($row['id_id'].$row['u_picid'].$row['u_picd'].$row['u_sex'], $row['u_picvalid'], 0, array('text' => $row['u_alias'].' '.$sex[$row['u_sex']].$user->doage($row['u_birth'], 0))).'</td>';
			if($i % 16 == 0) $nl = true; else $nl = false;
		}
	} else {
		$i = 0;
		foreach($res as $row) {
			$i++;
			#$gotpic = ($row['u_picvalid'] == '1')?true:false;
			$gotpic = false;
echo '
<tr'.(($gotpic)?' onmouseover="this.className = \'t1\'; dumblemumble(\''.$row['id_id'].$row['u_picid'].$row['u_picd'].$i.'\', 1);" onmouseout="this.className = \'\'; mumbledumble(\''.$row['id_id'].$row['u_picid'].$row['u_picd'].$i.'\', 0, 1);"':' onmouseover="this.className = \'t1\';" onmouseout="this.className = \'\';"').'>
	<td class="cur pdg spac" width="250">'.$user->getstring($row, '', array('icons' => 1)).'</td>
	<td class="cur pdg spac" onclick="goUser(\''.$row['id_id'].'\');"><nobr>'.ucwords(strtolower($row['u_pstort'].($row['u_pstlan']?', ':'').$row['u_pstlan'])).'</nobr></td>
	<td class="cur pdg spac cnt" onclick="goUser(\''.$row['id_id'].'\');">'.(($gotpic)?'<img src="./_img/icon_gotpic.gif" alt="har bild" style="margin-top: 2px;" />':'&nbsp;').'</td>
	<td class="cur pdg spac rgt" onclick="goUser(\''.$row['id_id'].'\');"><nobr>'.(($user->isonline($row['account_date']))?'<span class="on">online ('.nicedate($row['lastlog_date'], 2).')</span>':'<span class="off">'.nicedate($row['lastonl_date'], 2).'</span>').'</nobr></td>
</tr>';
if($gotpic) echo '<tr id="pic:'.$i.'" style="display: none;"><td colspan="2">'.$user->getimg($row['id_id'].$row['u_picid'].$row['u_picd'].$row['u_sex'], $row['u_picvalid']).'</td></tr>';
		}
	}
	} else {
		$do_p = false;
echo '
<tr>
	<td class="spac pdg cnt" width="786">Inga listade.</td>
</tr>
';
	}
?>
</table>
<?
	if($do && count($res)) dopaging($paging, 'javascript:changePage(\'', '\');', 'biggest', '&nbsp;', 0);
?>
			</div>
		</div>
<?
	include(DESIGN.'foot.php');
?>