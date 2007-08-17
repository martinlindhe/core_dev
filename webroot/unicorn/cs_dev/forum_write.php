<?
	require_once('config.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id']) || !$user->id) die('Tråden existerar inte.');

	$r = $db->getOneRow('SELECT main_id, main_ttl, main_cmt, subjects FROM s_ftopic f WHERE f.main_id = '.$_GET['id'].' AND f.status_id = "1" LIMIT 1');
	if (empty($r) || !count($r)) die('Rubriken existerar inte.');

	if(!empty($_POST['ins_ttl']) && !empty($_POST['ins_cmt'])) {
		if ($_SESSION['data']['status_id'] == '1') {
			$q = "INSERT INTO s_f SET topic_id = ".$r['main_id'].", parent_id = '0',
			sender_id = '".$user->id."', status_id = '1',
			sent_html = '".(($user->isAdmin)?'1':'0')."',
			sent_ttl = '".$db->escape($_POST['ins_ttl'])."',
			sent_cmt = '".$db->escape($_POST['ins_cmt'])."',
			change_date = NOW(), sent_date = NOW()";

			$ins = $db->insert($q);
			$db->update('UPDATE s_f SET top_id = main_id WHERE main_id = "'.$db->escape($ins).'" LIMIT 1');

			$user->counterIncrease('forum', $ins);
			$user->notifyIncrease('forum', $ins);
			popupACT('Tråd skapad!', '', 'forum_read.php?id='.$ins.'#R'.$ins, '500');
		} else die('Tråd ej sparad!');
	}

	require(DESIGN."head_popup.php");
?>
<script type="text/javascript">
	var sub_dis = false;
	function ActivateByKey(e) {
		if(!e) var e=window.event;
		if (e['keyCode'] == 27) window.close();
		if(e.ctrlKey && e['keyCode'] == 13) {
			if(!sub_dis && document.msg.onsubmit()) document.msg.submit();
			return false;
		}
	}
document.onkeydown = ActivateByKey;
function validateForm(tForm) {
	if(trim(tForm.ins_ttl.value).length == 0) {
		alert('Felaktigt titel: Minst 1 tecken!');
		tForm.ins_ttl.select();
		return false;
	}
	if(trim(tForm.ins_cmt.value).length < 2) {
		alert('Felaktigt meddelande: Minst 2 tecken!');
		tForm.ins_cmt.select();
		return false;
	}
	sub_dis = true; tForm.submit.disabled = true; return true;
}
</script>
<style type="text/css">
body {
	border: 6px solid #FFF;
	background: #FFF;
}
</style>

<div class="mainHeader2"><h4>ny tråd - <?=secureOUT($r['main_ttl'])?></h4></div>
<form name="msg" action="<?=$_SERVER['PHP_SELF'].'?id='.$r['main_id']?>" method="post" onsubmit="return validateForm(this);">
<div class="mainBoxed2">
	<table summary="" cellspacing="0" style="margin: 10px 0 0 10px;">
		<tr><td class="pdg" style="padding-top: 0;">
			titel:<br /><input type="text" name="ins_ttl" class="txt" style="width: 338px; margin-top: 2px;" onfocus="toggleInp(this, this.title, '');" onblur="toggleInp(this, '', this.title);" title="Titel" value="" /><br />
			meddelande:<br />
			<textarea name="ins_cmt" class="txt" style="width: 550px; height: 100px;"></textarea>
			<script type="text/javascript">document.msg.ins_ttl.focus();</script>
		</td></tr>
		<tr><td class="pdg rgt" style="padding-top: 0;">
			<input type="submit" class="btn2_sml r" value="skicka" name="sub"/>
		</td></tr>
	</table>
</div>
</form>

</body>
</html>