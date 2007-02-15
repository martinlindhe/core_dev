<?
	include('include_all.php');
	
	include('design_head.php');

	if ($_SESSION['loggedIn']) {
		echo '<h2>Start page</h2>';
		echo 'You are logged in as '.$_SESSION['userName'];
		if ($_SESSION['isSuperAdmin']) echo ' (super admin)';
		else if ($_SESSION['isAdmin']) echo ' (administrator)';
		else echo ' (normal user)';
		echo '<br><br>';
	} else {
		echo 'You are not logged in ...';
	}
	
	echo 'Uncategorized files:<br>';
	$list = getFilesByCategory($db, FILETYPE_NORMAL_UPLOAD, 0);
	
	
	
	for ($i=0; $i<count($list); $i++) {
		$pos = strrpos($list[$i]['fileName'], '.');
		$ext = '';
		if ($pos !== false) {
			$ext = substr($list[$i]['fileName'], $pos);
		}

		echo '<hr>';
		echo '<b>'.$list[$i]['fileName'].'</b><br>';
		echo 'Uploaded '.getRelativeTimeLong($list[$i]['uploadTime']).'<br>';

		if (in_array($ext, $config['allowed_image_extensions'])) {
			echo '<img src="'.$config['upload_dir'].$list[$i]['fileId'].'" width=150>';
			echo '<br>';
		} else {
			echo 'File type: '.$list[$i]['fileMime'].'<br>';
			echo '<br>';
		}

		echo '<a href="file.php?id='.$list[$i]['fileId'].'&view">View file</a><br>';
		echo '<a href="file.php?id='.$list[$i]['fileId'].'">Download file</a>';
		
	}

	include('design_foot.php');
?>