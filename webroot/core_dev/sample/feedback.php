<?
	require_once('config.php');

	require('design_head.php');

	wiki('Feedback');
	
	if (!empty($_POST['feedback_q'])) {
		saveFeedback($_POST['feedback_q']);
		echo 'Thank you - your question will be answered as soon as possible.<br/><br/>';
	}

	echo '<br/>';
	echo '<form method="post" action="">';
	echo '<textarea name="feedback_q" rows="8" cols="40"></textarea><br/>';
	echo '<input type="submit" class="button" value="Submit question"/>';
	echo '</form>';

	require('design_foot.php');
?>