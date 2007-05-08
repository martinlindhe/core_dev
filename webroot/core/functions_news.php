<?
	require_once('atom_comments.php');		//for news comment support
	require_once('atom_categories.php');	//for news categories support

	function addNews($title, $body, $topublish, $rss_enabled, $category_id = 0)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($rss_enabled) || !is_numeric($category_id)) return false;
		
		//$topublish är en sträng som innehåller ett datum, helst i detta format: 2007-04-02 18:22
		$topublish_time = strtotime($topublish);
		if ($topublish_time < time()) $topublish_time = time();
		$topublish = sql_datetime($topublish_time);

		$q = 'INSERT INTO tblNews SET title="'.$db->escape($title).'",body="'.$db->escape($body).'",rss_enabled='.$rss_enabled.',creatorId='.$session->id.',timeToPublish="'.$topublish.'",timeCreated=NOW(),categoryId='.$category_id;
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
	function getPublishedNews($_categoryId = 0, $limit = '')
	{
		global $db;
		
		if (!is_numeric($_categoryId)) return false;

		$q  = 'SELECT t1.*,t2.userName AS creatorName, t3.userName AS editorName FROM tblNews AS t1 ';
		$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
		$q .= 'LEFT JOIN tblUsers AS t3 ON (t1.editorId=t3.userId) ';
		$q .= 'WHERE timeToPublish<NOW() ';
		if  ($_categoryId) $q .= ' AND categoryId='.$_categoryId.' ';
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

	function showNews($limit = 3)
	{
		global $db;
		
		if (!empty($_GET['news']) && is_numeric($_GET['news'])) {
			/* Visar en artikel */
			
			$row = getNewsItem($_GET['news']);

			echo '<h2>'.$row['title'].'</h2>';
			if ($row['categoryId']) {
				echo '<a href="news.php?cat='.$row['categoryId'].'">'.getCategoryName($row['categoryId']).'</a><br/>';
			}
			echo 'By '.$row['creatorName'].', published '.$row['timeToPublish'].'<br/>';
			$art = parseArticle($row['body']);
			
			if ($art['head']) echo '<h4>'.$art['head'].'</h4>';
			echo $art['body'].'<br/>';

			if ($row['timeEdited'] > $row['timeCreated']) {
				echo '<i>Updated '.$row['timeEdited'].' by '.$row['editorName'].'</i><br/>';
			}

			showComments(COMMENT_NEWS, $_GET['news']);
			return;
		}
		
		$_cat_id = 0;
		if (!empty($_GET['cat']) && is_numeric($_GET['cat'])) $_cat_id = $_GET['cat'];

		/* visar en lista med de senaste nyheternas headlines, alla kategorier */
		$list = getPublishedNews($_cat_id, $limit);

		foreach ($list as $row) {
			echo '<div class="newsitem">';
			echo '<a href="'.$_SERVER['PHP_SELF'].'?news='.$row['newsId'].'">'.$row['title'].'</a>, published '.$row['timeToPublish'];
			if ($row['categoryId']) {
				echo ' - <a href="news.php?cat='.$row['categoryId'].'">'.getCategoryName($row['categoryId']).'</a>';
			}
			echo '<br/>';

			$art = parseArticle($row['body']);
			echo $art['head'].'<br/>';
			echo '</div><br/>';
		}
	}
?>