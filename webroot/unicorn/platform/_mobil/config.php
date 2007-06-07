<?
	session_start();

	require_once('../_config/main.fnc.php');		//l()

	require_once('../_administrator/set_onl.php');		//skapar $sql och $user klasser
	require_once('../_modules/member/auth.php');			//skapar $user_auth klassen för logins

	//funktioner
	require_once('../_modules/user/mail.fnc.php');			//funktioner för att skicka mail
	require_once('../_modules/user/relations.fnc.php');	//funktioner för att hantera relationer
	require_once('../_modules/user/gb.fnc.php');				//funktioner för att hantera gästböcker
	
	require_once('../_modules/list/search_users.fnc.php');	//funktioner för att söka användare
	
	require_once('../_modules/member/settings.fnc.php');	//funktioner för användar-inställningar
	
	require_once('functions_general.php');	//för min makePager()
	
	//$user->auth() uppdaterar "last online time" i databasen
	$s = &$_SESSION['data'];
	$l = $user->auth(@$_SESSION['data']['id_id'], true);
	
	$isAdmin = (@$_SESSION['data']['level_id'] == '10'?true:false);
	$isOk = true;
?>