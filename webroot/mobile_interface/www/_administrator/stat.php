<?
session_start();
ob_start();
    ob_implicit_flush(0);
    ob_start('ob_gzhandler');

	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}
	if(!$isCrew && strpos($_SESSION['u_a'][1], 'stat') === false) errorNEW('Ingen beh�righet.');
	$page = 'STATISTIK';
	$menu = $menu_STAT;
	$start = execSt();
	$start_date = gettxt('start_date');
	$today_date = date("Y-m-d");
	$total_days = date_diff($today_date, $start_date);
	$total_days = $total_days['days'];
	$this_day = date("Y-m-d");
	$this_hour = date("H");
	$total_today = date_diff(date("Y-m-d H:i"), $today_date.' 00:00');
	$total_min = 0;

	if($total_today['days']) $total_min += $total_today['days'] * 24 * 60;
	if($total_today['hours']) $total_min += $total_today['hours'] * 60;
	if($total_today['minutes']) $total_min += $total_today['minutes'];

	$total_all = 1 * 24 * 60;

	$e_count = 0;
	$change = false;
	$view = '1';
	$year = date("Y");
	$month = date("m");
	$sel_year = date("Y");
	$sel_month = date("m");

	if(!empty($_GET['view'])) {
		$view = 1;
	}


	if($view && !empty($_GET['date']) && is_numeric($_GET['date']) && strlen($_GET['date']) == 6) {
		$sel_year = substr($_GET['date'], 0, 4);
		$sel_month = substr($_GET['date'], 4, 2);
	}
	$prev = date("Ym", strtotime($sel_year.'-'.$sel_month.'-01 -1 MONTH'));
	$next = date("Ym", strtotime($sel_year.'-'.$sel_month.'-01 +1 MONTH'));

	if(!empty($_POST['do'])) {
		if(!empty($_POST['id']) && !empty($_POST['ins_filter'])) {
			@mysql_query("UPDATE {$t}logfilter SET unique_id = '".secureINS($_POST['ins_filter'])."' WHERE main_id = '".secureINS($_POST['id'])."' LIMIT 1");
		} elseif(!empty($_POST['ins_filter'])) {
			@mysql_query("INSERT INTO {$t}logfilter SET unique_id = '".secureINS($_POST['ins_filter'])."', status_id = '1'");
		}
		mysql_query("UPDATE {$t}logfilter SET status_id = '0'");
		foreach($_POST as $key => $val) {
			if(strpos($key, 'ch:') !== false) {
				$kid = explode(":", $key);
				$kid = $kid[1];
				if(isset($_POST['ch:' . $kid])) {
					mysql_query("UPDATE {$t}logfilter SET status_id = '1' WHERE main_id = '".secureINS($kid)."' LIMIT 1");
				}
			}
		}
		header("Location: stat.php");
		exit;
	}
	if(!empty($_GET['filter_id']) && is_numeric($_GET['filter_id'])) {
		$sql = mysql_query("SELECT * FROM {$t}logfilter WHERE main_id = '".secureINS($_GET['filter_id'])."' LIMIT 1");
		if(mysql_num_rows($sql)) {
			$change = true;
			$row = mysql_fetch_assoc($sql);
		}
	}
	if(!empty($_GET['filter_del']) && is_numeric($_GET['filter_del'])) {
		$sql = mysql_query("SELECT * FROM {$t}logfilter WHERE main_id = '".secureINS($_GET['filter_del'])."' LIMIT 1");
		if(mysql_num_rows($sql)) {
			@mysql_query("DELETE FROM {$t}logfilter WHERE main_id = '".secureINS($_GET['filter_del'])."' LIMIT 1");
		}
		header("Location: stat.php");
		exit;
	}

	$filter = mysql_query("SELECT * FROM {$t}logfilter");
	$do_filter = array();
	$use_filter = '';
	if(mysql_num_rows($filter)) {
		$i = 0;
		while($f = mysql_fetch_assoc($filter)) {
			if($f['status_id']) {
				$do_filter[] = "type_referer NOT LIKE '%".$f['unique_id']."%'";
				$i++;
			}
		}
		if($i) $use_filter .= implode(" AND ", $do_filter).' AND';
	}

	$try = mysql_query("SELECT type_referer, type_cnt FROM {$t}logreferer WHERE $use_filter type_referer != ''
GROUP BY type_referer HAVING(type_cnt > 1)
ORDER BY type_cnt DESC");


	$sqlt = array(
'today' => "SELECT COUNT(*) as count FROM {$t}logvisit WHERE date_snl = CURDATE()",
'yester' => "SELECT COUNT(*) as count FROM {$t}logvisit WHERE date_snl = DATE_ADD(CURDATE(), INTERVAL -1 DAY)",
'month' => "SELECT COUNT(*) as count FROM {$t}logvisit WHERE MONTH(date_snl) = MONTH(CURDATE())",
'total' => "SELECT COUNT(*) as count FROM {$t}logvisit",
'spec' => "SELECT COUNT(*) as count FROM {$t}logvisit WHERE MONTH(date_snl) = '$sel_month' AND YEAR(date_snl) = '$sel_year'",
);

	$today_tot = mysql_result(mysql_query($sqlt['today']), 0, 'count');

	$hours = date("H") + 1;
	require("./_tpl/admin_head.php");
?>
<script type="text/javascript" src="fnc_adm.js"></script>
<script type="text/javascript" src="fnc_txt.js"></script>
	<table width="100%" height="100%">
	<tr><td height="25" colspan="3"><nobr><?makeMenu($page, $menu, 0);?></td></tr>
	<tr>
		<td width="25%" style="padding: 0 10px 0 0">
			<table width="100%">
			<tr>
				<td height="25"><b>Prim�rstatistik</b></td>
			</tr>
			</table>
			<table cellspacing="0" width="100%">
			<tr><td colspan="2"><b>Just nu</b></td></tr>
			<tr>
				<td>Aktiva bes�kare sedan 8 minuter:</td>
				<td align="right" class="txt_chead txt_bld"><?=$online = mysql_result(mysql_query("SELECT COUNT(DISTINCT(sess_ip)) as count FROM {$t}log WHERE date_cnt > DATE_SUB(NOW(), INTERVAL 8 MINUTE)"), 0, 'count')?></td>
			</tr>
			<tr><td colspan="2"><br><b>Idag</b></td></tr>
			<tr>
				<td>Dygnsunika bes�kare:</td>
				<td align="right" class="txt_chead txt_bld"><?=$today_tot?></td>
			</tr>
			<tr>
				<td>Unika IP:</td>
				<td align="right" class="txt_chead txt_bld"><?=$today_ip = mysql_result(mysql_query("SELECT COUNT(DISTINCT(sess_ip)) as count FROM {$t}logvisit WHERE date_snl = NOW()"), 0, 'count')?></td>
			</tr>
			<tr>
				<td>Antal unika IP per timme i snitt:</td>
				<td align="right" class="txt_chead txt_bld"><?=round($today_ip / $hours, 2)?></td>
			</tr>
			<tr>
				<td>Prognos f�r dygnsunika bes�kare:</td>
				<td align="right" class="txt_chead txt_bld"><?=round(($today_tot / $total_min) * $total_all)?></td>
			</tr>
			<tr><td colspan="2"><br><b>Ig�r</b></td></tr>
			<tr>
				<td>Dygnsunika bes�kare:</td>
				<td align="right" class="txt_chead txt_bld"><?=$yester_v = mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}logvisit WHERE date_snl = DATE_ADD(CURDATE(), INTERVAL -1 DAY)"), 0, 'count')?></td>
			</tr>
			<tr>
				<td>Unika IP:</td>
				<td align="right" class="txt_chead txt_bld"><?=$yester_ip = mysql_result(mysql_query("SELECT COUNT(DISTINCT(sess_ip)) as count FROM {$t}logvisit WHERE date_snl = DATE_ADD(CURDATE(), INTERVAL -1 DAY)"), 0, 'count')?></td>
			</tr>
			<tr>
				<td>Antal unika IP per timme i snitt:</td>
				<td align="right" class="txt_chead txt_bld"><?=round($yester_ip / 24, 2)?></td>
			</tr>
			<tr><td colspan="2"><br><b>Totalt</b></td></tr>
			<tr>
				<td>Dygnsunika bes�kare:</td>
				<td align="right" class="txt_chead txt_bld"><?=$total_v = mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}logvisit"), 0, 'count')?></td>
			</tr>
			<tr>
				<td>Unika IP:</td>
				<td align="right" class="txt_chead txt_bld"><?=$total_ip = mysql_result(mysql_query("SELECT COUNT(DISTINCT(sess_ip)) as count FROM {$t}logvisit"), 0, 'count')?></td>
			</tr>
			<tr>
				<td>Antal unika IP per dag i snitt:<br>Start: <?=niceDate($start_date)?></td>
				<td align="right" class="txt_chead txt_bld"><?=round($total_ip / $total_days, 2)?></td>
			</tr>


			</table>
			<hr /><div class="hr"></div>
			<table width="100%" style="margin-bottom: 5px;">
			<tr>
				<td height="20" colspan="2"><b>Bes�karinfo</b></td>
			</tr>
