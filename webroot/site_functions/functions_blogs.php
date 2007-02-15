<?
	/*
		functions_blogs.php - Funktioner för bloggar
		
		uses functions_categories.php for blog categories
	*/
	
	$config['blog']['moderation'] = false;
	
	function addBlog(&$db, $categoryId, $title, $body)
	{
		global $config;

		if (!is_numeric($categoryId)) return false;

		$title = dbAddSlashes($db, $title);
		$body = dbAddSlashes($db, $body);
		
		$sql = 'INSERT INTO tblBlogs SET categoryId='.$categoryId.',userId='.$_SESSION['userId'].',blogTitle="'.$title.'",blogBody="'.$body.'",timeCreated=NOW()';
		dbQuery($db, $sql);
		$blogId = $db['insert_id'];

		/* Add entry to moderation queue */
		if ($config['blog']['moderation']) {
			if (isSensitive($db, $title) || isSensitive($db, $body)) addToModerationQueue($db, $blogId, MODERATION_SENSITIVE_BLOG);
		}

		return $blogId;
	}

	function deleteBlog(&$db, $blogId, $ownerId = 0)
	{
		if (!$_SESSION['loggedIn'] || !is_numeric($blogId) || !is_numeric($ownerId)) return false;

		$sql = 'DELETE FROM tblBlogs WHERE blogId='.$blogId;
		if ($ownerId) $sql .= ' AND userId='.$ownerId;
		
		dbQuery($db, $sql);
	}

	function updateBlog(&$db, $blogId, $categoryId, $title, $body)
	{
		global $config;
		
		if (!$_SESSION['loggedIn'] || !is_numeric($blogId) || !is_numeric($categoryId)) return false;

		$title = dbAddSlashes($db, $title);
		$body = dbAddSlashes($db, $body);

		$sql = 'UPDATE tblBlogs SET categoryId='.$categoryId.',blogTitle="'.$title.'",blogBody="'.$body.'",timeUpdated=NOW() WHERE blogId='.$blogId;
		dbQuery($db, $sql);
		
		/* Add entry to moderation queue */
		if ($config['blog']['moderation']) {
			if (isSensitive($db, $title) || isSensitive($db, $body)) addToModerationQueue($db, $blogId, MODERATION_SENSITIVE_BLOG);
		}
	}

	/*returns latest first */
	function getBlogs(&$db, $userId, $limit = 0)
	{
		if (!is_numeric($userId) || !is_numeric($limit)) return false;

		$sql  = 'SELECT t1.* FROM tblBlogs AS t1';
		$sql .= ' WHERE t1.userId='.$userId;
		$sql .= ' ORDER BY t1.timeCreated DESC';
		if ($limit) $sql .= ' LIMIT 0,'.$limit;

		return dbArray($db, $sql);
	}
	
	/* Sorterar resultat per kategori för snygg visning */
	function getBlogsByCategory(&$db, $userId, $limit = 0)
	{
		if (!is_numeric($userId) || !is_numeric($limit)) return false;

		$sql  = 'SELECT t1.*,t2.categoryName,t2.globalCategory FROM tblBlogs AS t1';
		$sql .= ' LEFT OUTER JOIN tblCategories AS t2 ON (t1.categoryId=t2.categoryId AND t2.categoryType='.CATEGORY_BLOGS.')';
		$sql .= ' WHERE t1.userId='.$userId;

		/* Return order: First blogs categorized in global categories, then blogs categorized in user's categories, then uncategorized blogs */
		$sql .= ' ORDER BY t2.globalCategory DESC, t1.categoryId ASC, t1.timeCreated DESC';
		if ($limit) $sql .= ' LIMIT 0,'.$limit;

		return dbArray($db, $sql);
	}
	
	function getBlogsNewest(&$db, $ammount = 5)
	{
		if (!is_numeric($ammount)) return false;

		$sql  = 'SELECT t1.*,t2.userName FROM tblBlogs AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$sql .= 'ORDER BY t1.timeCreated DESC';
		if ($ammount) 
			$sql .= ' LIMIT 0,'.$ammount;

		return dbArray($db, $sql);
	}

	function getBlog(&$db, $blogId)
	{
		if (!is_numeric($blogId)) return false;
		
		$sql  = 'SELECT t1.*,t2.categoryName,t3.userName FROM tblBlogs AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblCategories AS t2 ON (t1.categoryId=t2.categoryId AND t2.categoryType='.CATEGORY_BLOGS.') ';
		$sql .= 'INNER JOIN tblUsers AS t3 ON (t1.userId=t3.userId) ';
		$sql .= 'WHERE t1.blogId='.$blogId;

		return dbOneResult($db, $sql);
	}
	
	/* Returns all blogs from $userId for the specified month */
	/*
	
		//fixme: this function is broken, the SQL needs updating for DATETIME format change
	
	function getBlogsByMonth(&$db, $userId, $month, $year, $order_desc = true)
	{
		if (!is_numeric($userId) || !is_numeric($year) || !is_numeric($month) || !is_bool($order_desc)) return false;
		
		$time_start = mktime(0, 0, 0, $month, 1, $year);			//00:00 at first day of month
		$time_end   = mktime(23, 59, 59, $month+1, 0, $year);	//23:59 at last day of month
		
		$sql  = 'SELECT * FROM tblBlogs ';
		$sql .= 'WHERE userId='.$userId.' ';
		$sql .= 'AND timeCreated BETWEEN '.$time_start.' AND '.$time_end;
		if ($order_desc === true) {
			$sql .= ' ORDER BY timeCreated DESC';
		} else {
			$sql .= ' ORDER BY timeCreated ASC';
		}
		return dbArray($db, $sql);
	}
	
	*/

?>