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
				echo '<a href="'.$_SERVER['PHP_SELF'].'?delete='.$_GET['delete'].'&amp;confirmed'.getProjectPath().'">Yes</a><br/><br/>';
				echo '<a href="'.$_SERVER['PHP_SELF'].getProjectPath(false).'">No</a>';

				include($project.'design_foot.php');
				die;
			
		} else {
			removeNews($_GET['delete']);
		}
	}

	
	include($project.'design_head.php');
	
	if (!empty($_GET['edit'])) {

		$item = getNewsItem($_GET['edit']);

		echo '<h1>Edit news article</h1>';
		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?edit='.$_GET['edit'].getProjectPath().'">';
		echo '<input type="hidden" name="rss" value="0"/>';
		echo 'Title: <input type="text" name="title" size="50" value="'.$item['title'].'"/><br/>';
		echo 'Text:<br/>';
		echo '<textarea name="body" cols="60" rows="16">'.$item['body'].'</textarea><br/>';
		echo '<input name="rss" id="rss_check" type="checkbox" class="checkbox" value="1"'.($item['rss_enabled']?' checked="checked"':'').'/>';
		echo '<label for="rss_check">';
		echo '<img src="/gfx/icon_rss.png" width="16" height="16" alt="RSS enabled" title="RSS enabled"/>';
		echo 'Include this news in the RSS feed</label><br/><br/>';
		echo 'Time for publication:<br/>';
		echo '<input type="text" name="publish" value="'.$item['timeToPublish'].'"/> ';
		echo '<input type="submit" class="button" value="Save changes"/><br/>';
		echo '</form><br/>';
		
		echo '<a href="admin_news.php?id='.$item['newsId'].getProjectPath().'">Show this news</a><br/>';
		echo '<a href="'.$_SERVER['PHP_SELF'].'?delete='.$item['newsId'].getProjectPath().'">Delete this news</a>';

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
			
			echo '<a href="'.$_SERVER['PHP_SELF'].'?edit='.$row['newsId'].getProjectPath().'">Edit</a> ';
			echo '<a href="'.$_SERVER['PHP_SELF'].'?delete='.$row['newsId'].getProjectPath().'">Delete</a>';
			echo '</div><br/>';
		}
	}

	include($project.'design_foot.php');
?>