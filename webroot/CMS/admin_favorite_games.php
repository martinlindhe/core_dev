<?
	include_once('include_all.php');

	if (!$_SESSION['isAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (!empty($_GET['remove'])) {
		$data = getCategory($db, CATEGORY_FAVORITEGAMES, $_GET['remove']);
		$removed_category = $data['categoryName'];
		removeCategory($db, CATEGORY_FAVORITEGAMES, $_GET['remove']);
	}
	
	if (!empty($_POST['category'])) {
		addCategory($db, CATEGORY_FAVORITEGAMES, $_POST['category']);
		$saved_category = $_POST['category'];
	}

	include('design_head.php');
	include('design_user_head.php');
	
		$content = 'H&auml;r kan du skapa spelkategorier som anv&auml;ndarna sedan kan v&auml;lja mellan.<br><br>';

		if (isset($saved_category)) {
			$content .= '<span class="objectCritical">Kategorin '.$saved_category.' sparad</span><br><br>';
		}

		if (isset($removed_category)) {
			$content .= '<span class="objectCritical">Kategorin '.$removed_category.' raderad</span><br><br>';
		}

		$list = getCategories($db, CATEGORY_FAVORITEGAMES);
		if (count($list)) {
			$content .= 'Dessa kategorier finns nu att v&auml;lja mellan:<br>';
			for ($i=0; $i<count($list); $i++) {
				$content .= '<b>'.$list[$i]['categoryName'].'</b> (<a href="'.$_SERVER['PHP_SELF'].'?remove='.$list[$i]['categoryId'].'">radera</a>)<br>';
			}
			$content .= '<br>';
		}

		$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
		$content .= 'Skapa en ny kategori:<br>';
		$content .= '<input type="text" name="category" size=45><br><br>';
		$content .= '<input type="submit" class="button" value="L&auml;gg till">';
		$content .= '</form>';

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Redigera favoritspel', $content);
		echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');

?>