<?
	$sql = mysql_query("SELECT user_string, COUNT(user_string) as count FROM {$t}logvisit WHERE user_string != '' GROUP BY user_string ORDER BY `count` DESC");
	while($r = mysql_fetch_assoc($sql)) {
		echo '<tr><td><span class="txt_chead txt_bld">'.$r['count'].'</span>st:&nbsp;</td><td>'.secureOUT($r['user_string']).'</td></tr>';
	}
?>
			</table>
		</td>
		<td width="49%" style="padding: 0 10px 0 10px; background: url('_img/brd_h.gif'); background-repeat: repeat-y;">
			<table width="100%">
			<tr>
				<td height="25"><b>Periodstatistik</b></td>
			</tr>
			</table>
			Visar:<br>
<!--			<input type="radio" class="inp_chk" name="view" value="0" id="view_0" onclick="document.location.href = 'stat.php?view=' + this.value;"<?=(!$view)?' checked':'';?>><label for="view_0" class="txt_bld txt_look">Tidsbaserad</label>-->
			<input type="radio" class="inp_chk" name="view" value="1" id="view_1" onclick="document.location.href = 'stat.php?view=' + this.value;"<?=($view == '1')?' checked':'';?>><label for="view_1" class="txt_bld txt_look">Dygnsbaserad</label>
