<?
	if(!function_exists('notallowed') || notallowed()) {
		header("Location: ./");
		exit;
	}
	$thispage = 'obj.php?status=ue';
	$view_full = 0;
	if(!empty($_GET['all'])) {
		$view_full = intval($_GET['all']);
	}

	if(!empty($_POST['validate'])) {
		$doall = false;
		if(!empty($_POST['main_id:all']) && is_numeric($_POST['main_id:all'])) {
			$doall = true;
		}
		foreach($_POST as $key => $val) {
			if(strpos($key, 'status_id') !== false) {
				$kid = explode(':', $key);
				$kid = $kid[1];
				if(isset($_POST['status_id:' . $kid])) {
					if($view_full == '0' && $doall) {
						if(!empty($_POST['status_id:' . $kid])) {
// alla-knapp, men denna är markerad innan
							$thisstatus = $_POST['status_id:' . $kid];
							$sql->queryUpdate("UPDATE {$tab['user']}photomms SET view_id = '".$_POST['status_id:' . $kid]."' WHERE main_id = '".$kid."' LIMIT 1");
						} else {
// alla-knapp
							$thisstatus = $_POST['main_id:all'];
							$sql->queryUpdate("UPDATE {$tab['user']}photomms SET view_id = '".$_POST['main_id:all']."' WHERE main_id = '".$kid."' LIMIT 1");
						}
					} else {
						$thisstatus = $_POST['status_id:' . $kid];
						$sql->queryUpdate("UPDATE {$tab['user']}photomms SET view_id = '".$_POST['status_id:' . $kid]."' WHERE main_id = '".$kid."' LIMIT 1");
					}
					$ostatus = $_POST['otatus_id:' . $kid];
					$oblock = $_POST['blocked_id:' . $kid];
					if($thisstatus == '2' && $ostatus != '2') {
						$unique = md5(microtime().mt_rand(1, 23244));
						@rename('.'.UE_DIR.$kid.$_POST['file_id:' . $kid], '.'.UE_DIR.$kid.'-'.$unique.$_POST['file_id:' . $kid]);
						$sql->queryUpdate("UPDATE {$t}userphotomms SET blocked_id = '".$unique."' WHERE main_id = '".$kid."' LIMIT 1");
					} elseif($ostatus == '2' && $thisstatus != '2') {
						@rename('.'.UE_DIR.$kid.'-'.$oblock.$_POST['file_id:' . $kid], '.'.UE_DIR.$kid.$_POST['file_id:' . $kid]);
						$sql->queryUpdate("UPDATE {$t}userphotomms SET blocked_id = '' WHERE main_id = '".$kid."' LIMIT 1");
					}
				}
			}
		}
		header('Location: '.$thispage.'&all='.$view_cmt);
		exit;
	} elseif(!empty($_GET['del'])) {
		if($view_full == '2') {
			$res = $sql->queryLine("SELECT main_id, recieve_file, blocked_id FROM {$t}userphotomms WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
			if(!empty($res) && count($res)) {
				@unlink(ADMIN_UE_DIR.$res[0].'-'.$res[2].$res[1]);
				$sql->queryUpdate("DELETE FROM {$t}userphotomms WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
			}
		} else
			$sql->queryUpdate("UPDATE {$t}userphotomms SET view_id = '2' WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
		header("Location: ".$thispage);
		exit;
	}
	require("sms_ue231fetch.php");
	$full_arr = array(
"0" => mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}userphotomms a INNER JOIN {$t}user u ON u.id_id = a.id_id AND u.status_id = '1' WHERE a.view_id = '0'"), 0, 'count'),
"1" => mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}userphotomms a INNER JOIN {$t}user u ON u.id_id = a.id_id AND u.status_id = '1' WHERE a.view_id = '1'"), 0, 'count'),
"2" => mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}userphotomms a INNER JOIN {$t}user u ON u.id_id = a.id_id AND u.status_id = '1' WHERE a.view_id = '2'"), 0, 'count'));

	if($view_full == '1') {
		$paging = paging(@$_GET['p'], 20);
		$list = $sql->query("SELECT a.*, u.u_alias FROM {$t}userphotomms a INNER JOIN {$t}user u ON u.id_id = a.id_id AND u.status_id = '1' WHERE a.view_id = '1' ORDER BY a.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
	} elseif($view_full == '2') {
		$list = $sql->query("SELECT a.*, u.u_alias FROM {$t}userphotomms a INNER JOIN {$t}user u ON u.id_id = a.id_id WHERE a.view_id = '2' ORDER BY a.main_id DESC", 0, 1);
	} else {
		$list = $sql->query("SELECT a.*, u.u_alias FROM {$t}userphotomms a INNER JOIN {$t}user u ON u.id_id = a.id_id WHERE a.view_id = '0' ORDER BY a.main_id DESC", 0, 1);
	}
	require("./_tpl/obj_head.php");
?>
			<input type="radio" class="inp_chk" name="view" value="0" id="view_0" onclick="document.location.href = '<?=$thispage?>';"<?=(!$view_full)?' checked':'';?>><label for="view_0" class="txt_bld txt_look">Ogranskade</label> [<?=$full_arr[0]?>]
			<input type="radio" class="inp_chk" name="view" value="1" id="view_1" onclick="document.location.href = '<?=$thispage?>&all=1';"<?=($view_full == '1')?' checked':'';?>><label for="view_1" class="txt_bld txt_look">Godkända</label> [<?=$full_arr[1]?>]
			<input type="radio" class="inp_chk" name="view" value="2" id="view_2" onclick="document.location.href = '<?=$thispage?>&all=2';"<?=($view_full == '2')?' checked':'';?>><label for="view_2" class="txt_bld txt_look">Nekade</label> [<?=$full_arr[2]?>]

			<form name="upd" method="post" action="./<?=$thispage?>">
			<input type="hidden" name="main_id:all" id="main_id" value="0">
			<input type="hidden" name="validate" value="1">

			<input type="submit" class="inp_realbtn" value="Uppdatera" style="width: 70px; margin: 5px 2px 10px 0;">
			<input type="button" class="inp_realbtn" value="Neka blanka" style="width: 85px; margin: 5px 2px 10px 0;" onclick="document.getElementById('main_id').value = '2'; this.form.submit();">
			<input type="button" class="inp_realbtn" value="Godkänn blanka" style="width: 100px; margin: 5px 2px 10px 0;" onclick="document.getElementById('main_id').value = '1'; this.form.submit();">
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
		echo '<tr><th>Status</th><th>Bild</th><th>E-post</th><th>Datum</th></tr>';
		foreach($list as $row) {
		echo '<tr class="bg_gray">
			<td class="pdg cnt mid">
<input type="hidden" name="status_id:'.$row['main_id'].'" id="status_id:'.$row['main_id'].'" value="'.$row['view_id'].'">
<input type="hidden" name="file_id:'.$row['main_id'].'" value="'.$row['recieve_file'].'">
<input type="hidden" name="otatus_id:'.$row['main_id'].'" value="'.$row['view_id'].'">
<input type="hidden" name="blocked_id:'.$row['main_id'].'" value="'.$row['blocked_id'].'">
<img src="./_img/status_'.(($row['view_id'] == '1')?'green':'none').'.gif" style="margin: 0 1px -1px 2px;" id="1:'.$row['main_id'].'" onclick="changeStatus(\'status\', this.id);"><img src="./_img/status_'.(($row['view_id'] == '2')?'red':'none').'.gif" style="margin: 0 8px -1px 1px;" id="2:'.$row['main_id'].'" onclick="changeStatus(\'status\', this.id);"></td>
			<td class="pdg"><a href="'.ADMIN_UE_DIR.$row['main_id'].(($row['view_id'] == '2')?'-'.$row['blocked_id']:'').$row['recieve_file'].'"><img src="'.ADMIN_UE_DIR.$row['main_id'].(($row['view_id'] == '2')?'-'.$row['blocked_id']:'').$row['recieve_file'].'" height="40"></a></td>
			<td class="pdg cnt"><a href="user.php?t&id='.$row['id_id'].'"><b>'.secureOUT($row['u_alias']).'</b></a></td>
			<td class="pdg cnt"><b>'.secureOUT($row['recieve_sender']).'</b></td>
			<td class="pdg cnt">'.niceDate($row['recieve_date']).'</td>
			<td class="pdg" align="right"><nobr><a href="'.$thispage.'&del='.$row['main_id'].'&all='.$view_full.'" onclick="return confirm(\'Proceed ?\');">DELETE</a></nobr></td>
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