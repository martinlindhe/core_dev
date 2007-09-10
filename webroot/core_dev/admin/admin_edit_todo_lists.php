<?
	require_once('find_config.php');
	$session->requireSuperAdmin();

	if (!empty($_POST['cat_name'])) {
		$parentId = 0;
		if (!empty($_GET['parent'])) $parentId = $_GET['parent'];
		addTodoCategory($db, $_POST['cat_name'], $parentId);
	}

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	echo 'Admin edit todo lists<br/><br/>';

	if (!empty($_GET['delete'])) {

		$catname = getTodoCategoryName($db, $_GET['delete']);

		if (!isset($_GET['confirmed'])) {
			
			$subcats = getTodoCategoryCount($db, $_GET['delete']);

			if (!$subcats) {
				$pr_cnt = getTodoCategoryItemsCount($db, $_GET['delete']);
				
				$content .= 'Are you sure you wish to delete the todo list category <b>'.$catname.'</b>?<br><br>';
				if ($pr_cnt) $content .= '<b>'.$pr_cnt.' associated PR\'s will also be deleted.</b><br><br>';
				$content .= '<a href="'.$_SERVER['PHP_SELF'].'?delete='.$_GET['delete'].'&confirmed">Yes, I am sure</a><br><br>';
				$content .= '<a href="'.$_SERVER['PHP_SELF'].'">No, wrong button</a>';
			} else {
				$content .= 'You tried to delete the todo list category <b>'.$catname.'</b>, but it contains '.$subcats.'. Please delete those first!<br><br>';
				$content .= '<a href="'.$_SERVER['PHP_SELF'].'">Go back</a>';
			}

		} else {
			deleteTodoCategory($db, $_GET['delete']);
			deleteTodoItems($db, $_GET['delete']);
			JS_Alert('Todo list category '.$catname.' successfully deleted!');
		}
	} else {	
		$list = getTodoCategories($db, 0);
		
		for ($i=0; $i<count($list); $i++) {
			$content .= ($i+1).' - '. $list[$i]['categoryName'];
			$content .= ' <i>created by '.getUserName($db, $list[$i]['creatorId']).' '.getRelativeTimeLong($list[$i]['createdTime']).'</i>';
			$content .= ' <a href="'.$_SERVER['PHP_SELF'].'?delete='.$list[$i]['categoryId'].'">delete</a><br>';
	
			$sublist = getTodoCategories($db, $list[$i]['categoryId']);
			for ($j=0; $j<count($sublist); $j++) {
				$content .= '&nbsp;&nbsp;&nbsp;&nbsp;';
				$content .= ($i+1).':'.($j+1).' - '. $sublist[$j]['categoryName'];
				$content .= ' <i>created by '.getUserName($db, $sublist[$j]['creatorId']).' '.getRelativeTimeLong($sublist[$j]['createdTime']).'</i>';
				$content .= ' <a href="'.$_SERVER['PHP_SELF'].'?delete='.$sublist[$j]['categoryId'].'">delete</a><br>';
			}
	
			$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?parent='.$list[$i]['categoryId'].'" name="subcat'.$i.'">';
			$content .= '&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="cat_name"> <input type="submit" class="button" value="Add subcategory">';
			$content .= '</form><br>';
		}
	
	
	
		$content .= 'Create a new category:<br>';
		$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'" name="newcat">';
		$content .= 'Name: <input type="text" name="cat_name"> ';
		$content .= '<input type="submit" class="button" value="Add category"><br>';
		$content .= '</form>';
		
		$content .= '<br><br>';
		$content .= '<a href="admin_current_work.php">Back to current work</a>';
	}

	echo $content;

	require($project.'design_foot.php');
?>