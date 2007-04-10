<?





	//todo: all code below this line are not ported yet:
	
	function sendTextFile($realFileName, $sendFileName)
	{
		//required for IE6:
		header('Cache-Control: cache, must-revalidate');

		//header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($realFileName)) . ' GMT');
		header('Content-Length: '.filesize($realFileName));
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename="'.$sendFileName.'"');

		readfile($realFileName, 'r');
	}

	function getFilesByCategory(&$db, $fileType, $categoryId)
	{
		if (!is_numeric($fileType) || !is_numeric($categoryId)) return false;

		$sql = 'SELECT * FROM tblFiles WHERE fileType='.$fileType.' AND categoryId='.$categoryId;
		$sql .= ' ORDER BY timeCreated DESC ';

		return dbArray($db, $sql);
	}




	function getFileName(&$db, $fileId)
	{
		if (!is_numeric($fileId)) return false;
		
		$sql = 'SELECT fileName FROM tblFiles WHERE fileId='.$fileId;
		return dbOneResultItem($db, $sql);
	}
	
	function deleteFile(&$db, $fileId)
	{
		global $config;

		if (!is_numeric($fileId)) return false;

		$data = getFile($db, $fileId);
		if ($data['uploaderId'] != $_SESSION['userId']) {
			return false;
		}

		$sql = 'DELETE FROM tblFiles WHERE fileId='.$fileId;
		dbQuery($db, $sql);

		$filename = $config['upload_dir'].$fileId;
		unlink($filename);
	}


	function updateFileViews(&$db, $fileId)
	{
		if (!is_numeric($fileId) || empty($fileId)) return false;
		if (isset($_SESSION['viewed_files'][$fileId])) return false;		//count 1 view per session

		$sql = 'UPDATE tblFiles SET cnt=cnt+1 WHERE fileId='.$fileId;
		dbQuery($db, $sql);
		
		$_SESSION['viewed_files'][$fileId] = true;
	}
	



?>