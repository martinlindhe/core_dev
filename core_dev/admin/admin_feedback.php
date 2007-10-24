<?
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
			//Send response to user as a private message
			sendMessage($msg['userId'], 'System message', $_POST['msg']);
			echo 'The response has been sent!<br/>';

			if (!empty($_POST['fb_del'])) {
				echo 'Feedback entry deleted.<br/>';
				deleteFeedback($_GET['reply']);
			}

			require($project.'design_foot.php');
			die;
		}
		
		echo 'Reply to message from '.nameLink($msg['userId']).':<br/>';

		$text = "In response to:\n".
						'"'.$msg['text']."\"\n".
						"\n\n\n----------------------\n- Best regards\n- Administrator ".$session->username;

		echo '<form method="post" action="">';
		echo '<textarea name="msg" rows="8" cols="40">'.$text.'</textarea><br/>';
		echo '<input type="checkbox" name="fb_del" id="fb_del" value="1" checked="checked"/>';
		echo '<label for="fb_del">Delete from feedback queue</label><br/>';
		echo '<input type="submit" class="button" value="Send response"/>';
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
		echo 'From '.nameLink($row['userId'], $row['userName']).' at '.$row['timeCreated'].':<br/>';
		echo $row['text'].'<br/><br/>';
		
		if (!empty($row['text2'])) echo '<div class="item">Comment: '.$row['text2'].'</div><br/>';
		
		if ($row['userId']) echo '<a href="?reply='.$row['feedbackId'].getProjectPath().'">Reply</a><br/>';
		echo '<a href="?delete='.$row['feedbackId'].getProjectPath().'">Delete</a><br/>';
		echo '</div><br/>';
	}

	require($project.'design_foot.php');
?>