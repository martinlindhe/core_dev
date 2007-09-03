<?
	die;
	set_time_limit(0);
	include("_config/online.include.php");
	
	$tot_cnt = 0;
	$csv_file = '/home/martin/csbilder/harem_diary.csv';
	
	$handle = fopen($csv_file, "r");
	while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
		$num = count($data);
		if($num != 5 && $num != 1) die('ok:'.$num);
		$inp = '';

		if($num == 5) {
			$tot_cnt++;
			for ($c=0; $c < $num; $c++) {
				$data[$c] = secureINS($data[$c]);
			}
			$check1 = $sql->queryResult("SELECT COUNT(*) as count FROM s_user WHERE id_id = '".$data[1]."' AND status_id = '1' LIMIT 1");
			if($check1) {
	$sql->queryUpdate("REPLACE INTO s_userblog SET
	main_id = '".$data[0]."',
	user_id = '".$data[1]."',
	status_id = '1',
	blog_idx = '".$data[2]."',
	blog_date = '".$data[2]."',
	blog_cmt = '".$data[3]."',
	blog_title = '".$data[4]."'
	");
			 }
		}
	}
	fclose($handle);
	
	echo 'Done. Processed '.$tot_cnt.' rows';
	exit;
?>