<?
	$supported_apache = array('2.2.3', '2.2.4');

	$supported_php = array('5.2.0', '5.2.2');
	$supported_php_gd = array('2.0.34', '2.0.34');
	$supported_php_apc = array('3.0.14', '3.0.15');

	$supported_mysql = array('5.0.36', '5.1.17');

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

	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo '<h1>Compatiblity check</h1>';

	echo 'core version 0.1<br/>';
	echo 'Server OS: ';
	if (!empty($_SERVER['WINDIR'])) {
		echo 'Windows';
	} else {
		echo '<span class="critical">Unknown</span>';
	}
	echo '<br/>';
	echo ($config['debug']?'<div class="critical">Debug: On - turn off for production use</div>':'<div class="okay">Debug: OFF</div>');
	echo '<br/>';

	/************************************
	* Apache version checks             *
	************************************/
	echo '<h2>Apache</h2>';
	$current_apache = apache_get_version();
	if ($current_apache == 'Apache') {
		echo '<div class="okay" onclick="toggle_element_by_name(\'apache_info_noversion\')">';
		echo '(show-more-info image)';
		echo ' Version information not available</div>1';
		echo '<div id="apache_info_noversion" style="display: none">';
		echo 'Production servers are sometimes configured not to report version information (ServerTokens Prod), this also makes Apache not report version information to PHP).';
		echo '</div>';
	} else {
		//Version string come in this form: Apache/2.2.4 (Win32)
		if (substr($current_apache, 0, 7) == 'Apache/') $current_apache = substr($current_apache, strlen('Apache/'));
		echo 'Apache web server version: '.$current_apache.' '.(version_compare_array($supported_apache, $current_apache)?'<span class="okay">OK</span>':'<span class="critical">NOT TESTED</span>').'<br/>';
	}
	echo '<br/>';

	/************************************
	* PHP version checks                *
	************************************/
	echo '<h2>PHP</h2>';

	$current_php = phpversion();

	$x = gd_info();
	$current_php_gd = $x['GD Version'];	//looks like: "bundled (2.0.34 compatible)"
	$current_php_gd = str_replace('bundled (', '', $current_php_gd);
	$current_php_gd = str_replace(' compatible)', '', $current_php_gd);
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
		$current_mysql_server = $db->server_version;
		$current_mysql_client = $db->client_version;

		echo version_compare_array($supported_mysql, $current_mysql_server);
		echo 'MySQL database server version: '.$current_mysql_server.'</div>';
		
		echo version_compare_array($supported_mysql, $current_mysql_client);
		echo 'MySQL database client version: '.$current_mysql_client.'</div>';
	}

	require($project.'design_foot.php');
?>