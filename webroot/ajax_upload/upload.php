<?
	include('include_all.php');

	include('design_head.php');

	echo '<pre>';
	print_r($_FILES);

	if ($_FILES['file']) {
		$fileId = handleFileUpload($db, $_SESSION['userId'], FILETYPE_NORMAL_UPLOAD, $_FILES['file']);
		
		if (is_numeric($fileId)) {
			echo 'file uploaded!';
			echo '<a href="file.php?id='.$fileId.'&view">Show file</a>';
		} else {
			echo 'file upload error: '.$fileId;
		}

	}

	include('design_foot.php');
?>