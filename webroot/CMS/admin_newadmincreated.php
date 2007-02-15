<?
	include('include_all.php');

	if (!$_SESSION['isAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	include('design_user_head.php');

//	echo getInfoField($db, 'page_newuser_created');

		$content = '<br>Adminkontot &auml;r nu skapat - <b>du &auml;r fortfarande inloggad som '.$_SESSION['userName'].'</b>';

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Skapa nytt adminkonto', $content);
		echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');
?>