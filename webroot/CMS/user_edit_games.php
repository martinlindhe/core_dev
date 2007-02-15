<?
	include('include_all.php');

	if (!$_SESSION['loggedIn']) {
		header('Location: '.$config['start_page']);
		die;
	}

	/* Save changes */
	$list = getCategories($db, CATEGORY_FAVORITEGAMES);
	$saved_changes = false;
	for ($i=0; $i<count($list); $i++) {
		if (isset($_POST['game_'.$list[$i]['categoryId']])) {
			$value = $_POST['game_'.$list[$i]['categoryId']];
			if ($value) {
				setFavoriteGame($db, $list[$i]['categoryId']);
			} else {
				clearFavoriteGame($db, $list[$i]['categoryId']);
			}
			$saved_changes = true;
		}
	}
	
	if ($saved_changes) {
		header('Location: user_edit.php');
		die;
	}

	include('design_head.php');
	include('design_user_head.php');

	$list = getFavoriteGameCategoriesWithMyOptions($db);

	$content = 'Her kan du velge hvilken type spill du liker best (opp till 4 stycken).<br><br>';
	$content .= 'Dine valg vil bli vist p&aring; din side.<br><br>';

	if (count($list)) {
		$content .= '<form method="post" name="favgames" action="'.$_SERVER['PHP_SELF'].'">';
		for ($i=0; $i<count($list); $i++) {
				
			//$content .= makeCheckBox('favgames', 'game_'.$list[$i]['categoryId'], "1", $list[$i]['categoryName'], $list[$i]['selected'] != '').'<br>';
				
			$content .= '<input type="hidden" name="game_'.$list[$i]['categoryId'].'">';
			$content .= '<input name="game_'.$list[$i]['categoryId'].'" value="1" type="checkbox" class="checkbox"';
			if ($list[$i]['selected']) $content .= ' checked';
			$content .= '> '.$list[$i]['categoryName'].'<br>';
		}
		$content .= '<br><input type="submit" class="button" value="Lagre"></form>';
	} else {
		$content .= '<span class="objectCritical">Det er ingen kategorier og velge fra.</span>';
	}

	echo '<div id="user_misc_content">';
	echo MakeBox('<a href="user_edit.php">Lag profil</a>|Velg spill jeg liker', $content);
	echo '</div>';

	include('design_user_foot.php');
	include('design_foot.php');
?>