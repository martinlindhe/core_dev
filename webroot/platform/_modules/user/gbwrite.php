<?
	include('gb.fnc.php');
	if(!empty($_GET['r'])) $r = '1'; else $r = '0';
	if(!empty($_GET['a'])) $a = intval($_GET['a']); else $a = 0;
	if($own) popupACT('Du kan inte skicka till dig själv.');
	if(!empty($_POST['ins_cmt'])) {
		if($l['status_id'] == '1') {
			$prv = (!empty($_POST['ins_priv']) && $isOk)?1:0;
			gbWrite($_POST['ins_cmt'], $s['id_id'], $a, $prv);
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

<form name="msg" action="<?=l('user', 'gbwrite', $s['id_id'])?>r=<?=$r?>&amp;a=<?=$a?>" method="post" onsubmit="if(trim(this.ins_cmt.value).length > 1) { return true; } else { alert('Felaktigt meddelande: Minst 2 tecken!'); this.ins_cmt.select(); return false; }">
		<div class="smallWholeContent cnti mrg">
			<div class="leftMenuHeader">gästboksinlägg</div>
			<div class="leftMenuBodyWhite pdg_t">
				skriv till <?=$user->getstring($s, '', array('nolink' => 1))?><br/>
				<textarea class="txt" name="ins_cmt" style="width: 160px; height: 160px;"></textarea>
				<script type="text/javascript">document.msg.ins_cmt.focus();</script>
				<input type="submit" class="btn2_sml r" value="skicka!" style="margin-top: 5px;" /><br class="clr" />
			</div>
		</div>
</form>

</body>
</html>