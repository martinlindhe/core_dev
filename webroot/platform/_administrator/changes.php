<?
session_start();
#ob_start();
 #   ob_implicit_flush(0);
  #  ob_start('ob_gzhandler');
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	require("../_config/validate.fnc.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}

	$limit = 10;
	$ip_limit = 20;
	$page = 'LOGG';
	$menu = $menu_LOG;

	$change = false;
	$t_change = false;

	$colours = array("1" => "#CC0000", "2" => "#336699");

	if(!empty($_POST['ins_msg'])) {
		if(!empty($_POST['id']) && is_numeric($_POST['id'])) {
			mysql_query("UPDATE {$t}changes SET chg_text = '".secureINS($_POST['ins_msg'])."' WHERE main_id = '".secureINS($_POST['id'])."' LIMIT 1");
		} else {
			mysql_query("INSERT INTO {$t}changes SET chg_date = NOW(), chg_text = '".secureINS($_POST['ins_msg'])."', chg_all = '".(!$isCrew?'1':(@$_POST['all']?'1':'0'))."', c_type = 'c', user_id = '".secureINS($_SESSION['u_i'])."'");
		}
		if($isCrew && !empty($_POST['mailit']) && !empty($_POST['mailto'])) {
doMail($_POST['mailto'], substr(strip_tags($_POST['ins_msg']), 0, 30), nl2br(stripslashes($_POST['ins_msg'])), 1);
		}
		header("Location: changes.php");
		exit;
	}

	if(!empty($_POST['dotodo'])) {
		if(!empty($_POST['id']) && is_numeric($_POST['id'])) {
			mysql_query("UPDATE {$t}changes SET chg_text = '".secureINS($_POST['t'])."' WHERE main_id = '".secureINS($_POST['id'])."' LIMIT 1");
		} else {
			mysql_query("INSERT INTO {$t}changes SET chg_date = NOW(), chg_text = '".secureINS($_POST['t'])."', c_type = 't'");
		}
		header("Location: changes.php");
		exit;
	}

	if(!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$sql = mysql_query("SELECT * FROM {$t}changes WHERE main_id = '".secureINS($_GET['id'])."' LIMIT 1");
		if(mysql_num_rows($sql) == '1') {
			$change = true;
			$row = mysql_fetch_assoc($sql);
		}
	}

	if(!empty($_GET['t']) && is_numeric($_GET['t'])) {
		$sql = mysql_query("SELECT * FROM {$t}changes WHERE main_id = '".secureINS($_GET['t'])."' LIMIT 1");
		if(mysql_num_rows($sql) == '1') {
			$t_change = true;
			$t_row = mysql_fetch_assoc($sql);
		}
	}

	if(!empty($_GET['t_done']) && is_numeric($_GET['t_done'])) {
		$sql = mysql_query("SELECT * FROM {$t}changes WHERE main_id = '".secureINS($_GET['t_done'])."' LIMIT 1");
		if(mysql_num_rows($sql) == '1') {
			mysql_query("UPDATE {$t}changes SET c_done = '1', chg_date = NOW() WHERE main_id = '".secureINS($_GET['t_done'])."' LIMIT 1");
			header("Location: changes.php");
			exit;
		}
	}

	if(!empty($_GET['t_del']) && is_numeric($_GET['t_del'])) {
		$sql = mysql_query("SELECT * FROM {$t}changes WHERE main_id = '".secureINS($_GET['t_del'])."' LIMIT 1");
		if(mysql_num_rows($sql) == '1') {
			mysql_query("DELETE FROM {$t}changes WHERE main_id = '".secureINS($_GET['t_del'])."' LIMIT 1");
			header("Location: changes.php");
			exit;
		}
	}
	if($isCrew) {
		$sql = mysql_query("SELECT a.*, b.user_name FROM {$t}changes a LEFT JOIN {$t}admin b ON a.user_id = b.main_id WHERE c_type = 'c' ORDER BY chg_date DESC");
		$t_sql = mysql_query("SELECT * FROM {$t}changes WHERE c_type = 't' ORDER BY c_done ASC, chg_date DESC");
		$t_count = mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}changes WHERE c_type = 't' AND c_done = '0'"), 0, 'count');
	} else {
		$sql = mysql_query("SELECT a.*, b.user_name FROM {$t}changes a LEFT JOIN {$t}admin b ON a.user_id = b.main_id WHERE c_type = 'c' AND chg_all = '1' ORDER BY chg_date DESC");
		$t_sql = mysql_query("SELECT * FROM {$t}changes WHERE c_type = 't' AND chg_all = '1' ORDER BY c_done ASC, chg_date DESC");
		$t_count = mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}changes WHERE c_type = 't' AND c_done = '0' AND chg_all = '1'"), 0, 'count');
	}

	require("./_tpl/admin_head.php");