<?
	if(!$view) {
?>
			<center>
			<b>Idag</b>
			<table cellspacing="2" style="margin-bottom: 20px;">
			<tr>
<?
	#$tot = $today_tot;
	$tot = 600;
	for($i = 0; $i <= 23; $i++) {
		$c = mysql_query("SELECT type_cnt FROM $stat_tab WHERE date_cnt = CURDATE() AND type_inf = '$i'");
		if(!mysql_num_rows($c) || $this_hour == $i) {
			$single = mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}logvisit WHERE date_snl = CURDATE() AND HOUR(date_cnt) = $i"), 0, 'count');
			$t = mysql_query("INSERT INTO $stat_tab SET date_cnt = CURDATE(), type_inf = '$i', type_cnt = '$single'");
		} elseif($this_hour == $i) {
			$single = mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}logvisit WHERE date_snl = CURDATE() AND HOUR(date_cnt) = $i"), 0, 'count');
			$t = mysql_query("UPDATE $stat_tab SET type_cnt = '$single' WHERE date_cnt = CURDATE() AND type_inf = '$i'");
		} elseif($i < $this_hour && !mysql_result($c, 0, 'type_cnt')) {
			$single = mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}logvisit WHERE date_snl = CURDATE() AND HOUR(date_cnt) = $i"), 0, 'count');
			$t = mysql_query("UPDATE $stat_tab SET type_cnt = '$single' WHERE date_cnt = CURDATE() AND type_inf = '$i'");
		} else {
			$single = mysql_result($c, 0, 'type_cnt');
		}
		$proc = round(($single / $tot)*400);
		echo '<td class="btn cnt">'.$single.'<br><img src="_img/rlr.gif" height="'.$proc.'" width="7"'.((!$proc)?' style="visibility: hidden;"':'').'><br>'.$i.'</td>'."\n";
	}
?>
			</tr>
			</table>
			<b>Ig�r</b>
			<table cellspacing="2" style="margin-bottom: 20px;">
			<tr>
