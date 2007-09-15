<?
	set_time_limit(60*10);	//10 minute max, for big uploads

	require_once('config.php');

	require('design_head.php');

	if (!empty($_FILES['file2'])) {
		$fileId = processEvent(PROCESSUPLOAD_FORM, $_FILES['file2']);
		if ($fileId) {
			echo '<div class="okay">Your file has been uploaded successfully!</div><br/>';
			echo '<a href="http_enqueue.php?id='.$fileId.'">Click here</a> to perform further actions on this file.';
			require('design_foot.php');
			xdebug_break();
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