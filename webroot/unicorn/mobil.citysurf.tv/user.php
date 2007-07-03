<?
	require_once('config.php');
	if (!$l) die;	//user not logged in
	
	function mobilePresImage($_id)
	{
		global $sql, $user;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT * FROM s_user WHERE id_id='.$_id;
		$tmp = $sql->query($q, 0, 1);
		$s = $tmp[0];

		$id = $s['id_id'];
		$picid = $s['u_picid'];
		$sex = $s['u_sex'];
		$picd = $s['u_picd'];
		$valid = $s['u_picvalid'];
		$big = 0;
		
		if ($valid) {
			echo '<img src="http://www.citysurf.tv/'.UPLA.'images/'.$picd.'/'.$id.$picid.'_2.jpg" ';
		} else {
			echo '<img src="http://www.citysurf.tv/_objects/u_noimg'.$sex.'_2.gif" ';
		}
		echo 'alt="Thumb" style="width: 50px; height: 50px;" />';
	}

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) $_id = $l['id_id'];
	else $_id = $_GET['id'];

	require('design_head.php');

	$head = $user->getcontent($_id, 'user_head');

	if ($_id == $l['id_id']) {
		echo '<div class="h_profil"></div>';

		echo '<a href="relations.php"><img src="gfx/btn_friends.png" alt="Vänner"/></a> ';
		echo '<a href="settings.php"><img src="gfx/btn_facts.png" alt="Inställningar"/></a><br/>';
		//echo '<a href="settings.php">INSTÄLLNINGAR</a><br/>';
		//echo '<a href="user_change_image.php">Ladda upp bild</a><br/><br/>';
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
		//echo '<a href="friends.php?id='.$_id.'">VÄNNER</a> ';
		echo '<a href="gb.php?id='.$_id.'">GÄSTBOK</a>';
		echo '<br/><br/>';
	}

	$interests = '';

	if (!empty($head['det_civil'][1])) $interests .= '<b>Civilstånd:</b> '.$head['det_civil'][1].'<br/>';
	if (!empty($head['det_attitude'][1])) $interests .= '<b>Attityd:</b> '.$head['det_attitude'][1].'<br/>';
	if (!empty($head['det_children'][1])) $interests .= '<b>Barn:</b> '.$head['det_children'][1].'<br/>';
	if (!empty($head['det_alcohol'][1])) $interests .= '<b>Alkohol:</b> '.$head['det_alcohol'][1].'<br/>';
	if (!empty($head['det_tobacco'][1])) $interests .= '<b>Tobak:</b> '.$head['det_tobacco'][1].'<br/>';
	if (!empty($head['det_sex'][1])) $interests .= '<b>Sexliv:</b> '.$head['det_sex'][1].'<br/>';
	if (!empty($head['det_music'][1])) $interests .= '<b>Musiksmak:</b> '.$head['det_music'][1].'<br/>';
	if (!empty($head['det_length'][1])) $interests .= '<b>Längd:</b> '.$head['det_length'][1].'<br/>';
	if (!empty($head['det_weight'][1])) $interests .= '<b>Vikt:</b> '.$head['det_weight'][1].'<br/>';
	if (!empty($head['det_wants'][1])) $interests .= '<b>Vill ha:</b> '.secureOUT($head['det_wants'][1]).'<br/>';

	echo mobilePresImage($_id);

	echo '<div class="mid_content">';
		if ($interests) {
			echo $interests;
		} else {
			echo 'Uppgifter saknas';
		}
	echo '</div>';

	//echo 'Min persentation här...<br/>';

	require('design_foot.php');
?>