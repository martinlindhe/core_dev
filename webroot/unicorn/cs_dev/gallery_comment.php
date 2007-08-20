<?
	require_once('config.php');

	$id = $user->id;
	if (!empty($_GET['id']) && is_numeric($_GET['id'])) $id = $_GET['id'];

	if (empty($_GET['n']) || !is_numeric($_GET['n'])) die('no img id');
	$key = $_GET['n'];	//image id to display

//	$isFriends = $user->isFriends($id);
	//$allowed = ($user->id == $id || $isFriends || $user->isAdmin) ? true : false;

	if (!empty($key)) {
		$res = $db->getOneRow('SELECT main_id, status_id, user_id, pht_date, hidden_id, pht_cmt FROM s_userphoto WHERE main_id = '.$key.' LIMIT 1');
		if (!$res || empty($res['status_id']) || $res['status_id'] != '1' || $res['user_id'] != $id) {
			popupACT('Felaktigt inlägg.');
		}
	} else popupACT('Felaktigt inlägg.');

	if ($res['hidden_id'] && !getGallXStatus($id) && !$user->vip_check(10)) {
		popupACT('Felaktigt inlägg.');
	}

	if ($user->id == $res['user_id'] && !empty($_GET['a'])) $a = intval($_GET['a']); else $a = 0;

	//delete comments
	if (!empty($_GET['del_msg']) && is_numeric($_GET['del_msg'])) {
		$r = $sql->queryLine("SELECT main_id, status_id, user_id, id_id FROM s_userphotocmt WHERE main_id = '".secureINS($_GET['del_msg'])."' LIMIT 1");
		if(!empty($r) && count($r) && $r[1] == '1') {
			if($isAdmin || $r[2] == $l['id_id'] || $r[3] == $l['id_id']) {
				$re = $sql->queryUpdate("UPDATE s_userphotocmt SET status_id = '2' WHERE main_id = '".secureINS($r[0])."' LIMIT 1");
			}
			if($re) {
				$sql->queryUpdate("UPDATE s_userphoto SET pht_cmts = pht_cmts - 1 WHERE main_id = '".$res['main_id']."' LIMIT 1");
			}
			reloadACT(l('user', 'gallery', $s['id_id'], $res['main_id']));
		}
	}

	//add comments
	if(!empty($_POST['ins_cmt'])) {
		if($_SESSION['data']['status_id'] == '1') {
			$prv = (!empty($_POST['ins_priv']) && $user->id != $res['user_id']) ? 1 : 0;
			$r = $db->insert("INSERT INTO s_userphotocmt SET
			user_id = '".$id."',
			id_id = '".$user->id."',
			photo_id = '".$res['main_id']."',
			status_id = '1',
			private_id = '$prv',
			".(($user->isAdmin)?"c_html = '1',":'')."
			c_msg = '".$db->escape($_POST['ins_cmt'])."',
			c_date = NOW()");
			$db->update("UPDATE s_userphoto SET pht_cmts = pht_cmts + 1 WHERE main_id = '".$res['main_id']."' LIMIT 1");
		}
		popupACT('Meddelande skickat!', '', 'gallery_view.php?id='.$id.'&n='.$res['main_id'].'#cmt', 500);
	}
	$alias = '';
	if($a) {
		$alias = $sql->queryResult("SELECT u.u_alias FROM s_userphotocmt b INNER JOIN s_user u ON u.id_id = b.id_id AND u.status_id = '1' WHERE b.main_id = '".secureINS($a)."' AND b.user_id = '".$l['id_id']."' AND b.status_id = '1' LIMIT 1");
	}
	if($alias) $alias = $alias.': ';
	require(DESIGN.'head_popup.php');
?>

<form name="msg" action="<?=$_SERVER['PHP_SELF'].'?id='.$id.'&n='.$res['main_id']?>&amp;a=<?=$a?>" method="post" onsubmit="if(trim(this.ins_cmt.value).length > 1) { return true; } else { alert('Felaktigt meddelande: Minst 2 tecken!'); this.ins_cmt.select(); return false; }">
		<div class="smallWholeContent cnti mrg">
			<div class="smallHeader">skriv kommentar</div>
			<div class="smallBody pdg_t">
				kommentera bilden <br />
				<b><?=secureOUT($res['pht_cmt'])?></b><br />
				av <?=$user->getstring($id, '', array('nolink' => 1))?><br/>
				<textarea class="txt" name="ins_cmt" style="width: 160px; height: 120px;"><?=secureOUT($alias)?></textarea>
				<script type="text/javascript">document.msg.ins_cmt.focus();</script>
				<? if ($user->vip_check(VIP_LEVEL2)) echo '<input type="checkbox" name="ins_priv" id="ins_priv"><label for="ins_priv">Privat (VIP)</label>'; ?>
				<input type="submit" class="btn2_sml r" value="skicka!" style="margin-top: 5px;" /><br class="clr" />
			</div>
		</div>
</form>

</body>
</html>
