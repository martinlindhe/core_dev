<?
	include('include_all.php');

	include('design_head.php');
	include('design_forum_head.php');

	setUserStatus($db, 'Kollar vilka som &auml;r online');

	$list = getUsersOnline($db);
	
	$content  = 'Brukere online<br><br>';
	$content .= 'Det er '.count($list).' brukere online.<br>';

	for ($i=0; $i<count($list); $i++) {
		$content .= nameLink($list[$i]['userId'], $list[$i]['userName']).'<br>';
	}
	$content .= '<br><br>';


	/* Senast inloggade */
	//$content .= 'Senast inloggade<br><br>';
	//$content .= displayLatestLogins($db, 7);
	
	echo '<div id="user_forum_content">';
	echo MakeBox('<a href="forum.php">Forum</a>|Brukere online', $content, 500);
	echo '</div>';

	include('design_forum_foot.php');
	include('design_foot.php');
?>