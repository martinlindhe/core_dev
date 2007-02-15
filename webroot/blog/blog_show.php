<?
	include('include_all.php');

	if (!$_SESSION['loggedIn'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$blogId = $_GET['id'];
	$blog = getBlog($db, $blogId);
	if (!$blog) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	include('design_head.php');
	
		
	echo '<span style="font-size:16px; font-weight:bold;">'.$blog['blogTitle'].'</span><br>';
	if ($blog['categoryId']) echo '(i kategorin <b>'.$blog['categoryName'].'</b>)<br><br>';
	else echo ' (ingen kategori)<br><br>';

	echo 'Publicerad '. $blog['timeCreated'].' av '.$blog['userName'].'<br>';
	if ($blog['timeUpdated']) {
		echo '<b>Senast redigerad '. $blog['timeUpdated'].'</b><br>';
	}

	echo '<br>';
	
	if (get_magic_quotes_gpc()) {
		$blog['blogBody'] = stripslashes($blog['blogBody']);
	}
	
	echo '<div class="blog">'.formatUserInputText($blog['blogBody'], false).'</div>';

	if ($_SESSION['loggedIn']) {
		echo '<br><br><a href="blog_edit.php?id='.$blogId.'">'.$config['text']['link_edit'].' bloggen</a><br><br>';
	}


	include('design_foot.php');
?>