<?
	//functions for creating & handling of timed subscriptions.
	// a user can add a subscription & get a reminder by SMS, email or other means thru this interface.
	// the system could also automatically add various reminders


	function addTimedSubscriptionCategory(&$db, $categoryName, $parentId = 0)
	{
		if (!is_numeric($parentId)) return false;

		$categoryName = dbAddSlashes($db, trim($categoryName));
		
		$sql = 'INSERT INTO tblTimedSubscriptionCategories SET parentId='.$parentId.', categoryName="'.$categoryName.'", timeCreated='.time().', creatorId='.$_SESSION['userId'];
		dbQuery($db, $sql);

		return $db['insert_id'];
	}
	
	function getTimedSubscriptionCategories(&$db, $parentId = 0)
	{
		if (!is_numeric($parentId)) return false;

		$sql = 'SELECT * FROM tblTimedSubscriptionCategories WHERE parentId='.$parentId;

		return dbArray($db, $sql);
	}

	function getTimedSubscriptionCategory(&$db, $categoryId)
	{
		if (!is_numeric($categoryId)) return false;
		
		$sql =
			'SELECT t1.*,t2.categoryName AS parentName FROM tblTimedSubscriptionCategories AS t1 '.
			'LEFT OUTER JOIN tblTimedSubscriptionCategories AS t2 ON (t1.parentId=t2.categoryId) '.
			'WHERE t1.categoryId='.$categoryId;
		
		return dbOneResult($db, $sql);
	}

	function getTimedSubscriptionCategoryName(&$db, $categoryId)
	{
		if (!is_numeric($categoryId)) return false;
		
		$sql = 'SELECT categoryName FROM tblTimedSubscriptionCategories WHERE categoryId='.$categoryId;

		return dbOneResultItem($db, $sql);
	}
	
	function deleteTimedSubscriptionCategory(&$db, $categoryId)
	{
		if (!is_numeric($categoryId)) return false;
		
		$sql = 'DELETE FROM tblTimedSubscriptionCategories WHERE categoryId='.$categoryId;
		dbQuery($db, $sql);

		$sql = 'DELETE FROM tblTimedSubscriptionCategories WHERE parentId='.$categoryId;
		dbQuery($db, $sql);

		return true;
	}
	
	function updateTimedSubscriptionCategory(&$db, $categoryId, $categoryName)
	{
		if (!is_numeric($categoryId)) return false;
		
		$categoryName = dbAddSlashes($db, trim($categoryName));
		
		$sql = 'UPDATE tblTimedSubscriptionCategories SET categoryName="'.$categoryName.'" WHERE categoryId='.$categoryId;
		dbQuery($db, $sql);

		return true;
	}
	

	function getTimedSubscriptions(&$db)
	{
		$sql = 'SELECT * FROM tblTimedSubscriptions';

		return dbArray($db, $sql);
	}
	
?>