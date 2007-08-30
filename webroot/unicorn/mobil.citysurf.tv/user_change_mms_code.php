<?
	require_once('config.php');
	$user->requireLoggedIn();

	require('design_head.php');

	$error = updateMMSKey();

	$settings = $user->getcontent($user->id, 'user_settings');
?>
<b>INSTÄLLNINGAR</b><br/><br/>
Ändra MMS-kod<br/>
	<br/>
	<?=$error?>
	<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
		<input type="text" name="ins_mmskey" value="<?=@secureOUT(@$settings['mmskey'])?>" size="12"/><br/>
		<input type="submit" value="Spara"/>
	</form>

<?
	require('design_foot.php');
?>
