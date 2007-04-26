<?
	set_time_limit(0);
	if($action == 'fixretrieve') {
		include('fixretrieve.php');
		exit;
	} elseif($action == 'fixmap') {
		include('fixmap.php');
		exit;
	} elseif($action == 'fixdb') {
		include('fixdb.php');
		exit;
	} elseif($action == 'cs') {
		include('cs.php');
		exit;
	} elseif($action == 'fixlevel') {
		include('fixlevel.php');
		exit;
	} elseif($action == 'fixonline') {
		include('fixonline.php');
		exit;
	} elseif($action == 'fixage') {
		include('fixage.php');
		exit;
	}
	reloadACT(l());
?>