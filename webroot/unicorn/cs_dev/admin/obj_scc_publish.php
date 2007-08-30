<?
	require_once('find_config.php');

	$limit = 10;

	$edit = true;

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('Inget meddelande valt.');

	$row = $db->getOneRow("SELECT a.*, u.u_alias FROM s_contribute a LEFT JOIN s_user u ON u.id_id = a.con_user WHERE a.main_id = '".$db->escape($_GET['id'])."' LIMIT 1");
	if (!$row) die('Meddelandet existerar inte.');

	$conts = $db->getArray("SELECT con_onday, u_alias, SUBSTRING(con_msg, 1, 20) as con_msg FROM s_contribute a LEFT JOIN s_user u ON u.id_id = a.con_user AND u.status_id = '1' WHERE con_onday >= NOW() AND a.status_id = '1'");
	$arrday = array();
	for ($i = 0; $i < 30; $i++) {
		$arrday[date("Y-m-d", strtotime('+'.$i.' DAYS'))] = true;
	}
	foreach($conts as $con) {
		if(@$arrday[$con['con_onday']] === true) {
			$arrday[$con['con_onday']] = $con['u_alias'].' - '.$con['con_msg'].'...';
		}
	}

	if (!empty($_POST['dopost']) && is_numeric($_POST['dopost'])) {
		$emptied = false;
			if (@$_POST['oldday'] != $_POST['onday']) {
				$check = $db->getOneItem("SELECT main_id FROM s_contribute WHERE status_id = '1' AND con_onday = '".$db->escape($_POST['onday'])."' LIMIT 1");
				if ($check && $check != $_POST['id']) {
					$db->update("UPDATE s_contribute SET status_id = '0', con_onday = '' WHERE main_id = '".$check."' LIMIT 1");
					$emptied = true;
				}
			}
			//publicera!
			$db->update("UPDATE s_contribute SET
			status_id = '1',
			con_onday = '".$db->escape($_POST['onday'])."',
			con_user = '".$db->escape($_POST['id'])."',
			con_msg = '".$db->escape($_POST['msg'])."' WHERE main_id = '".$db->escape($row['main_id'])."'");
			$edit = false;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sv" lang="sv">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>VISDOM | <?=$title?>AMS</title>
	<link rel="stylesheet" href="default_adm.css" type="text/css">
</head>

<body>
<?	if(!$edit) { ?>
<table height="100%" width="100%">
	<tr><td style="padding-bottom: 20px; vertical-align: middle; text-align: center;">Inlägget är postat...<?=($emptied?'<br /><b>OBS! Du bytte ut en existerande som nu lades under "Ogranskade".</b>':'')?></td></tr>
</table>
<script type="text/JavaScript">
	window.opener.location.href = 'obj.php?status=scc';
	window.setTimeout('window.close()', <?=($emptied?'2500':'1000')?>)
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
	<form name="gb_answer" method="post" action="obj_scc_publish.php?id=<?=$row['main_id']?>">
	<input type="hidden" name="dopost" value="1">
	<input type="hidden" name="oldday" value="<?=$row['con_onday']?>">
	<table width="393" align="center" style="margin: 10px 0 0 0;">
	<tr><td height="25"><b><a href="javascript:self.close();">VISDOM</a> - ÄNDRA/PUBLICERA</b></td></tr>
	<tr>
		<td colspan="2">User-ID:<br><input name="id" class="inp_nrm" style="color: #525252; width: 393px;" value="<?=secureOUT($row['con_user'])?>"></td>
	</tr>
	<tr>
		<td colspan="2">Dag att publicera:<br><select name="onday" class="inp_nrm" style="color: #525252; width: 393px;">
<?
	$sel = false;
	foreach($arrday as $key => $arr) {
		if($arr !== true && $row['con_onday'] != $key)
		#	echo '<optgroup label="'.specialDate($key).' - '.$arr.'"></optgroup>';
			echo '<option value="'.$key.'">'.specialDate($key).' - '.$arr.'</option>';
		else if($row['con_onday'] == $key) {
			echo '<option value="'.$key.'" selected>'.specialDate($key).' - '.$arr.'</option>';
			$sel = true;
		} else {
			echo '<option value="'.$key.'"'.(!$sel?' selected':'').'>'.specialDate($key).'</option>';
			$sel = true;
		}
	}
?>
		</select></td>
	</tr>
	<tr>
		<td colspan="2">Meddelande - <?=niceDate($row['con_date'])?></em> (#<?=$row['main_id']?>):<br><textarea name="msg" class="inp_nrm" style="color: #525252; width: 393px; height: 83px;"><?=secureOUT($row['con_msg'])?></textarea></td>
	</tr>
	<tr>
		<td colspan="2"><!--<div style="float: left;"><input type="checkbox" class="inp_chk" name="SPY" value="1" id="spy_tell" checked><label for="spy_tell"> [BEVAKNING] Meddela om svar</label></div>--><div style="float: right;"><!--<input type="submit" onclick="this.form.msg.value = '';" value="Radera svar" class="inp_realbtn" style="margin: 4px 0 0 0;">--><input type="submit" value="Skicka" class="inp_realbtn" style="margin: 4px 0 0 10px;"></div></td>
	</tr>
	</table>
	</form>
</body>
</html>
