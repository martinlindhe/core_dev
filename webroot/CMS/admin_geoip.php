<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	if (isset($_GET['gen_cc'])) {

		$filename = 'functions_geoip_cc.php';

		$sql = 'SELECT * FROM import_geo_cc ORDER BY ci ASC';
		$list = dbArray($geodb, $sql);

		$data =
			"<?\n".
			"//File generated: ".date('Y.m.d')."\n".
			"\n".
			"\$GEOIP_COUNTRY_NAMES = array(\n";

		for ($i=0; $i<count($list); $i++) {
			$data .= "\t".$list[$i]['ci'].'=>"'.$list[$i]['cn'].'"';
			if ($i < (count($list)-1)) $data .= ',';
			$data .= "\n";
		}

		$data .=
			");\n".
			"\n".
			"\$GEOIP_COUNTRY_CODES = array(\n";

		for ($i=0; $i<count($list); $i++) {
			$data .= "\t".$list[$i]['ci'].'=>"'.addslashes($list[$i]['cc']).'"';
			if ($i < (count($list)-1)) $data .= ',';
			$data .= "\n";
		}
		$data .=
			");\n".
			"?>";

		header('Cache-Control: cache, must-revalidate');

		header('Content-Length: '.strlen($data));
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename="'.$filename.'"');

		echo $data;		
		die;
	}

	include('design_head.php');

	echo 'Admin geoip database...<br><br>';
	
	echo '<a href="'.$_SERVER['PHP_SELF'].'?gen_cc">Genererate functions_geoip_cc.php from database</a><br>';

	include('design_foot.php');
?>