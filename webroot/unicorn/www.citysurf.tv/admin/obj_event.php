<?
	if(!function_exists('notallowed') || notallowed()) {
		header("Location: ./");
		exit;
	}
	$thispage = 'obj.php?status=event';
	$view_full = 0;
	if(!empty($_GET['all'])) {
		$view_full = intval($_GET['all']);
	}
	$full_arr = array(
"0" => mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$tab['news']}event"), 0, 'count'));

	$paging = paging(@$_GET['p'], 300);
	$id = 0;
	if(!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$id = $_GET['id'];
	} elseif(!empty($_GET['del'])) {
		#$sql->queryUpdate("DELETE FROM {$tab['news']}event WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
		$sql->queryUpdate("DELETE FROM s_competitionvisit WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
		@header("Location: ".$thispage.'&id='.$id);
		exit;
	}

	if(!empty($id)) {
		$paging['co'] = $sql->queryResult("SELECT COUNT(*) as count FROM {$tab['news']}event WHERE e_id = '$id'");
		$list = $sql->query("SELECT a.main_id, a.e_name, a.e_cell, a.e_user, a.e_email, a.e_date, u.u_alias, n.ad_name FROM {$tab['news']}event a LEFT JOIN {$tab['user']} u ON u.id_id = a.e_user AND u.status_id = '1' LEFT JOIN {$tab['news']} n ON n.main_id = a.e_id WHERE a.e_id = '".$id."' ORDER BY a.main_id DESC", 0, 1);


	} else {
		$paging['co'] = $sql->queryResult("SELECT COUNT(*) as count FROM s_competitionvisit");
		$list = $sql->query("SELECT a.*, u.u_alias, u.id_id FROM s_competitionvisit a LEFT JOIN s_user u ON u.id_id = a.logged_in ORDER BY a.is_correct DESC, a.answer DESC", 0, 1);
		#		$paging['co'] = $sql->queryResult("SELECT COUNT(*) as count FROM {$tab['news']}event");
		#		$list = $sql->query("SELECT a.main_id, a.e_name, a.e_cell, a.e_user, a.e_email, a.e_date, u.u_alias, n.ad_name FROM {$tab['news']}event a LEFT JOIN {$tab['user']} u ON u.id_id = a.e_user AND u.status_id = '1' LEFT JOIN {$tab['news']} n ON n.main_id = a.e_id ORDER BY a.main_id DESC", 0, 1);
	}
	require("./_tpl/obj_head.php");
$news = $sql->query("SELECT main_id, ad_name FROM {$tab['news']} WHERE ad_type = 'event' ORDER BY main_id DESC");
?>
<!--			<input type="radio" class="inp_chk" name="view" value="0" id="view_0" onclick="document.location.href = '<?=$thispage?>';"<?=(!$view_full)?' checked':'';?>><label for="view_0" class="txt_bld txt_look">Alla</label> [<?=$full_arr[0]?>]-->
<select class="inp_nrm" onchange="document.location.href = '<?=$thispage?>&id=' + this.value;">
<option value="0">Alla</option>
<?
	foreach($news as $p) {
$sel = ($id && $p[0] == $id)?' selected':'';
		echo '<option value="'.$p[0].'"'.$sel.'>'.secureOUT($p[1]).'</option>';
	}
?>
</select>
			<form name="upd" method="post" action="./<?=$thispage?>">
			<input type="hidden" name="main_id:all" id="main_id" value="0">
			<input type="hidden" name="validate" value="1">
<br />Totalt: <b><?=$paging['co']?></b>.<br />
<?=($id)?'			<input type="button" class="inp_realbtn" onclick="window.open(\'news_extract.php?id='.$id.'\');" value="Ladda ner" style="width: 70px; margin: 5px 2px 10px 0;">':'';?>
<!--			<input type="button" class="inp_realbtn" value="Neka blanka" style="width: 85px; margin: 5px 2px 10px 0;" onclick="document.getElementById('main_id').value = '2'; this.form.submit();">
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
	if(empty($id)) {
		echo '<table cellspacing="2">';
		echo '<tr><th>Namn</th><th>Rätt svar</th><th>Utslagsfråga</th><th>Datum</th></tr>';
		$total = 0;
		foreach($list as $row) {
		echo '<tr class="bg_gray">
			<td class="pdg"><a href="user.php?t&id='.$row['id_id'].'">'.secureOUT($row['u_alias']).'</a></td>
			<td class="pdg">'.secureOUT($row['is_correct']).'</td>
			<td class="pdg">'.secureOUT($row['answer']).'</td>
			<td class="pdg cnt">'.niceDate($row['date_cnt']).'</td>
			<td class="pdg cnt"><a href="'.$thispage.'&del='.$row['main_id'].'">RADERA</a></td>
		</tr>';
		}
		echo '</table>';
	} else {
	if(!empty($list) && count($list)) {
		echo '<table cellspacing="2">';
		echo '<tr>'.((!$id)?'<th>Event</th>':'').'<th>Namn</th><th>E-post</th><th>Mobilnummer</th><th>Alias</th><th>Datum</th></tr>';
		$total = 0;
		foreach($list as $row) {
		echo '<tr class="bg_gray">
			'.((!$id)?'<td class="pdg bld">'.secureOUT($row['ad_name']).'</td>':'').'
			<td class="pdg">'.secureOUT(ucwords(strtolower($row['e_name']))).'</td>
			<td class="pdg">'.secureOUT(strtolower($row['e_email'])).'</td>
			<td class="pdg">'.secureOUT($row['e_cell']).'</td>
			<td class="pdg">'.(($row['e_user'])?'<a href="user.php?id='.$row['e_user'].'">'.secureOUT($row['u_alias']).'</a>':'-').'</td>
			<td class="pdg cnt">'.niceDate($row['e_date']).'</td>
			<td class="pdg cnt"><a href="'.$thispage.'&del='.$row['main_id'].'">RADERA</a></td>
		</tr>';
		}
		echo '</table>';
	} }
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