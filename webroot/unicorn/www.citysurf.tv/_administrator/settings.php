<?
	session_start();
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}
	if(!$isCrew) { errorNEW('Ingen behörighet.'); }
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
			$check = mysql_result(mysql_query("SELECT u_crew FROM {$t}admin WHERE main_id = '".secureINS($_POST['id'])."' LIMIT 1"), 0, 'u_crew');
			$sql->queryUpdate("UPDATE {$t}user SET level_id = '10' WHERE id_id = '".$_POST['id']."' LIMIT 1");
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
			$id = $sql->queryLine("SELECT id_id, u_pass FROM {$t}user WHERE u_alias = '".$_POST['s_u']."' AND status_id = '1' LIMIT 1");
			if(!empty($id) && count($id)) {
			$sql->queryUpdate("UPDATE {$t}user SET level_id = '10' WHERE id_id = '".$id[0]."' LIMIT 1");
			$sql = mysql_query("INSERT INTO {$t}admin SET
			user_user = '".secureINS($_POST['s_u'])."',
			user_name = '".secureINS($_POST['s_n'])."',
			main_id = '".$id[0]."',
			user_pass = '".$id[1]."',
			".($isCrew?"pos_all = '".@implode(',', @$_POST['pospos'])."',":'')."
			".(($list)?"status_id = 'L',":"status_id = '1',")."
			u_owner = '".secureINS($_SESSION['u_i'])."'");
			} else errorNEW('Användaren finns inte som medlem på communityn.', 'settings.php');
		}
		header("Location: settings.php");
		exit;
	}

	if(isset($_POST['ins_msg'])) {
		if(!empty($_POST['id'])) {
			mysql_query("UPDATE {$t}text SET text_cmt = '".secureINS($_POST['ins_msg'])."', text_date = NOW() WHERE main_id = '".secureINS($_POST['id'])."' LIMIT 1");
		}
		header("Location: settings.php");
		exit;
	}

	if(!empty($_GET['id'])) {
		$sql = mysql_query("SELECT * FROM {$t}text WHERE main_id = '".secureINS($_GET['id'])."' LIMIT 1");
		if(mysql_num_rows($sql) == '1') {
			$change = true;
			$row = mysql_fetch_assoc($sql);
		}
	}
	$u_change = false;
	$new = false;
	if(!empty($_GET['c'])) {
		$c_sql = mysql_query("SELECT * FROM {$t}admin WHERE main_id = '".secureINS($_GET['c'])."' LIMIT 1");
		if(mysql_num_rows($c_sql) > 0) {
			$c_row = mysql_fetch_assoc($c_sql);
			if(!$_SESSION['u_c'] && $c_row['u_crew']) {
				$u_change = false;
			} else {
				$u_change = true;
			}
		}
	} elseif(!empty($_GET['n'])) {
		$new = true;
	}

	if(!empty($_GET['b']) && is_md5($_GET['b'])) {
		mysql_query("UPDATE {$t}admin SET status_id = '2' WHERE main_id = '".secureINS($_GET['b'])."' AND u_crew = '0' LIMIT 1");
		header("Location: settings.php");
		exit;
	}
	if(!empty($_GET['b2']) && is_md5($_GET['b2'])) {
		mysql_query("UPDATE {$t}admin SET status_id = 'Z' WHERE main_id = '".secureINS($_GET['b2'])."' AND u_crew = '0' LIMIT 1");
		header("Location: settings.php");
		exit;
	}
	if(!empty($_GET['a']) && is_md5($_GET['a'])) {
		mysql_query("UPDATE {$t}admin SET status_id = '1' WHERE main_id = '".secureINS($_GET['a'])."' AND u_crew = '0' LIMIT 1");
		header("Location: settings.php");
		exit;
	}
	if(!empty($_GET['a2']) && is_md5($_GET['a2'])) {
		mysql_query("UPDATE {$t}admin SET status_id = 'L' WHERE main_id = '".secureINS($_GET['a2'])."' AND u_crew = '0' LIMIT 1");
		header("Location: settings.php");
		exit;
	}
	if(!empty($_GET['d']) && is_md5($_GET['d'])) {
		mysql_query("DELETE FROM {$t}admin WHERE main_id = '".secureINS($_GET['d'])."' AND u_crew = '0' AND (status_id = '0' OR status_id = '2') LIMIT 1");
		header("Location: settings.php");
		exit;
	}

	$u_sql = mysql_query("SELECT a.*, u.user_user AS owner_u FROM {$t}admin a LEFT JOIN {$t}admin u ON u.main_id = a.u_owner ORDER BY a.u_crew DESC, a.status_id ASC, a.user_name");

	$u_count = mysql_num_rows($u_sql);

	$texts = array(
'about' => 'OM KLUBBEN',
'thought' => 'TYCKA',
'contact' => 'KONTAKT',
'advertise' => 'ANNONSERA',
'cookies' => 'COOKIES',
'disclaimer' => 'DISCLAIMER',
'register-part0' => 'Text innan användarvillkoren',
'register-accept' => 'Användarvillkor',
'register-part1' => 'Text innan registering steg 1',
'register-part2' => 'Text innan registering steg 2',
'register-part3' => 'Text innan registering steg 3',
'disclaimer' => 'DISCLAIMER',
'disclaimer' => 'DISCLAIMER',
'url' => NAME_URL
);
	$faqr = false;
	if(!empty($_GET['faq']) && is_numeric($_GET['faq'])) {
		$sql = mysql_query("SELECT * FROM s_faq WHERE main_id = '".secureINS($_GET['faq'])."' LIMIT 1");
		if(mysql_num_rows($sql) == '1') {
			$faqr = mysql_fetch_assoc($sql);
		}
	}
	if(!empty($_POST['inp_q']) || !empty($_POST['Finp_q'])) {
		if($_POST['type'] == 'F') {
			if(!empty($_POST['Fid']) && is_numeric($_POST['Fid'])) {
				mysql_query("UPDATE s_faq SET item_a = '".secureINS($_POST['Finp_a'])."', item_type = 'F', item_q = '".secureINS($_POST['Finp_q'])."', order_id = '".secureINS($_POST['Forder'])."' WHERE main_id = '".secureINS($_POST['Fid'])."' LIMIT 1");
			} else {
				mysql_query("INSERT INTO s_faq SET item_a = '".secureINS($_POST['Finp_a'])."', item_type = 'F', item_q = '".secureINS($_POST['Finp_q'])."', order_id = '".secureINS($_POST['Forder'])."'");
			}
		} else {
			if(!empty($_POST['id']) && is_numeric($_POST['id'])) {
				mysql_query("UPDATE s_faq SET item_q = '".secureINS($_POST['inp_q'])."', item_type = 'U', item_a = '".serialize($_POST['item_a'])."', order_id = '".secureINS($_POST['order'])."' WHERE main_id = '".secureINS($_POST['id'])."' LIMIT 1");
			} else {
				mysql_query("INSERT INTO s_faq SET item_q = '".secureINS($_POST['inp_q'])."', item_type = 'U', item_a = '".serialize($_POST['item_a'])."', order_id = '".secureINS($_POST['order'])."'");
			}
		}
		header("Location: settings.php#FAQ");
		exit;
	}
	if(!empty($_GET['faqdel']) && is_numeric($_GET['faqdel'])) {
		$sql = mysql_query("SELECT * FROM s_faq WHERE main_id = '".secureINS($_GET['faqdel'])."' LIMIT 1");
		if(mysql_num_rows($sql) == '1') {
			mysql_query("DELETE FROM s_faq WHERE main_id = '".secureINS($_GET['faqdel'])."' LIMIT 1");
			header("Location: settings.php#FAQ");
			exit;
		}
	}


	$sql = mysql_query("SELECT * FROM {$t}text WHERE status_id = '1' ORDER BY main_id");

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
		<td width="50%" style="padding: 0 10px 0 0">
			<form name="change" action="settings.php" method="post">
