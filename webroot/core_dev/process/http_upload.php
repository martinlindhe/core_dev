<?
	require_once('config.php');

	if (!empty($_FILES['file2'])) {
		$fileId = processEvent(PROCESSUPLOAD_FORM, $_FILES['file2']);
		if ($fileId) {
			header('Location: http_enqueue.php?id='.$fileId);
			die;
		} else {
			echo 'file upload handling failed';
		}
	}

	require('design_head.php');

	wiki('ProcessAddOrder');

	echo 'Max allowed upload size is '.ini_get('upload_max_filesize').'<br/><br/>';
	echo 'Max allowed POST size is '.ini_get('post_max_size').'<br/><br/>';
	

	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'" enctype="multipart/form-data">';
	echo '<input type="file" name="file2"/>';
	echo '<input type="submit" class="button" value="Upload"/>';
	echo '</form>';

	require('design_foot.php');
?>