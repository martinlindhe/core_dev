<?
	include('include_all.php');

	include('design_head.php');

	echo getInfoField($db, 'page_newuser_created');

	if ($_SESSION['isAdmin']) {
		echo '<br>User created - <b>you are still logged in as '.$_SESSION['userName'].'</b>';
	}

	include('design_foot.php');
?>