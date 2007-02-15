<?
	include('include_all.php');

	if (!$_SESSION['loggedIn']) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (isset($_POST['name'])) {
		$global = false;
		if (!empty($_POST['global'])) $global = true;
		$categoryId = addCategory($db, CATEGORY_BLOGS, $_POST['name'], $global);
		if ($categoryId) {
			header('Location: index.php');
			die;
		} else {
			JS_Alert('Problems creating blog category!');
		}
	}

	include('design_head.php');

	echo 'H&auml;r kan du skapa nya blogg-kategorier.<br><br>';
	echo 'Dessa kategorier finns sedan tidigare.<br>';
	echo '<select>'.getCategoriesHTML_Options($db, CATEGORY_BLOGS).'</select>';

	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	echo 'Namn p&aring; kategorin.<br><input type="text" name="name" size=42 maxlength=40><br><br>';
	if ($_SESSION['isAdmin']) {
		echo '<input type="checkbox" name="global" value="1" checked>G&ouml;r denna kategori global (tillg&auml;nglig f&ouml;r alla)<br><br>';
	}
	echo '<input type="submit" class="button" value="Spara">';
	echo '</form><br>';

	include('design_foot.php');
?>