<?=($change)?'<input type="hidden" name="id" value="'.$row['main_id'].'">':'';?>
			<b>Texter</b><br>
			<table width="100%" cellspacing="0" style="margin-top: 5px;">
			<?=(!empty($row['main_id']))?'
			<tr><td style="padding: 0 0 20px 0;"><b>'.safeOUT($row['main_id']).'</b><br><textarea name="ins_msg" class="inp_nrm" style="width: 100%; height: 120px;">'.secureOUT($row['text_cmt']).'</textarea><br>
<input type="submit" value="Uppdatera" class="inp_realbtn" style="float: right; margin: 4px 0 0 0;">
</td></tr>':'';?>
<?
	while($row = mysql_fetch_assoc($sql)) {
		if($row['main_id'] != 'admcnt') {
/*<br><?=(!empty($colours[$row['main_id']]))?'<span style="color: '.$colours[$row['main_id']].';">'.safeOUT($row['text_cmt']).'</span>':(($row['auto_line'])?doURL(doMailto(safeOUT($row['text_cmt']))):stripslashes(doURL(doMailto($row['text_cmt']))));?>*/
?>

			<tr><td><a href="settings.php?id=<?=$row['main_id']?>"><?=(!empty($texts[$row['main_id']]))?'<span class="bld">'.$row['main_id'].' ('.$texts[$row['main_id']].')</span>':'<span class="bld">'.$row['main_id'].'</span>';?></a> - <em><?=niceDate($row['text_date'])?></em></td></tr>
<?
		}
	}
