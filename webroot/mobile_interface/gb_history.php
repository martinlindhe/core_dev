<?
	//todo: paging

	//id = userId på den andra personen vi vill se historik med
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$_id = $_GET['id'];

	require_once('config.php');

	if (!$l) die;	//user not logged in

	require('design_head.php');
	
	$list = gbHistory($l['id_id'], $_id);
	$user_data = $user->getuser($_id);
	
	echo 'GÄSTBOK - HISTORIK MELLAN DIG OCH '.$user_data['u_alias'].'<br/><br/>';

	if (!count($list)) {
		echo 'Ingen historik finns.';
	} else {
		//print_r($list);
		foreach ($list as $row) {
			echo $row['u_alias'].' sa '.$row['sent_date'].':<br/>';
			echo $row['sent_cmt'].'<br/><br/>';
		}
	}

	require('design_foot.php');
?>