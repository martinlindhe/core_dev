<?
	require_once('class.DB_MySQLi.php');

	require_once('functions/mail.fnc.php');
	require_once('functions/gb.fnc.php');
	require_once('functions/relations.fnc.php');
	require_once('functions/spy.fnc.php');
	require_once('functions/auth.php');

	session_start();

	error_reporting(E_ALL);
	date_default_timezone_set('Europe/Stockholm');

#local date-settings
setlocale(LC_TIME, 'sv_SE.ISO-8859-1');

#absolute path to www-root
//define('CS', '/cs_dev/');
$config['web_root'] = '/cs_dev/';
$config['core_root'] = 'D:/devel/webroot/cs_dev/';


define('OBJ', $config['web_root'].'_objects/');
define('DESIGN', $config['core_root'].'_design/');
define('CONFIG', '_config/');
define('PD', '02');
define('UPLA', $config['web_root'].'_input/');
define('UPLL', '.'.UPLA);
define('UIMG', '150x150');
define('MAXIMUM_USERS', 750);
#standard title of page
define('DEFAULT_USER', '48d40b8b5dee4c06cd8864be1b35456d');
define('NAME_TITLE', 'CitySurf.tv - Nu kör vi!');
$NAME_TITLE = NAME_TITLE;


//define('SMTP_SERVER', 'localhost');
define('P2B', 'http://www.citysurf.tv/');
define('URL', 'citysurf.tv');
define('NAME_URL', 'CitySurf');
define("UO", '30 MINUTES');
define('ADMIN_NAME', 'CitySurf');
define('USER_GALLERY', $config['web_root'].'_input/usergallery/');
define('USER_IMG', $config['web_root'].'_input/images/');
define('USER_FIMG', $config['web_root'].'user/image/');
//define('NEWS', '/_output/news_');
$sex = array('M' => 'm', 'F' => 'k');
$sex_name = array('M' => 'man', 'F' => 'kvinna');
//define('T', 's_');
//$t = T;
define("STATSTR", "listar <b>%1\$d</b> - <b>%2\$d</b> (totalt: <b>%3\$d</b>)");

	require('functions/main.fnc.php');
	execSt();
	
/*
define('SQL_U', 'cs_user');
define('SQL_P', 'cs8x8x9ozoSSpp');
define('SQL_D', 'cs_platform');
define('SQL_H', 'pc3.icn.se');
*/

	$config['debug'] = true;

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'cs_platform';
	$config['database']['host']	= 'localhost';
	$db = new DB_MySQLi($config['database']);

	require('functions/user.class.php');
	$user = new user();


	function makeButton($bool, $js, $img, $text, $number = false)
	{
		echo '<div class="'.($bool?'btnSelected':'btnNormal').'"'.($js?'onclick="'.$js.'"':'').'>';
		echo '<table summary="" cellpadding="0" cellspacing="0">';
		echo '<tr>';
			echo '<td width="3"><img src="/_gfx/themes/btn_c1.png" alt=""/></td>';
			echo '<td style="background: url(\'/_gfx/themes/btn_head.png\');"></td>';
			echo '<td width="3"><img src="/_gfx/themes/btn_c2.png" alt=""/></td>';
		echo '</tr>';

		echo '<tr style="height: 18px">';
			echo '<td width="3" style="background: url(\'/_gfx/themes/btn_left.png\');"></td>';
			echo '<td style="padding-left: 19px; padding-right: 4px; padding-top: 1px;">';
			if ($img) echo '<img src="/_gfx/'.$img.'" style="position: absolute; top: 5px; left: 4px;" alt=""/> ';
			echo $text;
			if ($number !== false) echo '&nbsp;&nbsp;'.$number;
			echo '</td>';
			echo '<td width="3" style="background: url(\'/_gfx/themes/btn_right.png\');"></td>';
		echo '</tr>';

		echo '<tr>';
			echo '<td width="3"><img src="/_gfx/themes/btn_c3.png" alt=""/></td>';
			echo '<td style="background: url(\'/_gfx/themes/btn_foot.png\');"></td>';
			echo '<td width="3"><img src="/_gfx/themes/btn_c4.png" alt=""/></td>';
		echo '</tr>';

		echo '</table>';
		echo '</div>';
	}
	
	//returnerar random nummer
	function gc($type = 1) {
		if(!empty($_REQUEST["PHPSESSID"]))
			$sess5454 = md5($_REQUEST["PHPSESSID"].'SALTHELGVETE');
		else
			$sess5454 = md5(microtime() . rand(1, 99999));
		if(!empty($_COOKIE['SOEBR']))
			$cookie_id = (is_md5($_COOKIE['SOEBR']))?$_COOKIE['SOEBR']:cookieSET("SOEBR", $sess5454);
		else
			$cookie_id = cookieSET("SOEBR", $sess5454);
		return ($type?$cookie_id:$sess5454);
	}

?>