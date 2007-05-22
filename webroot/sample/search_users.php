<?
	require_once('config.php');

	require('design_head.php');

	echo 'Search for users<br/><br/>';

	if (isset($_POST['c'])) {
		//$list = getUserSearchResult($db, $_POST);
		$list = getUserSearchResultOnNickname($db, $_POST['c']);

		echo 'Search result for "'.$_POST['c'].'", ';
		echo (count($list)?count($list).' hits':'1 hit');
		echo '<br/><br/>';

		$ids = '';
		for ($i=0; $i<count($list); $i++) {
			echo nameLink($list[$i]['userId'], $list[$i]['userName']).'<br/>';
			$ids .= $list[$i]['userId'].';';
		}
		echo '<br/>';
		echo '<a href="'.$_SERVER['PHP_SELF'].'">New search</a><br/>';
		/*
		if ($_SESSION['isAdmin']) {
			echo '<a href="mess_multi.php?ids='.$ids.'">Skicka meddelande till alla tr&auml;ffar</a><br>';
			echo '<a href="mail_multi.php?ids='.$ids.'">Skicka mail till alla tr&auml;ffar</a><br>';
		}*/
		echo '<br/>';

	} else if (isset($_GET['l']) && $_GET['l']) {
		/* Lista alla användare som börjar på en bokstav */
		
		//$list = searchUsernameBeginsWith($db, $_GET['l']);
		$list = searchNicknameBeginsWith($db, $_GET['l']);

		echo 'Search result for users beginning with "'.$_GET['l'].'", ';

		echo (count($list)?count($list).' hits':'1 hit');
		echo '<br/><br/>';
		
		for ($i=0; $i<count($list); $i++) {
			echo nameLink($list[$i]['userId'], $list[$i]['userName']).'<br>';
		}

		echo '<br>';
		echo '<a href="'.$_SERVER['PHP_SELF'].'">New search</a><br/>';

	} else {

		echo 'Sort users beginning with: ';
		for ($i=ord('A'); $i<=ord('Z'); $i++) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?l='.chr($i).'">'.chr($i).'</a> ';
		}
		echo '<br/><br/>';
		
		echo'<form name="searchusers" method="post" action="'.$_SERVER['PHP_SELF'].'">';

		echo 'Free-text: ';
		echo '<input type="text" name="c" maxlength="20" size="20"/>';

		$list = getUserdataFields();
		foreach ($list as $row) {
			if (!$row['private'] || $session->isAdmin) {

				switch ($row['fieldType']) {
					case USERDATA_TYPE_CHECKBOX:
						echo $row['fieldName'].': ';
						echo '<input name="'.$row['fieldId'].'" type="checkbox" value="1" class="checkbox"/> Require';
						break;

					case USERDATA_TYPE_RADIO:
						echo $row['fieldName'].': ';
						$sublist = getCategoriesByOwner(CATEGORY_USERDATA, $row['fieldId']);

						foreach ($sublist as $cat) {
							echo '<input type="radio" name="'.$row['fieldId'].'" id="radio_'.$cat['categoryId'].'" value="'.$cat['categoryId'].'" class="radio"/>';
							echo ' <label for="radio_'.$cat['categoryId'].'">'.$cat['categoryName'].'</label>';
						}
						break;

					case USERDATA_TYPE_SELECT:
						if ($row['fieldName'] == $config['settings']['default_theme']) break;
						echo getCategoriesSelect(CATEGORY_USERDATA, $row['fieldId'], 'userdata_'.$row['fieldId']);
						break;
				}
				echo '<br/>';
			}
		}

		echo '<input type="submit" class="button" value="Search"/>';
		echo '</form>';
	}
?>
<script type="text/javascript">
if (document.searchusers) document.searchusers.c.focus();
</script>
<?
	require('design_foot.php');
?>
