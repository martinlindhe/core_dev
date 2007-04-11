<?
	require_once('config.php');
	require('design_head.php');
	
	if(!empty($_POST['do'])) {
		storeFacts();
		echo 'Uppdaterat!';
	}

	$head = $user->getcontent($l['id_id'], 'user_head');


	//$drink = getset('', 'drink', 'mo', 'text_cmt ASC');

	//todo: gör om makeSelection!
?>

	ÄNDRA FAKTA<br/>
	<br/>

	<form action="" method="post">
	<input type="hidden" name="do" value="1"/>
	<b>Civilstånd:</b><br /><?=makeSelection('det_civil', getset('', 'civil', 'mo', 'text_cmt ASC'), $head['det_civil'][1])?><br/>
	<b>Attityd:</b><br /><?=makeSelection('det_attitude', getset('', 'attitude', 'mo', 'text_cmt ASC'), $head['det_attitude'][1])?><br/>
	<b>Barn:</b><br /><?=makeSelection('det_children', getset('', 'children', 'mo', 'text_cmt ASC'), $head['det_children'][1])?><br/>
	<b>Alkohol:</b><br /><?=makeSelection('det_alcohol', getset('', 'alcohol', 'mo', 'text_cmt ASC'), $head['det_alcohol'][1])?><br/>
	<b>Tobak:</b><br /><?=makeSelection('det_tobacco', getset('', 'tobacco', 'mo', 'text_cmt ASC'), $head['det_tobacco'][1])?><br/>
	<b>Sexliv:</b><br /><?=makeSelection('det_sex', getset('', 'sex', 'mo', 'text_cmt ASC'), $head['det_sex'][1])?><br/>
	<b>Musiksmak:</b><br /><?=makeSelection('det_music', getset('', 'music', 'mo', 'text_cmt ASC'), $head['det_music'][1])?><br/>
	<b>Längd:</b><br /><?=makeSelection('det_length', getset('', 'length', 'mo', 'text_cmt ASC'), $head['det_length'][1])?><br/>
	<b>Vill ha:</b><br /><input type="text" class="txt" style="width: 185px;" name="det_wants" value="<?=@secureOUT($head['det_wants'][1])?>"/><br/>
	<input type="submit" value="Spara"/>
	</form>

<?
	require('design_foot.php');
?>