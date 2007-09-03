<?
	//detta är TYCK TILL!

	$thispage = 'obj.php?status=thought';
	$view_gb = 0;
	$city = array();

	if(!$isCrew && !empty($_SESSION['u_a'][0])) {
		$city = explode(',', $_SESSION['u_a'][0]);
	}
	if(!empty($_GET['del']) && is_numeric($_GET['del'])) {
		$db->update("UPDATE s_thought SET status_id = '2' WHERE main_id = '".$_GET['del']."' LIMIT 1");
		header("Location: ".$thispage);
		die;
	}

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
					mysql_query("UPDATE s_thought SET status_id = '".secureINS($_POST['status_id:' . $kid])."' WHERE main_id = '".secureINS($kid)."' LIMIT 1");
				}
			}
		}
		if($doall) mysql_query("UPDATE s_thought SET status_id = '".secureINS($_POST['main_id:all'])."', view_id = '1' WHERE view_id = '0'");
		header("Location: obj.php?status=4");
		exit;
	}

	if(!empty($_GET['all'])) {
		$view_gb = 1;
	}
	if(!empty($city) && count($city)) {
		$arr = array();
		foreach($city as $v) {
			$arr[] = "a.p_city = '".$v."'";
		}
		$arr = implode(' OR ', $arr);
	} else $arr = false;
	if($view_gb) {
		$paging = paging(@$_GET['p'], 20);
		if($arr)
			$list = $db->getArray("SELECT a.*, u.id_id, u.u_alias, u.u_picd, u.u_picid, u.u_picvalid, u.u_sex, d.u_alias as admin_alias FROM s_thought a LEFT JOIN s_user u ON a.logged_in = u.id_id LEFT JOIN s_user d ON a.answer_id = d.id_id WHERE $arr ORDER BY a.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}");
		else
			$list = $db->getArray("SELECT a.*, u.id_id, u.u_alias, u.u_picd, u.u_picid, u.u_picvalid, u.u_sex, d.u_alias as admin_alias FROM s_thought a LEFT JOIN s_user u ON a.logged_in = u.id_id LEFT JOIN s_user d ON a.answer_id = d.id_id ORDER BY a.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}");
	} else {
		if($arr)
			$list = $db->getArray("SELECT a.*, u.id_id, u.u_alias, u.u_picd, u.u_picid, u.u_picvalid, u.u_sex, d.u_alias as admin_alias FROM s_thought a LEFT JOIN s_user u ON a.logged_in = u.id_id LEFT JOIN s_user d ON a.answer_id = d.id_id WHERE ($arr) AND a.view_id = '0' AND a.status_id = '0' ORDER BY a.main_id ASC");
		else
			$list = $db->getArray("SELECT a.*, u.id_id, u.u_alias, u.u_picd, u.u_picid, u.u_picvalid, u.u_sex, d.u_alias as admin_alias FROM s_thought a LEFT JOIN s_user u ON a.logged_in = u.id_id LEFT JOIN s_user d ON a.answer_id = d.id_id WHERE a.view_id = '0' AND a.status_id = '0' ORDER BY a.main_id ASC");
	}
	if($arr) {
		$view_c = array(
			'0' => $db->getOneItem("SELECT COUNT(*) FROM s_thought a WHERE ($arr) AND view_id = '0' AND status_id = '0'"),
			'1' => $db->getOneItem("SELECT COUNT(*) FROM s_thought a WHERE $arr")
		);
	} else {
		$view_c = array(
			'0' => $db->getOneItem("SELECT COUNT(*) FROM s_thought WHERE view_id = '0' AND status_id = '0'"),
			'1' => $db->getOneItem("SELECT COUNT(*) FROM s_thought")
		);
	}

	require('obj_head.php');
?>
<? if(!empty($city) && count($city)) foreach($city as $v) echo $cities[$v].' '; echo '<br />'; ?>
			<input type="radio" class="inp_chk" name="view" value="0" id="view_0" onclick="document.location.href = '<?=$thispage?>';"<?=(!$view_gb)?' checked':'';?>><label for="view_0" class="txt_bld txt_look">Ogranskade</label> [<?=$view_c[0]?>]
			<input type="radio" class="inp_chk" name="view" value="1" id="view_1" onclick="document.location.href = '<?=$thispage?>&all=1';"<?=($view_gb)?' checked':'';?>><label for="view_1" class="txt_bld txt_look">Alla</label> [<?=$view_c[1]?>]

			<form name="upd" method="post" action="./<?=$thispage?>">
			<input type="hidden" name="main_id:all" id="main_id" value="0">
			<input type="hidden" name="validate" value="1">
