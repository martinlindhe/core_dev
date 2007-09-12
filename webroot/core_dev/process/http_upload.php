<?
	require_once('config.php');

	if (!empty($_FILES['file2'])) {
		$fileId = processEvent(PROCESSUPLOAD_FORM, $_FILES['file2']);
		if ($fileId) {
			header('Location: http_enqueue.php?id='.$fileId);
			die;
		}
	}

	require('design_head.php');

	wiki('ProcessAddOrder');

	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'" enctype="multipart/form-data">';
	echo '<input type="file" name="file2"/>';
	echo '<input type="submit" class="button" value="Upload"/>';
	echo '</form>';

	require('design_foot.php');
?>