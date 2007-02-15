<?
	include('include_all.php');

	include('design_head.php');

	echo getInfoField($db, 'page_files').'<br>';
	
	$list = getFilesByUploader($db, $_SESSION['userId']);
	echo '<b>Files uploaded to the site by you ('.count($list).' files)</b><br><br>';
	for ($i=0; $i<count($list); $i++) {
		$list[$i]['uploaderName'] = $_SESSION['userName'];
		echo formatFileAttachment($db, $list[$i]);
	}
	
	include('design_foot.php');
?>