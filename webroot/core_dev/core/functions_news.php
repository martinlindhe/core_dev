<?
	require_once('atom_comments.php');		//for news comment support
	require_once('atom_categories.php');	//for news categories support
	require_once('atom_rating.php');			//for news rating support

	$config['news']['allowed_tabs'] = array('News', 'NewsEdit', 'NewsDelete', 'NewsNewCategory', 'NewsComment', 'NewsFiles');
	$config['news']['allow_rating'] = true;	//allow users to rate articles

	function addNews($title, $body, $topublish, $rss_enabled, $category_id = 0)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($rss_enabled) || !is_numeric($category_id)) return false;

		$q = 'SELECT newsId FROM tblNews WHERE title="'.$db->escape($title).'" AND body="'.$db->escape($body).'"';
		if ($db->getOneItem($q)) return false;

		$topublish_time = strtotime($topublish);
		if ($topublish_time < time()) $topublish_time = time();

		$q = 'INSERT INTO tblNews SET title="'.$db->escape($title).'",body="'.$db->escape($body).'",rss_enabled='.$rss_enabled.',creatorId='.$session->id.',timeToPublish="'.sql_datetime($topublish_time).'",timeCreated=NOW(),categoryId='.$category_id;
		$db->insert($q);

		return true;
	}

	function updateNews($newsId, $categoryId, $title, $body, $topublish, $rss_enabled)
	{
		global $db, $session;

		if (!$session->isAdmin || !is_numeric($newsId) || !is_numeric($categoryId) || !is_numeric($rss_enabled)) return false;

		$topublish = strtotime($topublish);

		$q = 'UPDATE tblNews SET categoryId='.$categoryId.',title="'.$db->escape($title).'",body="'.$db->escape($body).'",rss_enabled='.$rss_enabled.',timeEdited=NOW(),editorId='.$session->id.' WHERE newsId='.$newsId;
		$db->query($q);

		return true;
	}

	function removeNews($_id)
	{
		global $db, $session;
		if (!$session->isAdmin || !is_numeric($_id)) return false;

		$db->query('UPDATE tblNews SET deletedBy='.$session->id.',timeDeleted=NOW() WHERE newsId='.$_id);
		return true;
	}

	function getAllNews()
	{
		global $db;

		$q  = 'SELECT t1.*,t2.userName AS creatorName,t3.userName AS editorName FROM tblNews AS t1 ';
		$q .= 'INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
		$q .= 'LEFT OUTER JOIN tblUsers AS t3 ON (t1.editorId=t3.userId) ';
		$q .= 'WHERE t1.deletedBy=0 ';
		$q .= 'ORDER BY timeCreated DESC';

		return $db->getArray($q);
	}

	//used by showNews() and /core/rss_news.php
	function getPublishedNews($_categoryId = 0, $limit = '')
	{
		global $db;

		if (!is_numeric($_categoryId)) return false;

		$q  = 'SELECT t1.*,t2.userName AS creatorName, t3.userName AS editorName FROM tblNews AS t1 ';
		$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
		$q .= 'LEFT JOIN tblUsers AS t3 ON (t1.editorId=t3.userId) ';
		$q .= 'WHERE timeToPublish<NOW() AND deletedBy=0 ';
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
		$q .= 'WHERE t1.newsId='.$newsId.' AND deletedBy=0';
		return $db->getOneRow($q);
	}

	function showNewsArticle($_id = 0)
	{
		global $db, $session, $files, $config, $project;

		if (!is_numeric($_id)) return false;

		//Looks for formatted news section commands, like: News:ID, NewsEdit:ID, NewsDelete:ID, NewsComment:ID, BlogFiles:ID
		$cmd = fetchSpecialParams($config['news']['allowed_tabs']);
		if ($cmd) list($current_tab, $_id) = $cmd;
		if (empty($_id) || !is_numeric($_id)) return false;

		$news = getNewsItem($_id);
		if (!$news) return;

		echo '<div class="news">';
		echo '<div class="news_top">';
		if ($news['rss_enabled']) echo '<div class="news_title_rss">';
		else echo '<div class="news_title">';
		echo $news['title'].'</div>';

		if ($news['categoryId']) {
			echo '<a href="news.php?cat='.$news['categoryId'].'">'.getCategoryName(CATEGORY_NEWS, $news['categoryId']).'</a><br/>';
		}
		echo 'By '.$news['creatorName'].', published '.$news['timeToPublish'].'<br/>';
		if ($news['editorId']) echo '<i>Updated '.$news['timeEdited'].' by '.$news['editorName'].'</i><br/>';
		echo '</div>'; //class="news_top"
		echo '<br/>';

		if ($session->isAdmin) {
			$menu = array(
				$_SERVER['PHP_SELF'].'?News:'.$_id => 'Show news',
				$_SERVER['PHP_SELF'].'?NewsEdit:'.$_id => 'Edit',
				$_SERVER['PHP_SELF'].'?NewsFiles:'.$_id => 'Attachments',
				$_SERVER['PHP_SELF'].'?NewsDelete:'.$_id => 'Delete',
				$_SERVER['PHP_SELF'].'?NewsNewCategory:'.$_id => 'New category',
				$_SERVER['PHP_SELF'].'?NewsComment:'.$_id => 'Comments ('.getCommentsCount(COMMENT_NEWS, $_id).')'
				);
			echo createMenu($menu, 'blog_menu');
		}

		if ($current_tab == 'NewsEdit' && $session->isAdmin) {

			if (!empty($_POST['news_title'])) {
				updateNews($_id, $_POST['news_cat'], $_POST['news_title'], $_POST['news_body'], $_POST['news_publish'], $_POST['news_rss']);
			}

			$item = getNewsItem($_id);

			echo '<h1>Edit news article</h1>';
			echo '<form method="post" action="'.'?NewsEdit:'.$_id.getProjectPath().'">';
			echo '<input type="hidden" name="news_rss" value="0"/>';
			echo 'Title: <input type="text" name="news_title" size="50" value="'.$item['title'].'"/><br/>';
			echo 'Text:<br/>';
			echo '<textarea name="news_body" cols="60" rows="16">'.$item['body'].'</textarea><br/>';
			echo '<input name="news_rss" id="rss_check" type="checkbox" class="checkbox" value="1"'.($item['rss_enabled']?' checked="checked"':'').'/>';
			echo ' <label for="rss_check">';
			echo '<img src="/gfx/icon_rss.png" width="16" height="16" alt="RSS enabled" title="RSS enabled"/>';
			echo ' Include this news in the RSS feed</label><br/><br/>';
			echo 'Category: '.getCategoriesSelect(CATEGORY_NEWS, 0, 'news_cat', $item['categoryId']).'<br/><br/>';
			echo 'Time for publication:<br/>';
			echo '<input type="text" name="news_publish" value="'.$item['timeToPublish'].'"/> ';
			echo '<input type="submit" class="button" value="Save changes"/><br/>';
			echo '</form><br/>';

		} else if ($current_tab == 'NewsDelete') {

			//fixme: confirmed() skickar fel parametrar, så detta funkar inte
			if (confirmed('Are you sure you wish to delete this news entry?', 'NewsDelete:'.$_id, $_id)) {
				removeNews($_id);

				require_once($project.'design_head.php');
				echo 'News article successfully deleted!<br/><br/>';
				require_once($project.'design_foot.php');
				die;
			}

		} else if ($current_tab == 'NewsNewCategory') {

			makeNewCategoryDialog(CATEGORY_NEWS);

		} else if ($current_tab == 'NewsFiles') {

			echo $files->showFiles(FILETYPE_NEWS, $_id);

		} else if ($current_tab == 'NewsComment') {
			showComments(COMMENT_NEWS, $_id);
		} else {

			$art = parseArticle($news['body']);
			if ($art['head']) echo '<div class="news_head">'.$art['head'].'</div>';
			echo '<div class="news_body">'.$art['body'].'</div>';

			if ($config['news']['allow_rating']) {
				echo '<br/>';
				echo '<div class="news_rate">';
				echo ratingGadget(RATE_NEWS, $_id);
				echo '</div>';
			}
		}

		echo '</div>'; //class="news"

		return true;
	}

	function showNews($limit = 3)
	{
		global $db, $session;

		/* Displays one news article - returns if successful */
		if (showNewsArticle()) return;

		$_cat_id = 0;
		if (!empty($_GET['cat']) && is_numeric($_GET['cat'])) $_cat_id = $_GET['cat'];

		/* visar en lista med de senaste nyheternas headlines, alla kategorier */
		$list = getPublishedNews($_cat_id, $limit);

		foreach ($list as $row) {
			echo '<div class="newsitem">';
			echo '<a href="?News:'.$row['newsId'].'">'.$row['title'].'</a>, published '.$row['timeToPublish'];
			if ($row['categoryId']) echo ' - <a href="news.php?cat='.$row['categoryId'].'">'.getCategoryName(CATEGORY_NEWS, $row['categoryId']).'</a>';
			echo '<br/>';

			$art = parseArticle($row['body']);
			echo $art['head'].'<br/>';
			echo '</div><br/>';
		}
		if ($session->isAdmin) {
			echo '<a href="/admin/admin_news_add.php'.getProjectPath(0).'">Add news</a>';
		}
	}
?>