?>
			</table>
			</form>
<a name="FAQ"></a>
<hr /><div class="hr"></div><br /><br /><a href="settings.php?<?=microtime()?>#FAQ" class="no_lnk"><b>FAQ och UPPGRADERA</b></a> - <a href="?rand=<?=microtime()?>#FAQ">NY</a>
<form action="settings.php" method="post">
<table width="100%">
<tr><td>
	<input type="radio"<?=(@$faqr['item_type'] == 'F')?' checked':'';?> class="inp_chk" name="type" value="F" onclick="if(this.checked) { document.getElementById('tF').style.display = ''; document.getElementById('tU').style.display = 'none'; }" id="iF"><label for="iF"> FAQ</label>
	<input type="radio"<?=(@$faqr['item_type'] == 'U')?' checked':'';?> class="inp_chk" name="type" value="U" onclick="if(this.checked) { document.getElementById('tU').style.display = ''; document.getElementById('tF').style.display = 'none'; }" id="iU"><label for="iU"> Uppgradera</label>
</td></tr>
<?
	if(!@$faqr || @$faqr['item_type'] != 'U') {
?>
<tr id="tF"<?=(@$faqr['item_type'] == 'F')?'':' style="display: none;"';?>><td>
	<table width="100%">
	<tr><td colspan="2">
	<?=($faqr)?'<input type="hidden" name="Fid" value="'.$faqr['main_id'].'" />':'';?>
	<input type="hidden" name="dofaq" value="1" />
	Fråga:<br /><input type="text" name="Finp_q" class="txt" style="width: 378px;" value="<?=@safeOUT($faqr['item_q'])?>" /></td></tr>
	<tr><td colspan="2">Svar:<br /><textarea name="Finp_a" class="inp_nrm" style="height: 110px;"><?=@safeOUT($faqr['item_a'])?></textarea></td></tr>
	<tr><td>Sorteringsnummer:<br /><input type="text" name="Forder" class="txt" value="<?=@safeOUT($faqr['order_id'])?>" /></td><td align="right"><br /><br /><input type="submit" value="Spara" class="btn" /></td></tr>
	<tr><td colspan="2" style="padding: 0 0 10px 0;"><hr /><div class="hr"></div></td></tr>
	</table>
</td></tr>
<?
	}
	if(!@$faqr || @$faqr['item_type'] != 'F') {
?>
<tr id="tU"<?=(@$faqr['item_type'] == 'U')?'':' style="display: none;"';?>><td>
	<table width="100%">
	<tr><td colspan="2">
	<form action="settings.php" method="post">
	<?=($faqr)?'<input type="hidden" name="id" value="'.$faqr['main_id'].'" />':'';?>
	<input type="hidden" name="dofaq" value="1" />
	Namn:<br /><input type="text" name="inp_q" class="txt" style="width: 378px;" value="<?=@safeOUT($faqr['item_q'])?>" /></td></tr>
	<tr><td colspan="2">Värden:<br />
<?
	if($faqr) {
		$arr = @unserialize($faqr['item_a']);

	}

	for($i = 0; $i <= 4; $i++) {
echo '<b>'.$array[$i].'</b>: <input type="text" class="inp_nrm" style="width: 50px;" name="item_a[]" value="'.@secureOUT(@$arr[$i]).'" /><br />';
	}
?>
	</td></tr>
	<tr><td>Sorteringsnummer:<br /><input type="text" name="order" class="txt" value="<?=@safeOUT($faqr['order_id'])?>" /></td><td align="right"><br /><br /><input type="submit" value="Spara" class="btn" /></td></tr>
	<tr><td colspan="2" style="padding: 0 0 10px 0;"><hr /><div class="hr"></div></td></tr>
	</table>
</td></tr>
<?
	}
?>
</table>
</form>
<table width="100%" style="margin: 20px 0 20px 0;">
<?
	$faq = mysql_query("SELECT * FROM s_faq ORDER BY item_type, order_id, main_id");
	$ot = false;
	$types = array('F' => 'FAQ', 'U' => 'Uppgradera');
	while($faqr = mysql_fetch_assoc($faq)) {
		if($ot != $faqr['item_type']) {
echo '<tr><td><br /><hr /><div class="hr"></div><br /><br /><b>'.$types[$faqr['item_type']].'</b></td></tr>';
		}
if($faqr['item_type'] == 'F')
echo '<tr><td><br />'.$faqr['order_id'].' - <b>'.safeOUT($faqr['item_q']).'</b> - <a href="settings.php?faq='.$faqr['main_id'].'#FAQ">ÄNDRA</a> | <a href="settings.php?faqdel='.$faqr['main_id'].'">RADERA</a><br />'.safeOUT($faqr['item_a']).'</td></tr>';
else
echo '<tr><td><br />'.$faqr['order_id'].' - <b>'.safeOUT($faqr['item_q']).'</b> - <a href="settings.php?faq='.$faqr['main_id'].'#FAQ">ÄNDRA</a> | <a href="settings.php?faqdel='.$faqr['main_id'].'">RADERA</a><br />'.@safeOUT(implode(' : ', @unserialize($faqr['item_a']))).'</td></tr>';
		$ot = $faqr['item_type'];
	}
