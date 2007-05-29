<?
	if(!empty($key)) {
		$res = $sql->queryLine("SELECT main_id, status_id, user_id, pht_date, hidden_id, pht_cmt FROM {$t}userphoto WHERE main_id = '".secureINS($key)."' LIMIT 1", 1);
		if(empty($res) || !count($res) || empty($res['status_id']) || $res['status_id'] != '1' || $res['user_id'] != $s['id_id']) {
			popupACT('Felaktigt inlägg.');
		}
	} else popupACT('Felaktigt inlägg.');
	if($res['hidden_id'] && !$own && !$allowed) {
		popupACT('Felaktigt inlägg.');
	}
	if($own && !empty($_GET['a'])) $a = intval($_GET['a']); else $a = 0;
  if(!empty($_GET['del_msg']) && is_numeric($_GET['del_msg'])) {
  $r = $sql->queryLine("SELECT main_id, status_id, user_id, id_id FROM {$t}userphotocmt WHERE main_id = '".secureINS($_GET['del_msg'])."' LIMIT 1");
  if(!empty($r) && count($r) && $r[1] == '1') {
   if($isAdmin || $r[2] == $l['id_id'] || $r[3] == $l['id_id']) {
    $re = $sql->queryUpdate("UPDATE {$t}userphotocmt SET status_id = '2' WHERE main_id = '".secureINS($r[0])."' LIMIT 1");
   }
   if($re) {
    $sql->queryUpdate("UPDATE {$t}userphoto SET pht_cmts = pht_cmts - 1 WHERE main_id = '".$res['main_id']."' LIMIT 1");
   }
   reloadACT(l('user', 'gallery', $s['id_id'], $res['main_id']));
  }
 }

	if(!empty($_POST['ins_cmt'])) {
		if($l['status_id'] == '1') {
			$prv = (!empty($_POST['ins_priv']) && !$own)?1:0;
			$r = $sql->queryInsert("INSERT INTO {$t}userphotocmt SET
			user_id = '".$s['id_id']."',
			id_id = '".$l['id_id']."',
			photo_id = '".$res['main_id']."',
			status_id = '1',
			private_id = '$prv',
			".(($isAdmin)?"c_html = '1',":'')."
			c_msg = '".secureINS($_POST['ins_cmt'])."',
			c_date = NOW()");
			$sql->queryResult("UPDATE {$t}userphoto SET pht_cmts = pht_cmts + 1 WHERE main_id = '".$res['main_id']."' LIMIT 1");
			#if(!$own) $user->spy($s['id_id'], $res['main_id'], 'BCT', array($l['u_alias'], $s['id_id'], $res['main_id'], $res['blog_title']));
			#elseif($a) {
			#	$id = $sql->queryResult("SELECT b.id_id FROM {$t}userphotocmt b INNER JOIN {$t}user u ON u.id_id = b.id_id AND u.status_id = '1' WHERE b.main_id = '".secureINS($a)."' AND b.user_id = '".$l['id_id']."' AND b.status_id = '1' LIMIT 1");
			#	if($id) {
			#		$user->spy($id, $res['main_id'], 'BCA', array($l['u_alias'], $l['id_id'], $res['main_id'], $res['blog_title']));
			#	}
			#}
		}
		popupACT('Meddelande skickat!', '', l('user', 'gallery', $s['id_id'], $res['main_id']).'&'.mt_rand(10000, 99999).'#cmt', 500);
	}
	$alias = '';
	if($a) {
		$alias = $sql->queryResult("SELECT u.u_alias FROM {$t}userphotocmt b INNER JOIN {$t}user u ON u.id_id = b.id_id AND u.status_id = '1' WHERE b.main_id = '".secureINS($a)."' AND b.user_id = '".$l['id_id']."' AND b.status_id = '1' LIMIT 1");
	}
	if($alias) $alias = $alias.': ';
	require(DESIGN.'head_popup.php');
?>

<form name="msg" action="<?=l('user', 'gallerycomment', $s['id_id'], $res['main_id'])?>&amp;r=<?=$r?>&amp;a=<?=$a?>" method="post" onsubmit="if(trim(this.ins_cmt.value).length > 1) { return true; } else { alert('Felaktigt meddelande: Minst 2 tecken!'); this.ins_cmt.select(); return false; }">
		<div class="smallWholeContent cnti mrg">
			<div class="smallHeader">skriv kommentar</div>
			<div class="smallBody pdg_t">
				kommentera bilden <br />
				<b><?=secureOUT($res['pht_cmt'])?></b><br />
				av <?=$user->getstring($s, '', array('nolink' => 1))?><br/>
				<textarea class="txt" name="ins_cmt" style="width: 160px; height: 128px;"><?=secureOUT($alias)?></textarea>
				<script type="text/javascript">document.msg.ins_cmt.focus();</script>
				<input type="submit" class="btn2_sml r" value="skicka!" style="margin-top: 5px;" /><br class="clr" />
			</div>
		</div>
</form>

</body>
</html>