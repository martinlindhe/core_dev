<?php
	require_once('find_config.php');
	$session->requireSuperAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');
	if ($session->isSuperAdmin) {
		echo createMenu($super_admin_menu, 'blog_menu');
		echo createMenu($super_admin_tools_menu, 'blog_menu');
	}
	
	$searchtext = '';
	if (isset($_GET['searchtext'])) {
		$searchtext = $_GET['searchtext'];
	}

	echo '<h1>Content search</h1>';

	echo '<form action="?" method="get">';
	echo '<input type="hidden" name="search">';
	echo '<input type="text" name="searchtext" value="'.$searchtext.'"> Text';
	echo '<br/><br/><input type="radio" name="searchtype" value="guestbook"> Guestbook ';
	echo '<input type="radio" name="searchtype" value="mail"> Mail ';
	echo '<input type="radio" name="searchtype" value="comment"> Comment';

	echo '<br/><br/><input type="submit" value="Search">';
	echo '</form>';

	if (isset($_GET['search']) && isset($_GET['searchtext']) && isset($_GET['searchtype'])) {
		if ($_GET['searchtype'] == 'guestbook') {
			$tot_cnt = getGuestbookFreeTextSearchCount($searchtext);
			$pager = makePager($tot_cnt, 50);

			$results = getGuestbookFreeTextSearch($searchtext,$pager['limit']);
			echo $pager['head'];

			echo '<table width="100%">';
					echo '<tr><td>Time</td><td>Author</td><td>Text</td><td>Reciever</td></tr>';
				foreach ($results as $row) {
					echo '<tr><td>';
						echo $row['timeCreated'];
					echo '</td>';
					echo '<td>';
						echo $row['authorName'];
					echo '</td>';
					echo '<td>';
						echo $row['body'];
					echo '</td>';
					echo '<td>';
						echo $row['userName'];
					echo '</td></tr>';
				}
			echo '</table>';
		}
		else if ($_GET['searchtype'] == 'mail') {
			$tot_cnt = getMessageFreeTextSearchCount($searchtext);
			$pager = makePager($tot_cnt, 50);

			$results = getMessageFreeTextSearch($searchtext,$pager['limit']);
			echo $pager['head'];

			echo '<table width="100%">';
					echo '<tr><td>Time</td><td>Author</td><td>Subject</td><td>Text</td><td>Reciever</td></tr>';
				foreach ($results as $row) {
					echo '<tr><td>';
						echo $row['timeCreated'];
					echo '</td>';
					echo '<td>';
						echo $row['authorName'];
					echo '</td>';
					echo '<td>';
						echo $row['subject'];
					echo '</td>';
					echo '<td>';
						echo $row['body'];
					echo '</td>';
					echo '<td>';
						echo $row['userName'];
					echo '</td></tr>';
				}
			echo '</table>';
		}
		else if ($_GET['searchtype'] == 'comment') {
			$tot_cnt = getCommentFreeTextSearchCount($searchtext);
			$pager = makePager($tot_cnt, 50);

			$results = getCommentFreeTextSearch($searchtext,$pager['limit']);
			echo $pager['head'];

			echo '<table width="100%">';
					echo '<tr><td>Time</td><td>Author</td><td>Comment</td><td>Type</td></tr>';
				foreach ($results as $row) {
					echo '<tr><td>';
						echo $row['timeCreated'];
					echo '</td>';
					echo '<td>';
						echo $row['authorName'];
					echo '</td>';
					echo '<td>';
						echo $row['commentText'];
					echo '</td>';
					echo '<td>';
						echo $comment_constants[$row['commentType']];
					echo '</td></tr>';
				}
			echo '</table>';
		}
	}

	require($project.'design_foot.php');
?>
