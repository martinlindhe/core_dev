<?
	session_start();

	require(dirname(__FILE__).'/www/_administrator/set_onl.php');		//skapar $sql och $user klasser
	require(dirname(__FILE__).'/www/_modules/member/auth.php');			//skapar $user_auth klassen f�r logins

	
	//vafan e dealen med dessa?!?!?!
	$s = &$_SESSION['data'];
	$l = $user->auth(@$_SESSION['data']['id_id'], true);
	
	$isAdmin = (@$_SESSION['data']['level_id'] == '10'?true:false);
	$isOk = true;
?>