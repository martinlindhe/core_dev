<?
	if(!function_exists('notallowed') || notallowed()) {
		header("Location: ./");
		exit;
	}
	$thispage = 'obj.php?status=full';
	$view_full = 0;
	if(!empty($_GET['all'])) {
		$view_full = 1;
	}

	if(!empty($_GET['del'])) {
		$res = $sql->queryResult("SELECT is_on FROM {$tab['full']} WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
		if($res == '1')
			$sql->queryUpdate("UPDATE {$tab['full']} SET is_on = '0' WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
		else
			$sql->queryUpdate("UPDATE {$tab['full']} SET is_on = '1' WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
		header("Location: ".$thispage.'&all='.$view_full);
		exit;
	}
	$full_arr = array(
"0" => mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$tab['full']} WHERE download_times = '0' AND is_on = '1'"), 0, 'count'),
"1" => mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$tab['full']} WHERE download_times > 0"), 0, 'count'));

	if($view_full) {
		$paging = paging(@$_GET['p'], 20);
		$list = $sql->query("SELECT a.*, b.topic_id, b.p_pic, b.id, u.u_alias FROM {$tab['full']} a LEFT JOIN {$tab['pic']} b ON b.main_id = a.pic_id LEFT JOIN {$tab['user']} u ON u.id_id = a.id_id WHERE a.download_times > '0' ORDER BY a.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
	} else {
		$list = $sql->query("SELECT a.*, b.topic_id, b.p_pic, b.id, u.u_alias FROM {$tab['full']} a LEFT JOIN {$tab['pic']} b ON b.main_id = a.pic_id LEFT JOIN {$tab['user']} u ON u.id_id = a.id_id WHERE a.download_times = '0' AND is_on = '1' ORDER BY a.main_id DESC", 0, 1);
	}
	require("./_tpl/obj_head.php");
?>
			<input type="radio" class="inp_chk" name="view" value="0" id="view_0" onclick="document.location.href = '<?=$thispage?>';"<?=(!$view_full)?' checked':'';?>><label for="view_0" class="txt_bld txt_look">Icke hämtade</label> [<?=$full_arr[0]?>]
			<input type="radio" class="inp_chk" name="view" value="1" id="view_1" onclick="document.location.href = '<?=$thispage?>&all=1';"<?=($view_full)?' checked':'';?>><label for="view_1" class="txt_bld txt_look">Hämtade</label> [<?=$full_arr[1]?>]

			<form name="upd" method="post" action="./<?=$thispage?>">
			<input type="hidden" name="main_id:all" id="main_id" value="0">
			<input type="hidden" name="validate" value="1">

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
	if(count($list) && !empty($list)) {
		echo '<table cellspacing="2">';
		echo '<tr><th>Bild</th><th>Användare</th><th>Mobilnummer</th><th>Bildnummer</th><th>Hämtningskod</th><th>Antal hämtningar</th><th>Datum</th></tr>';
		foreach($list as $row) {
		echo '<tr class="bg_gray">
			<td class="pdg"><a href="javascript:vimmel(\''.secureOUT($row['pic_id']).'\', 692, 625);"><img src="'.IMAGE_DIR.$row['topic_id'].'/'.$row['id'].'-thumb.'.$row['p_pic'].'" height="40"></a></td>
			<td class="pdg cnt"><a href="user.php?id='.$row['id_id'].'"><b>'.secureOUT($row['u_alias']).'</b></a></td>
			<td class="pdg cnt"><b title="Mobilnummer som SMSade">'.secureOUT($row['order_cell']).'</b></td>
			<td class="pdg cnt"><a href="javascript:vimmel(\''.secureOUT($row['pic_id']).'\', 692, 625);">'.secureOUT($row['pic_id']).'</a></td>
			<td class="pdg cnt">'.(($row['is_on'])?'<a href="obj_full_get.php?&all='.$view_full.'&get='.$row['pic_id'].'">'.secureOUT($row['main_id'].$row['rand_id']).'</a>':'AVSTÄNGD').'</td>
			<td class="pdg cnt">'.secureOUT($row['download_times']).'</td>
			<td class="pdg cnt">'.(($row['download_date'] != '0000-00-00 00:00:00')?'hämtad<br>'.niceDate($row['download_date']):((strtotime($row['order_date']) < strtotime("-2 DAYS"))?'<b>stängd</b><br><strike>'.niceDate($row['order_date']).'</strike>':'betald<br>'.niceDate($row['order_date']))).'</td>
			<td class="pdg" align="right"><nobr><a href="'.$thispage.'&del='.$row['main_id'].'&all='.$view_full.'" onclick="return confirm(\'Säker ?\');">'.(($row['is_on'])?'STÄNG NER':'AKTIVERA').'</a></nobr></td>
		</tr>';
		}
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