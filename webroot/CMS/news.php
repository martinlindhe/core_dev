<?
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	
	$itemId = $_GET['id'];

	include_once('include_all.php');
	
	include('design_head.php');

	$item = getNewsItem($db, $itemId);
	
	echo '<h1>'. $item['title'].'</h1><br><br>';
	echo $item['body'].'<br><br>';
	
	echo 'Publicerad '.getRelativeTimeLong($item['timetopublish']).' av '.nameLink($item['userId'], $item['userName']);
	echo '<br><br>';
	
	if ($_SESSION['isAdmin']) {
		echo '<a href="admin_news.php?edit='.$itemId.'">'.$config['text']['link_edit'].'</a>';
	}

	include('design_foot.php');
?>