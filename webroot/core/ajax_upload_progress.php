<?
	//todo: this path is not good!
	require_once('../adblock/config.php');

	if (empty($_GET['s']) || !is_numeric($_GET['s'])) die;
	
  $status = apc_fetch('upload_'.$_GET['s']);
  print_r($status);
?>