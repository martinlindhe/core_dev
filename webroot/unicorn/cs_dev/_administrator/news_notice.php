<?
session_start();
#ob_start();
#    ob_implicit_flush(0);
#    ob_start('ob_gzhandler');
	ini_set("max_execution_time", 0);
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	require("./set_tmb.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}
	if(!$isCrew && strpos($_SESSION['u_a'][1], 'news_notice') === false) errorNEW('Ingen behörighet.');
	$page = 'NOTISER';
	$menu = $menu_NEWS;
	$change = false;
	$status_id = '1';
	if(!empty($_GET['status']) && is_numeric($_GET['status'])) {
		$status_id = $_GET['status'];
	} elseif($change) $status_id = $row['status_id'];

	if(!empty($_POST['donews']) && !empty($_POST['ins_cmt'])) {
		$status = (!empty($_POST['status_id']) && is_numeric($_POST['status_id']))?$_POST['status_id']:'0';
		if(!empty($_POST['id']) && is_numeric($_POST['id'])) {
			mysql_query("UPDATE s_newsnotice SET
			ad_cmt = '".secureINS($_POST['ins_cmt'])."',
			status_id = '$status',
			city_id = '".secureINS($_POST['ins_city'])."',
			ad_date = '".secureINS($_POST['ins_date'])."'
			WHERE main_id = '".secureINS($_POST['id'])."' LIMIT 1");
			$d_id = $_POST['id'];

		} else {
			mysql_query("INSERT INTO s_newsnotice SET
			ad_cmt = '".secureINS($_POST['ins_cmt'])."',
			city_id = '".secureINS($_POST['ins_city'])."',
			ad_date = NOW(),
			status_id = '$status'");
			$d_id = mysql_insert_id();
		}
		header("Location: news_notice.php?status=$status");
		exit;
	}

	if(!empty($_POST['doupd'])) {
		foreach($_POST as $key => $val) {
			if(strpos($key, 'status_id') !== false) {
				$kid = explode(":", $key);
				$kid = $kid[1];
				if(isset($_POST['status_id:' . $kid])) {
					mysql_query("UPDATE s_newsnotice SET status_id = '".secureINS($_POST['status_id:' . $kid])."' WHERE main_id = '".secureINS($kid)."' LIMIT 1");
				}
			}
		}
		header("Location: news_notice.php?status=$status_id");
		exit;
	}
	$change = false;
	if(!empty($_GET['del']) && is_numeric($_GET['del'])) {
		$sql = mysql_query("SELECT * FROM s_newsnotice WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
		if(mysql_num_rows($sql) > 0) {
			$row = mysql_fetch_assoc($sql);
			mysql_query("DELETE FROM s_newsnotice WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
			
		}
		header("Location: news_notice.php?status=$status_id");
		exit;
	}

	if(!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$sql = mysql_query("SELECT * FROM s_newsnotice WHERE main_id = '".secureINS($_GET['id'])."' LIMIT 1");
		if(mysql_num_rows($sql) != '1') {
			$change = false;
		} else {
			$row = mysql_fetch_assoc($sql);
			$change = true;
		}
	}

			$view_arr = array(
				"1" => mysql_result(mysql_query("SELECT COUNT(*) as count FROM s_newsnotice WHERE status_id = '1'"), 0, 'count'),
				"2" => mysql_result(mysql_query("SELECT COUNT(*) as count FROM s_newsnotice WHERE status_id = '2'"), 0, 'count'));

	if($status_id != '2') {
		$news = mysql_query("SELECT * FROM s_newsnotice WHERE status_id = '$status_id' ORDER BY city_id ASC, ad_date DESC");
	} else {
		$news = mysql_query("SELECT * FROM s_newsnotice WHERE status_id = '2' ORDER BY ad_date DESC");
	}

	require("./_tpl/admin_head.php");
?>
	<script type="text/javascript" src="fnc_adm.js"></script>
	<table height="100%">
	<tr><td colspan="2" height="25" class="nobr"><?makeMenuAdmin($page, $menu, 0);?></td></tr>
	<tr>
		<td width="50%" style="padding: 0 10px 0 0;">
			<form name="news" method="post" action="./news_notice.php?status=<?=$status_id?>">
			<input type="hidden" name="donews" value="1">
<?=($change)?'<input type="hidden" name="id" value="'.$row['main_id'].'">':'';?>
			<input type="hidden" name="status_id" id="status_id:X" value="<?=($change)?$row['status_id']:'2';?>">
			<table width="100%">
			<tr>
				<td colspan="2" style="padding: 5px 0 0 0;">Text<textarea name="ins_cmt" class="inp_nrm" tabindex="2" style="width: 100%; height: <?=($change && $row['ad_cmt'])?'300':'100';?>px;"><?=($change)?secureOUT($row['ad_cmt']):'';?></textarea></td>
			</tr>
			<tr>	<td colspan="2">
Stad<br /><select name="ins_city" size="1" class="inp_nrm" style="width: 180px;">
<?
	foreach($cities as $key => $val) {
		$select = ($change && $row['city_id'] == $key)?' selected':'';
		echo '<option value="'.$key.'"'.$select.'>'.$val.'</option>';
	}
?>
			</select></td></tr>
<?
	if($change) {
?>
			<tr>
				<td colspan="2" style="padding: 5px 0 0 0;">Datum<br><input type="text" name="ins_date" class="inp_nrm" value="<?=($change)?secureOUT($row['ad_date']):'';?>"></td>
			</tr>
<?
	}
?>
			<tr><td><img src="./_img/status_<?=($change && $row['status_id'] == '1')?'green':'none';?>.gif" style="margin: 0 1px 2px 0;" id="1:X" onclick="changeStatus('status', this.id);"><img src="./_img/status_<?=(($change && $row['status_id'] != '1') || !$change)?'red':'none';?>.gif" style="margin: 0 0 2px 1px;" id="2:X" onclick="changeStatus('status', this.id);"></td><td align="right"><input type="submit" class="inp_realbtn" tabindex="3" value="Uppdatera" style="width: 80px; margin: 5px 0 0 10px;"></td></tr>
			</table>
			</form>
		</td>
		<td style="padding: 0 0 0 10px; background: url('_img/brd_h.gif'); background-repeat: repeat-y;">
					<form action="news_notice.php" method="post">
					<input type="hidden" name="doupd" value="1">
			<input type="radio" class="inp_chk" value="1" id="view_1" onclick="document.location.href = 'news_notice.php?status=' + this.value;"<?=($status_id == '1')?' checked':'';?>><label for="view_1" class="txt_bld txt_look">Exponerade</label> [<?=$view_arr[1]?>]
			<input type="radio" class="inp_chk" value="2" id="view_2" onclick="document.location.href = 'news_notice.php?status=' + this.value;"<?=($status_id == '2')?' checked':'';?>><label for="view_2" class="txt_bld txt_look">Dolda</label> [<?=$view_arr[2]?>]
					<br><input type="submit" class="inp_realbtn" value="Uppdatera" style="width: 70px; margin: 11px 2px 0 0;">
					<table style="margin: 5px 0 10px 0; width: 545px;">
<?
	$nl = true;
	$ol = 0;
	$old = '';
	while($row = mysql_fetch_assoc($news)) {
		echo '<tr><td style="padding: 5px 4px 10px 1px;"><hr /><div class="hr"></div></td></tr><tr><td style="padding: 5px 1px 5px 1px;">';
		echo '<input type="hidden" name="status_id:'.$row['main_id'].'" id="status_id:'.$row['main_id'].'" value="'.$row['status_id'].'">';
		echo '<img src="./_img/status_'.(($row['status_id'] == '1')?'green':'none').'.gif" style="margin: 2px 1px 0 0;" id="1:'.$row['main_id'].'" onclick="changeStatus(\'status\', this.id);"><img src="./_img/status_'.(($row['status_id'] == '2')?'red':'none').'.gif" style="margin: 2px 0 0 1px;" id="2:'.$row['main_id'].'" onclick="changeStatus(\'status\', this.id);"> | <a href="news_notice.php?id='.$row['main_id'].'&status='.$status_id.'">ÄNDRA</a> | <a href="news_notice.php?del='.$row['main_id'].'" onclick="if(confirm(\'Säker ?\')) { return true; } else { return false; }">RADERA</a><br>';
		echo 'Publicerad: <b>'.niceDate($row['ad_date']).'</b> - '.$cities[$row['city_id']];
		echo ($row['ad_cmt'])?'<p>'.nl2br(stripslashes($row['ad_cmt'])).'</p>':'';
		echo '</td></tr>';
	}
?>
					</table>
					</form>
		</td>
	</tr>
	</table>
</body>
</html>