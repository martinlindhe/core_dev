<?
	require_once('config.php');
	if (!$l) die;	//user not logged in

	require('design_head.php');
	
	if(!empty($_POST['do'])) {
		storeFacts();
		echo 'Ändringar sparade!<br/>';
		require('design_foot.php');
		die;
	}

	$head = $user->getcontent($l['id_id'], 'user_head');

	//$drink = getset('', 'drink', 'mo', 'text_cmt ASC');
?>

	ÄNDRA FAKTA<br/>
	<br/>

	<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
	<input type="hidden" name="do" value="1"/>
	<b>Civilstånd:</b><br /><?=makeSelection('civil', $head['det_civil'][1])?><br/>
	<b>Attityd:</b><br /><?=makeSelection('attitude', $head['det_attitude'][1])?><br/>
	<b>Barn:</b><br /><?=makeSelection('children', $head['det_children'][1])?><br/>
	<b>Alkohol:</b><br /><?=makeSelection('alcohol', $head['det_alcohol'][1])?><br/>
	<b>Tobak:</b><br /><?=makeSelection('tobacco', $head['det_tobacco'][1])?><br/>
	<b>Sexliv:</b><br /><?=makeSelection('sex', $head['det_sex'][1])?><br/>
	<b>Musiksmak:</b><br /><?=makeSelection('music', $head['det_music'][1])?><br/>
	<b>Längd:</b><br /><?=makeSelection('length', $head['det_length'][1])?><br/>
	<b>Vikt:</b><br /><?=makeSelection('weight', $head['det_weight'][1])?><br/>
	<b>Vill ha:</b><br /><input type="text" class="txt" style="width: 185px;" name="det_wants" value="<?=@secureOUT($head['det_wants'][1])?>"/><br/>
	<input type="submit" value="Spara"/>
	</form>

<?
	require('design_foot.php');
?>