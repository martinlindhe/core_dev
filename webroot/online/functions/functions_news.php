<?
	function getNews($db, $count=10) {
		
		$sql = "SELECT * FROM tblNews ORDER by timestamp DESC";
		return dbArray($db, $sql);
	}
	
	function addNews($db, $subject, $body) {
		$subject = addslashes($subject);
		$body = addslashes($body);
		
		dbQuery($db, "INSERT INTO tblNews SET subject='".$subject."',body='".$body."',timestamp=".time() );
	}
	
	function deleteNews($db, $itemId) {
		if (!is_numeric($itemId)) return false;
		dbQuery($db, "DELETE FROM tblNews WHERE itemId=".$itemId);
	}
	
?>