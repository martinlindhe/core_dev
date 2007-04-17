<?
	require_once('config.php');
	if (!$l) die;	//user not logged in

	require('design_head.php');
	
	$settings = $user->getcontent($l['id_id'], 'user_settings');

	//if($isAdmin && @$settings['mmskey'] != @$_POST['ins_mmskey']) {
	if (@$settings['mmskey'] != @$_POST['ins_mmskey']) {
		$id = $user->setinfo($l['id_id'], 'mmskey', "'".$_POST['ins_mmskey']."'");
		if ($id[0]) $user->setrel($id[1], 'user_settings', $l['id_id']);
		$settings['mmskey'][1] = $_POST['ins_mmskey'];
	}

	print_r($settings);	
?>
	INSTÄLLNINGAR - ÄNDRA MMS KOD<br/>
	<br/>
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