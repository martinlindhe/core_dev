<?
	die;
	
	set_time_limit(0);
	include("_config/online.include.php");
	
	$tot_cnt = 0;
	
	$csv_file = '/home/martin/csbilder/harem_bann.csv';
	$handle = fopen($csv_file, "r");
	while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
		$num = count($data);
		if($num != 3 && $num != 1) die('wrong number of lines:'.$num);
		$inp = '';
	
		if($num == 3) {
			
			$tot_cnt++;
	
			for ($c=0; $c < $num; $c++) {
				$data[$c] = secureINS($data[$c]);
			}
			$check1 = $sql->queryResult("SELECT COUNT(*) as count FROM s_user WHERE id_id = '".$data[1]."' AND status_id = '1' LIMIT 1");
			$check2 = $sql->queryResult("SELECT COUNT(*) as count FROM s_user WHERE id_id = '".$data[2]."' AND status_id = '1' LIMIT 1");
			if($check1 && $check2) {
	$sql->queryUpdate("REPLACE INTO s_userblock SET
	main_id = '".$data[0]."',
	user_id = '".$data[1]."',
	friend_id = '".$data[2]."',
	rel_id = 'u',
	activated_date = NOW()
	");
	if(@$sql->queryUpdate("INSERT INTO s_userblock SET
	main_id = '".$data[0]."',
	user_id = '".$data[2]."',
	friend_id = '".$data[1]."',
	rel_id = 'f',
	activated_date = NOW()
	") == -1) {
	@$sql->queryUpdate("UPDATE s_userblock SET
	main_id = '".$data[0]."',
	activated_date = NOW()
	WHERE
	user_id = '".$data[2]."' AND
	friend_id = '".$data[1]."' AND
	rel_id = 'f'
	");
	}
			 }
		}
	}
	fclose($handle);
	
	echo 'Done. Processed '.$tot_cnt.' rows';
	
	exit;
?>