<?
	$config['blog']['moderation'] = false;
	
	function addBlog($categoryId, $title, $body)
	{
		global $db, $session, $config;

		if (!$session->id || !is_numeric($categoryId)) return false;

		$title = $db->escape($title);
		$body = $db->escape($body);
		
		$q = 'INSERT INTO tblBlogs SET categoryId='.$categoryId.',userId='.$session->id.',blogTitle="'.$title.'",blogBody="'.$body.'",timeCreated=NOW()';
		$db->query($q);
		$blogId = $db->insert_id;

		/* Add entry to moderation queue */
		if ($config['blog']['moderation']) {
			if (isSensitive($db, $title) || isSensitive($db, $body)) addToModerationQueue($db, $blogId, MODERATION_SENSITIVE_BLOG);
		}

		return $blogId;
	}

	function deleteBlog($blogId, $ownerId = 0)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($blogId) || !is_numeric($ownerId)) return false;

		$q = 'DELETE FROM tblBlogs WHERE blogId='.$blogId;
		if ($ownerId) $q .= ' AND userId='.$ownerId;

		$db->query($q);
	}

	function updateBlog($blogId, $categoryId, $title, $body)
	{
		global $db, $session, $config;
		
		if (!$session->id || !is_numeric($blogId) || !is_numeric($categoryId)) return false;

		$title = $db->escape($title);
		$body = $db->escape($body);

		$q = 'UPDATE tblBlogs SET categoryId='.$categoryId.',blogTitle="'.$title.'",blogBody="'.$body.'",timeUpdated=NOW() WHERE blogId='.$blogId;
		$db->query($q);

		/* Add entry to moderation queue */
		if ($config['blog']['moderation']) {
			if (isSensitive($db, $title) || isSensitive($db, $body)) addToModerationQueue($db, $blogId, MODERATION_SENSITIVE_BLOG);
		}
	}

	/* Sorterar resultat per kategori för snygg visning */
	function getBlogsByCategory($userId, $limit = 0)
	{
		global $db;

		if (!is_numeric($userId) || !is_numeric($limit)) return false;

		$q  = 'SELECT t1.*,t2.categoryName,t2.globalCategory FROM tblBlogs AS t1';
		$q .= ' LEFT OUTER JOIN tblCategories AS t2 ON (t1.categoryId=t2.categoryId AND t2.categoryType='.CATEGORY_BLOGS.')';
		$q .= ' WHERE t1.userId='.$userId;

		/* Return order: First blogs categorized in global categories, then blogs categorized in user's categories, then uncategorized blogs */
		$q .= ' ORDER BY t2.globalCategory DESC, t1.categoryId ASC, t1.timeCreated DESC';
		if ($limit) $q .= ' LIMIT 0,'.$limit;

		return $db->getArray($q);
	}
	
	/* Returns the latest blogs posted on the site */
	function getLatestBlogs($_cnt = 5)
	{
		global $db;

		if (!is_numeric($_cnt)) return false;

		$q  = 'SELECT t1.*,t2.userName FROM tblBlogs AS t1 ';
		$q .= 'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$q .= 'ORDER BY t1.timeCreated DESC';
		if ($_cnt) $q .= ' LIMIT '.$_cnt;

		return $db->getArray($q);
	}

	function getBlog($blogId)
	{
		global $db;

		if (!is_numeric($blogId)) return false;
		
		$q  = 'SELECT t1.*,t2.categoryName,t3.userName FROM tblBlogs AS t1 ';
		$q .= 'LEFT OUTER JOIN tblCategories AS t2 ON (t1.categoryId=t2.categoryId AND t2.categoryType='.CATEGORY_BLOGS.') ';
		$q .= 'INNER JOIN tblUsers AS t3 ON (t1.userId=t3.userId) ';
		$q .= 'WHERE t1.blogId='.$blogId;

		return $db->getOneRow($q);
	}
	
	/* Returns all blogs from $userId for the specified month */
		//fixme: this function is broken, the SQL needs updating for DATETIME format change
	
	function getBlogsByMonth($userId, $month, $year, $order_desc = true)
	{
		global $db;

		if (!is_numeric($userId) || !is_numeric($year) || !is_numeric($month) || !is_bool($order_desc)) return false;

		$time_start = mktime(0, 0, 0, $month, 1, $year);			//00:00 at first day of month
		$time_end   = mktime(23, 59, 59, $month+1, 0, $year);	//23:59 at last day of month

		$q  = 'SELECT * FROM tblBlogs ';
		$q .= 'WHERE userId='.$userId.' ';
		$q .= 'AND timeCreated BETWEEN "'.sql_datetime($time_start).'" AND "'.sql_datetime($time_end).'"';
		if ($order_desc === true) {
			$q .= ' ORDER BY timeCreated DESC';
		} else {
			$q .= ' ORDER BY timeCreated ASC';
		}
		return $db->getArray($q);
	}
	
?>