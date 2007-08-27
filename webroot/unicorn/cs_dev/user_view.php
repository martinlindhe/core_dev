<?
	require_once('config.php');

	$user->requireLoggedIn();

	$id = $user->id;
	if (!empty($_GET['id']) && is_numeric($_GET['id'])) $id = $_GET['id'];

	$profile = $user->getcontent($id, 'user_profile');
	$page = 'view';
	$isFriends = $user->isFriends($id);
	if ($id != $user->id) {
		$hidden = $user->getinfo($user->id, 'hidden_login');
		if (!$hidden) {
			$visit = $db->replace("REPLACE INTO s_uservisit SET visitor_id = '".$user->id."', user_id = '".$id."', status_id = '2', visit_date = NOW()");
			$beenhere = ($visit != '2') ? false : true;
		} else {
			$visit = $db->replace("REPLACE INTO s_uservisit SET visitor_id = '".$user->id."', user_id = '".$id."', status_id = '1', visit_date = NOW()");
			$beenhere = ($visit != '2') ? false : true;
		}
		if (!$hidden && !$beenhere) {
			$c = $user->getinfo($id, 'visit_cnt');
			$tmp = $user->setinfo($id, 'visit_cnt', ($c+1));
			if($tmp[0]) $user->setrel($tmp[1], 'user_head', $id);
		}
	} else {
		$user->fix_img();
	}
	define('U_VISIT', true);
	$action = 'view';
	require(DESIGN.'head_user.php');
?>
	<div class="subHead">presentation</div><br class="clr"/>
<?

	if (!empty($profile['user_pres'])) {
		echo formatText($profile['user_pres'], true);
	} else {
		echo 'Användaren har inte skrivit någon presentation.';
	}

	require(DESIGN.'foot_user.php');
?>
