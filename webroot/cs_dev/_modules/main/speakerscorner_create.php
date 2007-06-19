<?
	if(!$l) popupACT('Du måste vara inloggad för att skicka in en visdom.');
	if(!empty($_POST['ins_cmt'])) {
		if($l['status_id'] == '1') {
			$sql->queryInsert("INSERT INTO s_contribute SET con_msg = '".secureINS($_POST['ins_cmt'])."', con_date = NOW(), status_id = '0', con_user = '".$l['id_id']."'");
			popupACT('Visdomen skickad!', '', '', '500');
		}
		popupACT('Visdom ej skickad!', '', '500');
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

<form name="msg" action="<?=l('main', 'speakerscorner', 'create')?>" method="post" onsubmit="if(trim(this.ins_cmt.value).length > 1) { return true; } else { alert('Felaktigt meddelande: Minst 2 tecken!'); this.ins_cmt.select(); return false; }">
	<div class="smallWholeContent cnti mrg">
		<div class="leftMenuHeader">skriv en visdom</div>
		<div class="leftMenuBodyWhite pdg_t">
			<textarea class="txt" name="ins_cmt" style="width: 160px; height: 160px;"></textarea>
			<script type="text/javascript">document.msg.ins_cmt.focus();</script>
			<input type="submit" class="btn2_sml" value="skicka" />
		</div>
	</div>
</form>

</body>
</html>