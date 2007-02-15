<?
	function addNews(&$db, $userId, $title, $body, $topublish, $rss_enabled)
	{
		if (!is_numeric($userId) || !is_numeric($topublish) || !is_numeric($rss_enabled)) return false;

		$title = dbAddSlashes($db, $title);
		$body = dbAddSlashes($db, $body);
		
		$now = time();
		if ($topublish < time()) $topublish = $now;
		
		$sql = 'INSERT INTO tblNews SET title="'.$title.'",body="'.$body.'",rss_enabled='.$rss_enabled.',userId='.$userId.',timetopublish='.$topublish.',timecreated='.$now.',timeedited='.$now;
		dbQuery($db, $sql);

		return true;
	}
	
	function updateNews(&$db, $newsId, $title, $body, $topublish, $rss_enabled)
	{
		if (!is_numeric($newsId) || !is_numeric($topublish) || !is_numeric($rss_enabled)) return false;

		$title = dbAddSlashes($db, $title);
		$body = dbAddSlashes($db, $body);
		
		$sql = 'UPDATE tblNews SET title="'.$title.'",body="'.$body.'",rss_enabled='.$rss_enabled.',timeedited='.time().' WHERE newsId='.$newsId;
		dbQuery($db, $sql);

		return true;
	}

	function removeNews(&$db, $newsId)
	{
		if (!is_numeric($newsId)) return false;
		
		dbQuery($db, 'DELETE FROM tblNews WHERE newsId='.$newsId);
		return true;
	}
	
	function getAllNews(&$db)
	{
		$sql  = 'SELECT t1.*,t2.userName FROM tblNews AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$sql .= 'ORDER BY timecreated DESC';

		return dbArray($db, $sql);
	}
	
	function getPublishedNews(&$db, $limit='')
	{
		$sql  = 'SELECT t1.*,t2.userName FROM tblNews AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$sql .= 'WHERE timetopublish<'.time().' ';
		$sql .= 'ORDER BY timetopublish DESC';
		if ($limit) {
			$sql .= ' LIMIT 0,'.$limit;
		}

		return dbArray($db, $sql);		
	}
	
	function getNewsItem(&$db, $newsId)
	{
		if (!is_numeric($newsId)) return false;

		$sql  = 'SELECT t1.*,t2.userName FROM tblNews AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$sql .= 'WHERE t1.newsId='.$newsId;
		return dbOneResult($db, $sql);
	}

	function displayLatestNews(&$db, $limit=3)
	{
		$list = getPublishedNews($db, $limit);

		$str = '';

		if (count($list)) {
			$server_url = 'http://'.$_SERVER["SERVER_NAME"];
			if ($_SERVER["SERVER_PORT"] != 80) $server_url .= ':'.$_SERVER["SERVER_PORT"];
			$server_url .= '/comm/';

			$str .= '<link rel="alternate" type="application/rss+xml" title="Nyheter" href="'.$server_url.'/rss/news.php">';
		}

		for ($i=0; $i<count($list); $i++) {
			$str .= '<div class="newsitem">';
			$str .= '<b>'.$list[$i]['title'].'</b> ';
			$str .= '(av '.nameLink($list[$i]['userId'], $list[$i]['userName']).', publicerades '.strtolower(getRelativeTimeLong($list[$i]['timetopublish'])).')<br>';
			$str .= $list[$i]['body'].'<br>';
			if ($list[$i]['timeedited'] > $list[$i]['timecreated']) {
				$str .= 'Uppdaterades '.strtolower(getRelativeTimeLong($list[$i]['timeedited'])).'<br>';
			}
			$str .= '</div><br>';
		}
		
		if ($_SESSION['isAdmin']) {
			$str .= '<a href="admin_news.php">Admin news</a><br><br>';
		}

		return $str;
	}
?>