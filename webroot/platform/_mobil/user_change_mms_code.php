<?
	require_once('config.php');
	if (!$l) die;	//user not logged in

	require('design_head.php');

	$error = updateMMSKey();

	$settings = $user->getcontent($l['id_id'], 'user_settings');
?>
<b>INSTÄLLNINGAR</b><br/><br/>
Ändra MMS-kod<br/>
	<br/>
	<?=$error?>
	<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
		<input type="text" name="ins_mmskey" value="<?=@secureOUT(@$settings['mmskey'][1])?>" size="12"/><br/>
		<input type="submit" value="Spara"/>
	</form>

<?
	require('design_foot.php');
?>