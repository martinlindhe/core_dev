<?
	require_once('config.php');

	$session->requireAdmin();

	if (!empty($_GET['edit']) && !empty($_POST['title'])) {
		updateNews($_GET['edit'], $_POST['title'], $_POST['body'], strtotime($_POST['publish']), $_POST['rss']);
	} else {
		if (!empty($_POST['title']) && !empty($_POST['body']) && !empty($_POST['publish']) ) {
			addNews($_POST['title'], $_POST['body'], $_POST['publish'], $_POST['rss']);
		}
	}

	if (!empty($_GET['delete']) && is_numeric($_GET['delete'])) {
		if (!isset($_GET['confirmed'])) {
			
				include('design_head.php');

				echo 'Are you sure you wish to delete this news entry?<br/><br/>';
				echo '<a href="'.$_SERVER['PHP_SELF'].'?delete='.$_GET['delete'].'&amp;confirmed">Yes</a><br/><br/>';
				echo '<a href="'.$_SERVER['PHP_SELF'].'">No</a>';

				include('design_foot.php');
				die;
			
		} else {
			removeNews($_GET['delete']);
		}
	}

	
	include('design_head.php');
	
	if (!empty($_GET['edit'])) {

		$item = getNewsItem($_GET['edit']);

		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?edit='.$_GET['edit'].'">';
		echo '<input type="hidden" name="rss" value="0"/>';
		echo 'Redigera nyhet:<br/>';
		echo 'Titel: <input type="text" name="title" value="'.$item['title'].'"/><br/>';
		echo 'Text:<br/>';
		echo '<textarea name="body" cols=40 rows=6>'.$item['body'].'</textarea><br/>';
		echo '<input name="rss" id="rss_check" type="checkbox" class="checkbox" value="1"';
		if ($item['rss_enabled']) echo ' checked>';
		else echo '/>';
		echo '<label for="rss_check">Inkludera denna nyhet i RSS-sändningen</label><br/><br/>';
		echo 'Tid f&ouml;r publicering:<br/>';
		echo '<input type="text" name="publish" value="'.$item['timeToPublish'].'"/> ';
		echo '<input type="submit" class="button" value="Save changes"/><br/>';
		echo '</form><br/>';
		
		echo '<a href="news.php?id='.$item['newsId'].'">Visa denna nyhet</a><br/>';
		echo '<a href="'.$_SERVER['PHP_SELF'].'?delete='.$item['newsId'].'">Radera denna nyhet</a>';

	} else {

		$list = getAllNews();
		for ($i=0; $i<count($list); $i++) {
			echo '<div class="newsitem">';
			if ($list[$i]['rss_enabled']) echo '<img src="icons/rss_icon.png" width="16" height="16" alt="RSS enabled" title="RSS enabled"/>';
			echo '<b>'.$list[$i]['title'].'</b> (av '.$list[$i]['creatorName'].', ';
			echo 'skapad '.$list[$i]['timeCreated'].')<br/>';
			echo $list[$i]['body'].'<br/>';
			
			if ($list[$i]['timeToPublish'] != $list[$i]['timeCreated']) {
				echo '<span class="critical">';
				if ($list[$i]['timeToPublish'] > time()) {
					echo 'Ska publiceras '.$list[$i]['timeToPublish'].'<br/>';
				} else {
					echo 'Publicerades '.$list[$i]['timeToPublish'].'<br/>';
				}
				echo '</span>';
			}
			if ($list[$i]['timeEdited'] > $list[$i]['timeCreated']) {
				echo 'Uppdaterad '.$list[$i]['timeEdited'].'<br/>';
			}
			
			echo '<a href="'.$_SERVER['PHP_SELF'].'?edit='.$list[$i]['newsId'].'">Edit</a><br/>';
			echo '<a href="'.$_SERVER['PHP_SELF'].'?delete='.$list[$i]['newsId'].'">Remove</a>';
			echo '</div>';
			echo '<br/>';
		}
		echo '<br/>';
	
		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
		echo '<input type="hidden" name="rss" value="0"/>';
		echo 'Lägg till nyhet:<br/>';
		echo 'Titel: <input type="text" name="title"/><br/>';
		echo 'Text:<br/>';
		echo '<textarea name="body" cols="40" rows="6"></textarea><br/>';
		echo '<input name="rss" id="rss_check" type="checkbox" class="checkbox" value="1" checked="checked"/>';
		echo '<label for="rss_check">Inkludera denna nyhet i RSS-sändningen</label><br/><br/>';
		echo 'Tid för publicering:<br/>';
		echo '<input type="text" name="publish" value="'.date('Y-m-d H:i').'"/> ';
		echo '<input type="submit" class="button" value="Lägg till"/><br/>';
		echo '</form>';
		echo '<br/><br/>';
	}

	include('design_foot.php');
?>