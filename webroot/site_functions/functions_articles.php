<?

	/*
		skapad 2003.04.04
	
	*/
	
	
	function addArticle(&$db, $userId, $title, $body)
	{
		if (!is_numeric($userId)) return false;
		$title = dbAddSlashes($title);
		$body = dbAddSlashes($body);
		
		$sql = 'INSERT INTO tblArticles SET userId='.$userId.',title="'.$title.'",body="'.$body.'",timecreated='.time().',timemoderated=0,published=0';
		dbQuery($db, $sql);
	}
	
	
	function getUnmoderatedArticles(&$db)
	{		
		//returnerar älsta artiklarna först, så man inte regelmässigt råkar missa att moderera de älsta artiklarna
		
		$sql  = 'SELECT t1.*,t2.userName FROM tblArticles AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$sql .= 'WHERE published=0 AND refused=0 ';
		$sql .= 'ORDER BY timecreated ASC';

		return dbArray($db, $sql);
	}
	
	function getUnmoderatedArticlesCount(&$db)
	{		
		$sql  = 'SELECT COUNT(articleId) AS cnt FROM tblArticles ';
		$sql .= 'WHERE published=0 AND refused=0';
		
		return dbOneResultItem($db, $sql);
	}
	
	function getUnpublishedArticles(&$db)
	{
		//returnerar älsta artiklarna först (dvs dom som närmast kommer publiceras)
		
		$sql  = 'SELECT t1.*,t2.userName FROM tblArticles AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$sql .= 'WHERE published=1 AND timepublished>'.time().' ';
		$sql .= 'ORDER BY timepublished ASC';

		return dbArray($db, $sql);
	}

	function getPublishedArticles(&$db)
	{
		//returnerar älsta artiklarna först (dvs dom som närmast kommer publiceras)
		
		$sql  = 'SELECT t1.*,t2.userName FROM tblArticles AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$sql .= 'WHERE published=1 AND timepublished<'.time().' ';
		$sql .= 'ORDER BY timepublished ASC';

		return dbArray($db, $sql);
	}

	function getLastPublishedArticles(&$db, $count = 0)
	{
		if (!is_numeric($count)) return false;

		//returnerar senaste artiklarna först (dvs dom som publicerades senast)
		
		$sql  = 'SELECT t1.*,t2.userName FROM tblArticles AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$sql .= 'WHERE published=1 AND timepublished<'.time().' ';
		$sql .= 'ORDER BY timepublished DESC';
		if ($count>0) {
			$sql .= ' LIMIT 0,'.$count;
		}

		return dbArray($db, $sql);
	}


	function getArticle(&$db, $articleId)
	{
		if (!is_numeric($articleId)) return false;

		$sql  = 'SELECT t1.*,t2.userName FROm tblArticles AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$sql .= 'WHERE articleId='.$articleId;

		return dbOneResult($db, $sql);
	}

	function refuseArticle(&$db, $articleId)
	{
		if (!is_numeric($articleId)) return false;

		$sql = 'UPDATE tblArticles SET refused=1,timemoderated='.time().' WHERE articleId='.$articleId;
		dbQuery($db, $sql);
	}
	
	function acceptArticle(&$db, $articleId, $timetopublish)
	{
		if (!is_numeric($articleId)) return false;

		$sql = 'UPDATE tblArticles SET refused=0,published=1,timepublished='.$timetopublish.',timemoderated='.time().' WHERE articleId='.$articleId;
		dbQuery($db, $sql);
	}
	
	function removeArticle(&$db, $articleId)
	{
		if (!is_numeric($articleId)) return false;
		
		$sql = 'DELETE FROM tblArticles WHERE articleId='.$articleId;
		dbQuery($db, $sql);
		
		return true;
	}
?>