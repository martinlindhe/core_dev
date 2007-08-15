<?
#local date-settings
//setlocale(LC_TIME, 'sv_SE.ISO-8859-1');

#absolute path to www-root
define('CS', '/unicorn/cs_dev/');
define('OBJ', CS.'_objects/');
define('DESIGN', '_design/');
//define('CONFIG', '_config/');


define('PD', '02');
define('UPLA', CS.'_input/');
define('UPLL', '.'.UPLA);
define('UIMG', '150x150');
define('MAXIMUM_USERS', 750);
#standard title of page
define('DEFAULT_USER', '48d40b8b5dee4c06cd8864be1b35456d');
define('NAME_TITLE', 'CitySurf.tv - Nu kör vi!');
$NAME_TITLE = NAME_TITLE;

/*
define('CH', ' SQL_CACHE ');
define('SQL_U', 'cs_user');
define('SQL_P', 'cs8x8x9ozoSSpp');
define('SQL_D', 'cs_platform');
define('SQL_H', 'pc3.icn.se');
*/


//define('SMTP_SERVER', 'localhost');
define('P2B', 'http://www.citysurf.tv/');
define('URL', 'citysurf.tv');
define('NAME_URL', 'CitySurf');
define("UO", '30 MINUTES');
define('ADMIN_NAME', 'CitySurf');
define('USER_GALLERY', '_input/usergallery/');
define('USER_IMG', '_input/images/');
define('USER_FIMG', 'user/image/');
define('NEWS', '/_output/news_');
$sex = array('M' => 'm', 'F' => 'k');
$sex_name = array('M' => 'man', 'F' => 'kvinna');

//define('T', 's_');
//$t = T;

define("STATSTR", "listar <b>%1\$d</b> - <b>%2\$d</b> (totalt: <b>%3\$d</b>)");

?>
