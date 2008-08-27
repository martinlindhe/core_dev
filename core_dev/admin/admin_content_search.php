<?php
/**
 * $Id$
 */

require_once('find_config.php');

$session->requireSuperAdmin();

require('design_admin_head.php');

$searchtext = '';
if (isset($_GET['searchtext'])) {
	$searchtext = $_GET['searchtext'];
}

echo '<h1>Content search</h1>';

echo '<form action="?" method="get">';
echo '<input type="hidden" name="search">';
echo 'Text: '.xhtmlInput('searchtext', $searchtext).'<br/><br/>';

$types = array(
	'guestbook' => 'Guestbook',
	'mail' => 'Mail',
	'comment' => 'Comment'
);
echo xhtmlRadioArray('searchtype', $types, 'guestbook');
echo '<br/><br/>';
echo xhtmlSubmit('Search');
echo '</form>';

if (isset($_GET['search']) && isset($_GET['searchtext']) && isset($_GET['searchtype'])) {
	if ($_GET['searchtype'] == 'guestbook') {
		$tot_cnt = getGuestbookFreeTextSearchCount($searchtext);
		$pager = makePager($tot_cnt, 50);

		$results = getGuestbookFreeTextSearch($searchtext, $pager['limit']);
		echo $pager['head'];

		echo '<table width="100%">';
			echo '<tr><td>Time</td><td>Author</td><td>Text</td><td>Reciever</td></tr>';
			foreach ($results as $row) {
				echo '<tr><td>'.$row['timeCreated'].'</td>';
				echo '<td>'.$row['authorName'].'</td>';
				echo '<td>'.$row['body'].'</td>';
				echo '<td>'.$row['userName'].'</td></tr>';
			}
		echo '</table>';
	} else if ($_GET['searchtype'] == 'mail') {
		$tot_cnt = getMessageFreeTextSearchCount($searchtext);
		$pager = makePager($tot_cnt, 50);

		$results = getMessageFreeTextSearch($searchtext,$pager['limit']);
		echo $pager['head'];

		echo '<table width="100%">';
			echo '<tr><td>Time</td><td>Author</td><td>Subject</td><td>Text</td><td>Reciever</td></tr>';
			foreach ($results as $row) {
				echo '<tr><td>'.$row['timeCreated'].'</td>';
				echo '<td>'.$row['authorName'].'</td>';
				echo '<td>'.$row['subject'].'</td>';
				echo '<td>'.$row['body'].'</td>';
				echo '<td>'.$row['userName'].'</td></tr>';
			}
		echo '</table>';
	} else if ($_GET['searchtype'] == 'comment') {
		$tot_cnt = getCommentFreeTextSearchCount($searchtext);
		$pager = makePager($tot_cnt, 50);

		$results = getCommentFreeTextSearch($searchtext,$pager['limit']);
		echo $pager['head'];

		echo '<table width="100%">';
			echo '<tr><td>Time</td><td>Author</td><td>Comment</td><td>Type</td></tr>';
			foreach ($results as $row) {
				echo '<tr><td>'.$row['timeCreated'].'</td>';
				echo '<td>'.$row['authorName'].'</td>';
				echo '<td>'.$row['commentText'].'</td>';
				echo '<td>'.$comment_constants[ $row['commentType'] ].'</td></tr>';
			}
		echo '</table>';
	}
}

require('design_admin_foot.php');

?>
