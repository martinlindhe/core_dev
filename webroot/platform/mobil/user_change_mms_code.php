<?
	require_once('config.php');
	if (!$l) die;	//user not logged in

	require('design_head.php');

	$error = updateMMSKey();

	$settings = $user->getcontent($l['id_id'], 'user_settings');

	//print_r($settings);	
?>
	INSTÄLLNINGAR - ÄNDRA MMS KOD<br/>
	<br/>
	<?=$error?>
	<form method="post" action="">
		<input type="text" name="ins_mmskey" value="<?=@secureOUT(@$settings['mmskey'][1])?>"/>
		<input type="submit" value="Spara"/>
	</form>

<?
	//todo: gör mej till admin
	if ($isAdmin) {
		echo 'xx';
	}

	require('design_foot.php');
?>