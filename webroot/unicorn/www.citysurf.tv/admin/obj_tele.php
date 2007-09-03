<?
	if(!function_exists('notallowed') || notallowed()) {
		header("Location: ./");
		exit;
	}
	$thispage = 'obj.php?status=tele';
	$view_full = 0;
	if(!empty($_GET['all'])) {
		$view_full = intval($_GET['all']);
	}
	$level_price = array('3' => '15', '5' => '25', '6' => '50');
	$level_names = array('3' => 'BRONS', '5' => 'SILVER', '6' => 'GULD');
	$full_arr = array(
"0" => mysql_result(mysql_query("SELECT COUNT(*) as count FROM s_smstele s INNER JOIN s_userupgrade p ON p.main_id = s.str INNER JOIN {$tab['user']} u ON u.id_id = p.id_id"), 0, 'count'));

	$paging = paging(@$_GET['p'], 300);
	$paging['co'] = $full_arr[0];
#print "SELECT s.main_id, s.date_cnt, p.upgrade_level, u.id_id, u.u_alias FROM s_smstele s INNER JOIN s_userupgrade p ON p.main_id = s.str INNER JOIN {$tab['user']} u ON u.id_id = p.id_id ORDER BY p.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}";
	$list_tot = $sql->query("SELECT p.upgrade_level FROM s_smstele s INNER JOIN s_userupgrade p ON p.main_id = s.str LEFT JOIN {$tab['user']} u ON u.id_id = p.id_id ORDER BY p.main_id DESC", 0, 1);
	$list = $sql->query("SELECT s.main_id, s.date_cnt, s.sess_nmb, p.upgrade_level, u.id_id, u.u_alias, u.status_id, u.city_id FROM s_smstele s INNER JOIN s_userupgrade p ON p.main_id = s.str LEFT JOIN {$tab['user']} u ON u.id_id = p.id_id ORDER BY p.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
	require("./_tpl/obj_head.php");
?>
			<input type="radio" class="inp_chk" name="view" value="0" id="view_0" onclick="document.location.href = '<?=$thispage?>';"<?=(!$view_full)?' checked':'';?>><label for="view_0" class="txt_bld txt_look">Betalda</label> [<?=$full_arr[0]?>]

			<form name="upd" method="post" action="./<?=$thispage?>">
			<input type="hidden" name="main_id:all" id="main_id" value="0">
			<input type="hidden" name="validate" value="1">

<!--			<input type="submit" class="inp_realbtn" value="Uppdatera" style="width: 70px; margin: 5px 2px 10px 0;">
			<input type="button" class="inp_realbtn" value="Neka blanka" style="width: 85px; margin: 5px 2px 10px 0;" onclick="document.getElementById('main_id').value = '2'; this.form.submit();">
			<input type="button" class="inp_realbtn" value="Godkänn blanka" style="width: 100px; margin: 5px 2px 10px 0;" onclick="document.getElementById('main_id').value = '1'; this.form.submit();">-->
<?
	if(isset($paging) && ($paging['p'] > 1 || $full_arr[$view_full] > $paging['slimit'] + $paging['limit'])) {
?>
					<table width="100%">
					<tr>
						<?=($paging['p'] > 1)?'<td><a href="'.$thispage.'&all='.$view_full.'" class="txt_look txt_bld">tillbaka</a></td>':'';?>
						<td align="right" valign="center">
<?
	$pm1 = $paging['p'] - 1;
	$pp1 = $paging['p'] + 1;
		if($paging['p'] > 1) {
			echo '<a href="'.$thispage.'&all='.$view_full.'&p='.$pm1.'" class="txt_look txt_bld">framåt</a>&nbsp;';
		}
		if($full_arr[$view_full] > $paging['slimit'] + $paging['limit']) {
			echo '<a href="'.$thispage.'&all='.$view_full.'&p='.$pp1.'" class="txt_look txt_bld">bakåt</a>&nbsp;';
		}
?>
						</td>
					</tr>
					</table>
<?	} else echo '<div>&nbsp;</div>';
?>
			<hr /><div class="hr"></div>
<?
	if(count($list_tot) && !empty($list_tot)) {
		echo '<table cellspacing="2" style="margin: 0 0 10px 0;">';
		echo '<tr><th>Total</th></tr>';
		$total = 0;
		foreach($list_tot as $row) {
			$total += $level_price[$row['upgrade_level']];
		}
		echo '<tr class="bg_gray"><td class="pdg">'.$total.':-</td></tr>';
		echo '</table>';
	}
	if(count($list) && !empty($list)) {
		echo '<table cellspacing="2">';
		echo '<tr><th>Stad</th><th>Användare</th><th>Nivå</th><th>Kostnad</th><th>Nummer</th><th>Datum</th></tr>';
		$total = 0;
		foreach($list as $row) {
		$total += $level_price[$row['upgrade_level']];
		echo '<tr class="bg_gray">
			<td class="pdg">'.@$cities[$row['city_id']].'</td>
			<td class="pdg"><a href="user.php?t&id='.$row['id_id'].'">'.($row['status_id'] == '1'?secureOUT($row['u_alias']).'</a>':secureOUT($row['u_alias']).'</a> <b>[RADERAD]</b>').'</td>
			<td class="pdg bld">'.@$level_names[@$row['upgrade_level']].'</td>
			<td class="pdg cnt">'.$level_price[$row['upgrade_level']].':-</td>
			<td class="pdg cnt">'.secureOUT($row['sess_nmb']).'</td>
			<td class="pdg cnt">'.niceDate($row['date_cnt']).'</td>
		</tr>';
		}
		echo '<tr class="bg_gray"><td class="pdg">Totalt i listan:</td>
			<td colspan="4" align="right" class="pdg bld">'.$total.':-</td></tr>';
		echo '</table>';
	}
?>
			</form>
<?
	if(isset($paging) && ($paging['p'] > 1 || $full_arr[$view_full] > $paging['slimit'] + $paging['limit'])) {
?>
			<table width="100%">
			<tr>
				<?=($paging['p'] > 1)?'<td><a href="'.$thispage.'&all='.$view_full.'" class="txt_look txt_bld">tillbaka</a></td>':'';?>
				<td align="right" height="20" valign="center">
<?
		if($paging['p'] > 1) {
			echo '<a href="'.$thispage.'&all='.$view_full.'&p='.$pm1.'" class="txt_look txt_bld">framåt</a>&nbsp;';
		}
		if($full_arr[$view_full] > $paging['slimit'] + $paging['limit']) {
			echo '<a href="'.$thispage.'&all='.$view_full.'&p='.$pp1.'" class="txt_look txt_bld">bakåt</a>&nbsp;';
		}
?>
				</td>
			</tr>
			</table>
<?	}
?>
		</td>
	</tr>
	</table>
</body>
</html>