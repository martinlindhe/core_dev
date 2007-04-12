<?
	session_start();

	require_once(dirname(__FILE__).'/www/_administrator/set_onl.php');		//skapar $sql och $user klasser
	require_once(dirname(__FILE__).'/www/_modules/member/auth.php');			//skapar $user_auth klassen för logins

	//funktioner
	require_once(dirname(__FILE__).'/www/_modules/user/mail.fnc.php');			//funktioner för att skicka mail
	require_once(dirname(__FILE__).'/www/_modules/user/relations.fnc.php');	//funktioner för att hantera relationer
	require_once(dirname(__FILE__).'/www/_modules/user/gb.fnc.php');				//funktioner för att hantera gästböcker
	
	require_once(dirname(__FILE__).'/www/_modules/list/search_users.fnc.php');	//funktioner för att söka användare
	
	require_once(dirname(__FILE__).'/www/_modules/member/settings.fnc.php');	//funktioner för användar-inställningar
	

	
	//vafan e dealen med dessa?!?!?!
	$s = &$_SESSION['data'];
	$l = $user->auth(@$_SESSION['data']['id_id'], true);
	
	$isAdmin = (@$_SESSION['data']['level_id'] == '10'?true:false);
	$isOk = true;
?>