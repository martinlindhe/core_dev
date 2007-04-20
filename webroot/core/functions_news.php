<?
	function addNews($title, $body, $topublish, $rss_enabled)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($rss_enabled)) return false;
		
		//$topublish är en sträng som innehåller ett datum, helst i detta format: 2007-04-02 18:22
		$topublish_time = strtotime($topublish);
		if ($topublish_time < time()) $topublish_time = time();
		$topublish = sql_datetime($topublish_time);

		$q = 'INSERT INTO tblNews SET title="'.$db->escape($title).'",body="'.$db->escape($body).'",rss_enabled='.$rss_enabled.',creatorId='.$session->id.',timeToPublish="'.$topublish.'",timeCreated=NOW()';
		$db->query($q);

		return true;
	}
	
	function updateNews($newsId, $title, $body, $topublish, $rss_enabled)
	{
		global $db;

		if (!is_numeric($newsId) || !is_numeric($topublish) || !is_numeric($rss_enabled)) return false;

		$q = 'UPDATE tblNews SET title="'.$db->escape($title).'",body="'.$db->escape($body).'",rss_enabled='.$rss_enabled.',timeEdited=NOW() WHERE newsId='.$newsId;
		$db->query($q);

		return true;
	}

	function removeNews($newsId)
	{
		global $db;

		if (!is_numeric($newsId)) return false;
		
		$db->query('DELETE FROM tblNews WHERE newsId='.$newsId);
		return true;
	}
	
	function getAllNews()
	{
		global $db;

		$q  = 'SELECT t1.*,t2.userName AS creatorName FROM tblNews AS t1 ';
		$q .= 'INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
		$q .= 'ORDER BY timeCreated DESC';

		return $db->getArray($q);
	}
	
	function getPublishedNews($limit = '')
	{
		global $db;

		$q  = 'SELECT t1.*,t2.userName AS creatorName FROM tblNews AS t1 ';
		$q .= 'INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
		$q .= 'WHERE timeToPublish<NOW() ';
		$q .= 'ORDER BY timeToPublish DESC';
		if ($limit) $q .= ' LIMIT 0,'.$limit;
		
		return $db->getArray($q);		
	}
	
	function getNewsItem($newsId)
	{
		global $db;

		if (!is_numeric($newsId)) return false;

		$q  = 'SELECT t1.*,t2.userName AS creatorName FROM tblNews AS t1 ';
		$q .= 'INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
		$q .= 'WHERE t1.newsId='.$newsId;
		return $db->getOneRow($q);
	}

	function snowNews($limit = 3)
	{
		global $db;

		$list = getPublishedNews($limit);

		foreach ($list as $row) {
			echo '<div class="newsitem">';
			echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$row['newsId'].'">'.$row['title'].'</a> ';
			echo '(av '.$row['creatorName'].', publicerades '.$row['timeToPublish'].')<br/>';
			echo $row['body'].'<br/>';
			if ($row['timeEdited'] > $row['timeCreated']) {
				echo 'Uppdaterades '.$row['timeEdited'].'<br/>';
			}
			echo '</div><br/>';
		}
	}
?>