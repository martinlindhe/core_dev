<?

	/* Ignoring CLOSED items */
	function getTodoItems($db, $listId) {
		if (!is_numeric($listId)) return false;
		return dbArray($db, "SELECT * FROM tblTodoLists WHERE listId=".$listId." AND itemStatus!=".TODO_ITEM_CLOSED);
	}
	
	/* Only returns CLOSED items */
	function getClosedTodoItems($db, $listId) {
		if (!is_numeric($listId)) return false;
		return dbArray($db, "SELECT * FROM tblTodoLists WHERE listId=".$listId." AND itemStatus=".TODO_ITEM_CLOSED);
	}

	function addTodoItem($db, $listId, $userId, $desc, $details, $category) {
		if (!is_numeric($listId) || !is_numeric($userId) || !is_numeric($category)) return false;
		$desc = addslashes($desc);
		$details = addslashes($details);

		dbQuery($db, "INSERT INTO tblTodoLists SET listId=".$listId.",itemCreator=".$userId.",itemDesc='".$desc."',itemDetails='".$details."',itemCategory=".$category.",timestamp=".time());
	}

	function getTodoItem($db, $itemId) {
		
		if (substr(strtoupper($itemId), 0, 2) == "PR") {
			$itemId = substr($itemId, 2);
		}
		
		if (!is_numeric($itemId)) return false;
		
		$sql  = "SELECT tblTodoLists.*,tblUsers.userName FROM tblTodoLists ";
		$sql .= "LEFT OUTER JOIN tblUsers ON (tblTodoLists.itemCreator = tblUsers.userId) ";
		$sql .= "WHERE itemId=".$itemId;
		
		$check = dbQuery($db, $sql);
		return dbFetchArray($check);
	}
	
	function setTodoItemStatus($db, $itemId, $status) {
		if (!is_numeric($itemId) || !is_numeric($status)) return false;
		
		dbQuery($db, "UPDATE tblTodoLists SET itemStatus=".$status." WHERE itemId=".$itemId);
	}

	function addTodoItemComment($db, $userId, $itemId, $comment) {
		if (!is_numeric($userId) || !is_numeric($itemId)) return false;
		$comment = addslashes($comment);

		$sql = "INSERT INTO tblTodoListComments SET userId=".$userId.",itemId=".$itemId.",itemComment='".$comment."',timestamp=".time();
		dbQuery($db, $sql );
	}
	
	function getTodoItemComments($db, $itemId) {
		if (!is_numeric($itemId)) return false;
		
		$sql  = "SELECT tblTodoListComments.*, tblUsers.userName FROM tblTodoListComments ";
		$sql .= "LEFT OUTER JOIN tblUsers ON (tblTodoListComments.userId = tblUsers.userId) ";
		$sql .= "WHERE itemId=".$itemId." ORDER BY timestamp ASC";
		return dbArray($db, $sql);
	}

	function assignTodoItem($db, $itemId, $assignedId) {
		if (!is_numeric($itemId) || !is_numeric($assignedId)) return false;
		
		dbQuery($db, "UPDATE tblTodoLists SET assignedTo=".$assignedId.", itemStatus=".TODO_ITEM_ASSIGNED." WHERE itemId=".$itemId);
	}
	
	function unassignTodoItem($db, $itemId) {
		if (!is_numeric($itemId)) return false;
		
		dbQuery($db, "UPDATE tblTodoLists SET assignedTo=0 WHERE itemId=".$itemId);
	}
	
	/* Returns only assigned tasks that is not CLOSED */
	function getAssignedTasks($db, $userId) {
		if (!is_numeric($userId)) return false;
		
		$sql = "SELECT * FROM tblTodoLists WHERE assignedTo=".$userId." AND itemStatus!=".TODO_ITEM_CLOSED." ORDER BY timestamp ASC";
		return dbArray($db, $sql);
	}

	function getAssignedTasksCount($db, $userId) {
		if (!is_numeric($userId)) return false;

		$sql = 'SELECT COUNT(itemId) FROM tblTodoLists WHERE assignedTo=".$userId." AND itemStatus!='.TODO_ITEM_CLOSED;
		return dbOneResultItem($db, $sql);
	}

	/* Returns only CLOSED assigned tasks */
	function getClosedAssignedTasks($db, $userId) {
		if (!is_numeric($userId)) return false;

		$sql = "SELECT * FROM tblTodoLists WHERE assignedTo=".$userId." AND itemStatus=".TODO_ITEM_CLOSED." ORDER BY timestamp ASC";
		return dbArray($db, $sql);
	}
	
	function getClosedAssignedTasksCount($db, $userId) {
		if (!is_numeric($userId)) return false;

		$sql = 'SELECT COUNT(itemId) FROM tblTodoLists WHERE assignedTo=".$userId." AND itemStatus='.TODO_ITEM_CLOSED;
		return dbOneResultItem($db, $sql);
	}

	
	/* Returns the number of items in a todo category that is not CLOSED */
	function getTodoCategoryItemsCount($db, $listId) {
		if (!is_numeric($listId)) return false;

		$sql = 'SELECT COUNT(itemId) FROM tblTodoLists WHERE listId='.$listId.' AND itemStatus!='.TODO_ITEM_CLOSED;
		return dbOneResultItem($db, $sql);
	}
	
	/* Returns the number of items in all todo categories that is not CLOSED */
	function getTodoItemsCount($db) {
		$sql = 'SELECT COUNT(itemId) FROM tblTodoLists WHERE itemStatus!='.TODO_ITEM_CLOSED;
		return dbOneResultItem($db, $sql);
	}

	function getClosedTodoCategoryItems($db, $listId) {
		if (!is_numeric($listId)) return false;
		
		$sql = 'SELECT COUNT(itemId) FROM tblTodoLists WHERE listId='.$listId.' AND itemStatus='.TODO_ITEM_CLOSED;
		return dbOneResultItem($db, $sql);
	}
	
	
	
	function addBugReport($db, $userId, $desc) {
		if (!is_numeric($userId)) return false;
		$desc = addslashes(strip_tags($desc));

		dbQuery($db, "INSERT INTO tblBugReports SET bugDesc='".$desc."',bugCreator=".$userId.",reportMethod=0,timestamp=".time() );
	}
	
	function getBugReports($db) {
		$sql  = "SELECT tblBugReports.*,tblUsers.userName FROM tblBugReports ";
		$sql .= "LEFT OUTER JOIN tblUsers ON (tblBugReports.bugCreator=tblUsers.userId) ";
		$sql .= "WHERE bugClosed=0 ";
		$sql .= "ORDER By tblBugReports.timestamp ASC";
		return dbArray($db, $sql);
	}

	function getClosedBugReports($db) {
		$sql  = "SELECT tblBugReports.*,tblUsers.userName FROM tblBugReports ";
		$sql .= "LEFT OUTER JOIN tblUsers ON (tblBugReports.bugCreator=tblUsers.userId) ";
		$sql .= "WHERE bugClosed=1 ";
		$sql .= "ORDER By tblBugReports.timestamp ASC";
		return dbArray($db, $sql);
	}

	function getClosedBugReportsCount($db) {
		$sql = 'SELECT COUNT(bugId) FROM tblBugReports WHERE bugClosed=1';
		return dbOneResultItem($db, $sql);
	}

	/* Returnerar antal ÖPPNA buggar */
	function getBugReportsCount($db) {
		$sql = "SELECT COUNT(bugId) FROM tblBugReports WHERE bugClosed=0";
		return dbOneResultItem($db, $sql);
	}

	function getBugReport($db, $bugId) {
		if (!is_numeric($bugId)) return false;

		$sql  = "SELECT tblBugReports.*,tblUsers.userName FROM tblBugReports ";
		$sql .= "INNER JOIN tblUsers ON (tblBugReports.bugCreator=tblUsers.userId) ";
		$sql .= "WHERE bugId=".$bugId;
		$check = dbQuery($db, $sql);
		return dbFetchArray($check);	
	}

	/* Flyttar buggrapporten från tblBugReports till tblTodoLists */
	/* Returnerar ID för det nya todo-itemet */
	//userId : den som flyttar buggen, creator = den som skapat buggen */
	function moveBugReport($db, $userId, $bugId, $creator, $desc, $details, $timestamp, $category, $listId) {
		if (!is_numeric($bugId) || !is_numeric($creator) || !is_numeric($timestamp) || !is_numeric($category) || !is_numeric($listId)) return false;
		$desc = addslashes($desc);
		$details = addslashes($details);

		dbQuery($db, "INSERT INTO tblTodoLists SET listId=".$listId.",itemDesc='".$desc."',itemDetails='".$details."',itemCategory=".$category.",timestamp=".$timestamp.",itemCreator=".$creator);
		$itemId = dbInsertId();
		dbQuery($db, "DELETE FROM tblBugReports WHERE bugId=".$bugId);
		
		$comment = "Imported by ".getUserName($db, $userId)." from a report by ".getUserName($db, $creator).".";
		dbQuery($db, "INSERT INTO tblTodoListComments SET itemId=".$itemId.",itemComment='".$comment."',timestamp=".time().",userId=0");
		
		return $itemId;
	}
	
	function closeBugReport($db, $bugId, $reason) {
		if (!is_numeric($bugId) || !is_numeric($reason)) return false;
		
		dbQuery($db, "UPDATE tblBugReports SET bugClosed=1, bugClosedReason=".$reason." WHERE bugId=".$bugId);
	}
	
?>