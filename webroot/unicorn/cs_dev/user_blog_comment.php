<?
	require_once('config.php');
	$user->requireLoggedIn();

	if (empty($_GET['id']) || !is_numeric($_GET['id']) || empty($_GET['n']) || !is_numeric($_GET['n'])) die;
	$id = $_GET['id'];	//user-id
	$n = $_GET['n'];	//blog-id

	if (amIBlocked($id)) errorACT('Användaren har blockerat dig.');

	$res = $db->getOneRow('SELECT main_id, status_id, user_id, blog_title, blog_date, hidden_id FROM s_userblog WHERE main_id = '.$n.' LIMIT 1');
	if(empty($res) || !count($res) || empty($res['status_id']) || $res['status_id'] != '1') {
		popupACT('Felaktigt inlägg.');
	}

	if ($res['hidden_id'] && $user->id != $res['user_id']) {
		$isFriends = $user->isFriends($s['id_id']);
		$allowed = ($isFriends || $user->isAdmin) ? true : false;

		if (!$allowed) {
			popupACT('Felaktigt inlägg.');
		}
	}
	if ($user->id == $res['user_id'] && !empty($_GET['a'])) $a = intval($_GET['a']); else $a = 0;
  if(!empty($_GET['del_msg']) && is_numeric($_GET['del_msg'])) {
  $r = $sql->queryLine("SELECT main_id, status_id, user_id, id_id FROM s_userblogcmt WHERE main_id = '".secureINS($_GET['del_msg'])."' LIMIT 1");
  if(!empty($r) && count($r) && $r[1] == '1') {
   if($isAdmin || $r[2] == $l['id_id'] || $r[3] == $l['id_id']) {
    $re = $sql->queryUpdate("UPDATE s_userblogcmt SET status_id = '2' WHERE main_id = '".secureINS($r[0])."' LIMIT 1");
   }
   if($re) {
    $sql->queryUpdate("UPDATE s_userblog SET blog_cmts = blog_cmts - 1 WHERE main_id = '".$res['main_id']."' LIMIT 1");
   }
   reloadACT(l('user', 'blog', $s['id_id'], $res['main_id']));
  }
 }

	if(!empty($_POST['ins_cmt'])) {
		if($_SESSION['data']['status_id'] == '1') {
			$prv = (!empty($_POST['ins_priv']) && $user->id != $res['user_id'])?1:0;
			$q = "INSERT INTO s_userblogcmt SET
			user_id = '".$res['user_id']."',
			id_id = '".$user->id."',
			blog_id = '".$res['main_id']."',
			status_id = '1',
			private_id = '$prv',
			".(($user->isAdmin)?"c_html = '1',":'')."
			c_msg = '".$db->escape($_POST['ins_cmt'])."',
			c_date = NOW()";
			$r = $db->insert($q);
			$db->update("UPDATE s_userblog SET blog_cmts = blog_cmts + 1 WHERE main_id = '".$res['main_id']."' LIMIT 1");
		}
		popupACT('Meddelande skickat!', '', 'user_blog_read.php?id='.$res['user_id'].'&n='.$res['main_id'].'&'.mt_rand(10000, 99999).'#cmt', 500);
	}
	$alias = '';
	if($a) {
		$alias = $db->getOneItem("SELECT u.u_alias FROM s_userblogcmt b INNER JOIN s_user u ON u.id_id = b.id_id AND u.status_id = '1' WHERE b.main_id = '".$db->escape($a)."' AND b.user_id = '".$user->id."' AND b.status_id = '1' LIMIT 1");
	}
	if($alias) $alias = $alias.': ';
	require(DESIGN.'head_popup.php');
?>
<script type="text/javascript" src="<?=$config['web_root']?>js/text_control.js"></script>
<script type="text/javascript">
window.onload = TC_Init;
function addselOption(txt, file) {
	len = document.getElementById('photo_list').options.length;
	document.getElementById('photo_list').options[len] = new Option(txt, file);
}
function validateIt(tForm) {
	if(tForm.ins_title.value == '')	{
		alert('Du måste skriva en rubrik.');
		tForm.ins_title.focus();
		return false;
	}
	if(TC_active) TC_VarToHidden();
	return true;
}
</script>
<form name="msg" action="<?='user_blog_comment.php?id='.$res['user_id'].'&amp;n='.$res['main_id']?>&amp;r=<?=@$r?>&amp;a=<?=$a?>" method="post" onsubmit="if(trim(this.ins_cmt.value).length > 1) { return true; } else { alert('Felaktigt meddelande: Minst 2 tecken!'); this.ins_cmt.select(); return false; }">
		<div class="smallWholeContent cnti mrg">
			<div class="smallHeader">bloggkommentar</div>
			<div class="smallBody pdg_t">
				skriv kommentar till bloggen<br />
				<b><?=secureOUT($res['blog_title'])?></b><br />
				av <?=$user->getstring($res['user_id'], '', array('nolink' => 1))?><br/>
				<textarea class="txt" name="ins_cmt" style="width: 160px; height: 128px;"><?=secureOUT($alias)?></textarea>
				<script type="text/javascript">document.msg.ins_cmt.focus();</script>
				<? if ($user->vip_check(VIP_LEVEL2)) echo '<input type="checkbox" name="ins_priv" id="ins_priv"><label for="ins_priv">Privat (VIP)</label>'; ?>
				<input type="submit" class="btn2_sml r" value="skicka" style="margin-top: 5px;" /><br class="clr" />
			</div>
		</div>
</form>
</body>
</html>
