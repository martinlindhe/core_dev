<?php

require_once('config.php');
require('design_head.php');

echo createMenu($forum_menu, 'blog_menu');

if (isset($_POST['c']) || isset($_GET['c'])) {
	if (isset($_POST['c'])) $orgcrit = $_POST['c'];
	else $orgcrit = $_GET['c'];

	if (isset($_GET['m'])) $method = $_GET['m'];
	else if (isset($_POST['m'])) $method = $_POST['m'];
	else $method = 'newfirst';

	$criteria = substr($orgcrit, 0, 30); //chop off too long search queries

	$tot_cnt = getForumSearchResultsCount($criteria);

	$pager = makePager($tot_cnt, $config['forum']['search_results_per_page'], '&amp;c='.$criteria);

	$list = getForumSearchResults($criteria, $method, $pager['limit']);

	echo $pager['head'];
	echo $tot_cnt.' hits on "'.$criteria.'", ';

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
		//echo showForumPost($list[$i], 'Search result #'.($i+1), false, $criteria).'<br/>';
		echo showForumPost($list[$i], 'Search result #'.($i+1), false).'<br/>';
	}

	$criteria = urlencode($criteria);

	echo 'Order search result by:<br/>';

	if ($method == 'newfirst') echo '<b>';
	echo '<a href="'.$_SERVER['PHP_SELF'].'?c='.$criteria.'&m=newfirst">Newest first</a><br/>';
	if ($method == 'newfirst') echo '</b>';

	if ($method == 'oldfirst') echo '<b>';
	echo '<a href="'.$_SERVER['PHP_SELF'].'?c='.$criteria.'&m=oldfirst">Oldest first</a><br/>';
	if ($method == 'oldfirst') echo '</b>';

	if ($method == 'mostread') echo '<b>';
	echo '<a href="'.$_SERVER['PHP_SELF'].'?c='.$criteria.'&m=mostread">Most read first</a><br/>';
	if ($method == 'mostread') echo '</b>';

	echo '<br/>';
	echo '<a href="'.$_SERVER['PHP_SELF'].'">New search</a><br/><br/>';

} else {

	//wiki('Forum search').'<br/>';

	echo '<form name="f_src" method="post" action="'.$_SERVER['PHP_SELF'].'">';
	echo 'Search phrase: <input type="text" name="c" size="40"/><br/>';
	echo 'Show result: ';
	echo '<input type="radio" class="radio" name="m" value="newfirst" checked="checked"/>Newest first ';
	echo '<input type="radio" class="radio" name="m" value="oldfirst"/>Oldest first ';
	echo '<input type="radio" class="radio" name="m" value="mostread"/>Most read first ';
	echo '<br/><br/>';

	echo '<input type="submit" class="button" value="Search"/>';
	echo '</form>';
}
?>
<script type="text/javascript">
if (document.f_src) document.f_src.c.focus();
</script>
<?php
	require('design_foot.php');
?>
