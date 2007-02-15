<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	include('design_user_head.php');

	$content = getInfoField($db, 'page_admin_notes');

	echo '<div id="user_admin_content">';
	echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Anteckningar', $content);
	echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');
?>