<?
	include('include_all.php');	

	if (!$_SESSION['loggedIn']) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (isset($_POST['avatar'])) {
		saveSetting($db, $_SESSION['userId'], 'avatar', $_POST['avatar']);
		$setting_saved = true;
	}
	
	$choosen_avatar = getUserSetting($db, $_SESSION['userId'], 'avatar');

	include('design_head.php');
	include('design_user_head.php');

	$content  = 'Her kan du endre din avatar som vises p&aring; forumet.<br><br>';
	$content .= '<a href="user_edit.php">Klikk her</a> hvis du vil laste opp din egen avatar, som brukes i forumet.<br><br>';
	$content .= '<form name="editprof" method="post" action="'.$_SERVER['PHP_SELF'].'">';
		
	$cnt = 0;
	for ($i=0; $i<count($config['avatars']); $i++) {
		if ($config['avatars'][$i]) {
			$image = '<img src="avatars/'.$config['avatars'][$i].'" width=80 height=80>';

			$content .= makeRadioButton('editprof', 'avatar', $cnt, $i, $image, $choosen_avatar == $i);
			$cnt++;

			$content .= ' &nbsp; &nbsp; &nbsp; ';
		}
	}
		
	$content .= '<input type="submit" class="button" value="'.$config['text']['link_save'].'">';
	$content .= '</form>';
		
	echo '<div id="user_choose_avatar_content">';
	echo MakeBox('<a href="user_edit.php">Lag profil</a>|Velg avatar', $content);
	echo '</div>';

	include('design_user_foot.php');
	include('design_foot.php');
	
	if (isset($setting_saved)) {
		JS_Alert('Ditt valg av avatar har blitt lagret!');
	}
?>