<?
	if($l) reloadACT(l('main', 'start'));
	$topic = 'register';
	$complete = false;
	$error = array();
	$msg = array();
	$key = @intval($_GET['key']);
	if(!empty($_POST['key'])) $key = $_POST['key'];
	$id = @$_GET['id'];
	if(strpos($id, '__at__') !== false) {
		$id = @explode('__at__', $id);
		$lastar = @$id[count($id)-1];
		unset($id[count($id)-1]);
		$id = @implode('__at__', $id);
		$fid = $id.'__at__'.$lastar;
		$id = $id.'@'.$lastar;
	} elseif(strpos($id, '@') !== false) {
		reloadACT(l('member', 'activate', str_replace('@', '__at__', $id), $key));
	}
	if(!empty($_POST['id'])) {
		$id = @$_POST['id'];
		if(strpos($id, '__at__') !== false) {
			$id = @explode('__at__', $id);
			$lastar = @$id[count($id)-1];
			unset($id[count($id)-1]);
			$id = @implode('__at__', $id);
			$fid = $id.'__at__'.$lastar;
			$id = $id.'@'.$lastar;
		} elseif(strpos($id, '@') !== false) {
			reloadACT(l('member', 'activate', str_replace('@', '__at__', $id), $key));
		}
	}

	if(!empty($_POST['go']) && !empty($id) && !empty($_POST['key'])) {
		reloadACT(l('member', 'activate', $id, $_POST['key']));
	} else {
		if(!empty($key)) {
		if(empty($id) || !valiField($id, 'email')) {
			$error['email'] = true;
			$msg[] = 'Du måste skriva en godkänd e-postadress.';
		}
		if(empty($error['email'])) {
			$res = $sql->queryLine("SELECT status_id, id_id, u_email, u_sex, u_alias FROM {$t}user WHERE u_email = '".secureINS(trim($id))."' AND status_id != '2' LIMIT 1");
			if(!empty($key) && !is_numeric($key)) {
				$error['key'] = true;
				$msg[] = 'Felaktig aktiveringskod.';
			} elseif($res[0] == 'F') {
				$res[5] = $sql->queryResult("SELECT reg_code FROM {$t}userinfo WHERE id_id = '".$res[1]."' LIMIT 1");
				if(empty($key) || !is_numeric($key) || empty($res[5]) || $res[5] != $key) {
					$error['key'] = true;
					$msg[] = 'Felaktig aktiveringskod.';
				} else $complete = true;
			} elseif(empty($res[0])) {
				$error['main'] = true;
				$error['email'] = true;
				$error['key'] = true;
				$msg[] = 'Felaktig e-postadress eller aktiveringskod.';
			} else {
				errorACT('Aktiveringen är redan slutförd.', './');
			}
		}
		}
	}
	if($complete && !empty($res[1])) {
		$id_u = $res[1];
		$sex = $res[3];
		require("part3.php");
		exit;
	}
	require(DESIGN.'head.php');
?>
		<div class="bigHeader">Aktivera konto</div>
		<div class="bigBody">
			<form name="l" method="post" action="<?=l('member', 'activate')?>">
			<input type="hidden" name="activate">
			<input type="hidden" name="go" value="1">
<p class="bld">steg 2 av 3</p>
<p><?=safeOUT(gettxt('register-part2'))?></p>
<table cellspacing="0" style="margin-bottom: 10px;" class="mrg">
<tr>
	<td><span class="bld<?=(isset($error['email']))?'_red':'';?>">e-post</span><br /><input type="text" class="txt" name="id" value="<?=(!empty($id))?secureOUT($id):'';?>" /></td>
	<td style="padding-left: 15px;"><span class="bld<?=(isset($error['key']))?'_red':'';?>">aktiveringskod</span><br /><input type="text" class="txt" name="key" value="<?=(!empty($key))?secureOUT($key):'';?>" /></td>
</tr>
<tr>
	<td colspan="2"><br /><?=(!empty($msg) && count($msg))?'<span class="bld">OBS!</span><br />'.implode('<br />', $msg):'';?></td>
</tr>
</table>
	<input type="submit" class="btn2_min r" value="nästa" /><br class="clr" />
			</form>
		</div>
<?
	include(DESIGN.'foot.php');
?>