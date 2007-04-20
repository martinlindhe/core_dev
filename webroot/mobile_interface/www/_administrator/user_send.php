<?
session_start();
ob_start();
    ob_implicit_flush(0);
    ob_start('ob_gzhandler');
	ini_set("max_execution_time", 0);
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	require("./lib_dir.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}
	if(!$isCrew) errorNEW('Ingen behörighet.');
	$page = 'MASSMESS';
	$menu = $menu_NEWS;
	$sql = &new sql();
	$user = &new user($sql);

	$check = 0;
	if(!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$check = $_GET['id'];
	}
	if(!empty($_GET['del']) && is_numeric($_GET['del'])) {
		$sql->queryUpdate("DELETE FROM {$tab['admin_send']} WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
		reloadACT('aMsgInfo.php');
	}
	$imgdir = './nyheter';
	$u_list = gettxt('admin_sendlist');
	if($u_list) {
		$f_list = array();
		$u_list = explode("\n", $u_list);
		foreach($u_list as $val) {
			$u_id = $sql->queryResult("SELECT id_id FROM {$tab['user']} WHERE u_alias = '".trim($val)."' AND status_id = '1' LIMIT 1");
			if($u_id) {
				$f_list[$u_id] = $val;
			}
		}
	}
	$dir = getDirList('.'.$imgdir);
sort($dir['files']);
$send_copy = array(
'0aa9d5754dc1d4ec9e47a9cd7661138a',
'857a398e20e4182211b8f0d1f3fe902f'
);
$to_type = array(
	'100' => 'ALLA',
	'40' => 'UTAN BILD',
	'1' => 'STANDARD',
	'3' => 'BRONS',
	'5' => 'SILVER',
	'6' => 'GULD',
	'7' => 'STAFF',
	'10' => 'ADMIN');
$to_type_str = array(
	'100' => "1 = 1",
	'40' => "u.u_picvalid != '1'",
	'1' => "u.level_id = '1'",
	'3' => "u.level_id = '3'",
	'5' => "u.level_id = '5'",
	'6' => "u.level_id = '6'",
	'7' => "u.level_id = '7'",
	'10' => "u.level_id = '10'");

$to_sex = array(
	'M' => 'Killar',
	'F' => 'Tjejer');
$to_sex_str = array(
	'M' => "u.u_sex = 'M'",
	'F' => "u.u_sex = 'F'");

	if(!empty($_POST['dosend'])) {
# från
		$msg = '';
		$from = '';
		if(!empty($_POST['from'])) {
			if(is_md5($_POST['from'])) {
				$from = $_POST['from'];
			} else {
				$from = $sql->queryResult("SELECT id_id FROM {$tab['user']} WHERE u_alias = '".secureINS($_POST['from'])."' AND status_id = '1' LIMIT 1");
				if(!$from) errorNEW('Felaktig avsändare.', 'user_send.php');
			}
		} else $msg = '-A';
		$msg_str = array();
# nivå
		if(!empty($_POST['to']) && is_md5($_POST['to'])) {
			$msg_str[] = "u.id_id = '".$_POST['to']."'";
		} elseif(!empty($_POST['to']) && array_key_exists($_POST['to'], $to_type_str)) {
			if(!empty($to_type_str[$_POST['to']])) {
				$msg_str[] = $to_type_str[$_POST['to']];
			}
		}
# kön
		if(!empty($_POST['sex']) && array_key_exists($_POST['sex'], $to_sex_str)) {
			if(!empty($to_sex_str[$_POST['sex']])) {
				$msg_str[] = $to_sex_str[$_POST['sex']];
			}
		}
		if(!empty($_POST['ins_city']) && array_key_exists($_POST['ins_city'], $cities)) {
			$msg_str[] = "u.city_id = '".$_POST['ins_city']."'";
		}
# ålder
		if(!empty($_POST['birth']) && is_numeric($_POST['birth'])) {
			$msg_str[] = "YEAR(u_birth) = '".$_POST['birth']."'";
		}
# klar, lägg in vanlig aktiv-check!
		$msg_str[] = "u.status_id = '1'";

		if(count($msg_str)) {
			$msg_str = implode(' AND ', $msg_str);
			#$msg_str = ' AND '.$msg_str;
		} else $msg_str = '';

		if(!empty($_POST['ins_cmt']) || !empty($_POST['img'])) {
			$gotimg = false;
			if(!empty($_POST['img'])) {
				$gotimg = true;
				if(!empty($_POST['link'])) {
					$_POST['ins_cmt'] = '<a href="'.stripslashes($_POST['link']).'"><img src="'.$imgdir.'/'.$_POST['img'].'" style="margin-bottom: 6px;" /></a><div style="padding: 6px;">'.$_POST['ins_cmt'].'</div>';
				} else {
					$_POST['ins_cmt'] = '<img src="'.$imgdir.'/'.$_POST['img'].'" style="margin-bottom: 6px;" /><div style="padding: 6px;">'.$_POST['ins_cmt'].'</div>';
				}
			}
			$c = $sql->query("SELECT u.id_id FROM {$tab['user']} u WHERE $msg_str");
			if(empty($c) || !count($c)) {
				$error = 'Ingen mottagare.';
				$_SESSION['temp_msg'] = $_POST['ins_cmt'];
				/*$sql->queryInsert("INSERT INTO {$tab['admin_send']} SET
				user_id = '$to',
				sender_id = '$from',
				send_link = '".secureINS($_POST['link'])."',
				sent_date = NOW(),
				sent_type = '$type',
				sent_ttl = '".secureINS(substr($ttl, 0, 50))."',
				".(($gotimg)?"send_img = '1',":'')."
				sent_cmt = '".(($gotimg)?secureINS($_POST['img']):secureINS($_POST['ins_cmt']))."'");*/
			} elseif($msg == '-A') {
				foreach($c as $r) {
					$user->spy($r[0], 'MSG', 'MSG', array($_POST['ins_cmt']));
					#$user->sendMSG('MESS-A', $r[0], $_POST['ins_cmt'], '', 'HTML');
				}
				foreach($send_copy as $c2) $user->spy($c2, 'MSG', 'MSG', array($_POST['ins_cmt']));

				/*$sql->queryInsert("INSERT INTO {$tab['admin_send']} SET
					user_id = '{$_POST['level']}',
					sender_id = '$from',
					send_link = '".secureINS($_POST['link'])."',
					sent_date = NOW(),
					sent_type = '$type',
					sent_ttl = '".secureINS(substr($ttl, 0, 50))."',
					".(($gotimg)?"send_img = '1',":'')."
					sent_cmt = '".(($gotimg)?secureINS($_POST['img']):secureINS($_POST['ins_cmt']))."'");*/
			} else {
				foreach($c as $r) {
					#$user->sendMSG('MESS', $r[0], $_POST['ins_cmt'], $from, 'HTML');
				}
				/*$sql->queryInsert("INSERT INTO {$tab['admin_send']} SET
					user_id = '{$_POST['level']}',
					sender_id = '$from',
					sent_type = '$type',
					sent_ttl = '$priv',
					send_link = '".secureINS($_POST['link'])."',
					sent_date = NOW(),
					".(($gotimg)?"send_img = '1',":'')."
					sent_cmt = '".(($gotimg)?secureINS($_POST['img']):secureINS($_POST['ins_cmt']))."'");*/
			}
			$_SESSION['temp_msg'] = '';
			unset($_SESSION['temp_msg']);
			errorNEW('Meddelandet är skickat till <b>'.count($c).'</b>!', 'user_send.php', 'SKICKAT');
			#errorNEW('Meddelandet är skickat till <b>'.count($c).'</b>st!');
		}
	}

	$error = false;
#	$list = $sql->query("SELECT a.*, u.u_alias FROM {$tab['admin_send']} a LEFT JOIN {$tab['user']} u ON a.sender_id != 'SYS' AND a.sender_id = u.id_id ORDER BY a.sent_date DESC");

	require("./_tpl/admin_head.php");
?>
<script type="text/javascript" src="fnc_adm.js"></script>
<script type="text/javascript" src="fnc_txt.js"></script>
<script type="text/javascript">
function loadtop() {
	if(parent.head)
	parent.head.show_active('user');
}
<?=(isset($_GET['t']))?'loadtop();':'';?>
</script>
<script type="text/javascript">
function fixVal(na) {
	name = na;
	cmt = document.getElementById('ins_cmt');
	span = document.getElementById('spanen');
	if(name == '0') {
		//cmt.style.display = '';
		span.innerHTML = '';
	} else {
		//cmt.style.display = 'none';
		span.innerHTML = '<img src="' + name + '"><br>';
	}
}

function fixSpec(t) {
	t2 = document.getElementById('tr_t2');
	if(!t) {
		t2.style.display = '';
	} else {
		t2.style.display = 'none';
	}
}
</script>

	<table width="100%" height="100%">
<?makeMenu($page, $menu);?>
	<tr>
		<td width="50%" style="padding: 0 10px 0 0">
			<form name="msg_w" method="post" action="user_send.php">
			<input type="hidden" name="dosend" value="1">
			<table cellspacing="0" width="100%">
			<tr>
				<td><b>Från (<a href="settings.php?t&id=admin_sendlist">Lägg till användare</a>):</b><br>
<!--
<?
	$i = 0;
	foreach($f_list as $key => $val) {
	if(!$check && !$i) $checked = ' checked'; else $checked = '';
	$i++;
echo '<input type="radio" name="from" id="u_'.$key.'" value="'.$key.'"'.$checked.'><label for="u_'.$key.'">'.$val.'</label>';
	}
?>-->
				<input name="from" value="0" type="radio" id="sys" checked><label for="sys">BEVAKNING (360-crew)</label>
				</td>
			</tr>
			<tr>
				<td class="nobr"><b>Till nivå/eller user:</b><br>
<?
	$i = 0;
	foreach($f_list as $key => $val) {
	if(!$check && !$i) $checked = ' checked'; else $checked = '';
	$i++;
echo '<input type="radio" name="to" id="ul_'.$key.'" value="'.$key.'"'.$checked.'><label for="ul_'.$key.'">'.$val.'</label>';
	}
	foreach($to_type as $key => $val) echo '<input name="to" value="'.$key.'" type="radio" id="s'.$key.'"><label for="s'.$key.'">'.$val.'</label>';
?>
				</td>
			</tr>
			<tr>
				<td class="nobr"><b>Till kön:</b><br>
<input name="sex" value="0" type="radio" id="sex0" checked><label for="sex0">Alla</label>
<input name="sex" value="M" type="radio" id="sexM"><label for="sexM">Killar</label>
<input name="sex" value="F" type="radio" id="sexF"><label for="sexF">Tjejer</label>
				</td>
			</tr>
			<tr>
				<td><b>Till ålder:</b><br>
<input name="birth" value="" type="text" class="txt">
				</td>
			</tr>
			<tr>	<td>
<b>Till stad</b><br /><select name="ins_city" size="1" class="inp_nrm" style="width: 180px;">
<option value="0">ALLA</option>
<?
	foreach($cities as $key => $val) {
		#$select = ($change && $row['city_id'] == $key)?' selected':'';
		echo '<option value="'.$key.'">'.$val.'</option>';
	}
?>
			</select></td></tr>
			<tr>
				<td><span id="spanen"></span>
<textarea name="ins_cmt" id="ins_cmt" class="inp_nrm" style="width: 100%; height: 150px;"><?=(!empty($_SESSION['temp_msg']))?secureOUT($_SESSION['temp_msg']):'';?></textarea><script type="text/javascript">document.msg_w.ins_cmt.focus();</script><br>
<select name="img" onchange="fixVal(((this.value == '0')?0:'.<?=$imgdir?>/' + this.value));">
<option value="0">Välj en bild ifrån <?=$imgdir?>/</option>
<?
	foreach($dir['files'] as $val) {
		$val = substr(strrchr($val, "/"), 1);
		echo '<option value="'.$val.'">'.$val.'</option>';
	}
?>
</select>
				</td>
			</tr>
			<tr>
				<td><b>Länk (om bild används):</b><br>
<input name="link" value="" type="text" class="txt">
				</td>
			</tr>
			<tr>
				<td>
<input type="submit" value="Skicka" class="btn" style="float: right;"> 
				</td>
			</tr>
			</table>
			</form>
		</td>
		<td width="50%" style="padding: 0 10px 0 10px; background: url('_img/brd_h.gif'); background-repeat: repeat-y;">
		<table cellspacing="1">
<?
/*
		$bg = 0;
		while($row = mysql_fetch_assoc($list)) {
			$bg = ($bg)?0:1;
echo '<tr>
	<td class="r5"><a href="aMsgInfo.php?id='.$row['main_id'].'" class="nrm">'.(($row['sent_type'] == 'msg')?'Mail':'GB-inlägg').' från <b>'.(($row['sender_id'] == 'SYS')?'SYSTEM':$row['u_alias']).'</b> till <b>'.$to_name[$row['user_id']].'</b></a></td>
	<td class="l5" align="right">'.doDate($row['sent_date']).'</td>
	<td class="l5" align="right"><a href="aMsgInfo.php?del='.$row['main_id'].'">radera</a></td>
</tr>';
			if($check == $row['main_id']) {
echo '
<tr>
	<td colspan="3" style="padding: 10px 5px 10px 5px; border: 1px solid #000; border-width: 1px 0 1px 0;">
'.((!empty($row['sent_ttl']) && $row['sent_ttl'] != '1')?'ämne: <b>'.stripslashes($row['sent_ttl']).'</b><br><br>':'').'
'.(($row['send_img'])?'<img src=".'.$imgdir.'/'.stripslashes($row['sent_cmt']).'">':stripslashes($row['sent_cmt'])).'
	</td>
</tr>
';
			}
		}
*/
?>
		</table>
		</td>
	</tr>
	</table>
</body>
</html>