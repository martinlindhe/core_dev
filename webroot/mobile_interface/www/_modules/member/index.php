<?
/*
elseif($action == 'activate') {
		include('auth.php');
		include(CONFIG.'validate.fnc.php');
		include("part2.php");
		exit;
	} elseif($action == 'update') {
		include("update.php");
		exit;
	} 
*/
	if($action == 'logout') {
		include('auth.php');
		if(!empty($_GET['id']))
			$user_auth->logout(1);
		else
			$user_auth->logout();
	} elseif($action == 'login') {
		if(!$l && !empty($_POST['a']) && !empty($_POST['p'])) {
			include('auth.php');
			checkBan(1);
			$user_auth->login($_POST['a'], $_POST['p']);
		} else if($l) reloadACT(l('main', 'start')); else reloadACT(l());
	} elseif($action == 'settings') {
		if(!$l) {
			loginACT();
		}
		include('settings.php');
		exit;
	} elseif($action == 'forgot') {
		include(CONFIG.'validate.fnc.php');
		include('forgot.php');
		exit;
	} elseif($action == 'register') {
		include('auth.php');
		include(CONFIG.'validate.fnc.php');
		include('register.php');
		exit;
	} elseif($action == 'mms') {
		include("mms.php");
		exit;
	} elseif($action == 'block') {
		include("block.php");
		exit;
	} elseif($action == 'retrieve') {
		include("retrieve.php");
		exit;
	} elseif($action == 'retrieveajax') {
		include("retrieveajax.php");
		exit;
	} elseif($action == 'preimage') {
		include("preimage.php");
		exit;
	}
	reloadACT(l());
?>