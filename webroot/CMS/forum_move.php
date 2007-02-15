<?
	include('include_all.php');

	if (($_SESSION['loggedIn'] === false) || ($_SESSION['isAdmin'] != true)) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$itemId = $_GET['id'];
	$item = getForumItem($db, $itemId);
		
	if (!$item) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (isset($_POST['destId'])) {
		setForumItemParent($db, $itemId, $_POST['destId']);
		header('Location: forum.php?id='.$itemId);
		die;
	}

	include('design_head.php');
	include('design_forum_head.php');

	//$content = 'Denna diskussionstr&aring;d kommer att flyttas:<br><br>';
	$content = 'Denne diskusjonen vil bli flyttet:<br><br>';
	$content .= showForumPost($db, $item, '', false).'<br>';

	//$content .= 'V&auml;lj vart du vill flytta tr&aring;den:<br>';
	$content .= 'Hvor vil du flytte tr&aring;den:<br>';
	$content .= '<form name="change" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';
	
		$content .= '<select name="destId">';
		//$content .= '<option value="0">Flytta till roten';
		$list = getForumStructure($db, 0);

		for ($i=0; $i<count($list); $i++) {
			$content .= '<option value="'.$list[$i]['itemId'].'">'.$list[$i]['name'];
		}
		$content .= '</select>';

	$content .= '<br><br>';
	$content .= '<input type="submit" class="button" value="'.$config['text']['link_move'].'">';
	$content .= '</form><br><br>';
	$content .= '<a href="javascript:history.go(-1);">'.$config['text']['link_return'].'</a>';	

	echo '<div id="user_forum_content">';
	echo MakeBox('<a href="forum.php">Forum</a>|Flytte tr&aring;d', $content, 500);
	echo '</div>';

	include('design_forum_foot.php');
	include('design_foot.php');
?>