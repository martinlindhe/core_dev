<?
	define('TODO_ITEM_OPEN',    0); $todo_item_status[TODO_ITEM_OPEN]     = 'OPEN';
	define('TODO_ITEM_ASSIGNED',1); $todo_item_status[TODO_ITEM_ASSIGNED] = 'ASSIGNED';
	define('TODO_ITEM_CLOSED',  2); $todo_item_status[TODO_ITEM_CLOSED]   = 'CLOSED';

	$todo_item_category[0] = 'Missing feature';
	$todo_item_category[1] = 'Bug';
	$todo_item_category[2] = 'Code rewrite';
	$todo_item_category[3] = 'Other';

	define('CLOSE_BUG_BOGUS',        0); $close_bug_reason[CLOSE_BUG_BOGUS]        = 'BOGUS';
	define('CLOSE_BUG_ALREADYFIXED', 1); $close_bug_reason[CLOSE_BUG_ALREADYFIXED] = 'ALREADY FIXED';


	/* Ignoring CLOSED items */
	function getTodoItems(&$db, $categoryId)
	{
		if (!is_numeric($categoryId)) return false;
		
		$sql = 'SELECT * FROM tblTodoLists WHERE categoryId='.$categoryId.' AND itemStatus!='.TODO_ITEM_CLOSED;
		return dbArray($db, $sql);
	}
	
	/* Only returns CLOSED items */
	function getClosedTodoItems(&$db, $categoryId)
	{
		if (!is_numeric($categoryId)) return false;

		$sql = 'SELECT * FROM tblTodoLists WHERE categoryId='.$categoryId.' AND itemStatus='.TODO_ITEM_CLOSED;
		return dbArray($db, $sql);
	}

	function addTodoItem(&$db, $categoryId, $userId, $desc, $details, $category)
	{
		if (!is_numeric($categoryId) || !is_numeric($userId) || !is_numeric($category)) return false;
		$desc = dbAddSlashes($db, $desc);
		$details = dbAddSlashes($db, $details);

		$sql = 'INSERT INTO tblTodoLists SET categoryId='.$categoryId.',itemCreator='.$userId.',itemDesc="'.$desc.'",itemDetails="'.$details.'",itemCategory='.$category.',timestamp='.time();
		dbQuery($db, $sql);
	}

	/* Delete all todo items in a whole category */
	function deleteTodoItems(&$db, $categoryId)
	{
		if (!is_numeric($categoryId)) return false;
		
		$sql = 'DELETE FROM tblTodoLists WHERE categoryId='.$categoryId;
		dbQuery($db, $sql);
		return true;
	}

	function getTodoItem(&$db, $itemId)
	{
		if (substr(strtoupper($itemId), 0, 2) == 'PR') {
			$itemId = substr($itemId, 2);
		}

		if (!is_numeric($itemId)) return false;

		$sql  = 'SELECT tblTodoLists.*,tblUsers.userName FROM tblTodoLists ';
		$sql .= 'LEFT OUTER JOIN tblUsers ON (tblTodoLists.itemCreator = tblUsers.userId) ';
		$sql .= 'WHERE itemId='.$itemId;

		return dbOneResult($db, $sql);
	}

	/* Move $itemId to category $categoryId */
	function moveTodoItem(&$db, $itemId, $categoryId)
	{
		if (!is_numeric($itemId) || !is_numeric($categoryId)) return false;

		$sql = 'UPDATE tblTodoLists SET categoryId='.$categoryId.' WHERE itemId='.$itemId;
		dbQuery($db, $sql);
	}

	function setTodoItemStatus(&$db, $itemId, $status)
	{
		if (!is_numeric($itemId) || !is_numeric($status)) return false;
		
		dbQuery($db, 'UPDATE tblTodoLists SET itemStatus='.$status.' WHERE itemId='.$itemId);
	}

	function addTodoItemComment(&$db, $userId, $itemId, $comment)
	{
		if (!is_numeric($userId) || !is_numeric($itemId)) return false;
		$comment = dbAddSlashes($db, $comment);

		$sql = 'INSERT INTO tblTodoListComments SET userId='.$userId.',itemId='.$itemId.',itemComment="'.$comment.'",timestamp='.time();
		dbQuery($db, $sql);
	}
	
	function getTodoItemComments(&$db, $itemId, $order = 'asc')
	{
		if (!is_numeric($itemId)) return false;
		
		$sql  = 'SELECT tblTodoListComments.*, tblUsers.userName FROM tblTodoListComments ';
		$sql .= 'LEFT OUTER JOIN tblUsers ON (tblTodoListComments.userId = tblUsers.userId) ';
		$sql .= 'WHERE itemId='.$itemId;

		if ($order == 'desc') {
			$sql .= ' ORDER BY timestamp DESC';
		} else {
			$sql .= ' ORDER BY timestamp ASC';
		}

		return dbArray($db, $sql);
	}

	function assignTodoItem(&$db, $itemId, $assignedId)
	{
		if (!is_numeric($itemId) || !is_numeric($assignedId)) return false;
		
		dbQuery($db, 'UPDATE tblTodoLists SET assignedTo='.$assignedId.', itemStatus='.TODO_ITEM_ASSIGNED.' WHERE itemId='.$itemId);
	}
	
	function unassignTodoItem(&$db, $itemId)
	{
		if (!is_numeric($itemId)) return false;
		
		$sql = 'UPDATE tblTodoLists SET assignedTo=0, itemStatus='.TODO_ITEM_OPEN.' WHERE itemId='.$itemId;
		dbQuery($db, $sql);
	}
	
	/* Returns only assigned tasks that is not CLOSED */
	function getAssignedTasks(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;
		
		$sql = 'SELECT * FROM tblTodoLists WHERE assignedTo='.$userId.' AND itemStatus!='.TODO_ITEM_CLOSED.' ORDER BY timestamp ASC';
		return dbArray($db, $sql);
	}

	function getAssignedTasksCount(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$sql = 'SELECT COUNT(itemId) FROM tblTodoLists WHERE assignedTo='.$userId.' AND itemStatus!='.TODO_ITEM_CLOSED;
		return dbOneResultItem($db, $sql);
	}

	/* Returns only CLOSED assigned tasks */
	function getClosedAssignedTasks(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$sql = 'SELECT * FROM tblTodoLists WHERE assignedTo='.$userId.' AND itemStatus='.TODO_ITEM_CLOSED.' ORDER BY timestamp ASC';
		return dbArray($db, $sql);
	}
	
	function getClosedAssignedTasksCount(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$sql = 'SELECT COUNT(itemId) FROM tblTodoLists WHERE assignedTo='.$userId.' AND itemStatus='.TODO_ITEM_CLOSED;
		return dbOneResultItem($db, $sql);
	}

	
	/* Returns the number of items in a todo category that is not CLOSED */
	function getTodoCategoryItemsCount(&$db, $categoryId)
	{
		if (!is_numeric($categoryId)) return false;

		$sql = 'SELECT COUNT(itemId) FROM tblTodoLists WHERE categoryId='.$categoryId.' AND itemStatus!='.TODO_ITEM_CLOSED;
		return dbOneResultItem($db, $sql);
	}
	
	/* Returns the number of items in all todo categories that is not CLOSED */
	function getTodoItemsCount(&$db)
	{
		$sql = 'SELECT COUNT(itemId) FROM tblTodoLists WHERE itemStatus!='.TODO_ITEM_CLOSED;
		return dbOneResultItem($db, $sql);
	}

	function getClosedTodoCategoryItems(&$db, $categoryId)
	{
		if (!is_numeric($categoryId)) return false;
		
		$sql = 'SELECT COUNT(itemId) FROM tblTodoLists WHERE categoryId='.$categoryId.' AND itemStatus='.TODO_ITEM_CLOSED;
		return dbOneResultItem($db, $sql);
	}

	function addBugReport(&$db, $desc)
	{
		$desc = dbAddSlashes($db, strip_tags($desc));

		$sql = 'INSERT INTO tblBugReports SET bugDesc="'.$desc.'",bugCreator='.$_SESSION['userId'].',reportMethod=0,timestamp='.time();
		dbQuery($db, $sql);
	}
	
	function getBugReports(&$db)
	{
		$sql  = 'SELECT tblBugReports.*,tblUsers.userName FROM tblBugReports ';
		$sql .= 'LEFT OUTER JOIN tblUsers ON (tblBugReports.bugCreator=tblUsers.userId) ';
		$sql .= 'WHERE bugClosed=0 ';
		$sql .= 'ORDER By tblBugReports.timestamp ASC';
		return dbArray($db, $sql);
	}

	function getClosedBugReports(&$db)
	{
		$sql  = 'SELECT tblBugReports.*,tblUsers.userName FROM tblBugReports ';
		$sql .= 'LEFT OUTER JOIN tblUsers ON (tblBugReports.bugCreator=tblUsers.userId) ';
		$sql .= 'WHERE bugClosed=1 ';
		$sql .= 'ORDER By tblBugReports.timestamp ASC';
		return dbArray($db, $sql);
	}

	function getClosedBugReportsCount(&$db)
	{
		$sql = 'SELECT COUNT(bugId) FROM tblBugReports WHERE bugClosed=1';
		return dbOneResultItem($db, $sql);
	}

	/* Returnerar antal ÖPPNA buggar */
	function getBugReportsCount(&$db)
	{
		$sql = 'SELECT COUNT(bugId) FROM tblBugReports WHERE bugClosed=0';
		return dbOneResultItem($db, $sql);
	}

	function getBugReport(&$db, $bugId)
	{
		if (!is_numeric($bugId)) return false;

		$sql  = 'SELECT tblBugReports.*,tblUsers.userName FROM tblBugReports ';
		$sql .= 'INNER JOIN tblUsers ON (tblBugReports.bugCreator=tblUsers.userId) ';
		$sql .= 'WHERE bugId='.$bugId;
		return dbOneResult($db, $sql);	
	}

	/* Flyttar buggrapporten från tblBugReports till tblTodoLists */
	/* Returnerar ID för det nya todo-itemet */
	//userId : den som flyttar buggen, creator = den som skapat buggen */
	function moveBugReport(&$db, $userId, $bugId, $creator, $desc, $details, $timestamp, $category, $categoryId)
	{
		if (!is_numeric($bugId) || !is_numeric($creator) || !is_numeric($timestamp) || !is_numeric($category) || !is_numeric($categoryId)) return false;
		$desc = dbAddSlashes($db, $desc);
		$details = dbAddSlashes($db, $details);

		dbQuery($db, 'INSERT INTO tblTodoLists SET categoryId='.$categoryId.',itemDesc="'.$desc.'",itemDetails="'.$details.'",itemCategory='.$category.',timestamp='.$timestamp.',itemCreator='.$creator);
		$itemId = $db['insert_id'];
		dbQuery($db, 'DELETE FROM tblBugReports WHERE bugId='.$bugId);
		
		$comment = 'Imported by '.getUserName($db, $userId).' from a report by '.getUserName($db, $creator).'.';
		$sql = 'INSERT INTO tblTodoListComments SET itemId='.$itemId.',itemComment="'.$comment.'",timestamp='.time().',userId=0';
		dbQuery($db, $sql);

		return $itemId;
	}
	
	function closeBugReport(&$db, $bugId, $reason)
	{
		if (!is_numeric($bugId) || !is_numeric($reason)) return false;
		
		dbQuery($db, 'UPDATE tblBugReports SET bugClosed=1, bugClosedReason='.$reason.' WHERE bugId='.$bugId);
	}
	
	
	
	
	
	
	
	
	/* kategorier sparade i databasen */
	function addTodoCategory(&$db, $categoryName, $parentId)
	{
		if (!is_numeric($parentId)) return false;

		$categoryName = dbAddSlashes($db, $categoryName);
		$sql = 'INSERT INTO tblTodoListCategories SET categoryName="'.$categoryName.'", parentId='.$parentId.', creatorId='.$_SESSION['userId'].', createdTime='.time();
		dbQuery($db, $sql);
	}

	function getTodoCategories(&$db, $parentId)
	{
		if (!is_numeric($parentId)) return false;

		$sql = 'SELECT * FROM tblTodoListCategories WHERE parentId='.$parentId.' ORDER BY categoryName ASC';
		return dbArray($db, $sql);
	}

	function getTodoCategoryCount(&$db, $parentId = 0)
	{
		if (!is_numeric($parentId)) return false;

		$sql = 'SELECT COUNT(categoryId) FROM tblTodoListCategories WHERE parentId='.$parentId;
		return dbOneResultItem($db, $sql);
	}

	function deleteTodoCategory(&$db, $categoryId)
	{
		if (!is_numeric($categoryId)) return false;
		
		$sql = 'DELETE FROM tblTodoListCategories WHERE categoryId='.$categoryId;
		dbQuery($db, $sql);

		return true;
	}
	
	function getTodoCategoryName(&$db, $categoryId)
	{
		if (!is_numeric($categoryId)) return false;
		
		$sql  = 'SELECT t1.categoryName, t2.categoryName AS parentName FROM tblTodoListCategories AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblTodoListCategories AS t2 ON (t1.parentId=t2.categoryId) ';
		$sql .= 'WHERE t1.categoryId='.$categoryId;

		$data = dbOneResult($db, $sql);
		if ($data['parentName']) {		
			return $data['parentName'].' - '.$data['categoryName'];
		} else {
			return $data['categoryName'];
		}
	}

?>