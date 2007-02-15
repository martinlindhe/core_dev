<?
	include('include_all.php');

	if (!$_SESSION['loggedIn'] || empty($_GET['y']) || !is_numeric($_GET['y']) || empty($_GET['m']) || !is_numeric($_GET['m'])) {
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

	$show_year = $_GET['y'];
	$show_month = $_GET['m'];

		$content = 'Arkiv for '.strtolower($config['time']['month']['long'][$show_month]).' '.$show_year.'<br><br>';

		$list = getBlogsByMonth($db, $show, $show_month, $show_year);
		for ($i=0; $i<count($list); $i++) {
			$content .= formatShortDate($list[$i]['timeCreated']).' - <a href="blog_show.php?id='.$list[$i]['blogId'].'">'.$list[$i]['blogTitle'].'</a><br>';
		}
		
		if (!count($list)) {
			$content .= '<span class="objectCritical">Inga bloggar finns arkiverade fr&aring;n denna m&aring;nad.</span>';
		}

		echo '<div id="user_blog_content">';
		echo MakeBox('<a href="blogs.php">Blogger</a>', $content);
		echo '</div>';

	include('design_blog_foot.php');
	include('design_foot.php');
?>