<?
	#$tot = mysql_result(mysql_query($sqlt['yester']), 0, 'count');
	$tot = 600;
	for($i = 0; $i <= 23; $i++) {
		$c = mysql_query("SELECT type_cnt FROM $stat_tab WHERE date_cnt = DATE_ADD(CURDATE(), INTERVAL -1 DAY) AND type_inf = '$i'");
		if(!mysql_num_rows($c)) {
			$single = mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}logvisit WHERE date_snl = DATE_ADD(CURDATE(), INTERVAL -1 DAY) AND HOUR(date_cnt) = $i"), 0, 'count');
			$t = mysql_query("INSERT INTO $stat_tab SET date_cnt = DATE_ADD(CURDATE(), INTERVAL -1 DAY), type_inf = '$i', type_cnt = '$single'");
		} elseif(!mysql_result($c, 0, 'type_cnt')) {
			$single = mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}logvisit WHERE date_snl = DATE_ADD(CURDATE(), INTERVAL -1 DAY) AND HOUR(date_cnt) = $i"), 0, 'count');
			$t = mysql_query("UPDATE $stat_tab SET type_cnt = '$single' WHERE date_cnt = DATE_ADD(CURDATE(), INTERVAL -1 DAY) AND type_inf = '$i'");
		} else {
			$single = mysql_result($c, 0, 'type_cnt');
		}
		$proc = round(($single / $tot)*400);
		echo '<td class="btn cnt">'.$single.'<br><img src="_img/rlr.gif" height="'.$proc.'" width="7"'.((!$proc)?' style="visibility: hidden;"':'').'><br>'.$i.'</td>'."\n";
	}
?>
			</tr>
			</table>
			<b>Totalt</b>
			<table cellspacing="2" style="margin-bottom: 20px;">
			<tr>
<?
	#$tot = mysql_result(mysql_query($sqlt['total']), 0, 'count');
	$tot = 600;
	for($i = 0; $i <= 23; $i++) {
#		$single = mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}logvisit WHERE HOUR(date_cnt) = $i"), 0, 'count');
#		$unique = mysql_result(mysql_query("SELECT COUNT(DISTINCT(sess_ip)) as count FROM {$t}logvisit WHERE HOUR(date_cnt) = $i"), 0, 'count');
		$c = mysql_query("SELECT type_cnt FROM $stat_tab WHERE type_inf = '$i'");
		if(!mysql_num_rows($c)) {
			$single = mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}logvisit WHERE HOUR(date_cnt) = $i"), 0, 'count');
		} else {
			$single = 0;
			while($rr = mysql_fetch_row($c)) {
				$single += $rr[0];
			}
		}
		$u_proc = round(($single / $tot)*150);
		echo '<td class="btn cnt"><b>'.$single.'</b><br><img src="_img/rlr.gif" height="'.$u_proc.'" width="7"'.((!$u_proc)?' style="visibility: hidden;"':'').'><br>'.$i.'</td>'."\n";
	}
?>
			</tr>
			</table>
			</center>
			<!-- <img src="_img/rlrb.gif" height="8" width="7"> = Skillnaden mellan unika IP och antal bes�kare.<br><b>1000</b> = Totalantal f�r hela stapeln. -->

<?
	} else {
?>
			<!-- <br>Start<br><input type="text" class="txt" value="��MMDD"> -->
			<center>
<br>
			<div style="float: left;"><a href="stat.php?view=1&date=<?=$prev?>">F�reg�ende m�nad</a></div><div style="float: right;"><a href="stat.php?view=1&date=<?=$next?>">N�sta m�nad</a></div><b><?=ucfirst(strftime("%B", strtotime($sel_year.'-'.$sel_month.'-01')))?></b>
			<table cellspacing="2" width="100%" style="margin: 25px 0 10px 0;">
			<tr>
<?
	$tot = mysql_result(mysql_query($sqlt['spec']), 0, 'count');
	$cor = 0;
	$days = array();
	for($i = 0; $i <= 31; $i++) {
		if(checkdate($sel_month, $i, $year)) {
			$single = mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}logvisit WHERE date_snl = '$sel_year-$sel_month-$i'"), 0, 'count');
			$unique = mysql_result(mysql_query("SELECT COUNT(DISTINCT(sess_ip)) as count FROM {$t}logvisit WHERE date_snl = '$sel_year-$sel_month-$i'"), 0, 'count');
			if($tot) {
				$u_proc = round(($unique / $tot)*300);
				$proc = round((($single-$unique) / $tot)*300);
			} else {
				$proc = 0;
				$u_proc = 0;
			}
			$days["$year-$sel_month-".addzero($i)] = $single;
			if($i == 16) print '</tr><tr><td colspan="31"><br>&nbsp;</td></tr><tr>';
			if($single) $cor++;
			echo '<td class="btn cnt"><b>'.$single.'</b><br><img src="_img/rlrb.gif" height="'.$proc.'" width="7"'.((!$proc)?' style="visibility: hidden;"':'').'><br>'.$unique.'<br><img src="_img/rlr.gif" height="'.$u_proc.'" width="7"'.((!$u_proc)?' style="visibility: hidden;"':'').'><br>'.$i.'</td>'."\n";
		}
	}
