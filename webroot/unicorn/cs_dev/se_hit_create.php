<?
	require_once('config.php');

	if (!$user->id) popupACT('Du måste vara inloggad för att skicka in en visdom.');

	if (!empty($_POST['ins_cmt'])) {
		if($_SESSION['data']['status_id'] == '1') {
			$q = 'INSERT INTO s_contribute '.
						'SET con_msg = "'.$db->escape($_POST['ins_cmt']).'", con_date = NOW(), status_id = "0", '.
						'con_onday = "0000-00-00",'.
						'con_user = '.$user->id;
			$db->insert($q);
			popupACT('Visdomen skickad!', '', '', '500');
		}
		popupACT('Visdom ej skickad!', '', '500');
	}
	require(DESIGN.'head_popup.php');
?>

<body style="background: #FFF;" class="cnt">

<form name="msg" action="<?=$_SERVER['PHP_SELF']?>" method="post">
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