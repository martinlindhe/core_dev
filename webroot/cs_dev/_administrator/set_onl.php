<?
	error_reporting(E_ALL);
	require_once(dirname(__FILE__).'/set_c.php');
	require_once(dirname(__FILE__).'/set_fnc.php');

	//require_once(dirname(__FILE__).'/../_config/main.fnc.php');
	
	$user = new user($sql);

/*
	if(!empty($_REQUEST["PHPSESSID"]))
		$sess5454 = md5($_REQUEST["PHPSESSID"].'SALTHELGVETE');
	else
		$sess5454 = md5(microtime() . rand(1, 99999));

	if(!empty($_COOKIE['SOEBR'])) {
			$cookie_id = (is_md5($_COOKIE['SOEBR']))?$_COOKIE['SOEBR']:cookieSET("SOEBR", $sess5454);
	} else
		$cookie_id = cookieSET("SOEBR", $sess5454);
*/

	$connection = @mysql_connect(SQL_H, SQL_U, SQL_P);
	if(!$connection) { 
		echo 'Det går inte att kontakta databasen.';
		#doMail('frans@freshfly.se', 'Connect', 'En error har uppstått! Kolla sidan!');
		exit;
	}
	if(!@mysql_select_db(SQL_D)) { 
		echo 'Det går inte att välja databas.';
		#doMail('frans@freshfly.se', 'Select', 'En error har uppstått! Kolla sidan!');
		exit;
	}
?>