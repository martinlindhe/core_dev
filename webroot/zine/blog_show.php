<?
	require_once('config.php');

	$session->requireLoggedIn();

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$blogId = $_GET['id'];
	$blog = getBlog($blogId);
	if (!$blog) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	include('design_head.php');
	
		$content = '<div style="float:right; width:150px;">';
			$c2 = '';
			if ($session->id == $blog['userId']) {
				$c2 .= '<a href="blog_edit.php?id='.$blogId.'">Edit blog</a><br><br>';
			} else {
				$c2 .= '<a href="blog_report.php?id='.$blogId.'">Report blog</a><br><br>';
			}

			$content .= $c2;
		$content .= '</div>';
		
		
		$content .= '<span style="font-size:16px; font-weight:bold;">'.$blog['blogTitle'].'</span><br>';
		if ($blog['categoryId']) $content .= '(in the category <b>'.$blog['categoryName'].'</b>)<br><br>';
		else $content .= ' (ingen kategori)<br><br>';

		$content .= 'Published '. $blog['timeCreated'].' by '.nameLink($blog['userId'], $blog['userName']).'<br>';
		if ($blog['timeUpdated']) {
			$content .= '<b>Updated '. $blog['timeUpdated'].'</b><br>';
		}

		$content .= '<br>';
		$content .= '<div class="blog">'.formatUserInputText($blog['blogBody']).'</div>';

		echo $content;

	include('design_foot.php');
?>