?>
</table>





		</td>
		<td width="50%" style="padding: 0 10px 0 10px; background: url('_img/brd_h.gif'); background-repeat: repeat-y;">
			<table width="100%">
			<tr>
				<td height="25" colspan="3"><b><?=($u_change)?'Ändra a':'A';?>nvändare</b> [<a href="settings.php?n=1">Ny användare</a>]</td>
			</tr>
<?
	if($new || $u_change) {
?>
			<tr>
				<td colspan="3" style="height: 0%; padding: 5px 0 10px 0;">
			<form action="settings.php" method="post" enctype="application/x-www-form-urlencoded">
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
		$pages = getEnumOptions($t.'admin', 'pos_all');
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
			<tr><td height="25" colspan="3">Det finns <span class="txt_chead txt_bld"><?=$u_count?></span> användare.</td></tr>
<?
	if(mysql_num_rows($u_sql) > 0) {
		echo '			<tr><td style="padding: 0 0 10px 0;">';
		$ostat = '';
		while($row = mysql_fetch_assoc($u_sql)) {
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
				<td><?=($row['status_id'] == '2' || $row['status_id'] == 'Z')?'[<b>BLOCKAD</b>] ':'';?><?=(!$_SESSION['u_c'] && $row['u_crew'])?'<b>'.secureOUT($row['user_user']).'</b>':'<a href="settings.php?c='.secureOUT($row['main_id']).'">'.secureOUT($row['user_user']).'</a>';?><?=($row['u_crew'])?' [<b>CREW</b>]':(($row['status_id'] == 'L')?' [<b>LISTAN</b>]':'');?></td>
				<td><?=($onl)?'<b class="txt_chead" title="Online">'.secureOUT($row['user_name']).'</b>':'<span title="Offline">'.secureOUT($row['user_name']).'</span>';?></td>
				<td align="right" style="padding-right: 10px;"><?=(!empty($row['owner_u']))?'['.$row['owner_u'].']':'';?><?=(!$row['u_crew'])?' <a href="settings.php?'.(($row['status_id'] == '1' || $row['status_id'] == 'L')?(($row['status_id'] == 'L')?'b2':'b').'='.$row['main_id'].'"'.(($row['main_id'] == $_SESSION['u_i'])?' onclick="return (confirm(\'Du vill blockera dig själv!\n\nDu kommer att loggas ut direkt och kommer INTE att kunna logga in igen.\n\nSäker ?\'))?true:false;"':'').'>BLOCKERA':(($row['status_id'] == 'Z')?'a2':'a').'='.$row['main_id'].'">TILLÅT</a> | <a href="settings.php?d='.$row['main_id'].'" onclick="return (confirm(\'Säker ?\'))?true:false;">RADERA').'</a>':'';?></td>
			</tr>
<?
		}
	}
?>
			</table>
<?
	if($u_change && ($_SESSION['u_c'] || $c_row['user_user'] == $_SESSION['u_u'])) {
		$log = mysql_query("SELECT * FROM {$t}adminlog WHERE login_name LIKE '%".secureINS($c_row['user_user'])."%' ORDER BY login_date DESC");
?>
			<hr /><div class="hr"></div>
			<table style="margin-bottom: 5px;">
			<tr>
				<td height="25" colspan="3" class="bld">Inloggningar för <b><?=$c_row['user_user']?></b></td>
			</tr>
<?
	if(mysql_num_rows($log) > 0) { while($row = mysql_fetch_assoc($log)) {
		echo '<tr><td style="padding-right: 20px;"><b>'.secureOUT($row['login_name']).'</b>'.((!empty($row['login_pass']))?' med <b>'.secureOUT($row['login_pass']).'</b>':'').'</td><td style="padding-right: 20px;"><a href="gb.php?t&s='.$row['login_ip'].'">'.$row['login_ip'].'</a> | <a href="http://ripe.net/whois?form_type=simple&full_query_string=&searchtext='.$row['login_ip'].'" target="_blank">INFO</a></td><td>'.niceDate($row['login_date']).'</td></tr>';
	} } else echo '<tr><td class="cnt">'.secureOUT($c_row['user_user']).' har aldrig loggat in.</td></tr>';
?>
			</table>
<?
	}
?>
		</td>
	</tr>
	</table>
</body>
</html>