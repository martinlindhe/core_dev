<?php

require_once('config.php');
$h->session->requireLoggedIn();

set_time_limit(60*10);	//10 minute max, for big uploads

require('design_head.php');

if (!empty($_FILES['file2'])) {
	$eventId = addProcessEvent(PROCESS_UPLOAD, $h->session->id, $_FILES['file2']);
	if ($eventId) {
		echo '<div class="okay">Your file has been uploaded successfully!</div><br/>';
		echo '<a href="http_enqueue.php?id='.$eventId.'">Click here</a> to perform further actions on this file.';
		require('design_foot.php');
		die;
	} else {
		echo 'file upload handling failed';
	}
}

wiki('ProcessAddOrder');

echo 'Max allowed upload size is '.ini_get('upload_max_filesize').'<br/><br/>';
echo 'Max allowed POST size is '.ini_get('post_max_size').'<br/><br/>';

echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'" enctype="multipart/form-data">';
echo '<input type="file" name="file2"/>';
echo '<input type="submit" class="button" value="Upload"/>';
echo '</form>';

require('design_foot.php');
?>
