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
	$page = 'TEXT';
	$menu = $menu_NEWS;
	$array = array('Oregistrerad', 'Standard', 'Brons', 'Silver', 'Guld');
	$limit = 10;
	$ip_limit = 20;
	if(!$isCrew && strpos($_SESSION['u_a'][1], 'text') === false) errorNEW('Ingen behörighet.');
	$change = false;
	$t_change = false;
	$error = '';

	if(isset($_POST['ins_msg'])) {
		if(!empty($_POST['id'])) {
			mysql_query("UPDATE {$t}text SET text_cmt = '".secureINS($_POST['ins_msg'])."', text_date = NOW() WHERE main_id = '".secureINS($_POST['id'])."' LIMIT 1");
		}
		header("Location: text.php");
		exit;
	}

	if(!empty($_GET['id'])) {
		$sql = mysql_query("SELECT * FROM {$t}text WHERE main_id = '".secureINS($_GET['id'])."' LIMIT 1");
		if(mysql_num_rows($sql) == '1') {
			$change = true;
			$row = mysql_fetch_assoc($sql);
		}
	}

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
		header("Location: text.php#FAQ");
		exit;
	}
	if(!empty($_GET['faqdel']) && is_numeric($_GET['faqdel'])) {
		$sql = mysql_query("SELECT * FROM s_faq WHERE main_id = '".secureINS($_GET['faqdel'])."' LIMIT 1");
		if(mysql_num_rows($sql) == '1') {
			mysql_query("DELETE FROM s_faq WHERE main_id = '".secureINS($_GET['faqdel'])."' LIMIT 1");
			header("Location: text.php#FAQ");
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
		$err = str_replace("wrongpass", "Felaktigt fält: Nuvarande lösenord", $_SESSION['err']);
		$err = str_replace("wronguser", "Felaktigt fält: Användarnamn", $_SESSION['err']);
		echo '
alert("'.$err.'");
document.location.href = \'text.php\';
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
	if(parent.<?=FRS?>head)
	parent.<?=FRS?>head.show_active('info');
}
<?=(isset($_GET['t']))?'loadtop();':'';?>
</script>
	<table width="100%" height="100%">
	<tr><td colspan="2" height="25" class="nobr"><?makeMenu($page, $menu, 0);?></td></tr>
	<tr>
		<td width="50%" style="padding: 0 10px 0 0">
			<form name="change" action="text.php" method="post">
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

			<tr><td><a href="text.php?id=<?=$row['main_id']?>"><?=(!empty($texts[$row['main_id']]))?'<span class="bld">'.$row['main_id'].' ('.$texts[$row['main_id']].')</span>':'<span class="bld">'.$row['main_id'].'</span>';?></a> - <em><?=niceDate($row['text_date'])?></em></td></tr>
<?
		}
	}
?>
			</table>
			</form>






		</td>
		<td width="50%" style="padding: 0 10px 0 10px; background: url('_img/brd_h.gif'); background-repeat: repeat-y;">
			<table width="100%">
			<tr>
				<td height="25" colspan="3"><a href="text.php?<?=microtime()?>#FAQ" class="no_lnk"><b>FAQ och UPPGRADERA</b></a> - <a href="?rand=<?=microtime()?>#FAQ">NY</a></td>
			</tr>
<form action="text.php" method="post">
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
	Fråga:<br /><input type="text" name="Finp_q" class="txt" style="width: 378px;" value="<?=@safeOUT($faqr['item_q'], 0)?>" /></td></tr>
	<tr><td colspan="2">Svar:<br /><textarea name="Finp_a" class="inp_nrm" style="height: 110px;"><?=@safeOUT($faqr['item_a'], 0)?></textarea></td></tr>
	<tr><td>Sorteringsnummer:<br /><input type="text" name="Forder" class="txt" value="<?=@safeOUT($faqr['order_id'], 0)?>" /></td><td align="right"><br /><br /><input type="submit" value="Spara" class="btn" /></td></tr>
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
	<form action="text.php" method="post">
	<?=($faqr)?'<input type="hidden" name="id" value="'.$faqr['main_id'].'" />':'';?>
	<input type="hidden" name="dofaq" value="1" />
	Namn:<br /><input type="text" name="inp_q" class="txt" style="width: 378px;" value="<?=@safeOUT($faqr['item_q'], 0)?>" /></td></tr>
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
	<tr><td>Sorteringsnummer:<br /><input type="text" name="order" class="txt" value="<?=@safeOUT($faqr['order_id'], 0)?>" /></td><td align="right"><br /><br /><input type="submit" value="Spara" class="btn" /></td></tr>
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
echo '<tr><td><br />'.$faqr['order_id'].' - <b>'.safeOUT($faqr['item_q']).'</b> - <a href="text.php?faq='.$faqr['main_id'].'#FAQ">ÄNDRA</a> | <a href="text.php?faqdel='.$faqr['main_id'].'">RADERA</a><br />'.safeOUT($faqr['item_a']).'</td></tr>';
else
echo '<tr><td><br />'.$faqr['order_id'].' - <b>'.safeOUT($faqr['item_q']).'</b> - <a href="text.php?faq='.$faqr['main_id'].'#FAQ">ÄNDRA</a> | <a href="text.php?faqdel='.$faqr['main_id'].'">RADERA</a><br />'.@safeOUT(implode(' : ', @unserialize($faqr['item_a']))).'</td></tr>';
		$ot = $faqr['item_type'];
	}
?>
</table>
		</td>
	</tr>
	</table>
</body>
</html>