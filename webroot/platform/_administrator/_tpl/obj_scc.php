<?
	if(!function_exists('notallowed') || notallowed()) {
		header("Location: ./");
		exit;
	}
	$thispage = 'obj.php?status=scc';
	$view_gb = 0;
	$city = array();
	if(!$isCrew && !empty($_SESSION['u_a'][0])) {
		$city = explode(',', $_SESSION['u_a'][0]);
	}
	if(!empty($_GET['del']) && is_numeric($_GET['del'])) {
		$sql->queryUpdate("UPDATE {$t}contribute SET status_id = '2', con_onday = '' WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
		header("Location: ".$thispage);
		exit;
	}

/*
	if(!empty($_POST['validate'])) {

		$doall = false;
		if(!empty($_POST['main_id:all']) && is_numeric($_POST['main_id:all'])) {
			$doall = true;
		}
		foreach($_POST as $key => $val) {
			if(strpos($key, 'status_id') !== false) {
				$kid = explode(":", $key);
				$kid = $kid[1];
				if(isset($_POST['status_id:' . $kid])) {
					mysql_query("UPDATE {$t}thought SET status_id = '".secureINS($_POST['status_id:' . $kid])."' WHERE main_id = '".secureINS($kid)."' LIMIT 1");
				}
			}
		}
		if($doall) mysql_query("UPDATE {$t}thought SET status_id = '".secureINS($_POST['main_id:all'])."', view_id = '1' WHERE view_id = '0'");
		header("Location: obj.php?status=4");
		exit;
	}
*/

	if(!empty($_GET['all']) && is_numeric($_GET['all'])) {
		$view_gb = $_GET['all'];
	}
	if($view_gb == 1) {
		$paging = paging(@$_GET['p'], 20);
		$list = $sql->query("SELECT a.*, u.id_id, u.u_alias, u.u_picd, u.u_picid, u.u_picvalid, u_sex FROM {$t}contribute a LEFT JOIN {$t}user u ON u.id_id = a.con_user AND u.status_id = '1' WHERE a.status_id = '1' AND a.con_onday >= NOW() ORDER BY a.con_onday ASC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
	} elseif($view_gb == 2) {
		$paging = paging(@$_GET['p'], 20);
		$list = $sql->query("SELECT a.*, u.id_id, u.u_alias, u.u_picd, u.u_picid, u.u_picvalid, u_sex FROM {$t}contribute a LEFT JOIN {$t}user u ON u.id_id = a.con_user AND u.status_id = '1' WHERE a.status_id = '1' AND a.con_onday < NOW() ORDER BY a.con_onday DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
	} else { 
		$paging = paging(@$_GET['p'], 20);
		$list = $sql->query("SELECT a.*, u.id_id, u.u_alias, u.u_picd, u.u_picid, u.u_picvalid, u_sex FROM {$t}contribute a LEFT JOIN {$t}user u ON u.id_id = a.con_user AND u.status_id = '1' WHERE a.status_id = '0' ORDER BY a.main_id ASC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
	}
#print_r($list);
#print mysql_error();
	$view_c = array(
'0' => $sql->queryResult("SELECT ".CH." COUNT(*) as count FROM {$t}contribute WHERE status_id = '0'"),
'1' => $sql->queryResult("SELECT ".CH." COUNT(*) as count FROM {$t}contribute WHERE status_id = '1' AND con_onday >= NOW()"),
'2' => $sql->queryResult("SELECT ".CH." COUNT(*) as count FROM {$t}contribute WHERE status_id = '1' AND con_onday < NOW()"));

	require("./_tpl/obj_head.php");
?>
			<input type="radio" class="inp_chk" name="view" value="0" id="view_0" onclick="document.location.href = '<?=$thispage?>';"<?=(!$view_gb)?' checked':'';?>><label for="view_0" class="txt_bld txt_look">Ogranskade</label> [<?=$view_c[0]?>]
			<input type="radio" class="inp_chk" name="view" value="1" id="view_1" onclick="document.location.href = '<?=$thispage?>&all=1';"<?=($view_gb == 1)?' checked':'';?>><label for="view_1" class="txt_bld txt_look">Kommer att publiceras</label> [<?=$view_c[1]?>]
			<input type="radio" class="inp_chk" name="view" value="2" id="view_2" onclick="document.location.href = '<?=$thispage?>&all=2';"<?=($view_gb == 2)?' checked':'';?>><label for="view_2" class="txt_bld txt_look">Har publicerats</label> [<?=$view_c[2]?>]

			<form name="upd" method="post" action="./<?=$thispage?>">
			<input type="hidden" name="main_id:all" id="main_id" value="0">
			<input type="hidden" name="validate" value="1">
<?
	/*if(count($list)) {
			<input type="submit" class="inp_realbtn" value="Uppdatera" style="width: 70px; margin: 10px 2px 10px 0;">
			<input type="button" class="inp_realbtn" value="Neka blanka" style="width: 85px; margin: 10px 2px 10px 0;" onclick="document.getElementById('main_id').value = '2'; this.form.submit();">
	}*/

	if(isset($paging) && ($paging['slimit'] > 1 || $view_c[$view_gb] > $paging['slimit'] + $paging['limit'])) {
?>
					<table width="100%">
					<tr>
						<?=($paging['p'] > 1)?'<td><a href="'.$thispage.'&all='.$view_gb.'" class="txt_look txt_bld">tillbaka</a></td>':'';?>
						<td align="right">
<?
$pm1 = $paging['p'] - 1;
$pp1 = $paging['p'] + 1;
		if($paging['slimit'] > 1) {
			echo '<a href="'.$thispage.'&all='.$view_gb.'&p='.$pm1.'" class="txt_look txt_bld">framåt</a>&nbsp;';
		}
		if( $view_c[$view_gb] > $paging['slimit'] + $paging['limit']) {
			echo '<a href="'.$thispage.'&all='.$view_gb.'&p='.$pp1.'" class="txt_look txt_bld">bakåt</a>&nbsp;';
		}
?>
						</td>
					</tr>
					</table>
<?
	} else echo '<div>&nbsp;</div>';
?>
			<hr /><div class="hr"></div>
<?
	if(count($list)) { 
	echo '			<table style="width: 500px;">';
	foreach($list as $row) {
		$row['con_msg'] = trim($row['con_msg']);
		$class = ($row['status_id'] == '2')?'bg_blk':'';
		$txt = ($row['status_id'] == '2')?' wht':'';
?>

			<tr class="<?=$class.$txt?>"> 
				<td style="padding-bottom: 8px;"><img src="./_img/status_<?=($row['status_id'] == '1')?'green':'none';?>.gif" style="margin: 0 1px -1px 2px;" id="1:<?=$row['main_id']?>" onclick="changeStatus('status', this.id);"><img src="./_img/status_<?=($row['status_id'] == '2')?'red':'none';?>.gif" style="margin: 0 8px -1px 1px;" id="2:<?=$row['main_id']?>" onclick="changeStatus('status', this.id);"><?=($row['status_id'] == '2')?'<span class="bg_blk txt_wht">':'';?>
<?=(!empty($row['id_id'])?'<a href="user.php?t&id='.$row['id_id'].'" class="txt_big user">'.secureOUT($row['u_alias']).'</a>':'[raderad]')?>
 - <em><?=niceDate($row['con_date'])?></em> (#<?=$row['main_id']?>)<?=($row['status_id'] == '2')?'</span>':'';?><?=($row['con_onday'] != '0000-00-00'?' - <b>PUBLICERINGSDATUM: </b> '.specialDate($row['con_onday']):'')?>
</td>
			</tr>
			<tr class="<?=$class.$txt?>">
				<td>
				<table cellspacing="0">
				<tr>
<?=(!empty($row['id_id']))?'					<td style="width: 57px; padding-right: 10px;">'.getadminimg($row['id_id'].$row['u_picid'].$row['u_picd'].$row['u_sex'], $row['u_picvalid']).'</td>':'';?>
					<td style="width: 100%;"><div style="width: 450px; overflow: hidden;">
<?=secureOUT($row['con_msg'])?><br>
					</div></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr class="<?=$class.$txt?>">
				<td style="padding: 8px 0 0 0;">
				<div style="float: right;"><input type="hidden" name="status_id:<?=$row['main_id']?>" id="status_id:<?=$row['main_id']?>" value="<?=$row['status_id']?>"><a href="<?=$thispage?>&del=<?=$row['main_id']?>" onclick="return confirm('Säker ?');">RADERA</a> | <a href="javascript:openWin('obj_scc_publish.php?id=<?=$row['main_id']?>');">ÄNDRA/PUBLICERA</a></div>
				</td>
			</tr>
			<tr><td style="padding: 0 0 10px 0;"><hr /><div class="hr"></div></td></tr>
<?	}
echo '			</table>';
	}
?>
			</form>
<?
	if(isset($paging) && ($paging['slimit'] > 1 || $view_c[$view_gb] > $paging['slimit'] + $paging['limit'])) {
?>
			<table width="100%">
			<tr>
				<?=($paging['p'] > 1)?'<td><a href="'.$thispage.'&all='.$view_gb.'" class="txt_look txt_bld">tillbaka</a></td>':'';?>
				<td align="right" height="20" valign="center">
<?
		if($paging['slimit'] > 1) {
			echo '<a href="'.$thispage.'&all='.$view_gb.'&p='.$pm1.'" class="txt_look txt_bld">framåt</a>&nbsp;';
		}
		if($view_c[$view_gb] > $paging['slimit'] + $paging['limit']) {
			echo '<a href="'.$thispage.'&all='.$view_gb.'&p='.$pp1.'" class="txt_look txt_bld">bakåt</a>&nbsp;';
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