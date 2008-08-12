<?php
/**
 * $Id$
 */

require_once('find_config.php');
$session->requireAdmin();

require($project.'design_head.php');

echo createMenu($admin_menu, 'blog_menu');

if (empty($config['feedback']['enabled'])) {
	echo 'Feedback feature is not enabled';
	require($project.'design_foot.php');
	die;
}

if (!empty($_GET['reply']) && is_numeric($_GET['reply'])) {
	$msg = getFeedbackItem($_GET['reply']);

	if (!empty($_POST['msg'])) {
		if (isset($_GET['private'])) {
			//Send response to user as a private message
			sendMessage($msg['userId'], 'System message', $_POST['msg']);

			if (!empty($_POST['fb_del'])) {
				deleteFeedback($_GET['reply']);
				echo 'Feedback entry deleted.<br/>';
			}

		} else {
			answerFeedback($_GET['reply'], $_POST['msg']);
		}
		echo 'The response has been sent!<br/>';
		require($project.'design_foot.php');
		die;
	}

	if (isset($_GET['private'])) {
		echo 'Send a private reply to message from '.Users::link($msg['userId']).':<br/>';

		$text = "In response to:\n".
				"\"".$msg['body']."\"\n".
				"\n\n\n----------------------\n- Best regards\n- Administrator ".$session->username;
	} else {
		echo 'Publish a public reply to message from '.Users::link($msg['userId']).':<br/>';
		echo '<b>'.$msg['body'].'</b>';
		$text = '';
	}

	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?reply='.$_GET['reply'].'&amp;'.(isset($_GET['private']) ? 'private' : 'public').'">';
	echo xhtmlTextarea('msg', $text, 40, 8).'<br/>';
	if (isset($_GET['private'])) {
		echo xhtmlCheckbox('fb_del', 'Delete from feedback queue', 1, true);
	}
	echo xhtmlSubmit('Send reply');
	echo '</form>';
	require($project.'design_foot.php');
	die;
}

echo 'Showing user-submitted feedback - oldest items first<br/><br/>';
	
if (!empty($_GET['delete'])) deleteFeedback($_GET['delete']);

$tot_cnt = getFeedbackCnt(0);
$pager = makePager($tot_cnt, 5);

$list = getFeedback(0, $pager['limit']);
echo $pager['head'];
	
foreach ($list as $row) {
	echo '<div class="item">';
	switch ($row['feedbackType']) {

		case FEEDBACK_SUBMIT:
			echo '<h2>General feedback</h2>';
			break;
				
		case FEEDBACK_ADBLOCK_ADS:
			echo '<h2>Site contains ads</h2>';
			break;

		case FEEDBACK_ADBLOCK_BROKEN_RULE:
			echo '<h2>Blocking rules breaks the site</h2>';
			break;

		default: die('EEEP!!! error');
	}
	echo t('From').' '.Users::link($row['userId'], $row['userName']).' '.t('at').' '.$row['timeCreated'].':<br/>';
	echo $row['subj'].'<br/><br/>';
		
	if (!empty($row['body'])) echo '<div class="item">'.t('Comment').': '.$row['body'].'</div><br/>';
		
	if ($row['userId']) {
		echo '<a href="?reply='.$row['feedbackId'].getProjectPath().'&amp;public">Public reply</a><br/>';
		echo '<a href="?reply='.$row['feedbackId'].getProjectPath().'&amp;private">Reply with private message</a><br/>';
	}
	coreButton('Delete', '?delete='.$row['feedbackId'].getProjectPath() );
	echo '</div><br/>';
}

require($project.'design_foot.php');

?>
