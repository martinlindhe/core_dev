<?
	include('include_all.php');

	if (!$_SESSION['isAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$show = '';
	if (!empty($_GET['id']) && $_GET['id'] != $_SESSION['userId']) {
		$show = $_GET['id'];
		$showname = getUserName($db, $show);
		if (!$showname) {
			header('Location: '.$config['start_page']);
			die;
		}
	} else if ($_SESSION['userId']) {
		$show     = $_SESSION['userId'];
		$showname = $_SESSION['userName'];
	}
	
	if (substr($showname, -1) == 's') {
		$niceshowname = $showname."'";
	} else {
		$niceshowname = $showname.'s';
	}

	if ($show == $_SESSION['userId']) {
		setUserStatus($db, 'Kollar upp sej sj&auml;lv');
	} else {
		setUserStatus($db, 'Kollar upp '.$showname);
	}


	include('design_head.php');

		echo nameLink($show, ucfirst($niceshowname).' sida').' - Admininfo<br><br>';

		echo 'Information om anv&auml;ndaren '.nameLink($show, $showname).':<br><br>';
		
		//if (userAccess($db, 'admin_can_delete_users')) {
			echo '<a href="admin_user_delete.php?id='.$show.'">Radera anv&auml;ndaren</a><br><br>';
		//}


		echo '<b>Senaste inloggningarna:</b><br>';
		echo displayUserLatestLogins($db, $show);
					
		echo displayUserAccessgroups($db, $show);
		
	include('design_foot.php');

?>