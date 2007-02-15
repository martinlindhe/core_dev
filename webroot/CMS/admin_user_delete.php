<?
	include_once('include_all.php');

	if (!$_SESSION['isSuperAdmin'] || !isset($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$show = $_GET['id'];
	$showname = getUserName($db,$show);
	if (!$showname) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');

	echo 'Radera anv&auml;ndare '.$showname.'<br>';
	
	if (isset($_GET['confirm']) && ($_GET['confirm'] == 1)) {
		removeUser($db, $show);
		
		echo 'Anv&auml;ndaren raderad!<br><br>';
		echo '<a href="user_sok.php">S&ouml;k anv&auml;ndare &raquo;</a><br>';
	} else {	

		echo 'Vill du verkligen radera den h&auml;r anv&auml;ndaren? All information associerad med denne kommer att tas bort.<br><br>';

		echo '<table width="200" border=0><tr>';
		echo '<td align="center"><a href="'.$_SERVER['PHP_SELF'].'?id='.$show.'&confirm=1">'.$config['text']['prompt_yes'].'</a></td>';
		echo '<td align="center"><a href="javascript:history.go(-1);">'.$config['text']['prompt_no'].'</a></td>';
		echo '</tr></table>';
	}

	include('design_foot.php');	
?>