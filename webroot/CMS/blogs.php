<?
	include('include_all.php');

	if (!$_SESSION['loggedIn']) {
		header('Location: '.$config['start_page']);
		die;
	}

	$show = '';
	if (isset($_GET['id'])) {
		$show = $_GET['id'];
		$showname = getUserName($db, $show);
		if (!$showname) {
			header('Location: '.$config['start_page']);
			die;
		}
	} else {
		$show = $_SESSION['userId'];
		$showname = $_SESSION['userName'];
	}

	if (substr($showname, -1) == 's') {
		$niceshowname = $showname."'";
	} else {
		$niceshowname = $showname.'s';
	}

	if ($show == $_SESSION['userId']) {
		setUserStatus($db, 'Bloggar');
	} else {
		setUserStatus($db, 'L&auml;ser '.$niceshowname.' bloggar');
	}

	include('design_head.php');
	include('design_user_head.php');

		$list = getBlogsByCategory($db, $_SESSION['userId']);
		$content = '<div style="float:right; width:150px;">';
			$c2  = '<a href="blog_new.php">Lage ny blogg</a><br><br>';
			$c2 .= '<a href="blog_categories.php">Lage ny kategori</a>';
			$content .= MakeBox('|Valg', $c2);
		$content .= '</div>';

		$content .= 'Mine blogger:<br>';

		$shown_category = false;
		for ($i=0; $i<count($list); $i++) {
			if ($list[$i]['categoryId'] != $shown_category) {
				$catName = $list[$i]['categoryName'];
				if (!$catName) $catName = 'Okategoriserade bloggar';
				$content .= '<br><b>'.$catName.'</b><br>';
				$shown_category = $list[$i]['categoryId'];
			}
			$content .= formatShortDate($list[$i]['timeCreated']).' - <a href="blog_show.php?id='.$list[$i]['blogId'].'">'.$list[$i]['blogTitle'].'</a><br>';
		}
		$content .= '<br>';
		
		$content .= 'De siste publiserte blogger:<br>';
		$list = getBlogsNewest($db, 5);
		for ($i=0; $i<count($list); $i++) {
			$content .= formatShortDate($list[$i]['timeCreated']).' - <a href="blog_show.php?id='.$list[$i]['blogId'].'">'.$list[$i]['blogTitle'].'</a> ';
			$content .= 'av '.nameLink($list[$i]['userId'], $list[$i]['userName']).'<br>';
		}

		echo '<div id="user_blog_content">';
		echo MakeBox('<a href="blogs.php">Blogger</a>', $content);
		echo '</div>';

	include('design_blog_foot.php');
	include('design_foot.php');
?>