<?
	if(count($list)) {
?>
			<input type="submit" class="inp_realbtn" value="Uppdatera" style="width: 70px; margin: 10px 2px 10px 0;">
			<input type="button" class="inp_realbtn" value="Neka blanka" style="width: 85px; margin: 10px 2px 10px 0;" onclick="document.getElementById('main_id').value = '2'; this.form.submit();">
<?
	}

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
		$row['gb_msg'] = trim($row['gb_msg']);
		$row['answer_msg'] = secureOUT($row['answer_msg']);
		$class = ($row['status_id'] == '2')?'bg_blk':'';
		$txt = ($row['status_id'] == '2')?' wht':'';
?>

			<tr class="<?=$class.$txt?>"> 
				<td style="padding-bottom: 8px;"><img src="./_img/status_<?=($row['status_id'] == '1')?'green':'none';?>.gif" style="margin: 0 1px -1px 2px;" id="1:<?=$row['main_id']?>" onclick="changeStatus('status', this.id);"><img src="./_img/status_<?=($row['status_id'] == '2')?'red':'none';?>.gif" style="margin: 0 8px -1px 1px;" id="2:<?=$row['main_id']?>" onclick="changeStatus('status', this.id);"><?=($row['status_id'] == '2')?'<span class="bg_blk txt_wht">':'';?>
<?=
($row['logged_in'])?
	((!empty($row['id_id']))?'<a href="user.php?t&id='.$row['id_id'].'" class="txt_big user">'.secureOUT($row['u_alias']).'</a>':'[raderad]')
:((!empty($row['c_email']))?
	'<a href="mailto:'.secureOUT($row['c_email']).'"><span class="txt_big">'.secureOUT($row['gb_name']).'</span></a>'
:'<span class="txt_bld txt_big">'.secureOUT($row['gb_name']).'</span>');?>


 - <b><?=@$cities[$row['p_city']]?></b> - <em><?=niceDate($row['gb_date'])?></em> (#<?=$row['main_id']?>)<?=($row['status_id'] == '2')?'</span>':'';?>
</td>
			</tr>
			<tr class="<?=$class.$txt?>">
				<td>
				<table cellspacing="0">
				<tr>
<?=(!empty($row['logged_in']))?'					<td style="width: 57px; padding-right: 10px;">'.getadminimg($row['id_id']).'</td>':'';?>
					<td style="width: 100%;"><div style="width: 450px; overflow: hidden;">
<?=($row['gb_html'])?stripslashes($row['gb_msg']):secureOUT($row['gb_msg']);?><br>
<?	if(!empty($row['answer_msg'])) { ?>
<br><span class="txt_other"><?=$row['answer_msg']?><br><span class="txt_bld"><?=$row['admin_alias']?></span> - <em>inlägg besvarat <?=niceDate($row['answer_date'])?></em></span>
<?
	} elseif($row['gb_html']) echo '<br><b>ADMIN</b>'; else echo '<br><b>OBESVARAT</b>';
?>
					</div></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr class="<?=$class.$txt?>">
				<td style="padding: 8px 0 0 0;" class="nobr">
				<div style="float: right;"><input type="hidden" name="status_id:<?=$row['main_id']?>" id="status_id:<?=$row['main_id']?>" value="<?=$row['status_id']?>"><a href="<?=$thispage?>&del=<?=$row['main_id']?>" onclick="return confirm('Säker ?');">RADERA</a> | <a href="javascript:openWin('obj_thought_answer.php?id=<?=$row['main_id']?>');">ÄNDRA/SVARA</a></div>
				<a href="search.php?s=<?=secureOUT($row['sess_id'])?>"><?=substr(secureOUT($row['sess_id']), 0, 5)?></a> | <a href="search.php?s=<?=secureOUT($row['sess_ip'])?>"><?=secureOUT($row['sess_ip'])?></a>
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
