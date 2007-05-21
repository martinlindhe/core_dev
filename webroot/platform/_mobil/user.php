<?
	require_once('config.php');
	if (!$l) die;	//user not logged in

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) $_id = $l['id_id'];
	else $_id = $_GET['id'];

	require('design_head.php');

	$head = $user->getcontent($_id, 'user_head');

	if ($_id == $l['id_id']) {
		echo 'DIN PROFIL<br/>';
		echo '<br/>';
		echo '<a href="relations.php"><img src="gfx/q_relations.png" alt="Relationer"/></a> ';
		echo '<a href="settings.php"><img src="gfx/q_settings.png" alt="Inställningar"/></a><br/>';
		//echo '<a href="settings.php">INSTÄLLNINGAR</a><br/>';
		echo '<a href="user_change_image.php">Ladda upp bild</a><br/><br/>';
	} else {
		$friends = areTheyFriends($l['id_id'], $_id);

		echo 'PROFIL - '.$user->getstringMobile($_id).'<br/>';
		echo '<br/>';
		if (!$friends) {
			echo '<a href="friends_create.php?id='.$_id.'">BLI VÄN</a> ';
			echo '<a href="friends_block.php?id='.$_id.'">BLOCKERA</a> ';
		} else {
			echo '<a href="friends.php?remove='.$_id.'">TA BORT RELATION</a> ';
		}
		echo '<a href="mail_new.php?id='.$_id.'">MAILA</a> ';
		//echo '<a href="gallery.php?id='.$_id.'">GALLERI</a> ';
		echo '<a href="friends.php?id='.$_id.'">VÄNNER</a> ';
		echo '<a href="gb.php?id='.$_id.'">GÄSTBOK</a>';
		echo '<br/><br/>';
	}

	echo '<div class="mid_content">';
	if (!empty($head['det_civil'][1])) echo '<b>Civilstånd:</b> '.$head['det_civil'][1].'<br/>';
	if (!empty($head['det_attitude'][1])) echo '<b>Attityd:</b> '.$head['det_attitude'][1].'<br/>';
	if (!empty($head['det_children'][1])) echo '<b>Barn:</b> '.$head['det_children'][1].'<br/>';
	if (!empty($head['det_alcohol'][1])) echo '<b>Alkohol:</b> '.$head['det_alcohol'][1].'<br/>';
	if (!empty($head['det_tobacco'][1])) echo '<b>Tobak:</b> '.$head['det_tobacco'][1].'<br/>';
	if (!empty($head['det_sex'][1])) echo '<b>Sexliv:</b> '.$head['det_sex'][1].'<br/>';
	if (!empty($head['det_music'][1])) echo '<b>Musiksmak:</b> '.$head['det_music'][1].'<br/>';
	if (!empty($head['det_length'][1])) echo '<b>Längd:</b> '.$head['det_length'][1].'<br/>';
	if (!empty($head['det_wants'][1])) echo '<b>Vill ha:</b> '.secureOUT($head['det_wants'][1]).'<br/>';
	echo '</div>';

	//echo 'Min persentation här...<br/>';

	require('design_foot.php');
?>