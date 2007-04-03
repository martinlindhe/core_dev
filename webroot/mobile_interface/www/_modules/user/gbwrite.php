<?
	if(!empty($_GET['r'])) $r = '1'; else $r = '0';
	if(!empty($_GET['a'])) $a = intval($_GET['a']); else $a = 0;
	if($own) popupACT('Du kan inte skicka till dig själv.');
	if(!empty($_POST['ins_cmt'])) {
		if($l['status_id'] == '1') {
			$prv = (!empty($_POST['ins_priv']) && $isOk)?1:0;
			$res = $sql->queryInsert("INSERT INTO {$t}usergb SET
			user_id = '".$s['id_id']."',
			sender_id = '".$l['id_id']."',
			private_id = '$prv',
			status_id = '1',
			user_read = '0',
			sent_cmt = '".secureINS($_POST['ins_cmt'])."',
			sent_html = '".(($isAdmin)?'1':'0')."',
			sent_date = NOW()");
			$or = array($s['id_id'], $l['id_id']);
			sort($or);
			$sql->queryInsert("INSERT INTO {$t}usergbhistory SET users_id = '".implode('', $or)."', msg_id = '$res'");
			if($a) {
				$sql->queryUpdate("UPDATE {$t}usergb SET is_answered = '1' WHERE main_id = '".secureINS($a)."' AND sender_id = '".$s['id_id']."' AND user_id = '".$l['id_id']."' LIMIT 1");
			}
			$user->counterIncrease('gb', $s['id_id']);
			$user->notifyIncrease('gb', $s['id_id']);

			if(!empty($_GET['main'])) {
				reloadACT(l('user', 'gb', $s['id_id']));
			} else {
				if($r) popupACT('Meddelande skickat!', '', l('user', 'gb', $s['id_id']), '500');
				else popupACT('Meddelande skickat!', '', '', '500');
			}
		}
		popupACT('Meddelande ej skickat!', '', '500');
	}
	if(!empty($_GET['main'])) {
		reloadACT(l('user', 'gb', $s['id_id']));
	}
	require(DESIGN.'head_popup.php');
?>
<script type="text/javascript">
	function ActivateByKey(e) {
		if(!e) var e=window.event;
		if (e['keyCode'] == 27) window.close();
		if(e.ctrlKey && e['keyCode'] == 13) {
			if(document.msg.onsubmit()) document.msg.submit();
			return false;
		}
	}
document.onkeydown = ActivateByKey;
</script>
<body style="background: #FFF;" class="cnt">
<form name="msg" action="<?=l('user', 'gbwrite', $s['id_id'])?>r=<?=$r?>&a=<?=$a?>" method="post" onsubmit="if(trim(this.ins_cmt.value).length > 1) { return true; } else { alert('Felaktigt meddelande: Minst 2 tecken!'); this.ins_cmt.select(); return false; }">
		<div class="smallWholeContent cnti mrg">
			<div class="smallHeader1"><h4>skriv gästboksinlägg</h4></div>
			<div class="smallFilled2 cnt pdg_t">
				<div class="rgt mrg"><span class="bg_wht">till: <?=$user->getstring($s, '', array('nolink' => 1))?></span></div>
				<textarea class="txt" name="ins_cmt" style="width: 160px; height: 160px;"></textarea><script type="text/javascript">document.msg.ins_cmt.focus();</script>
				<input type="submit" class="btn2_sml r" value="skicka!" style="margin-top: 5px;" /><br class="clr" />
			</div>
		</div>
</form>
<?
/*
<div class="box1" style="margin: 25px 15px 0 15px;">
		<img src="<?=CS?>_objects/_heads/head_gb_write.png" style="position: absolute; top: -10px; left: -10px;" />
	<div class="box1mid">
	<table cellspacing="0" style="margin: 38px 0 1px 0;">
	<tr><td class="rgt">till: <?=$user->getstring($s, '', array('nolink' => 1))?></td></tr>
	<tr><td><textarea name="ins_cmt" class="txt" style="width: 344px; height: 140px;"></textarea><script type="text/javascript">document.msg.ins_cmt.focus();</script></td></tr>
	<tr><td class="rgt"><?=($isOk)?'<div style="float: left; margin-top: 3px;"><input type="checkbox" class="chk" name="ins_priv" id="ins_priv" value="1"><label for="ins_priv"> Privat inlägg</label></div>':'';?></td></tr>
	</table>
	</div>
	<input type="image" accesskey="s" style="position: absolute; margin: 0; padding: 0; right: -10px; bottom: -11px;" src="/_objects/_heads/btn1_send.png" /></a>
</div>
</form>
<!--<div style="padding-top: 6px;"><?doADP('468_inside_popup');?></div>-->
*/?>
</body>
</html>