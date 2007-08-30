<?
	require_once('config.php');
	$user->requireLoggedIn();

	require('design_head.php');
	
	if(!empty($_POST['do'])) {
		storeFacts();
		echo 'Ändringar sparade!<br/>';
		require('design_foot.php');
		die;
	}

	$head = $user->getcontent($user->id, 'user_head');

	//$drink = getset('', 'drink', 'mo', 'text_cmt ASC');
?>

	ÄNDRA FAKTA<br/>
	<br/>

	<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
	<input type="hidden" name="do" value="1"/>
	<b>Civilstånd:</b><br /><?=@makeSelection('civil', $head['det_civil'])?><br/>
	<b>Attityd:</b><br /><?=@makeSelection('attitude', $head['det_attitude'])?><br/>
	<b>Barn:</b><br /><?=@makeSelection('children', $head['det_children'])?><br/>
	<b>Alkohol:</b><br /><?=@makeSelection('alcohol', $head['det_alcohol'])?><br/>
	<b>Tobak:</b><br /><?=@makeSelection('tobacco', $head['det_tobacco'])?><br/>
	<b>Sexliv:</b><br /><?=@makeSelection('sex', $head['det_sex'])?><br/>
	<b>Musiksmak:</b><br /><?=@makeSelection('music', $head['det_music'])?><br/>
	<b>Längd:</b><br /><?=@makeSelection('length', $head['det_length'])?><br/>
	<b>Vikt:</b><br /><?=@makeSelection('weight', $head['det_weight'])?><br/>
	<b>Vill ha:</b><br /><input type="text" class="txt" style="width: 185px;" name="det_wants" value="<?=@secureOUT($head['det_wants'])?>"/><br/>
	<input type="submit" value="Spara"/>
	</form>

<?
	require('design_foot.php');
?>
