<?
	require_once('config.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('ingen mottagare');
	$id = $_GET['id'];
	if ($user->id == $id) popupACT('Du kan inte skicka till dig själv.');

	$s = $user->getuser($id);

	if(!empty($_GET['r'])) $r = '1'; else $r = '0';
	if(!empty($_GET['a'])) $a = intval($_GET['a']); else $a = 0;


	if(!empty($_POST['ins_cmt'])) {
		if($_SESSION['data']['status_id'] == '1') {
			$prv = 0;
			if ($user->vip_check(VIP_LEVEL1) && !empty($_POST['ins_priv'])) $prv = 1;
			gbWrite($_POST['ins_cmt'], $id, $a, $prv);
			if(!empty($_GET['main'])) {
				reloadACT('user_gb.php?id='.$id);
			} else {
				popupACT('Meddelande skickat!', '', 'user_gb.php?id='.$id, '500');
			}
		}
		popupACT('Meddelande ej skickat!', '', '500');
	}
	if(!empty($_GET['main'])) {
		reloadACT(l('user', 'gb', $id));
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

<form name="msg" action="<?=$_SERVER['PHP_SELF'].'?id='.$id?>&amp;r=<?=$r?>&amp;a=<?=$a?>" method="post" onsubmit="if(trim(this.ins_cmt.value).length > 1) { return true; } else { alert('Felaktigt meddelande: Minst 2 tecken!'); this.ins_cmt.select(); return false; }">
		<div class="smallWholeContent cnti mrg">
			<div class="smallHeader">gästboksinlägg</div>
			<div class="smallBody pdg_t">
				skriv till <?=$user->getstring($s, '', array('nolink' => 1))?><br/>
				<textarea class="txt" name="ins_cmt" style="width: 160px; height: 145px;"></textarea>
				<script type="text/javascript">document.msg.ins_cmt.focus();</script>
				<? if ($user->vip_check(VIP_LEVEL1)) echo '<input type="checkbox" name="ins_priv" id="ins_priv"><label for="ins_priv">Privat (VIP)</label>'; ?>
				<input type="submit" class="btn2_sml r" value="skicka!" style="margin-top: 5px;" /><br class="clr" />
			</div>
		</div>
</form>

</body>
</html>