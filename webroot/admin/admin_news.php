<?
	require_once('find_config.php');

	$session->requireAdmin();

	require($project.'design_head.php');
	
	$list = getAllNews();
	foreach ($list as $row) {
		echo '<div class="newsitem">';
		if ($row['rss_enabled']) echo '<img src="/gfx/icon_rss.png" width="16" height="16" alt="RSS enabled" title="RSS enabled"/>';
		echo '<b>'.$row['title'].'</b> (by '.$row['creatorName'].', ';
		echo 'created '.$row['timeCreated'].')<br/>';
			
		if ($row['timeToPublish'] != $row['timeCreated']) {
			echo '<span class="critical">';
			if ($row['timeToPublish'] > time()) {
				echo 'Will be published '.$row['timeToPublish'].'<br/>';
			} else {
				echo 'Was published '.$row['timeToPublish'].'<br/>';
			}
			echo '</span>';
		}
		if ($row['timeEdited'] > $row['timeCreated']) {
			echo 'Updated '.$row['timeEdited'].' by '.$row['editorName'].'<br/>';
		}
		
		echo '<a href="admin_news_edit.php?id='.$row['newsId'].getProjectPath().'">Edit</a> ';
		echo '<a href="admin_news_delete.php?id='.$row['newsId'].getProjectPath().'">Delete</a>';
		echo '</div><br/>';
	}

	require($project.'design_foot.php');
?>