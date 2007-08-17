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
	if(!$isCrew && strpos($_SESSION['u_a'][1], 'poll') === false) errorNEW('Ingen behörighet.');
	$limit = 10;
	$ip_limit = 20;
	$change = false;
	$menu = $menu_NEWS;
	$page = 'POLL'; 
	if(!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$sql = mysql_query("SELECT * FROM s_poll WHERE main_id = '{$_GET['id']}'");
		if(mysql_num_rows($sql) == '1') {
			$c_row = mysql_fetch_assoc($sql);
			$change = true;
		}
	}
	if(!empty($_GET['deny']) && is_numeric($_GET['deny'])) {
		$editr = mysql_num_rows(mysql_query("SELECT main_id FROM {$tab['pollv']} WHERE main_id = '{$_GET['deny']}' AND is_enabled = '1'"));
		$pollr = mysql_num_rows(mysql_query("SELECT main_id FROM s_poll WHERE main_id = '{$_GET['view']}' AND poll_ans".secureINS($_GET['answer'])." != ''"));
		if($editr == '1' && $pollr == '1') {
			mysql_query("UPDATE s_pollvisit SET is_enabled = '0' WHERE main_id = '{$_GET['deny']}' AND is_enabled = '1'");
			mysql_query("UPDATE s_poll SET poll_res{$_GET['answer']} = poll_res{$_GET['answer']} - 1 WHERE main_id = '{$_GET['view']}' LIMIT 1");
			header("Location: poll.php?view=".$_GET['view'].'&answer='.$_GET['answer']);
			exit;
		}
	}
	if(!empty($_GET['acc']) && is_numeric($_GET['acc'])) {
		$editr = mysql_num_rows(mysql_query("SELECT main_id FROM s_pollvisit WHERE main_id = '{$_GET['acc']}' AND is_enabled = '0'"));
		$pollr = mysql_num_rows(mysql_query("SELECT main_id FROM s_poll WHERE main_id = '{$_GET['view']}' AND poll_ans".secureINS($_GET['answer'])." != ''"));
		if($editr == '1' && $pollr == '1') {
			mysql_query("UPDATE s_pollvisit SET is_enabled = '1' WHERE main_id = '{$_GET['acc']}' AND is_enabled = '0'");
			mysql_query("UPDATE s_poll SET poll_res{$_GET['answer']} = poll_res{$_GET['answer']} + 1 WHERE main_id = '{$_GET['view']}' LIMIT 1");
			header("Location: poll.php?view=".$_GET['view'].'&answer='.$_GET['answer']);
			exit;
		}
	}
	if(!empty($_POST['dopost']) && !empty($_POST['ins_quest'])) {
		if(!empty($_POST['id'])) {
			$csql = mysql_query("SELECT main_id FROM s_poll WHERE main_id = '{$_POST['id']}'");
			if(mysql_num_rows($csql) == '1') {
				$csql = mysql_query("SELECT main_id FROM s_poll WHERE poll_month = '".secureINS($_POST['ins_start'])."' AND main_id != '{$_POST['id']}'");
				if(mysql_num_rows($csql) > 0) {
					header("Location: poll.php?taken=1");
					exit;
				} else {
					mysql_query("UPDATE s_poll SET
						poll_quest = '{$_POST['ins_quest']}',
						poll_text = '{$_POST['ins_text']}',
						poll_month = '{$_POST['ins_month']}',
						poll_ans1 = '{$_POST['ins_ans1']}',
						poll_ans2 = '{$_POST['ins_ans2']}',
						poll_ans3 = '{$_POST['ins_ans3']}',
						poll_ans4 = '{$_POST['ins_ans4']}',
						poll_ans5 = '{$_POST['ins_ans5']}',
						poll_ans6 = '{$_POST['ins_ans6']}'
						WHERE main_id = '{$_POST['id']}'");
					header("Location: poll.php?view=".$_POST['id']."&upd=1");
					exit;
				}
			}
			header("Location: poll.php?error=1");
			exit;
		} else {
			$csql = mysql_query("SELECT main_id FROM s_poll WHERE poll_month = '".secureINS($_POST['ins_month'])."'");
			if(mysql_num_rows($csql) > 0) {
				header("Location: poll.php?taken=1");
				exit;
			} else {
				$sql = mysql_query("INSERT INTO s_poll SET
					poll_quest = '{$_POST['ins_quest']}',
					poll_month = '{$_POST['ins_month']}',
					poll_ans1 = '{$_POST['ins_ans1']}',
					poll_ans2 = '{$_POST['ins_ans2']}',
					poll_ans3 = '{$_POST['ins_ans3']}',
					poll_ans4 = '{$_POST['ins_ans4']}',
					poll_ans5 = '{$_POST['ins_ans5']}',
					poll_ans6 = '{$_POST['ins_ans6']}',
					poll_text = '{$_POST['ins_text']}'");
				$sqlins = mysql_insert_id();
				header("Location: poll.php?view=$sqlins&upd=1");
				exit;
			}
			header("Location: poll.php?error=1");
			exit;
		}
	}

	if(!empty($_GET['taken'])) {
		print '<script type="text/javascript">alert(\'Det finns redan en poll för vald månad.\n\nAntingen tar du bort den som finns, eller ändrar datum på den nya.\'); history.go(-1);</script>';
		exit;
	} elseif(!empty($_GET['error'])) {
		print '<script type="text/javascript">alert(\'Felaktigt val.\'); history.go(-1);</script>';
		exit;
	} elseif(!empty($_GET['del']) && is_numeric($_GET['del'])) {
		$sql = mysql_query("SELECT main_id FROM s_poll WHERE main_id = '{$_GET['del']}' LIMIT 1");
		if(mysql_num_rows($sql) == '1') {
			mysql_query("DELETE FROM s_poll WHERE main_id = '{$_GET['del']}' LIMIT 1");
			mysql_query("DELETE FROM s_pollvisit WHERE category_id = '{$_GET['del']}'");
			header("Location: poll.php");
			exit;
		}
	}

	$sql = mysql_query("SELECT * FROM s_poll ORDER BY poll_month DESC");


	require("./_tpl/admin_head.php");
?>
<script type="text/javascript" src="fnc_txt.js"></script>
<script type="text/JavaScript">
function loadtop() {
	if(parent.head)
	parent.head.show_active('poll');
}
<?=(isset($_GET['t']))?'loadtop();':'';?>
</script>
	<table width="100%" height="100%">
	<tr><td colspan="2" height="25" class="nobr"><?makeMenuAdmin($page, $menu, 0);?></td></tr>
	<tr>
		<td width="50%" style="padding: 0 10px 0 0">
					<form name="poll" method="post" action="./poll.php">
<?=($change)?'<input type="hidden" name="id" value="'.$c_row['main_id'].'">':'';?>
					<input type="hidden" name="dopost" value="1">
					<table style="margin-bottom: 15px; width: 325px;">
					<tr>
						<td colspan="2" style="padding: 0 0 0 0;">
							<table cellspacing="0">
							<tr>
								<td>
								Fråga<br />
								<input type="text" name="ins_quest" class="inp_nrm" value="<?=($change)?secureOUT($c_row['poll_quest']):'';?>" />
								</td>
								<td style="padding-left: 5px;">
								Vecka<br />
								<input type="text" name="ins_month" class="inp_nrm" style="width: 77px;" value="<?=($change)?$c_row['poll_month']:date("Y-W");?>" />
								</td>
							</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="padding: 0 0 0 0;">Text om röstningen:<br><textarea style="width: 300px; height: 60px;" name="ins_text" class="inp_nrm"><?=($change)?secureOUT($c_row['poll_text']):'';?></textarea></td>
					</tr>
					<tr>
						<td style="padding: 15px 0 0 0;">
							Svar 1<br />
							<input type="text" name="ins_ans1" class="inp_nrm" value="<?=($change)?secureOUT($c_row['poll_ans1']):'';?>" />
						</td>
						<td style="padding: 15px 0 0 5px;">
							Svar 2<br />
							<input type="text" name="ins_ans2" class="inp_nrm" value="<?=($change)?secureOUT($c_row['poll_ans2']):'';?>" />
						</td>
					</tr>
					<tr>
						<td style="padding: 0 0 0 0;">
							Svar 3<br />
							<input type="text" name="ins_ans3" class="inp_nrm" value="<?=($change)?secureOUT($c_row['poll_ans3']):'';?>" />
						</td>
						<td style="padding: 0 0 0 5px;">
							Svar 4<br />
							<input type="text" name="ins_ans4" class="inp_nrm" value="<?=($change)?secureOUT($c_row['poll_ans4']):'';?>" />
						</td>
					</tr>
					<tr>
						<td style="padding: 0 0 0 0;">
							Svar 5<br />
							<input type="text" name="ins_ans5" class="inp_nrm" value="<?=($change)?secureOUT($c_row['poll_ans5']):'';?>" />
						</td>
						<td style="padding: 0 0 0 5px;">
							Svar 6<br />
							<input type="text" name="ins_ans6" class="inp_nrm" value="<?=($change)?secureOUT($c_row['poll_ans6']):'';?>" />
						</td>
					</tr>
					<tr>
						<td colspan="2" align="right"><input type="reset" value="Rensa" class="inp_realbtn" style="margin-top: 4px;"><input type="submit" value="Skicka" class="inp_realbtn" style="margin: 4px 0 0 10px;"></td>
					</tr>
					</table>
					</form>
					<hr /><div class="hr"></div>

					<table cellspacing="0" width="100%">
<?
	$i = 1;
	if(mysql_num_rows($sql) > 0) mysql_data_seek($sql, 0);
		while($row = mysql_fetch_assoc($sql)) {
			$class=(++$i % 2 == 0) ? 'bg2' : 'bg3';
			if($row['poll_month'] == date("Y-W"))
				$extra = ' [<span class="bld txt_chead">PÅ</span>]';
			elseif($row['poll_month'] < date("Y-W"))
				$extra = ' [AV]';
			elseif($row['poll_month'] > date("Y-W"))
				$extra = '';

			print '<tr>
				<td class="'.$class.'"><a href="poll.php?view='.$row['main_id'].'">'.$row['poll_quest'].'</a>'.$extra.'</td>
				<td class="'.$class.'">'.$row['poll_month'].'</td>
				<td align="right" class="'.$class.'"><a href="poll.php?id='.$row['main_id'].'">ÄNDRA</a> | <a href="poll.php?del='.$row['main_id'].'">RADERA</a></td>
				</tr>';
	}

?>
					</table>
		</td>
		<td width="50%" style="padding: 0 10px 0 10px; background: url('_img/brd_h.gif'); background-repeat: repeat-y;">
<?
		$active = false;
		$is_poll = false;
		$pollsql = @mysql_query("SELECT * FROM s_poll WHERE poll_month = '".date("Y-m-00")."' LIMIT 1");
		if(mysql_num_rows($pollsql) > 0) {
			$is_poll = true;
		}
		if($is_poll) {
			$poll = mysql_fetch_assoc($pollsql);
			$active = $poll['main_id'];
			$tot = $poll['poll_res1'] + $poll['poll_res2'] + $poll['poll_res3'] + $poll['poll_res4'];


?>	
			<table cellspacing="0" width="100%">
			<tr>
				<td><?=$poll['poll_quest']?></td>
			</tr>
<?			foreach($poll as $key => $val) {
			$res = 0;
				if(strpos($key, 'poll_ans') !== false && !empty($val)) {
					$ans = substr($key, -1);
					if(!$tot)
						$res = 0;
					else
						$res = round(($poll['poll_res'.substr($key, -1)] / $tot) * 100);
					$resl = round(($res) * 1.5);
?>
			<tr>
				<td style="padding-top: 10px;"><a href="poll.php?view=<?=$poll['main_id']?>&answer=<?=$ans?>"><?=$val?></a> (<?=$res?>% / <?=$poll['poll_res'.substr($key, -1)]?>st)<br><img src="../_img/rlr.gif" height="5" style="margin-top: 1px;" width="<?=$resl?>" /></td>
			</tr>
<?
					if(!empty($_GET['view']) && $_GET['view'] == $poll['main_id'] && !empty($_GET['answer']) && $_GET['answer'] == $ans) {
						$iql = mysql_query("SELECT main_id, sess_ip, date_cnt, is_enabled FROM s_pollvisit WHERE category_id = '{$poll['main_id']}' AND unique_id = '$ans' ORDER BY is_enabled, date_cnt DESC");
						if(mysql_num_rows($iql) > 0) {
						print '<tr><td><table cellspacing="0" style="margin-top: 5px;">';
						while($irow = mysql_fetch_assoc($iql)) {
							$disabled = (!$irow['is_enabled'])?'[<b>SUBTRAHERAD</b>] ':'';
							print '<tr><td style="padding-right: 20px;">'.$disabled.'<span>' . $irow['sess_ip'].'</td><td style="padding-right: 10px;">'.niceDate($irow['date_cnt']).'</span></td><td><a href="poll.php?view='.$poll['main_id'].'&answer='.$_GET['answer'].'&'.(($irow['is_enabled'])?'deny='.$irow['main_id'].'">SUBTRAHERA':'acc='.$irow['main_id'].'">ADDERA').'</a></td></tr>';
						}
						print '</table></td></tr>';
						}
					}
				}
			}
?>
			<tr>
				<td style="padding: 10px 0 0 0;"s>Antal röster: <b><?=$tot?></b></td>
			</tr>
			</table>
<?
		}

	if(!empty($_GET['view']) && $active != $_GET['view']) {
		$is_poll = false;
		$pollsql = @mysql_query("SELECT * FROM s_poll WHERE main_id = '{$_GET['view']}' LIMIT 1");
		if(mysql_num_rows($pollsql) > 0) {
			$is_poll = true;
			echo '<hr /><div class="hr"></div>';
		}
		if($is_poll) {
			while($poll = mysql_fetch_assoc($pollsql)) {
				$tot = $poll['poll_res1'] + $poll['poll_res2'] + $poll['poll_res3'] + $poll['poll_res4'];


?>	
			<table cellspacing="0" width="100%">
			<tr>
				<td style="padding: 10px 0 10px 0;" class="bld">VALD POLL</td>
			</tr>
			<tr>
				<td><?=$poll['poll_quest']?></td>
			</tr>
<?				foreach($poll as $key => $val) {
			$res = 0;
					if(strpos($key, 'poll_ans') !== false && !empty($val)) {
						$ans = substr($key, -1);
						if(!$tot)
							$res = 0;
						else
							$res = round(($poll['poll_res'.substr($key, -1)] / $tot) * 100);
						$resl = round(($res) * 1.5);
?>
			<tr>
				<td style="padding-top: 10px;"><a href="poll.php?view=<?=$poll['main_id']?>&answer=<?=$ans?>"><?=$val?></a> (<?=$res?>% / <?=$poll['poll_res'.substr($key, -1)]?>st)<br><img src="../_img/rlr.gif" height="5" style="margin-top: 1px;" width="<?=$resl?>" /></td>
			</tr>
<?
						if(!empty($_GET['view']) && $_GET['view'] == $poll['main_id'] && !empty($_GET['answer']) && $_GET['answer'] == $ans) {
							$iql = mysql_query("SELECT main_id, sess_ip, date_cnt, is_enabled FROM s_pollvisit WHERE category_id = '{$poll['main_id']}' AND unique_id = '$ans' ORDER BY is_enabled, date_cnt DESC");
							if(mysql_num_rows($iql) > 0) {
							print '<tr><td><table cellspacing="0" style="margin-top: 5px;">';
							while($irow = mysql_fetch_assoc($iql)) {
								$disabled = (!$irow['is_enabled'])?'[<b>SUBTRAHERAD</b>] ':'';
								print '<tr><td style="padding-right: 20px;">'.$disabled.'<span>' . $irow['sess_ip'].'</td><td style="padding-right: 10px;">'.niceDate($irow['date_cnt']).'</span></td><td><a href="poll.php?view='.$poll['main_id'].'&answer='.$_GET['answer'].'&'.(($irow['is_enabled'])?'deny='.$irow['main_id'].'">SUBTRAHERA':'acc='.$irow['main_id'].'">ADDERA').'</a></td></tr>';
							}
							print '</table></td></tr>';
							}
						}

					}
				}
?>
			<tr>
				<td style="padding: 10px 0 0 0;"s>Antal röster: <b><?=$tot?></b></td>
			</tr>
<?
			}
		}
	}
?>
			</table>
		</td>
	</tr>
	</table>
</body>
</html>