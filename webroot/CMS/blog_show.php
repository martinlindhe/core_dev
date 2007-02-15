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
	include('design_user_head.php');
	
		$content = '<div style="float:right; width:150px;">';
			$c2 = '';
			if ($_SESSION['userId'] == $blog['userId']) {
				$c2 .= '<a href="blog_edit.php?id='.$blogId.'">'.$config['text']['link_edit'].' bloggen</a><br><br>';
			} else {
				$c2 .= '<a href="blog_report.php?id='.$blogId.'">'.$config['text']['link_report'].' bloggen</a><br><br>';
			}

			$c2 .= '<a href="javascript:history.go(-1);">'.$config['text']['link_return'].'</a>';
			$content .= MakeBox('|Valg', $c2);
		$content .= '</div>';
		
		
		$content .= '<span style="font-size:16px; font-weight:bold;">'.$blog['blogTitle'].'</span><br>';
		if ($blog['categoryId']) $content .= '(i kategorien <b>'.$blog['categoryName'].'</b>)<br><br>';
		else $content .= ' (ingen kategori)<br><br>';

		$content .= 'Publisert '. getRelativeTimeLong($blog['timeCreated']).' av '.nameLink($blog['userId'], $blog['userName']).'<br>';
		if ($blog['timeUpdated']) {
			$content .= '<b>Oppdatert '. getRelativeTimeLong($blog['timeUpdated']).'</b><br>';
		}

		$content .= '<br>';
		$content .= '<div class="blog">'.formatUserInputText($blog['blogBody']).'</div>';
		//$content .= showFileAttachments($db, $blogId, FILETYPE_BLOG);

		echo '<div id="user_blog_content">';
		echo MakeBox('<a href="blogs.php">Blogger</a>|Vise blogg', $content);
		echo '</div>';

	include('design_blog_foot.php');
	include('design_foot.php');
?>