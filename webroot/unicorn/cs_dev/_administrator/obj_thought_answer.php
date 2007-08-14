<?
session_start();
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}

	$limit = 10;
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
error_reporting(E_ALL);


	$edit = true;

	if(empty($_GET['id'])) {
		$error = 'Inget meddelande valt.';
	}
	$sql = mysql_query("SELECT a.*, u.u_alias, d.u_alias as admin_alias FROM {$t}thought a LEFT JOIN {$t}user u ON a.logged_in = u.id_id LEFT JOIN {$t}user d ON a.answer_id = d.id_id WHERE a.main_id = '".secureINS($_GET['id'])."' LIMIT 1");
	if(mysql_num_rows($sql) != '1') {
		$error = 'Meddelandet existerar inte.';
	}
	$row = mysql_fetch_array($sql);

	if(!empty($_POST['dopost']) && is_numeric($_POST['dopost'])) {
			if(!empty($_POST['strlen']) && $_POST['strlen'] > 0) {
			//			p_city = '".secureINS($_POST['city'])."',	
			}
			mysql_query("UPDATE {$t}thought SET
			status_id = '1',
			view_id = '1',
			logged_in = '".secureINS($_POST['id'])."',
			gb_name = '".secureINS($_POST['name'])."',
			gb_email = '".secureINS($_POST['email'])."',
			".((empty($row['answer_id']))?"
			answer_date = NOW(),
			answer_id = '".@secureINS($_SESSION['u_i'])."',
			":"")."
			answer_msg = '".@secureINS($_POST['ans'])."',
			".((empty($_POST['strlen']) || $_POST['strlen'] < 0)?"answer_id = '".secureINS($_SESSION['u_i'])."',":"")."
			gb_msg = '".secureINS($_POST['msg'])."' WHERE main_id = '".secureINS($row['main_id'])."'");
			$edit = false;
			/*if(!empty($_POST['SPY']) && @is_md5($_POST['id'])) {
				$sql = new sql();
				$user = new user($sql);
				$user->spy($_POST['id'], $row['main_id'], 'THO');
			}*/
	}



?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>SVARA | <?=$title?>AMS</title>
	<link rel="stylesheet" href="default_adm.css" type="text/css">
</head>

<body>
<?	if(!$edit) { ?>
<table height="100%" width="100%">
	<tr><td style="padding-bottom: 20px; vertical-align: middle; text-align: center;">Svaret är postat...</td></tr>
</table>
<script type="text/JavaScript">
	window.opener.location.href = 'obj.php?status=thought';
	window.setTimeout('window.close()', 1000)
</script>
</body>
</html>
<?
		exit;
	}
?>
<script type="text/JavaScript">
  function changeByKey(e) {
	if (!e) var e=window.event;

	if (e['keyCode'] == 27) window.close();

	if (e.ctrlKey && e['keyCode'] == 13) document.gb_answer.submit();
  }
document.onkeydown = changeByKey;
</script>
	<form name="gb_answer" method="post" action="obj_thought_answer.php?id=<?=$row['main_id']?>">
	<input type="hidden" name="dopost" value="1">
	<input type="hidden" name="strlen" value="<?=strlen($row['answer_msg'])?>">
	<table width="393" align="center" style="margin: 10px 0 0 0;">
	<tr><td height="25"><b><a href="javascript:self.close();">DISKUTERA</a> - ÄNDRA/SVARA</b></td></tr>
	<tr>
		<td>Namn:</td>
		<td style="padding-left: 10px;">E-post:</td>
	</tr>
	<tr>
		<td><input name="name" class="inp_nrm" style="color: #525252; width: 180px;" value="<?=secureOUT($row['gb_name'])?>"></td>
		<td style="padding-left: 10px;"><input name="email" class="inp_nrm" style="color: #525252; width: 180px;" value="<?=secureOUT($row['gb_email'])?>"></td>
	</tr>
	<tr>
		<td colspan="2">STAD: <b><?=@$cities[$row['p_city']]?></b>:<br><input name="city" class="inp_nrm" style="color: #525252; width: 393px;" value="<?=secureOUT($row['p_city'])?>"></td>
	</tr>
	<tr>
		<td colspan="2">Id (Endast om personen skrev som inloggad):<br><input name="id" class="inp_nrm" style="color: #525252; width: 393px;" value="<?=secureOUT($row['logged_in'])?>"></td>
	</tr>
	<tr>
		<td colspan="2">Meddelande - <?=niceDate($row['gb_date'])?></em> (#<?=$row['main_id']?>):<br><textarea name="msg" class="inp_nrm" style="color: #525252; width: 393px; height: 83px;"><?=secureOUT($row['gb_msg'])?></textarea></td>
	</tr>
	<tr>
		<td colspan="2">Svar:<br><textarea name="ans" class="inp_nrm txt_other" style="width: 393px; height: 83px;"><?=secureOUT($row['answer_msg'])?></textarea><script type="text/javascript">document.gb_answer.ans.focus();</script></td>
	</tr>
	<tr>
		<td colspan="2"><div style="float: left;"><input type="checkbox" class="inp_chk" name="SPY" value="1" id="spy_tell" checked><label for="spy_tell"> [BEVAKNING] Meddela om svar</label></div><div style="float: right;"><input type="submit" onclick="this.form.msg.value = '';" value="Radera svar" class="inp_realbtn" style="margin: 4px 0 0 0;"><input type="submit" value="Skicka" class="inp_realbtn" style="margin: 4px 0 0 10px;"></div></td>
	</tr>
	</table>
	</form>
<? if(!empty($row['admin_tips'])) echo '<script type="text/javascript">alert(\''.str_replace("\r", '', str_replace("\n", '\n', $row['admin_tips'])).'\');</script>'; ?>
</body>
</html>