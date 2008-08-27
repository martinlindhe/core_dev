<?php
/**
 * $Id$
 */

require_once('find_config.php');
$session->requireSuperAdmin();

require('design_admin_head.php');

$supported_apache = array('1.3.37', '2.2.99');	//Apache 2.2.x is the current stable branch

$supported_php = array('5.2.0', '5.2.99');	//PHP 5.2.x is the current stable branch
$supported_php_gd = array('2.0', '2.0.34');
$supported_php_apc = array('3.0.14', '3.0.15');

$supported_mysql = array('4.1.15', '5.1.17');	//afaik no 5.x specific features are used

/* Returns true if $curr_ver is in the range of $ver_range */
function version_compare_array($ver_range, $curr_ver)
{
	list($min_ver, $max_ver) = $ver_range;

	//version_compare() returns -1 if the first version is lower than the second, 0 if they are equal, and +1 if the second is lower.
	if (version_compare($min_ver, $curr_ver, "<=") && version_compare($max_ver, $curr_ver, ">=")) {
		return '<div class="okay">';
	}

	return '<div class="critical">';
}

echo '<h1>Compatiblity check</h1>';
if (empty($_SERVER['SERVER_SOFTWARE'])) {	//This is available if php is running thru Apache
	echo '<div class="critical">Server OS: CANT DETECT</div>';
} else if (strpos($_SERVER['SERVER_SOFTWARE'], 'Win32') !== false) {	//Apache/2.2.4 (Win32)
	echo '<div class="okay">Server OS: Windows</div>';
} else if (strpos($_SERVER['SERVER_SOFTWARE'], 'Ubuntu') !== false) {	//Apache/2.2.3 (Ubuntu) PHP/5.2.1 mod_ssl/2.2.3 OpenSSL/0.9.8c
	echo '<div class="okay">Server OS: Ubuntu Linux</div>';
} else {
	echo '<div class="critical">Server OS: Unrecognized</div>';
}
echo ($config['debug']?'<div class="critical">Debug is turned on in the core - turn off for production use</div>':'<div class="okay">Debug: OFF</div>');
echo (extension_loaded('xdebug')?'<div class="critical">Debug: The xdebug extension is enabled. Please turn it off for production use</div>':'<div class="okay">xdebug is not enabled</div>');
echo '<br/>';

/************************************
* Apache version checks             *
************************************/
echo '<h2>Apache</h2>';
$current_apache = apache_get_version();
if ($current_apache == 'Apache') {
	echo '<div class="okay" onclick="toggle_element_by_name(\'apache_info_noversion\')">';
	echo ' Version information not available';
	echo ' <img src="'.$config['core']['web_root'].'gfx/icon_info.png">';
	echo '</div>';
	echo '<div id="apache_info_noversion" style="display: none">';
	echo 'Production servers are sometimes configured not to report version information (ServerTokens Prod), ';
	echo 'this also makes Apache not report version information to PHP). This is not a bad thing.';
	echo '</div>';
} else {
	//Apache version strings look like this:
	//	Apache/2.2.4 (Win32)
	//	Apache/2.2.3 (Ubuntu) PHP/5.2.1 mod_ssl/2.2.3 OpenSSL/0.9.8c
	if (substr($current_apache, 0, 7) == 'Apache/') $current_apache = substr($current_apache, strlen('Apache/'));
	$pos = strpos($current_apache, '(');
	if ($pos !== false) $current_apache = trim(substr($current_apache, 0, $pos));
	echo version_compare_array($supported_apache, $current_apache);
	echo 'Apache web server version: '.$current_apache.'</div>';
}
echo '<br/>';

/************************************
* PHP version checks                *
************************************/
echo '<h2>PHP</h2>';
$current_php = phpversion();
$current_php_gd = false;

if (function_exists("gd_info")) {
	$x = gd_info();
	$current_php_gd = $x['GD Version'];
	//GD version string looks like this:
	//	Windows: "bundled (2.0.34 compatible)"
	//	Linux: "2.0 or higher"
	$current_php_gd = str_replace('bundled (', '', $current_php_gd);
	$current_php_gd = str_replace(' compatible)', '', $current_php_gd);
	$current_php_gd = str_replace(' or higher', '', $current_php_gd);
}

$current_php_apc = phpversion('apc');

echo version_compare_array($supported_php, $current_php);
echo 'PHP script language version: '.$current_php.'</div>';

if ($current_php_gd === false) {
	echo '<div class="critical">gd extension not found! it is required for image handling to function</div>';
} else {
	echo version_compare_array($supported_php_gd, $current_php_gd);
	echo 'Required PHP extension "gd": '.$current_php_gd.'</div>';
}
if ($current_php_apc === false) {
	echo '<div class="okay">apc extension not found. ajax file upload progress not available</div>';
} else {
	echo version_compare_array($supported_php_apc, $current_php_apc);
	echo 'Optional PHP extension "apc": '.$current_php_apc.'</div>';
}

//Settings checks
echo 'display_errors = '.ini_get('display_errors').'<br/>';
if (!$config['debug'] && ini_get('display_errors')) echo '<div class="critical">display_errors are turned ON on a production server!</div>';

echo 'post_max_size = '.ini_get('post_max_size').'<br/>';
echo 'upload_max_filesize = '.ini_get('upload_max_filesize').'<br/>';
echo '<br/>';

/************************************
* MySQL version checks              *
************************************/
if ($db->dialect == 'mysql') {
	echo '<h2>MySQL</h2>';

	echo version_compare_array($supported_mysql, $db->server_version);
	echo 'MySQL database server version: '.$db->server_version.'</div>';

	echo version_compare_array($supported_mysql, $db->client_version);
	echo 'MySQL database client version: '.$db->client_version.'</div>';
}

require('design_admin_foot.php');
?>
