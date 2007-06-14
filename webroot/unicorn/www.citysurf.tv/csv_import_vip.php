<?
	die;

	set_time_limit(0);
	include("_config/online.include.php");
	
	$tot_cnt = 0;
	$csv_file = '/home/martin/csbilder/harem_vip.csv';
	
	$handle = fopen($csv_file, "r");
	while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
		$num = count($data);
		if($num != 3) die('ok:'.$num);
		$inp = '';
		
		if($num == 3) {
			$tot_cnt++;
			for ($c=0; $c < $num; $c++) {
				$data[$c] = secureINS($data[$c]);
			}
			$check1 = $sql->queryResult("SELECT COUNT(*) as count FROM s_user WHERE id_id = '".$data[1]."' AND status_id = '1' LIMIT 1");
			if($check1) {
				
				
				$timestamp = strtotime($data[2]);

				if ($timestamp < time()) {
					echo 'skipping expired vip period '.$data[2].'<br>';
					continue;
				}

				$time_diff = $timestamp - time();

				$days = round($time_diff / 86400, 0) +1;

				echo 'Giving user '.$data[1].' vipd '.$days.' days<br/>';

				addVIP($data[1], VIP_LEVEL2, $days);
			 }
		}
	}
	fclose($handle);
	
	echo 'Done. Processed '.$tot_cnt.' rows';
	exit;
?>