<?
	require('config.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) $_id = $l['id_id'];
	else $_id = $_GET['id'];

	require('design_head.php');

	$user_data = $user->getuser($_id);
	$head = $user->getcontent($_id, 'user_head');

	if ($_id == $l['id_id']) {
		echo 'DIN PROFIL<br/>';
		echo '<br/>';
		echo '<a href="mail.php?id='.$_id.'">MAIL</a> ';
		//echo '<a href="gallery.php?id='.$_id.'">GALLERI</a> ';
		echo '<a href="friends.php?id='.$_id.'">VÄNNER</a> ';
		echo '<a href="guestbook.php?id='.$_id.'">GÄSTBOK</a>';
		echo '<br/>';
		echo '<a href="settings.php">INSTÄLLNINGAR</a>';
	} else {
		$friends = areTheyFriends($l['id_id'], $_id);
		
		
		echo 'PROFIL - <b>'.$user_data['u_alias'].'</b> kön &amp; ålder (onlinestatus?)<br/>';
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
		echo '<a href="guestbook.php?id='.$_id.'">GÄSTBOK</a>';
	}
	echo '<br/><br/>';

	echo '(MIN BILD HÄR)<br/>';
	echo '<br/>';

	echo 'Mina fakta:<br/>';
	echo '<b>Civilstånd:</b>: '.	(!empty($head['det_civil'][1])		? $head['det_civil'][1]:'obesvarat').'<br/>';
	echo '<b>Attityd:</b>: '.			(!empty($head['det_attitude'][1])	? $head['det_attitude'][1]:'obesvarat').'<br/>';
	echo '<b>Barn:</b>: '.				(!empty($head['det_children'][1])	? $head['det_children'][1]:'obesvarat').'<br/>';
	echo '<b>Alkohol:</b>: '.			(!empty($head['det_alcohol'][1])	? $head['det_alcohol'][1]:'obesvarat').'<br/>';
	echo '<b>Tobak:</b>: '.				(!empty($head['det_tobacco'][1])	? $head['det_tobacco'][1]:'obesvarat').'<br/>';
	echo '<b>Sexliv:</b>: '.			(!empty($head['det_sex'][1])			?	$head['det_sex'][1]:'obesvarat').'<br/>';
	echo '<b>Musiksmak:</b>: '.		(!empty($head['det_music'][1])		? $head['det_music'][1]:'obesvarat').'<br/>';
	echo '<b>Längd:</b>: '.				(!empty($head['det_length'][1])		? $head['det_length'][1]:'obesvarat').'<br/>';
	echo '<b>Vill ha:</b>: '.			(!empty($head['det_wants'][1])		? secureOUT($head['det_wants'][1]):'obesvarat').'<br/>';

	//echo 'Min persentation här...<br/>';

	require('design_foot.php');
?>