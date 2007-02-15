<?
	include('include_all.php');

	if (!$_SESSION['loggedIn']) {
		header('Location: '.$config['start_page']);
		die;
	}

	$setting_saved = false;
	if (isset($_POST['presentation'])) {
		saveSetting($db, $_SESSION['userId'], 'presentation', $_POST['presentation']);
		$setting_saved = true;
	}

	$profil = getUserSetting($db, $_SESSION['userId'], 'presentation');

	include('design_head.php');
	include('design_user_head.php');

		$content  = 'Her kan du redigere din presentasjon som vises til andre brukere.<br><br>';
		$content .= '<form name="editprof" method="post" action="'.$_SERVER['PHP_SELF'].'">';
		$content .= '<textarea cols=40 rows=28 name="presentation">'.$profil.'</textarea><br><br>';
		$content .= '<input type="submit" class="button" value="'.$config['text']['link_save'].'">';
		$content .= '</form><br>';
		
		$content .= '<a href="user_show.php?id='.$_SESSION['userId'].'">Vise siden</a>';

		echo '<div id="user_misc_content">';
		echo MakeBox('<a href="user_edit.php">Lag profil</a>|Endre presentasjon', $content);
		echo '</div>';

	include('design_user_foot.php');
	include('design_foot.php');
	
	if ($setting_saved) {
		JS_Alert('Presentasjonen er lagret!');
	}
?>

<script type="text/javascript">
document.editprof.presentation.focus();
</script>