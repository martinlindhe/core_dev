<?
	/* Returns true if $curr_ver is "higher than or equal" than any of the versions in the array $arr */
	function version_compare_array($arr, $curr_ver)
	{
		foreach ($arr as $supp_ver)
		{
			if (version_compare($supp_ver, $curr_ver, ">=")) return true;
		}
		return false;
	}

	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo '<h1>Compatiblity check</h1>';

	$supported_apache = array('2.2.0', '2.2.4');
	
	$current_apache = apache_get_version();
		
	echo 'core version 0.1<br/<br/>';

	echo 'Apache web server version: '.$current_apache.'<br/>';

	/************************************
	* PHP version checks                *
	************************************/
	$supported_php = array('5.1.6', '5.2.2');
	$current_php = phpversion();
	$current_php_gd = phpversion('gd2');	//todo: funkar ej
	$current_php_apc = phpversion('apc');

	echo 'PHP script language version: '.$current_php.' '.(version_compare_array($supported_php, $current_php)?'<span class="okay">OK</span>':'<span class="critical">NOT TESTED</span>').'<br/>';
	echo 'Required PHP extension "gd2": '.$current_php_gd.'<br/>';
	echo 'Optional PHP extension "apc": '.$current_php_apc.'<br/>';

	/************************************
	* MySQL version checks              *
	************************************/
	if ($db->dialect == 'mysql') {
		$supported_mysql = array('5.1.17');
		$current_mysql_server = $db->server_version;
		$current_mysql_client = $db->client_version;

		echo 'MySQL database server version: '.$current_mysql_server.' '.(version_compare_array($supported_mysql, $current_mysql_server)?'<span class="okay">OK</span>':'<span class="critical">NOT TESTED</span>').'<br/>';
		echo 'MySQL database client version: '.$current_mysql_client.' '.(version_compare_array($supported_mysql, $current_mysql_client)?'<span class="okay">OK</span>':'<span class="critical">NOT TESTED</span>').'<br/>';
	}

	require($project.'design_foot.php');
?>