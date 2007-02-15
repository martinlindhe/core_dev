<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	include('design_user_head.php');

	$content = '';

	$newCatId = 0;
	if (!empty($_POST['catname'])) {
		$parentId = 0;
		if (!empty($_GET['parent'])) $parentId = $_GET['parent'];
		$newCatId = addTimedSubscriptionCategory($db, $_POST['catname'], $parentId);
	}

	$editcat = 0;
	if (!empty($_GET['editcat']) && is_numeric($_GET['editcat'])) $editcat = $_GET['editcat'];

	if (!empty($_POST['editcatname']) && $editcat) {
		updateTimedSubscriptionCategory($db, $editcat, $_POST['editcatname']);
	}


	if (!empty($_GET['delete'])) {
		$data = getTimedSubscriptionCategory($db, $_GET['delete']);
		if (!$data['parentId'] && !isset($_GET['confirmed'])) {
			$content .= 'You are trying to delete the category '.$data['categoryName'].', all associated options will also be deleted.<br>Are you sure you want to proceed?<br><br>';
			$content .= '<a href="'.$_SERVER['PHP_SELF'].'?delete='.$_GET['delete'].'&confirmed">Yes, I am sure</a><br><br>';
			$content .= '<a href="'.$_SERVER['PHP_SELF'].'?editcat='.$_GET['delete'].'">No, wrong button</a>';
		} else {
			deleteTimedSubscriptionCategory($db, $_GET['delete']);
			if ($data['parentId']) JS_Alert('Option deleted!');
			else JS_Alert('Category deleted!');
		}
	} else {

		if (!$editcat) {
			$content .= '<b>Administer Timed Subscriptions - Overview</b><br><br>';
	
			$list = getTimedSubscriptionCategories($db, 0); //root level categories
	
			for ($i=0; $i<count($list); $i++) {
				$content .= '<b>'.($i+1).'</b>: '.$list[$i]['categoryName'].'<br>';
	
				$sublist = getTimedSubscriptionCategories($db, $list[$i]['categoryId']);
				$content .= '<select>';
				$has_newitem = 0;
				for ($j=0; $j<count($sublist); $j++) {
					if ($newCatId == $sublist[$j]['categoryId']) {
						$content .= '<option selected>'.$sublist[$j]['categoryName'];
						$has_newitem = 1;
					} else {
						$content .= '<option>'.$sublist[$j]['categoryName'];
					}
				}
				$content .= '</select>';
				if ($has_newitem) $content .= ' <span class="msg_success">New entry saved!</span>';
				$content .= '<br>';
	
				$content .= 'Add option:'.
							'<form method="post" action="'.$_SERVER['PHP_SELF'].'?parent='.$list[$i]['categoryId'].'">'.
							'<input type="text" name="catname" size=40> '.
							'<input type="submit" class="button" value="Add">'.
							'</form>'.
							'&raquo; <a href="'.$_SERVER['PHP_SELF'].'?editcat='.$list[$i]['categoryId'].'">Edit category <b>'.$list[$i]['categoryName'].'</b></a><br>'.
							'<br><br>';
			}
	
			$content .= 'Add new category:<br>'.
				'<form method="post" action="'.$_SERVER['PHP_SELF'].'">'.
				'<input type="text" name="catname" size=40><br>'.
				'<input type="submit" class="button" value="Add category"><br>'.
				'</form>';
		} else {
			$title = getTimedSubscriptionCategoryName($db, $editcat);
			$content .= '<b>Administer Timed Subscriptions - Edit category "'.$title.'"</b><br><br>';
			
			$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?editcat='.$editcat.'">';
			$content .= 'Category title:<br>';
			$content .= '<input type="text" name="editcatname" value="'.$title.'" size=40> <input type="submit" class="button" value="Save"> ';
			if (!empty($_POST['editcatname'])) $content .= '<span class="msg_success">Change saved!</span>';
			$content .= '<br><br>';
			$content .= '&raquo; <a href="'.$_SERVER['PHP_SELF'].'?delete='.$editcat.'">Delete category</a><br><br>';
			$content .= '</form><br>';
			
			$content .= 'Category options:<br>';
			$list = getTimedSubscriptionCategories($db, $editcat);
			$content .= '<ul>';
			for ($i=0; $i<count($list); $i++) {
				$content .= '<li>'.$list[$i]['categoryName'];
				$content .= ' (<a href="'.$_SERVER['PHP_SELF'].'?editcat='.$editcat.'&delete='.$list[$i]['categoryId'].'">Delete</a>)';
			}
			$content .= '</ul>';
			
			$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?editcat='.$editcat.'&parent='.$editcat.'">';
			$content .= 'Add option:<br>';
			$content .= '<input type="text" name="catname" size=40> <input type="submit" class="button" value="Add"><br>';
			$content .= '</form><br>';
			
			$content .= '&raquo; <a href="'.$_SERVER['PHP_SELF'].'">Return to Timed Subscriptions overview</a>';
		}
	}
		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Edit timed subscriptions', $content);
		echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');
?>