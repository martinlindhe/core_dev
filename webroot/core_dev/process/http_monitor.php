<?
/*
	here you ask the server to monitor a remote server
*/

	require_once('config.php');

	require('design_head.php');

	if (!empty($_POST['adr'])) {

		$fileId = processEvent(PROCESSMONITOR_SERVER, serialize($_POST));

		echo 'Server monitoring has been added.';
		require('design_foot.php');
		die;
	}


	wiki('ProcessMonitorServer');

	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	echo 'Enter server address:<br/>';
	echo '<input type="text" name="adr" size="40" id="url"/><img src="'.$config['core_web_root'].'gfx/arrow_next.png" align="absmiddle" onclick="expand_input(\'adr\')"/><br/>';
	echo 'Port: <input type="text" name="port" size="5"/><br/>';
	echo 'Server type:<br/>';
	echo '<input type="radio" name="type" value="ping" checked="checked"/>Just track uptime<br/>';
	echo '<input type="radio" name="type" value="snmp"/>Fetch SNMP data<br/>';
	echo '<input type="radio" name="type" value="httpd"/>Web server<br/>';
	echo '<input type="radio" name="type" value="mysql"/>MySQL<br/>';
	
	echo '<input type="submit" class="button" value="Add"/>';
	echo '</form>';

	require('design_foot.php');
?>