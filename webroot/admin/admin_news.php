<?
	require_once('find_config.php');

	$session->requireAdmin();

	if (!empty($_GET['edit']) && !empty($_POST['title'])) {
		updateNews($_GET['edit'], $_POST['title'], $_POST['body'], strtotime($_POST['publish']), $_POST['rss']);
	}

	if (!empty($_GET['delete']) && is_numeric($_GET['delete'])) {
		if (!isset($_GET['confirmed'])) {
			
				include($project.'design_head.php');

				echo 'Are you sure you wish to delete this news entry?<br/><br/>';
				echo '<a href="'.$_SERVER['PHP_SELF'].'?delete='.$_GET['delete'].'&amp;confirmed">Yes</a><br/><br/>';
				echo '<a href="'.$_SERVER['PHP_SELF'].'">No</a>';

				include($project.'design_foot.php');
				die;
			
		} else {
			removeNews($_GET['delete']);
		}
	}

	
	include($project.'design_head.php');
	
	if (!empty($_GET['edit'])) {

		$item = getNewsItem($_GET['edit']);

		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?edit='.$_GET['edit'].getProjectPath().'">';
		echo '<input type="hidden" name="rss" value="0"/>';
		echo 'Edit news:<br/>';
		echo 'Title: <input type="text" name="title" value="'.$item['title'].'"/><br/>';
		echo 'Text:<br/>';
		echo '<textarea name="body" cols="40" rows="6">'.$item['body'].'</textarea><br/>';
		echo '<input name="rss" id="rss_check" type="checkbox" class="checkbox" value="1"'.($item['rss_enabled']?' checked="checked"':'').'/>';
		echo '<label for="rss_check">';
		echo '<img src="/gfx/icon_rss.png" width="16" height="16" alt="RSS enabled" title="RSS enabled"/>';
		echo 'Include this news in the RSS feed</label><br/><br/>';
		echo 'Time for publication:<br/>';
		echo '<input type="text" name="publish" value="'.$item['timeToPublish'].'"/> ';
		echo '<input type="submit" class="button" value="Save changes"/><br/>';
		echo '</form><br/>';
		
		echo '<a href="news.php?id='.$item['newsId'].'">Visa denna nyhet</a><br/>';
		echo '<a href="'.$_SERVER['PHP_SELF'].'?delete='.$item['newsId'].'">Radera denna nyhet</a>';

	} else {

		$list = getAllNews();
		foreach ($list as $row) {
			echo '<div class="newsitem">';
			if ($row['rss_enabled']) echo '<img src="/gfx/icon_rss.png" width="16" height="16" alt="RSS enabled" title="RSS enabled"/>';
			echo '<b>'.$row['title'].'</b> (av '.$row['creatorName'].', ';
			echo 'skapad '.$row['timeCreated'].')<br/>';
			
			if ($row['timeToPublish'] != $row['timeCreated']) {
				echo '<span class="critical">';
				if ($row['timeToPublish'] > time()) {
					echo 'Ska publiceras '.$row['timeToPublish'].'<br/>';
				} else {
					echo 'Publicerades '.$row['timeToPublish'].'<br/>';
				}
				echo '</span>';
			}
			if ($row['timeEdited'] > $row['timeCreated']) {
				echo 'Uppdaterad '.$row['timeEdited'].' av '.$row['editorId'].'<br/>';
			}
			
			echo '<a href="'.$_SERVER['PHP_SELF'].'?edit='.$row['newsId'].getProjectPath().'">Edit</a><br/>';
			echo '<a href="'.$_SERVER['PHP_SELF'].'?delete='.$row['newsId'].getProjectPath().'">Remove</a>';
			echo '</div>';
			echo '<br/>';
		}
		
		echo '<a href="admin_news_add.php'.getProjectPath(false).'">Add news</a><br/>';

	}

	include($project.'design_foot.php');
?>