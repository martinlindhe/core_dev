<?
	require_once('functions_comments.php');	//for news comment support

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
		global $db, $session;

		if (!$session->isAdmin || !is_numeric($newsId) || !is_numeric($topublish) || !is_numeric($rss_enabled)) return false;

		$q = 'UPDATE tblNews SET title="'.$db->escape($title).'",body="'.$db->escape($body).'",rss_enabled='.$rss_enabled.',timeEdited=NOW(),editorId='.$session->id.' WHERE newsId='.$newsId;
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

		$q  = 'SELECT t1.*,t2.userName AS creatorName,t3.userName AS editorName FROM tblNews AS t1 ';
		$q .= 'INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
		$q .= 'LEFT OUTER JOIN tblUsers AS t3 ON (t1.editorId=t3.userId) ';
		$q .= 'ORDER BY timeCreated DESC';

		return $db->getArray($q);
	}
	
	//used by getPublishedNews() and rss_news.php
	function getPublishedNews($limit = '')
	{
		global $db;

		$q  = 'SELECT t1.*,t2.userName AS creatorName, t3.userName AS editorName FROM tblNews AS t1 ';
		$q .= 'INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
		$q .= 'LEFT OUTER JOIN tblUsers AS t3 ON (t1.editorId=t3.userId) ';
		$q .= 'WHERE timeToPublish<NOW() ';
		$q .= 'ORDER BY timeToPublish DESC';
		if ($limit) $q .= ' LIMIT 0,'.$limit;
		
		return $db->getArray($q);		
	}
	
	function getNewsItem($newsId)
	{
		global $db;

		if (!is_numeric($newsId)) return false;

		$q  = 'SELECT t1.*,t2.userName AS creatorName,t3.userName AS editorName FROM tblNews AS t1 ';
		$q .= 'INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
		$q .= 'LEFT OUTER JOIN tblUsers AS t3 ON (t1.editorId=t3.userId) ';
		$q .= 'WHERE t1.newsId='.$newsId;
		return $db->getOneRow($q);
	}

	function snowNews($limit = 3)
	{
		global $db;
		
		if (!empty($_GET['news']) && is_numeric($_GET['news'])) {
			/* Visar en artikel */
			
			$row = getNewsItem($_GET['news']);

			echo '<h2>'.$row['title'].'</h2>';
			echo 'By '.$row['creatorName'].', published '.$row['timeToPublish'].'<br/>';
			$art = parseArticle($row['body']);
			
			echo '<h4>'.$art['head'].'</h4>';
			echo $art['body'].'<br/>';

			if ($row['timeEdited'] > $row['timeCreated']) {
				echo '<i>Updated '.$row['timeEdited'].' by '.$row['editorName'].'</i><br/>';
			}

			showComments(COMMENT_NEWS, $_GET['news']);
			return;
		}

		/* visar en lista med de senaste nyheternas headlines */
		$list = getPublishedNews($limit);

		foreach ($list as $row) {
			echo '<div class="newsitem">';
			echo '<a href="'.$_SERVER['PHP_SELF'].'?news='.$row['newsId'].'">'.$row['title'].'</a> ';
			echo ', published '.$row['timeToPublish'].'<br/>';
			$art = parseArticle($row['body']);
			echo $art['head'].'<br/>';
			echo '</div><br/>';
		}
	}
?>