<?
	//image viewer

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$fileId = $_GET['id'];

	$browse = false;
	if (isset($_GET['browse'])) $browse = true;
	// if browse is set, we will let the user browse through all images with the same owner id

	include('include_all.php');

	$file = getFile($db, $fileId);

	$file_name = $config['upload_dir'].$file['fileId'];
	if (!file_exists($file_name)) die;

	list($img_width, $img_height) = getimagesize($file_name);

	if (($img_width >= $_SESSION['browser']['width']) || ($img_height >= $_SESSION['browser']['height'])) {
		$img_tag = '<img src="file.php?id='.$fileId.'&width='.($img_width-60).'" title="'.$file['fileName'].'" border=0>';
	} else {
		$img_tag = '<img src="file.php?id='.$fileId.'" title="'.$file['fileName'].'" border=0>';
	}

?>
<html>
<head>
<title><?=$file['fileName']?> - image viewer</title>
<script type="text/javascript">
window.focus();
</script>
</head>
<body marginheight=0 marginwidth=0 topmargin=0 leftmargin=0 bottommargin=0 bgcolor=#FFFFFF>
<a href="javascript:window.close();"><?=$img_tag?></a>
<?

	if ($browse) {
		function getFilesByOwnerCount(&$db, $ownerId, $fileType=0)
		{
			if (!is_numeric($ownerId) || !is_numeric($fileType)) return array();
	
			$sql = 'SELECT COUNT(fileId) FROM tblFiles AS t1 ';
			$sql .= 'WHERE ownerId='.$ownerId;
			if ($fileType) $sql .= ' AND fileType='.$fileType;
	
			return dbOneResultItem($db, $sql);
		}
	
		function getFilesByOwner(&$db, $ownerId, $fileType=0)
		{
			if (!is_numeric($ownerId) || !is_numeric($fileType)) return array();
	
			$sql = 'SELECT * FROM tblFiles AS t1 ';
			$sql .= 'WHERE ownerId='.$ownerId;
			if ($fileType) $sql .= ' AND fileType='.$fileType;
			$sql .= ' ORDER BY uploadTime ASC';
	
			return dbArray($db, $sql);
		}

		$list = getFilesByOwner($db, $file['ownerId']);

		$prev = 0;
		$next = 0;
		$current_pos = false;
		$totcnt = 0;

		for ($i=0; $i<count($list); $i++) {
			if (in_array($list[$i]['fileMime'], $config['allowed_image_mimetypes'])) {
				/* This is a allowed image */
				if ($list[$i]['fileId'] == $fileId) $current_pos = ($totcnt+1);
				
				if (!$current_pos) $prev = $list[$i]['fileId'];
				if ($current_pos && ($list[$i]['fileId'] != $fileId) && !$next) $next = $list[$i]['fileId'];
				$totcnt++;
			}
		}

		$cnt = getFilesByOwnerCount($db, $file['ownerId']);
		
		echo '<table width="100%" cellpadding=2 cellspacing=0 border=1>';
		echo '<tr>';

		echo '<td width="40%">';
			if ($prev) echo '<a href="file_show_image.php?id='.$prev.'&browse">Previous image</a>';
			else echo '&nbsp;';
		echo '</td>';

		echo '<td width="20%" align="center">'.$current_pos.'/'.$totcnt.'</td>';

		echo '<td width="40%" align="right">';
			if ($next) echo '<a href="file_show_image.php?id='.$next.'&browse">Next image</a>';
			else echo '&nbsp;';
		echo '</td>';

		echo '</tr>';
		echo '</table>';
	}

?>
</html>