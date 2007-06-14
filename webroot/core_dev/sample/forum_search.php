<?
	require_once('config.php');

	if (isset($_GET['p'])) {
		$page = $_GET['p'];
	} else {
		$page = 1;
	}

	if (isset($_GET['l'])) {
		$limit = $_GET['l'];
	} else {
		$limit = $config['forum']['search_results_per_page'];
	}

	require('design_head.php');
	
	if (isset($_POST['c']) || isset($_GET['c'])) {
		if (isset($_POST['c'])) $orgcrit = $_POST['c'];
		else $orgcrit = $_GET['c'];

		if (isset($_GET['m'])) $method = $_GET['m'];
		else if (isset($_POST['m'])) $method = $_POST['m'];
		else $method = 'newfirst';

		$criteria = substr($orgcrit, 0, 30); //chop off too long search queries

		$list = getForumSearchResults($criteria, $method, $page, $limit);
		$hits = getForumSearchResultsCount($criteria);

		echo 'Showing search result page '.$page.' of '.round(($hits/$limit)+0.49).', '.$limit.' posts per page.<br>';
		echo $hits.' hits on "'.$criteria.'", ';

		switch ($method) {
			case 'oldfirst':
				echo 'oldest first.';
				break;

			case 'newfirst':
				echo 'newest first.';
				break;

			case 'mostread':
			default:
				echo 'most read first.';
				break;
		}

		echo '<br/><br/>';

		for ($i=0; $i<count($list); $i++) {
			//$content .= showForumPost($list[$i], 'S&ouml;kresultat #'.($i+1), false, $criteria).'<br>';
			echo showForumPost($list[$i], 'Search result #'.($i+1), false).'<br>';
		}

		$criteria = urlencode($criteria);

		echo 'Page: ';

		if ($page > 1) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?c='.$criteria.'&p='.($page-1).'&l='.$limit.'&m='.$method.'">&laquo;</a> ';
		}

		for ($i=1; $i<=round(($hits/$limit)+0.49); $i++) {
			if ($i == $page) echo '<b>';
			echo '<a href="'.$_SERVER['PHP_SELF'].'?c='.$criteria.'&p='.$i.'&l='.$limit.'&m='.$method.'">'.$i.'</a> ';
			if ($i == $page) echo '</b>';
		}

		if ($page < ($hits/$limit)) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?c='.$criteria.'&p='.($page+1).'&l='.$limit.'&m='.$method.'">&raquo;</a>';
		}

		echo '<br>';
		echo 'Number on each page: ';
		for ($i=5; $i<=20; $i+=5) {
			if ($limit == $i) echo '<b>';
			echo '<a href="'.$_SERVER['PHP_SELF'].'?c='.$criteria.'&p='.$page.'&l='.$i.'&m='.$method.'">'.$i.'</a> ';
			if ($limit == $i) echo '</b>';
		}
		echo '<br><br>';

		echo 'Search method:<br>';

		if ($method == 'newfirst') echo '<b>';
		echo '<a href="'.$_SERVER['PHP_SELF'].'?c='.$criteria.'&p='.$page.'&l='.$limit.'&m=newfirst">Newest first</a><br>';
		if ($method == 'newfirst') echo '</b>';

		if ($method == 'oldfirst') echo '<b>';
		echo '<a href="'.$_SERVER['PHP_SELF'].'?c='.$criteria.'&p='.$page.'&l='.$limit.'&m=oldfirst">Oldest first</a><br>';
		if ($method == 'oldfirst') echo '</b>';

		if ($method == 'mostread') echo '<b>';
		echo '<a href="'.$_SERVER['PHP_SELF'].'?c='.$criteria.'&p='.$page.'&l='.$limit.'&m=mostread">Most read first</a><br>';
		if ($method == 'mostread') echo '</b>';

		echo '<br>';
		echo '<a href="'.$_SERVER['PHP_SELF'].'">New search</a><br/><br/>';

	} else {

		wiki('Forum search').'<br>';

		echo '<form name="f_src" method="post" action="'.$_SERVER['PHP_SELF'].'">';
		echo 'Search phrase: <input type="text" name="c" size="50"/><br/>';
		echo 'Show result: ';
		echo '<input type="radio" class="radio" name="m" value="newfirst" checked="checked"/>Newest first ';
		echo '<input type="radio" class="radio" name="m" value="oldfirst"/>Oldest first ';
		echo '<input type="radio" class="radio" name="m" value="mostread"/>Most read first ';
		echo '<br><br>';

		echo '<input type="submit" class="button" value="Search"/>';
		echo '</form>';
	}

	require('design_foot.php');
?>
<script type="text/javascript">
if (document.f_src) document.f_src.c.focus();
</script>