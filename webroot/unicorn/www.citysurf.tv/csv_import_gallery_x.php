<?
	die;

	/*
		script för att importera galleribilder från gamla citysurf.tv till nya av Martin Lindhe, 2007
	*/

	function getFileLastname($name)
	{
		$result = substr($name, strrpos($name, '.') + 1);
		if (!$result) return false;
		return strtolower($result);
	}

	function sql_datetime($timestamp)
	{
		return date('Y-m-d H:i:s', $timestamp);
	}
	
	$time_start = microtime(true);

	set_time_limit(60*60*2);
	include("_config/online.include.php");

	$file_csv = '/home/martin/csbilder/harem_vipgallery.csv';
	$fp = fopen($file_csv, 'r');
	if (!$fp) die('cant read file');


	$fcnt = 6;

	$src_dir = '/home/martin/csbilder/cs_vipgallery/';
	$dst_dir = '/home/martin/www/_input/usergallery/';

	$i = $tot_files = 0;
	$handled = array();
	$notfound = 0;

	$allowed_images = array('jpg', 'gif', 'png');

	do
	{
		//list($main_id,$user_id,$file_name,$pht_cmt) = explode(';', $row);
		//$pht_cmt=eregi_replace("\"",'',$pht_cmt);
		//$file_name = str_replace('"', '', $file_name);
		
		$data = fgetcsv($fp, 0, ';');
		
		if (count($data) != 4) {
			echo '<h1>fel antal kolumner!</h1>';
			continue;
		}
		
		$main_id = $data[0];
		$user_id = $data[1];
		$file_name = $data[2];
		$pht_cmt = $data[3];

		$lastname = getFileLastname($file_name);
		if (!in_array($lastname, $allowed_images)) {
			echo 'refused file type: '.$lastname.'<br/>';
			continue;
		}

		//skippar filename dupes
		if (!empty($handled[ $file_name ] )) {
			echo 'skipping dupe '.$file_name.'<br/>';
			continue;
		}

		//kontrollera att profilbilden finns på hårddisken
		if ($user_id < 10000) $curr_dir = 'vg_10000';
		else if ($user_id < 20000) $curr_dir = 'vg_20000';
		else if ($user_id < 30000) $curr_dir = 'vg_30000';
		else if ($user_id < 40000) $curr_dir = 'vg_40000';
		else if ($user_id < 50000) $curr_dir = 'vg_50000';
		else if ($user_id < 60000) $curr_dir = 'vg_60000';
		else if ($user_id < 70000) $curr_dir = 'vg_70000';
		else if ($user_id < 80000) $curr_dir = 'vg_80000';
		else if ($user_id < 90000) $curr_dir = 'vg_90000';
		else if ($user_id < 100000) $curr_dir = 'vg_100000';
		else if ($user_id < 110000) $curr_dir = 'vg_110000';
		else if ($user_id < 120000) $curr_dir = 'vg_120000';
		else if ($user_id < 130000) $curr_dir = 'vg_130000';
		else if ($user_id < 140000) $curr_dir = 'vg_140000';
		else if ($user_id < 150000) $curr_dir = 'vg_150000';
		else if ($user_id < 160000) $curr_dir = 'vg_160000';
		else if ($user_id < 170000) $curr_dir = 'vg_170000';
		else if ($user_id < 180000) $curr_dir = 'vg_180000';
		else if ($user_id < 190000) $curr_dir = 'vg_190000';
		else if ($user_id < 200000) $curr_dir = 'vg_200000';
		else if ($user_id < 210000) $curr_dir = 'vg_210000';
		else if ($user_id < 220000) $curr_dir = 'vg_220000';
		else if ($user_id < 230000) $curr_dir = 'vg_230000';
		else if ($user_id < 240000) $curr_dir = 'vg_240000';
		else if ($user_id < 250000) $curr_dir = 'vg_250000';
		else if ($user_id < 260000) $curr_dir = 'vg_260000';
		else if ($user_id < 270000) $curr_dir = 'vg_270000';
		else if ($user_id < 280000) $curr_dir = 'vg_280000';
		else if ($user_id < 290000) $curr_dir = 'vg_290000';
		else if ($user_id < 300000) $curr_dir = 'vg_300000';
		else if ($user_id < 310000) $curr_dir = 'vg_310000';
		else if ($user_id < 320000) $curr_dir = 'vg_320000';
		else if ($user_id < 330000) $curr_dir = 'vg_330000';
		else if ($user_id < 340000) $curr_dir = 'vg_340000';
		else if ($user_id < 350000) $curr_dir = 'vg_350000';
		else if ($user_id < 360000) $curr_dir = 'vg_360000';
		else if ($user_id < 370000) $curr_dir = 'vg_370000';
		else if ($user_id < 380000) $curr_dir = 'vg_380000';
		else if ($user_id < 390000) $curr_dir = 'vg_390000';
		else if ($user_id < 400000) $curr_dir = 'vg_400000';
		else die('too hi uid '.$user_id);

		$src_file = $src_dir.$curr_dir.'/ORG/ORG_'.$file_name;
		$src_thumb = $src_dir.$curr_dir.'/MIN/MIN_'.$file_name;
		
		if (!file_exists($src_file)) {
			$notfound++;
			//echo 'file not found '.$notfound.'<br/>';
			continue;
		}

		$file_time = sql_datetime(filemtime($src_file));
		$file_size = filesize($src_file);

		$i++;
		if ($i >= 10000) {
			$i=0; $fcnt++;
			echo '10k files copied!<br/>';
		}
		if ($fcnt<10) $folder = '0'.$fcnt;
		else $folder = $fcnt;

		$hidden_value = md5(microtime().mt_rand(10000,999999));

		$dst_file = $dst_dir.$folder.'/'.$main_id.'_'.$hidden_value.'.'.$lastname;
		$dst_thumb = $dst_dir.$folder.'/'.$main_id.'-tmb.'.$lastname;
		
		if (!is_dir($dst_dir.$folder.'/')) {
			mkdir($dst_dir.$folder.'/');
			echo 'Created directory '.$dst_dir.$folder.'/<br/>';
		}
		
		if (!copy($src_file, $dst_file)) {
			echo 'failed to copy file! src '.$src_file.', dst '. $dst_file.'<br/>';
			continue;
		}

		$tot_files++;

		if (!copy($src_thumb, $dst_thumb)) {
			echo '<h1>Failed to copy thumb! src '.$src_thumb.', dst '. $dst_thumb.'</h1>';
			continue;
		}

		#echo "$main_id | $user_id | $fcnt | $file_name | $pht_cmt<br />";

		//query for "galleri x" pics, status_id = 1, hidden_id = 1. view_id=1 = granskad & godkänd
		$q = 'REPLACE INTO s_userphoto SET status_id="1", hidden_id="1", view_id="1", hidden_value="'.$hidden_value.'",pht_date="'.$file_time.'", pht_size="'.$file_size.'", main_id="'.$main_id.'", user_id="'.$user_id.'", picd="'.$folder.'", old_filename="'.$file_name.'", pht_cmt="'.$pht_cmt.'"';
		$sql->queryInsert($q);
		echo '.';

		$handled[ $file_name ] = true;
	} while (!feof($fp));

	$time_spent = microtime(true) - $time_start;
	echo $tot_files.' files imported in '.round($time_spent, 3).' seconds';
?>