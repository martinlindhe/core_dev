<?
	require_once('find_config.php');
	if (!$isCrew) { errorNEW('Ingen behörighet.'); }

	$page = 'ANVÄNDARE';
	$menu = $menu_LOG;
	$array = array('Oregistrerad', 'Standard', 'Brons', 'Silver', 'Guld');
	$limit = 10;
	$ip_limit = 20;

	$change = false;
	$t_change = false;
	$error = '';

	if(!empty($_POST['do']) && !empty($_POST['s_u'])) {
		$cpass = false;
		if(!empty($_POST['s_p'])) {
			$cpass = true;
		}
		$list = (!empty($_POST['list']))?'1':'0';
		if(!empty($_POST['id']) && is_md5($_POST['id'])) {
			$check = mysql_result(mysql_query("SELECT u_crew FROM s_admin WHERE main_id = '".secureINS($_POST['id'])."' LIMIT 1"), 0, 'u_crew');
			$sql->queryUpdate("UPDATE s_user SET level_id = '10' WHERE id_id = '".$_POST['id']."' LIMIT 1");
			$sql = mysql_query("UPDATE {$tab['admin']} SET
			user_user = '".secureINS($_POST['s_u'])."',
			".($isCrew && !$check?"pos_all = '".implode(',', $_POST['pospos'])."',":'')."
			".(($list && !$check)?"status_id = 'L',":"status_id = '1',")."
			user_name = '".secureINS($_POST['s_n'])."'
			WHERE main_id = '".secureINS($_POST['id'])."'
			LIMIT 1");
			if($sql) {
				if($_POST['id'] == $_SESSION['u_i']) {
					$_SESSION['u_u'] = $_POST['s_u'];
					$_SESSION['u_n'] = $_POST['s_n'];
				}
			} else $_SESSION['err'] = 'wronguser';
		} else {
			$id = $db->getOneRow("SELECT id_id, u_pass FROM s_user WHERE u_alias = '".$db->escape($_POST['s_u'])."' AND status_id = '1' LIMIT 1");
			if (!empty($id)) {
				$db->update("UPDATE s_user SET level_id = '10' WHERE id_id = '".$id['id_id']."' LIMIT 1");
				$db->insert("INSERT INTO s_admin SET
					user_user = '".$db->escape($_POST['s_u'])."',
					user_name = '".$db->escape($_POST['s_n'])."',
					main_id = '".$id['id_id']."',
					user_pass = '".$id['u_pass']."',
					".($isCrew?"pos_all = '".@implode(',', @$_POST['pospos'])."',":'')."
					".(($list)?"status_id = 'L',":"status_id = '1',")."
					u_owner = '".$user->id."'");
			} else {
				errorNEW('Användaren finns inte som medlem på communityn.', 'settings.php');
			}
		}
		header("Location: settings.php");
		die;
	}

	$u_change = false;
	$new = false;
	if (!empty($_GET['c']) && is_numeric($_GET['c'])) {
			$c_row = $db->getOneRow("SELECT * FROM s_admin WHERE main_id = '".$_GET['c']."' LIMIT 1");
			$u_change = true;
	} elseif(!empty($_GET['n'])) {
		$new = true;
	}

	if(!empty($_GET['b']) && is_numeric($_GET['b'])) {
		$db->update("UPDATE s_admin SET status_id = '2' WHERE main_id = '".$db->escape($_GET['b'])."' AND u_crew = '0' LIMIT 1");
		header("Location: settings.php");
		die;
	}
	if(!empty($_GET['b2']) && is_md5($_GET['b2'])) {
		mysql_query("UPDATE s_admin SET status_id = 'Z' WHERE main_id = '".secureINS($_GET['b2'])."' AND u_crew = '0' LIMIT 1");
		header("Location: settings.php");
		exit;
	}
	if (!empty($_GET['a']) && is_numeric($_GET['a'])) {
		$db->update("UPDATE s_admin SET status_id = '1' WHERE main_id = '".$db->escape($_GET['a'])."' AND u_crew = '0' LIMIT 1");
		header("Location: settings.php");
		die;
	}
	if(!empty($_GET['a2']) && is_md5($_GET['a2'])) {
		mysql_query("UPDATE s_admin SET status_id = 'L' WHERE main_id = '".secureINS($_GET['a2'])."' AND u_crew = '0' LIMIT 1");
		header("Location: settings.php");
		exit;
	}
	if (!empty($_GET['d']) && is_numeric($_GET['d'])) {
		$db->delete("DELETE FROM s_admin WHERE main_id = '".$db->escape($_GET['d'])."' AND u_crew = '0' AND (status_id = '0' OR status_id = '2') LIMIT 1");
		header("Location: settings.php");
		die;
	}

	$u_sql = $db->getArray("SELECT a.*, u.user_user AS owner_u FROM s_admin a LEFT JOIN s_admin u ON u.main_id = a.u_owner ORDER BY a.u_crew DESC, a.status_id ASC, a.user_name");

	require('admin_head.php');
?>
<script type="text/javascript">
function changeByKey(e) {
	if(!e) var e=window.event;
	if(e.ctrlKey && e['keyCode'] == 13) document.change.submit();
	if(e['keyCode'] == 120) addText(0);
}
document.onkeydown = changeByKey;
function checkthesame(obj) {
	if(obj.value != '' && document.getElementById('s_p').value != obj.value) {
		obj.className = 'inp_adm not_done';
	} else {
		obj.className = 'inp_adm';
	}
}
<?
	if(!empty($_SESSION['err'])) {
		$err = str_replace("wronguser", "Felaktigt fält: Alias", $_SESSION['err']);
		echo '
alert("'.$err.'");
document.location.href = \'settings.php\';
';
		unset($_SESSION['err']);
	}
?>
var ptimes = 0;
function checkPhoto() {
	if(ptimes < 1) {
		if(confirm("För att lägga in en bild, skriv in filnamnet som står med blå text i EXTRA, mellan [bild] och [/bild]\n\nVill du göra den till en länk, skriv då [bild=http://www.adress.com] istället för [bild]")) {
			ptimes++;
			return true;
		}
	} else return true;
	return false;
}
function loadtop() {
	if(parent.head)
	parent.head.show_active('settings');
}
<?=(isset($_GET['t']))?'loadtop();':'';?>
</script>
	<table width="100%" height="100%">
<?makeMenuAdmin($page, $menu);?>
	<tr>
		<td>
			<table width="400">
			<tr>
				<td height="25" colspan="3"><b><?=($u_change)?'Ändra a':'A';?>nvändare</b> [<a href="settings.php?n=1">Ny användare</a>]</td>
			</tr>
<?
	if($new || $u_change) {
?>
			<tr>
				<td colspan="3" style="height: 0%; padding: 5px 0 10px 0;">
			<form action="<?=$_SERVER['PHP_SELF']?>" method="post" enctype="application/x-www-form-urlencoded">
			<input type="hidden" name="do" value="1">
<?=($u_change)?'<input type="hidden" name="id" value="'.secureOUT($c_row['main_id']).'">':'';?>
			Namn:<br>
			<input type="text" name="s_n" class="inp_adm" value="<?=($u_change)?$c_row['user_name']:'';?>">
			<br>Alias på communityn:<br>
			<input type="text" name="s_u" class="inp_adm" value="<?=($u_change)?$c_row['user_user']:'';?>">
<?
	if(!$u_change || ($u_change && !$c_row['u_crew'])) {
?>
			<br />Användarnivå:<br>
			<input type="radio" class="inp_chk" name="list" value="0" id="list_0"<?=($u_change && $c_row['status_id'] == '1')?' checked':' checked';?>><label for="list_0" class="txt_bld txt_look"><?=$title?>AMS</label><br>
<?
	}
?>
<?
	if($isCrew && (!$u_change || !$c_row['u_crew'])) {
		$pages = getEnumOptions('s_admin', 'pos_all');
?>
			Tillgång:<br>
			<select name="pospos[]" multiple=true style="height: 75px; width: 220px;" class="inp_nrm">
<?
	foreach($pages as $page) {
		$sel = (!empty($c_row['pos_all']) && strpos($c_row['pos_all'], $page) !== false?true:false);
echo '<option value="'.$page.'"'.($sel?' selected':'').'>'.(array_key_exists($page, $anv_txt)?$anv_txt[$page]:$page).'</option>';
	}
?>
			</select>
<?
	}
?>
			<input type="submit" value="Uppdatera" class="inp_realbtn" style="margin: 4px 0 0 95px;">
			</form>
				</td>
			</tr>
			<tr><td colspan="3"><hr /><div class="hr"></div></td></tr>
<?
	}
?>
			<tr><td height="25" colspan="3">Det finns <span class="txt_chead txt_bld"><?=count($u_sql)?></span> användare.</td></tr>
<?
	if (count($u_sql)) {
		echo '<tr><td style="padding: 0 0 10px 0;">';
		$ostat = '';
		foreach ($u_sql as $row) {
			if($row['user_user'] == 'demo2') continue;
			$stat = $row['status_id'];
			if($row['status_id'] == 'Z') $stat = 'L';
			if($stat == 'L' && $ostat != $stat) {
				echo '<tr><td colspan="3" style="padding: 0 0 5px 0;"><br><br><b>LISTAN</b></td></tr>';
				$ostat = $stat;
			}
			$onl = (strtotime($row['u_date']) > strtotime(timeout()))?true:false;
?>
			<tr> 
				<td>
					<?=($row['status_id'] == '2' || $row['status_id'] == 'Z')?'[<b>BLOCKAD</b>] ':'';?>
					<?=($row['u_crew'])?'<b>'.secureOUT($row['user_user']).'</b>':'<a href="settings.php?c='.secureOUT($row['main_id']).'">'.secureOUT($row['user_user']).'</a>';?>
					<?=($row['u_crew'])?' [<b>CREW</b>]':(($row['status_id'] == 'L')?' [<b>LISTAN</b>]':'');?>
				</td>
				<td><?=($onl)?'<b class="txt_chead" title="Online">'.secureOUT($row['user_name']).'</b>':'<span title="Offline">'.secureOUT($row['user_name']).'</span>';?></td>
				<td align="right" style="padding-right: 10px;">
					<?=(!empty($row['owner_u']))?'['.$row['owner_u'].']':'';?>
					<?=(!$row['u_crew'])?' <a href="settings.php?'.(($row['status_id'] == '1' || $row['status_id'] == 'L')?(($row['status_id'] == 'L')?'b2':'b').'='.$row['main_id'].'"'.(($row['main_id'] == $user->id)?' onclick="return (confirm(\'Du vill blockera dig själv!\n\nDu kommer att loggas ut direkt och kommer INTE att kunna logga in igen.\n\nSäker ?\'))?true:false;"':'').'>BLOCKERA':(($row['status_id'] == 'Z')?'a2':'a').'='.$row['main_id'].'">TILLÅT</a> | <a href="settings.php?d='.$row['main_id'].'" onclick="return (confirm(\'Säker ?\'))?true:false;">RADERA').'</a>':'';?>
				</td>
			</tr>
<?
		}
	}
?>
			</table>
<?
	if ($u_change || (!empty($c_row) && $c_row['user_user'] == $_SESSION['data']['u_alias'])) {
		$log = $db->getArray("SELECT * FROM s_adminlog WHERE login_name LIKE '%".$db->escape($c_row['user_user'])."%' ORDER BY login_date DESC");
?>
			<hr /><div class="hr"></div>
			<table style="margin-bottom: 5px;">
			<tr>
				<td height="25" colspan="3" class="bld">Inloggningar för <b><?=$c_row['user_user']?></b></td>
			</tr>
<?
	if (count($log)) {
		foreach ($log as $row) {
			echo '<tr><td style="padding-right: 20px;"><b>'.secureOUT($row['login_name']).'</b>'.((!empty($row['login_pass']))?' med <b>'.secureOUT($row['login_pass']).'</b>':'').'</td><td style="padding-right: 20px;"><a href="gb.php?t&s='.$row['login_ip'].'">'.$row['login_ip'].'</a> | <a href="http://ripe.net/whois?form_type=simple&full_query_string=&searchtext='.$row['login_ip'].'" target="_blank">INFO</a></td><td>'.niceDate($row['login_date']).'</td></tr>';
		}
	} else {
		echo '<tr><td class="cnt">'.secureOUT($c_row['user_user']).' har aldrig loggat in.</td></tr>';
	}
?>
			</table>
<?
	}
?>
		</td>
	</tr>
	</table>
<? require('admin_foot.php'); ?>
