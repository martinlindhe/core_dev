<?
	if(empty($_GET['id']) || !is_numeric($_GET['id'])) {
		popupACT('Tråden existerar inte.');
	}
	#$isAdmin = ($l)?$user->level($l['level_id'], 10):false;
	$r = $sql->queryLine("SELECT main_id, main_ttl, main_cmt, subjects FROM {$t}ftopic f WHERE f.main_id = '".secureINS($_GET['id'])."' AND f.status_id = '1' LIMIT 1", 1);
	if(empty($r) || !count($r)) {
		popupACT('Rubriken existerar inte.');
	}
	#$subs = explode(';', $r['subjects']);
	#$gotsub = (!empty($_GET['sub']) && is_numeric($_GET['sub']) && $_GET['sub'] >= 1 && $_GET['sub'] <= 3)?intval($_GET['sub']):0;

	if(!empty($_POST['ins_ttl']) && !empty($_POST['ins_cmt'])) {
		#if(!$gotsub) $gotsub = (!empty($_POST['sub']) && is_numeric($_POST['sub']) && $_POST['sub'] >= 1 && $_POST['sub'] <= 3)?intval($_POST['sub']):0;
		#if($gotsub) {
			if($l['status_id'] == '1') {
				$ins = $sql->queryInsert("INSERT INTO {$t}f SET
				topic_id = '{$r['main_id']}',
				parent_id = '0',
				sender_id = '".$l['id_id']."',
				status_id = '1',
				subject_id = '".$gotsub."',
				sent_html = '".(($isAdmin)?'1':'0')."',
				sent_ttl = '".secureINS($_POST['ins_ttl'])."',
				sent_cmt = '".secureINS($_POST['ins_cmt'])."',
				change_date = NOW(),
				sent_date = NOW()");
				$sql->queryUpdate("UPDATE {$t}f SET top_id = main_id WHERE main_id = '".secureINS($ins)."' LIMIT 1");
				$user->counterIncrease('forum', $ins_to);
				$user->notifyIncrease('forum', $ins_to);
				popupACT('Tråd skapad!', '', l('forum','read',$ins.'#R'.$ins), '500');
			} else popupACT('Tråd ej sparad!');
		#} else popupACT('Inget ämne valt.');
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
<body style="border: 6px solid #FFF; background: #FFF;">
<div class="mainHeader2"><h4>ny tråd - <?=secureOUT($r['main_ttl'])?></h4></div>
<form name="msg" action="<?=l('forum','write',$r['main_id']);?>" method="post" onsubmit="return validateForm(this);">
<div class="mainBoxed2">
<table cellspacing="0" style="margin: 10px 0 0 10px;">
<tr><td class="pdg" style="padding-top: 0;">titel:<br /><input type="text" name="ins_ttl" class="txt" style="width: 338px; margin-top: 2px;" onfocus="toggleInp(this, this.title, '');" onblur="toggleInp(this, '', this.title);" title="Titel" value="" /><script type="text/javascript">//document.msg.ins_ttl.focus();</script><br />meddelande:<br /><textarea name="ins_cmt" class="txt" style="width: 550px; height: 100px;"></textarea><script type="text/javascript">document.msg.ins_cmt.focus();</script></td></tr>
<tr><td class="pdg rgt" style="padding-top: 0;"><input type="submit" class="btn2_sml r" value="skicka" name="sub"></td></tr>
</table>
</div>
</form>
</body>
</html>