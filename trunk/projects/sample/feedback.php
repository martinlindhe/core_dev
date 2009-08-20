<?php

require_once('config.php');
require('design_head.php');

wiki('Feedback');
echo '<br/>';
	
if (!empty($_POST['feedback_q'])) {
	saveFeedback(FEEDBACK_SUBMIT, $_POST['feedback_q']);
	echo 'Thank you - your question will be answered as soon as possible.<br/><br/>';
} else {

	echo '<form method="post" action="">';
	echo '<textarea name="feedback_q" rows="8" cols="40"></textarea><br/>';
	echo '<input type="submit" class="button" value="Submit question"/>';
	echo '</form>';
}

require('design_foot.php');
?>
