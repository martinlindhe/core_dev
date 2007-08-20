<?
	require_once('config.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('ingen mottagare');
	$id = $_GET['id'];

	if ($user->id == $id) popupACT('Du kan inte skapa en relation med dig själv.');

	$isFriends = $user->isFriends($id, 1);
	if ($isFriends) popupACT('Ni har redan en aktiv relation.');
	
	$s = $user->getuser($id);

	if (!empty($_POST['ins_rel'])) {
		$error = sendRelationRequest($id, $_POST['ins_rel']);
		if ($error == true) popupACT('Du har nu skickat en förfrågan.');
	}

	$rel = getset(0, 'r', 'mo', 'text_cmt ASC');

	require(DESIGN.'head_popup.php');
?>

<div class="popupWholeContent cnti mrg">
	<div class="smallHeader">bli vän</div>
	<div class="smallBody">

		<form name="msg" action="<?=$_SERVER['PHP_SELF'].'?id='.$id?>" method="post">
		bli vän med:<br />
		<?=$user->getstring($s, '', array('nolink' => true))?><br /><br />
		<b>relationstyp:</b><br />
		<select name="ins_rel" class="txt">
		<? foreach ($rel as $row) echo '<option value="'.$row['main_id'].'">'.secureOUT($row['text_cmt']).'</option>'; ?>
		</select><br/>
		<input type="submit" class="btn2_sml r" value="spara!" style="margin-top: 5px;" /><br class="clr" />
		</form>

	</div>
</div>

</body>
</html>