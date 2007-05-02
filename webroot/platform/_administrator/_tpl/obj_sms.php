<?
	if(!function_exists('notallowed') || notallowed()) {
		header("Location: ./");
		exit;
	}
	$thispage = 'obj.php?status=sms';
	$view_full = 0;
	if(!empty($_GET['all'])) {
		$view_full = intval($_GET['all']);
	}
	$level_price = array('3' => '15', '5' => '25', '6' => '50', '99' => '5');
	$level_names = array('3' => 'BRONS', '5' => 'SILVER', '6' => 'GULD', '99' => 'HÖGUPPLÖST');
	$full_arr = array(
"0" => mysql_result(mysql_query("SELECT COUNT(*) as count FROM s_smsin s"), 0, 'count'),
"1" => mysql_result(mysql_query("SELECT COUNT(*) as count FROM s_smsin s WHERE status_id = '1' AND (tracking_status = '' OR tracking_status = 'unknown')"), 0, 'count'));

	$paging = paging(@$_GET['p'], 300);
	$paging['co'] = $full_arr[$view_full];
	$list_tot = $sql->query("SELECT s.level_id, s.tracking_status FROM s_smsin s LEFT JOIN {$tab['user']} u ON u.id_id = s.user_id ORDER BY s.main_id DESC", 0, 1);
	if($view_full == 1) $list = $sql->query("SELECT s.main_id, s.str, s.level_id, s.sess_sender, s.status_id, s.price_id, s.sess_date, s.tracking_status, s.tracking_id, s.sess_id, u.u_alias, u.id_id, u.u_cell, u.status_id as user_status, u.city_id FROM s_smsin s LEFT JOIN {$tab['user']} u ON u.id_id = s.user_id WHERE s.status_id = '1' AND (s.tracking_status = '' OR s.tracking_status = 'unknown') ORDER BY s.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
	else $list = $sql->query("SELECT s.main_id, s.str, s.level_id, s.sess_sender, s.status_id, s.price_id, s.sess_date, s.tracking_status, s.tracking_id, s.sess_id, u.u_alias, u.id_id, u.u_cell, u.status_id as user_status, u.city_id FROM s_smsin s LEFT JOIN {$tab['user']} u ON u.id_id = s.user_id ORDER BY s.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
	require("./_tpl/obj_head.php");
?>
			<input type="radio" class="inp_chk" name="view" value="0" id="view_0" onclick="document.location.href = '<?=$thispage?>';"<?=(!$view_full)?' checked':'';?>><label for="view_0" class="txt_bld txt_look">Inkommande</label> [<?=$full_arr[0]?>]
			<input type="radio" class="inp_chk" name="view" value="1" id="view_1" onclick="document.location.href = '<?=$thispage?>&all=1';"<?=($view_full)?' checked':'';?>><label for="view_1" class="txt_bld txt_look">Väntar</label> [<?=$full_arr[1]?>]

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
			$total += (($row['tracking_status'] == 'failed')?0:@$level_price[@$row['level_id']]);
		}
		echo '<tr class="bg_gray"><td class="pdg">'.$total.':-</td></tr>';
		echo '</table>';
	}
	if(count($list) && !empty($list)) {
		echo '<table cellspacing="2">';
		echo '<tr><th>Stad</th><th>Sträng</th><th>Användare</th><th>Typ</th><th>Kostnad</th><th>Nummer</th><th>Status för SMS</th><th>Status för KÖP</th><th>Logg-id</th><th>Datum</th></tr>';
		$total = 0;
		foreach($list as $row) {
		$total += (($row['tracking_status'] == 'failed')?0:@$level_price[@$row['level_id']]);
		$price = ($row['tracking_status'] == 'failed')?'0':@$row['price_id'];
		echo '<tr class="bg_gray">
			<td class="pdg">'.@$cities[$row['city_id']].'</td>
			<td class="pdg">'.secureOUT($row['str']).'</td>
			<td class="pdg"><a href="user.php?t&id='.$row['id_id'].'">'.($row['user_status'] == '1'?secureOUT($row['u_alias']).'</a>':(!empty($row['id_id'])?secureOUT($row['u_alias']).'</a> <b>[RADERAD]</b>':'')).'</td>
			<td class="pdg bld">'.@$level_names[@$row['level_id']].'</td>
			<td class="pdg cnt">'.($price).':-</td>
			<td class="pdg rgt"><span title="Mobilnummer som skickade SMS">'.secureOUT($row['sess_sender']).'</span><br /><span title="Användarens mobilnummer">'.secureOUT($row['u_cell']).'</span></td>
			<td class="pdg">'.secureOUT($row['tracking_status']).'</td>
			<td class="pdg">'.(($row['status_id'] == '1')?(($price)?(($row['tracking_status'] == 'delivered')?'GODKÄNT':'VÄNTAR...'):'<b>FELAKTIGT</b>'):'<b>FELAKTIGT</b>').'</td>
			<td class="pdg">'.secureOUT($row['tracking_id']).'<br />'.secureOUT($row['sess_id']).'</td>
			<td class="pdg cnt">'.niceDate($row['sess_date']).'</td>
		</tr>';
		}
		echo '<tr class="bg_gray"><td class="pdg">Totalt i listan:</td>
			<td colspan="8" align="right" class="pdg bld">'.$total.':-</td></tr>';
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