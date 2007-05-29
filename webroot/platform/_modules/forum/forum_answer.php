<?
	require_once(dirname(__FILE__).'/../user/spy.fnc.php');

	if(empty($_GET['id']) || !is_numeric($_GET['id'])) {
		popupACT('Tråden existerar inte.');
	}
	$res = $sql->queryLine("SELECT f.main_id, f.topic_id, f.sent_html, f.top_id, f.subject_id, f.parent_id, f.view_id, f.sender_id, f.sent_ttl, f.sent_cmt, f.sent_date, u.id_id, u.u_alias, u.account_date, u.u_sex, u.u_birth, u.u_picid, u.u_picd, u.u_picvalid, u.level_id FROM {$t}f f LEFT JOIN {$t}user u ON u.id_id = f.sender_id AND u.status_id = '1' WHERE f.main_id = '".secureINS($_GET['id'])."' AND f.status_id = '1' LIMIT 1", 1);
	if(!count($res) || $res['view_id'] != '1') {
		popupACT('Inlägget existerar inte.');
	}
	$isAdmin = ($l)?$user->level($l['level_id'], 10):false;
	$r = $sql->queryLine("SELECT main_id, main_ttl, main_cmt, subjects FROM {$t}ftopic f WHERE f.main_id = '".$res['topic_id']."' AND f.status_id = '1' LIMIT 1", 1);
	if(empty($r) || !count($r)) {
		popupACT('Rubriken existerar inte.');
	}
	$res['sent_cmt'] = (strlen($res['sent_cmt']) > 50?substr($res['sent_cmt'], 0, 50).'...':$res['sent_cmt']);
	if(substr($res['sent_cmt'], 0, 4) == 'Sv: ') $res['sent_cmt'] = substr($res['sent_cmt'], 4);
	if(!empty($_POST['ins_cmt'])) {
		if($l['status_id'] == '1') {
			$parent = $res['main_id'];
			$top = $res['top_id'];

			$ins = $sql->queryInsert("INSERT INTO {$t}f SET
			topic_id = '{$r['main_id']}',
			parent_id = '".secureINS($top)."',
			top_id = '".secureINS($top)."',
			sender_id = '".$l['id_id']."',
			status_id = '1',
			sent_html = '".(($isAdmin)?'1':'0')."',
			sent_ttl = '".secureINS($res['sent_cmt'])."',
			sent_cmt = '".secureINS($_POST['ins_cmt'])."',
			sent_date = NOW()");

			$q = "SELECT sent_ttl FROM ${t}f WHERE main_id = '".secureINS($parent)."'";
			$res2 = $sql->queryLine($q, 1);
			spyPost($top, 'f', $res2['sent_ttl']);
			//spyPost($top, 'f', $ins);

			$sql->queryUpdate("UPDATE {$t}f SET change_date = NOW() WHERE main_id = '".secureINS($top)."' LIMIT 1");
			$user->counterDecrease('forum', $s['id_id']);

			popupACT('Meddelande skickat!', '', l('forum','read',$res['top_id']).'&item='.$ins.'#R'.$ins, '500');
		} else popupACT('Meddelande ej sparat!', '', l('forum','read',$res['top_id']), '500');
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
</script>

<div class="bigHeader">svara på inlägg</div>
<form name="msg" action="<?=l('forum', 'answer' ,$res['main_id'])?>" method="post" onsubmit="if(trim(this.ins_cmt.value).length > 1) { sub_dis = true; this.sub.disabled = true; return true; } else { alert('Felaktigt meddelande: Minst 2 tecken!'); this.ins_cmt.select(); return false; }">
<div class="bigBody">
	<table summary="" cellspacing="0" style="margin-left: 10px;">
		<tr><td class="pdg">Svar till: <b><?=secureOUT($res['sent_cmt'])?></b></td></tr>
		<tr><td class="pdg" style="padding-top: 0;"><textarea name="ins_cmt" class="txt" style="width: 550px; height: 100px;"></textarea><script type="text/javascript">document.msg.ins_cmt.focus();</script></td></tr>
		<tr><td class="pdg rgt" style="padding-top: 0;"><input type="submit" class="btn2_sml r" value="skicka" name="sub"/></td></tr>
	</table>
</div>
</form>

</body>
</html>