?>
<script type="text/javascript" src="fnc_txt.js"></script>
<script type="text/JavaScript">
function changeByKey(e) {
	if(!e) var e=window.event;
	if(e.ctrlKey && e['keyCode'] == 13) document.change.submit();
	if(e['keyCode'] == 120) addText(0);
}
document.onkeydown = changeByKey;
function loadtop() {
	if(parent.<?=FRS?>head)
	parent.<?=FRS?>head.show_active('changes');
}
<?=(isset($_GET['t']))?'loadtop();':'';?>
</script>
	<table width="100%" height="100%">
	<?makeMenuAdmin($page, $menu);?>
	<tr>
		<td width="50%" style="padding: 0 10px 0 0">
			<form name="change" action="changes.php" method="post">
<?=($change)?'<input type="hidden" name="id" value="'.$row['main_id'].'">':'';?>
			<table width="500">
			<tr><td style="padding: 0 0 10px 0;" align="right"><textarea name="ins_msg" class="inp_nrm" style="width: 100%; height: 70px;"><?=($change)?secureOUT($row['chg_text']):'';?></textarea><br><div style="float: left;"><?=($isCrew?'<input type="checkbox" id="mailit" value="1" name="mailit" onclick="document.getElementById(\'mailinf\').style.display = (this.checked?\'\':\'none\');"><label for="mailit">Maila detta</label><span id="mailinf" style="display: none;"> till: <input type="text" class="inp_nrm" name="mailto" style="width: 280px;" value="'.ADMIN_EMAIL.'"></span>':'')?></div><?=($isCrew?'<input type="checkbox" name="all"'.($change && $row['chg_all']?' checked':'').' value="1" id="all"><label for="all" class="bld">För alla</label> ':'')?><input type="submit" value="Skicka" class="inp_realbtn" style="margin: 4px 0 0 0;"></td></tr>
<?
	while($row = mysql_fetch_assoc($sql)) {
?>

			<tr><td class="pdg_btn"><div style="width: 500px; overflow: hidden;"><?=(!empty($colours[$row['user_id']]))?'<span class="txt_bld" style="color: '.$colours[$row['user_id']].';">'.$row['user_name'].'</span>':'<span class="txt_bld">'.((!$row['user_name'])?'SYSTEM':$row['user_name']).'</span>';?> - <?=($row['chg_all']?'<b>FÖR ALLA</b> - ':' (DOLD) - ')?><a href="changes.php?id=<?=$row['main_id']?>"><span class="txt_bld"><?=strtolower(specialDate($row['chg_date']).' '.date("H:i", strtotime($row['chg_date'])))?></span></a><br /><?=(!empty($colours[$row['user_id']]))?'<span style="color: '.$colours[$row['user_id']].';">'.safeOUT($row['chg_text']).'</span>':safeOUT($row['chg_text']);?><br><a href="changes.php?t_del=<?=$row['main_id']?>" onclick="return (confirm('Säker ?'))?true:false;" style="float: right;">Radera</a></div></td></tr>
<?
	}
?>
			</table>
			</form>
		</td>
<?
	if($isCrew) {
?>
		<td width="50%" style="padding: 0 10px 0 10px; background: url('_img/brd_h.gif'); background-repeat: repeat-y;">
			<form name="todo" method="post" action="./changes.php">
			<input type="hidden" name="dotodo" value="1">
<?=($t_change)?'<input type="hidden" name="id" value="'.$t_row['main_id'].'">':'';?>
			<table width="100%">
			<tr>
				<td height="35"><b>Att göra</b> - (DOLD)<br><input type="text" name="t" class="inp_nrm" value="<?=($t_change)?secureOUT($t_row['chg_text']):'';?>" /></td>
			</tr>
			<tr><td height="25">Det finns <span class="txt_chead txt_bld"><?=$t_count?></span> sak<?=(($t_count != '1')?'er':'')?> att göra.</td></tr>
<?
	if(mysql_num_rows($t_sql) > 0) {
		print '			<tr><td style="padding: 0 0 10px 0;"><hr /><div class="hr"></div></td></tr>';
		while($row = mysql_fetch_assoc($t_sql)) {
?>
			<tr> 
				<td style="padding-bottom: 3px;"><br><?=($row['c_done'])?'<span class=" txt_bld">'.secureOUT($row['chg_text']).'</span> - <em>färdig '.niceDate($row['chg_date']).'</em> - <a href="changes.php?t_del='.$row['main_id'].'">RADERA</a>':'<a href="changes.php?t='.secureOUT($row['main_id']).'"><span>'.secureOUT($row['chg_text']).'</span></a> - <em>adderad '.niceDate($row['chg_date']).'</em> - <a href="changes.php?t_done='.$row['main_id'].'">FÄRDIG</a>';?></td>
			</tr>
<?
		}
	}
?>
		</table>
		</td>
<?
	}
?>
	</tr>
	</table>
</body>
</html>