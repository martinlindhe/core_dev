<?php

require_once('config.php');
require('design_head.php');

echo '<h1>New issue</h1>';

if (isset($_POST['desc'])) {
	$issueId = addIssue($session->id, 0, $_POST['desc']);
	if ($issueId) {

		echo 'Thank you for the report!<br/>';
		echo 'The issue have been stored and will be overlooked as soon as possible!<br/><br/>';

		echo 'What do you want to do now?<br/><br/>';

		echo '* <a href="show_issue.php?id='.$issueId.'">Go to issue report</a><br/>';
		echo '* <a href="'.$_SERVER['PHP_SELF'].'">Report another issue</a><br/>';
	} else {
		echo 'Error adding the issue.';
	}

} else {
	echo 'From here you can submit bug reports or feature requests regarding the game or website.<br/>';
	echo 'Please leave as many details as possible.<br/><br/>';

	echo xhtmlForm();
	//FIXME categories dropdown
	echo 'Description:<br/>';
	echo xhtmlTextarea('desc', '', 60, 8).'</td></tr>';
	echo '<tr><td><br>'.xhtmlSubmit('Submit issue').'</td></tr>';
	echo xhtmlFormClose();
}

require('design_foot.php');
?>
