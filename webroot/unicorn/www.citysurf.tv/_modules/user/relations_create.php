<?
	if($own) popupACT('Du kan inte skapa en relation med dig sj�lv.');
	#	$user->blocked($s['id_id']);
	$isFriends = $user->isFriends($s['id_id'], 1);
	if($isFriends) {
		popupACT('Ni har redan en aktiv relation.');
	}

	if(!empty($_POST['ins_rel'])) {
		$error = sendRelationRequest($s['id_id'], $_POST['ins_rel']);
		if ($error == true) {
			popupACT('Du har nu skickat en f�rfr�gan.');
			die;
		}
	}

	$rel = getset('', 'r', 'mo', 'text_cmt ASC');

	require(DESIGN.'head_popup.php');
?>

<div class="popupWholeContent cnti mrg">
	<div class="smallHeader">bli v�n</div>
	<div class="smallBody">

		<form name="msg" action="<?=l('user', 'relations', $s['id_id'], '0')?>create=1" method="post">
		bli v�n med:<br />
		<?=$user->getstring($s, '', array('nolink' => true))?><br /><br />
		<b>relationstyp:</b><br />
		<select name="ins_rel" class="txt">
		<? foreach($rel as $row) echo '<option value="'.$row[0].'">'.secureOUT($row[1]).'</option>'; ?>
		</select><br/>
		<input type="submit" class="btn2_sml r" value="spara!" style="margin-top: 5px;" /><br class="clr" />
		</form>

	</div>
</div>

</body>
</html>