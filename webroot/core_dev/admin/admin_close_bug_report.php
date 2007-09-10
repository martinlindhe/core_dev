<?
	require_once('find_config.php');
	$session->requireAdmin();

	if (!isset($_GET['id'])) {
		header('Location: admin_bug_reports.php');
		die;
	}

	$bugId = $_GET['id'];
		
	if (isset($_POST['reason'])) {
		closeBugReport($db, $_GET['id'], $_POST['reason']);
		header('Location: admin_bug_reports.php');
		die;
	}

	require($project.'design_head.php');

	$content = '<b>Administration screen - Close bug report</b><br><br>';
		
	$item = getBugReport($db, $bugId);
	
	$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$bugId.'">';
	$content .= getRelativeTimeLong($item['timestamp']).', by '.nameLink($item["bugCreator"], $item["userName"]).'<br>';
	$content .= 'Details: <br>';
	$content .= nl2br($item['bugDesc']).'<br>';
	$content .= 'Close reason: <select name="reason">';
		for ($i=0; $i<count($close_bug_reason); $i++) {
			$content .= '<option value="'.$i.'">'.$close_bug_reason[$i];
		}
	$content .= '</select><br>';
	$content .= '<input type="submit" class="button" value="Close bug report">';
	$content .= '</form>';

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Close bug report', $content);
		echo '</div>';

	require($project.'design_foot.php');
?>