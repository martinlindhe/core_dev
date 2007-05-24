<?
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$_id = $_GET['id'];

	require_once('config.php');
	if (!$l) die;	//user not logged in

	require('design_head.php');
	
	if (!empty($_POST['ins_rel'])) {
		//Registrera relations-förfrågan
		$check = sendRelationRequest($_id, $_POST['ins_rel']);
		if ($check === true) {
			echo 'Du har nu skickat en förfrågan.<br/><br/>';
			echo '<a href="friends.php">MINA VÄNNER</a>';
			require('design_foot.php');
			die;
		}
	}
	
	echo 'SKAPA RELATION MED '.$user->getstringMobile($_id).'<br/>';
	echo '<br/>';

	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';

	$rel = getset('', 'r', 'mo', 'text_cmt ASC');

	echo '<select name="ins_rel" class="txt">';
	foreach($rel as $row) {
		echo '<option value="'.$row[0].'">'.secureOUT($row[1]).'</option>';
	}
	echo '</select> ';

	echo '<input type="submit" value="Skapa"/>';
	echo '</form>';

	require('design_foot.php');
?>