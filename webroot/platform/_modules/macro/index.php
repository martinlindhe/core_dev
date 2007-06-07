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
	} elseif($action == 'csgb') {
		include('cs_gb.php');
		exit;
	} elseif($action == 'csmail') {
		include('cs_mail.php');
		exit;
	} elseif($action == 'csblock') {
		include('cs_block.php');
		exit;
	} elseif($action == 'csblog') {
		include('cs_blog.php');
		exit;
	} elseif($action == 'csrel') {
		include('cs_rel.php');
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
	} elseif($action == 'updatevip') {
		include('updatevip.php');
		exit;
	}
	reloadACT(l());
?>