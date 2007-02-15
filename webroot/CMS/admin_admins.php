<?  
	include_once('include_all.php');
  
	if (!$_SESSION['isAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (!empty($_POST['addadminid']) && is_numeric($_POST['addadminid'])) {
		$sql = 'UPDATE tblusers SET userMode=2 WHERE userId='.$_POST['addadminid'];
		dbQuery($db, $sql);
	}

	if (!empty($_GET['unadminid']) && is_numeric($_GET['unadminid'])) {
		$sql = 'UPDATE tblusers SET userMode=0 WHERE userId='.$_GET['unadminid'];
		dbQuery($db, $sql);
	}

	include('design_head.php');
	include('design_user_head.php');
  
	$admins = getAdministrators($db);

	$content = '';
	$content .= '<table width=100%">';
	$content .= '<tr><td><b>ID</b></td><td><b>Anv&auml;ndarnamn</b></td><td><b>Systemnamn</b></td><td>&nbsp;</td></tr>';

	foreach($admins as $item) {
		$content .= '<tr>';
		//$content .= $name . $item["userId"] . "<br>\n";

		$name = getUserdataByFieldname($db, $item['userId'], 'Nickname');

		$content .= '<td>'.$item['userId'].'</td>';
		$content .= '<td>'.$name.'</td>';
		$content .= '<td>'.$item['userName'].'</td>';
		$content .= '<td><a href="'.$_SERVER['PHP_SELF'].'?unadminid='.$item['userId'].'">Inte admin!</a></td>';
		$content .= '</tr>';
	}
	$content .= '</table>';

	$content .= '<br><br>';
	$content .= '<b>L&auml;gg till admin</b><br><br>';
	$content .= '<form name="addadmin" method="post" action="'.$_SERVER['PHP_SELF'].'">';
	$content .= 'Fyll i anv&auml;ndarens ID f&ouml;r att ge administrat&ouml;rsr&auml;ttigheter.<br><br>';
	$content .= '<input type="text" name="addadminid"><br>';
	$content .= '<input type="submit" class="button" value="L&auml;gg till">';
	$content .= '</form>';

	echo '<div id="user_admin_content">';
	echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Administrat&ouml;rer', $content);
	echo '</div>';
  
	include('design_admin_foot.php');
	include('design_foot.php');
?>