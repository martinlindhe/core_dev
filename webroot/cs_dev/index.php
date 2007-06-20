<?
/*
	if ($_SERVER['REMOTE_ADDR'] != '213.80.11.162') {
		echo 'Citysurf uppdateras!<br/><br/>Under eftermiddagen onsdagen den 13:e juni genomför vi en uppdatering av citysurf.<br/><br/>';
		echo 'Passa på att njuta av en glass i solen och kika tillbaka om några timmar!<br><br>';
		die;
	}
*/

	require_once('functions/config.include.php');

	$action = (!empty($_GET['action'])?$_GET['action']:false);
	$id = (!empty($_GET['id'])?$_GET['id']:false);
	$key = (!empty($_GET['key'])?$_GET['key']:false);
	
	if(!empty($_GET['type'])) {
		$type = $_GET['type'];
		if($type == 'main') {
			include('_modules/main/index.php');
		} elseif($type == 'text') {
			include('_modules/text/index.php');
		} elseif($type == 'macro') {
			include('_modules/macro/index.php');
		} else {
			$isAdmin = (@$_SESSION['data']['level_id'] == '10'?true:false);
			$isOk = true;
			// here's user dependent pages
			if($type == 'member') {
				include('_modules/member/index.php');
			} else {
				// heres only logged in user allowed
				if($type != 'user') {
					loginACT();
				}
				if($type == 'user') {
					include('_modules/user/index.php');
				} else if($type == 'list') {
					include('_modules/list/index.php');
				} else if($type == 'forum') {
					include('_modules/forum/index.php');
				} else if($type == 'thought') {
					include('_modules/thought.php');
				}
			}
		}
		die;
	}
	require('_modules/main/index_p.php');
?>
