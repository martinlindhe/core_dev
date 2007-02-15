<?
	include_once('include_all.php');

	if (!$_SESSION['isAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (!empty($_GET['edit']) && !empty($_POST['title'])) {
		updateNews($db, $_GET['edit'], $_POST['title'], $_POST['body'], strtotime($_POST['publish']), $_POST['rss']);
	} else {
		if (!empty($_POST['title']) && !empty($_POST['body']) && !empty($_POST['publish']) ) {
			addNews($db, $_SESSION['userId'], $_POST['title'], $_POST['body'], strtotime($_POST['publish']), $_POST['rss']);
		}
	}

	if (!empty($_GET['delete']) && is_numeric($_GET['delete'])) {
		if (!isset($_GET['confirmed'])) {
			
				include('design_head.php');
				include('design_user_head.php');

				$content = 'Are you sure you wish to delete this news entry?<br><br>';
				$content .= '<a href="'.$_SERVER['PHP_SELF'].'?delete='.$_GET['delete'].'&confirmed">'.$config['text']['prompt_yes'].'</a><br><br>';
				$content .= '<a href="javascript:history.go(-1);">'.$config['text']['prompt_no'].'</a>';

				echo '<div id="user_admin_content">';
				echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Nyheter', $content);
				echo '</div>';

				include('design_admin_foot.php');
				include('design_foot.php');
				die;
			
		} else {
			removeNews($db, $_GET['delete']);
		}
	}

	
	include('design_head.php');
	include('design_user_head.php');
	
	$content = '';

	if (!empty($_GET['edit'])) {

		$item = getNewsItem($db, $_GET['edit']);

		$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?edit='.$_GET['edit'].'">';
		$content .= '<input type="hidden" name="rss" value="0">';
		$content .= 'Redigera nyhet:<br>';
		$content .= 'Titel: <input type="text" name="title" value="'.$item['title'].'"><br>';
		$content .= 'Text:<br>';
		$content .= '<textarea name="body" cols=40 rows=6>'.$item['body'].'</textarea><br>';
		$content .= '<input name="rss" type="checkbox" class="checkbox" value="1"';
		if ($item['rss_enabled']) $content .= ' checked>';
		else $content .= '>';
		$content .= ' Inkludera denna nyhet i RSS-s&auml;ndningen<br><br>';
		$content .= 'Tid f&ouml;r publicering:<br>';
		$content .= '<input type="text" name="publish" value="'.date('Y-m-d H:i', $item['timetopublish']).'"> ';
		$content .= '<input type="submit" class="button" value="'.$config['text']['link_save_changes'].'"><br>';
		$content .= '</form><br>';
		
		$content .= '<a href="news.php?id='.$item['newsId'].'">Visa denna nyhet</a><br>';
		$content .= '<a href="'.$_SERVER['PHP_SELF'].'?delete='.$item['newsId'].'">Radera denna nyhet</a>';

	} else {

		$list = getAllNews($db);
		for ($i=0; $i<count($list); $i++) {
			$content .= '<div class="newsitem">';
			if ($list[$i]['rss_enabled']) $content .= '<img src="icons/rss_icon.png" width=16 height=16 title="RSS enabled">';
			$content .= '<b>'.$list[$i]['title'].'</b> (av '.nameLink($list[$i]['userId'], $list[$i]['userName']).', ';
			$content .= 'skapad '.getRelativeTimeLong($list[$i]['timecreated']).')<br>';
			$content .= $list[$i]['body'].'<br>';
			
			if ($list[$i]['timetopublish'] != $list[$i]['timecreated']) {
				$content .= '<span class="objectCritical">';
				if ($list[$i]['timetopublish'] > time()) {
					$content .= 'Ska publiceras '.getRelativeTimeLong($list[$i]['timetopublish']).'<br>';
				} else {
					$content .= 'Publicerades '.getRelativeTimeLong($list[$i]['timetopublish']).'<br>';
				}
				$content .= '</span>';
			}
			if ($list[$i]['timeedited'] > $list[$i]['timecreated']) {
				$content .= 'Uppdaterad '.getRelativeTimeLong($list[$i]['timeedited']).'<br>';
			}
			
			$content .= '<a href="'.$_SERVER['PHP_SELF'].'?edit='.$list[$i]['newsId'].'">'.$config['text']['link_edit'].'</a><br>';
			$content .= '<a href="'.$_SERVER['PHP_SELF'].'?delete='.$list[$i]['newsId'].'">'.$config['text']['link_remove'].'</a>';
			$content .= '</div>';
			$content .= '<br>';
		}
		$content .= '<br>';
	
		$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
		$content .= '<input type="hidden" name="rss" value="0">';
		$content .= 'L&auml;gg till nyhet:<br>';
		$content .= 'Titel: <input type="text" name="title"><br>';
		$content .= 'Text:<br>';
		$content .= '<textarea name="body" cols=40 rows=6></textarea><br>';
		$content .= '<input name="rss" type="checkbox" class="checkbox" value="1" checked> Inkludera denna nyhet i RSS-s&auml;ndningen<br><br>';
		$content .= 'Tid f&ouml;r publicering:<br>';
		$content .= '<input type="text" name="publish" value="'.date('Y-m-d H:i').'"> ';
		$content .= '<input type="submit" class="button" value="L&auml;gg till"><br>';
		$content .= '</form>';
		$content .= '<br><br>';
	}

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Nyheter', $content);
		echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');
?>