?>
			</tr>
			</table>
			</center>
			<span class="txt_chead bld"><?=($cor)?round($tot/$cor, 2):0;?></span> per dag i snitt f�r <b><?=ucfirst(strftime("%B", strtotime($sel_year.'-'.$sel_month.'-01')))?></b>.
			<br><img src="_img/rlrb.gif" height="8" width="7"> = Skillnaden mellan unika IP och antal bes�kare.<br><b>1000</b> = Totalantal f�r hela stapeln.
			<br><br><br><b>Tabellstatistik</b>
			<table cellspacing="0" style="margin-top: 5px;">
<?
	foreach($days as $key => $val) {
		if($val)
		echo '<tr><td style="padding-right: 5px;"><b>'.strtoupper(strftime('%A', strtotime($key))).' '.specialDate($key).'</b></td><td>&nbsp;'.$val.'</td></tr>';
	}
?>
			</table>
<?
	}
?>
		</td>
		<td width="26%" style="padding: 0 10px 0 10px; background: url('_img/brd_h.gif'); background-repeat: repeat-y;">
			<form action="stat.php" method="post">
			<input type="hidden" name="do" value="1">
<?=($change)?'<input type="hidden" name="id" value="'.$row['main_id'].'">':'';?>
			<table width="100%">
			<tr>
				<td colspan="2" height="45"><b>Filter f�r referenser</b><br><input type="text" name="ins_filter" class="inp_nrm" value="<?=($change)?secureOUT($row['unique_id']):'';?>" /></td>
			</tr>
<?
	$i = 0;
	if(mysql_num_rows($filter)) mysql_data_seek($filter, 0);
	while($f = mysql_fetch_assoc($filter)) {
		if($f['status_id']) $i++;
		echo '<tr><td><input type="checkbox" class="chk" name="ch:'.$f['main_id'].'" id="ch'.$f['main_id'].'"'.(($f['status_id'])?' checked':'').'> <label for="ch'.$f['main_id'].'">'.$f['unique_id'].'</label></td><td align="right"><a href="stat.php?filter_id='.$f['main_id'].'">�NDRA</a> | <a href="stat.php?filter_del='.$f['main_id'].'">RADERA</a></td></tr>';
	}
?>
			<tr><td colspan="2" align="right"><input type="submit" value="Uppdatera" class="inp_realbtn" style="width: 70px; margin: 4px 0 0 0;"></td></tr>
			<tr><td colspan="2"><hr /><div class="hr"></div></td></tr>
			<tr>
				<td colspan="2" height="25"><b>Referenser</b> [<a href="javascript:popup('help.php?id=referer', 'help', 316, 355);">Hj�lp</a>]</td>
			</tr>
			<tr><td colspan="2" height="25">Det finns <span class="txt_chead txt_bld"><?=mysql_num_rows($try)?></span> olika referens<?=((mysql_num_rows($try) != '1')?'er':'')?> efter <span class="txt_chead txt_bld"><?=$i?></span> aktiv<?=($i != '1')?'a':'';?> filter.</td></tr>
			</table>
			</form>
			<table width="100%" cellspacing="0" style="margin-bottom: 10px;">
<?
	while($r = mysql_fetch_assoc($try)) {
		echo '<tr><td><span class="txt_chead txt_bld">'.$r['type_cnt'].'</span>st:&nbsp;</td><td><input type="text" readonly onfocus="this.select();" style="height: 11px; width: 250px; padding: 0; line-height: 11px;" value="'.secureOUT($r['type_referer']).'"></td></tr>';
	}
?>
			</table>
		</td>
	</tr>
	</table>
</body>
</html>
<?
	require("./_tpl/cnt_f.php");
?>