<?
	require_once('config.php');

	$session->requireLoggedIn();

	require('design_head.php');

	createMenu($profile_menu, 'blog_menu');

	$list = getSubscriptions(SUBSCRIPTION_FORUM);
	echo '<h2>Your forum subscriptions</h2>';
	//d($list);
	foreach ($list as $row) {
		echo '<a href="forum.php?id='.$row['itemId'].'">'.$row['itemSubject'].'</a> subscribed since '.$row['timeCreated'].'<br/>';
	}

	require('design_foot.php');
?>