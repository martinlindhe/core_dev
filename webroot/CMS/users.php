<?
	include('include_all.php');

	include('design_head.php');

	echo getInfoField($db, 'page_users').'<br>';
		
	echo '<a href="users_search.php">S&ouml;k anv&auml;ndare</a><br>';
	echo '<a href="users_online.php">Online just nu</a><br>';
		
	if ($_SESSION['isAdmin']) {
		echo '<a href="newuser.php">Skapa ny anv&auml;ndare</a>';
	}

	include('design_foot.php');
?>