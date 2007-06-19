<?
	die;


	/*
		script för att importera userdata & presentationsbilder från gamla citysurf.tv till nya av Frans Rosén & Martin Lindhe, 2007
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

	$file_csv = '/home/martin/csbilder/harem_users.csv';
	$fp = fopen($file_csv, 'r');
	if (!$fp) die('cant read file');

	$fcnt = 2;

	$src_dir = '/home/martin/csbilder/cs_profilbilder/';
	$dst_dir = '/home/martin/www/_input/images/';

	$skipped = 0;
	$i = $j = $k = 0;
	$handled = array();

	$allowed_images = array('jpg', 'gif', 'png');
	
	$skip_files = array('def_man.jpg', 'def_kvinna.jpg');
	
	$unvalidated = $banned = $badusername = 0;

	do
	{
		$data = fgetcsv($fp, 0, ';');

		if (count($data) != 68) {
			echo '<h1>WRONG NUMBER OF COLUMS FETCHED: '.count($data).'</h1>';
			continue;
		}
		$j++;

		if (trim($data[1]) != $data[1] || !$data[1]) {
			echo '<h1>Bad username "'.$data[1].'"</h1>';
			$badusername++;
			continue;
		}
		
		if ($data[64] != 'False') {
			echo '<h1>skipping BANNED USER '.$data[1].'</h1>';
			$banned++;
			continue;
		}

		for ($c=0; $c < count($data); $c++) {
			$data[$c] = secureINS($data[$c]);
		}

		$uid = $data[0];	//[0]  = userId
		$file_name = $data[60];	//[60] = presentationsbild. def_man.jpg / def_kvinna.jpg = defaults
		
		//start frans-kod:
		if($data[4] == 'True') $data[4] = 'F'; else $data[4] = 'M';	//kön
		if($data[10] == 'False') $data[10] = '2'; else $data[10] = '1';	//validerad
		
		if ($data[10] != '1') {
			echo '<h1>USER NOT VALIDATED '.$data[1].'</h1>';
			$unvalidated++;
			continue;
		}
		
		if($data[65] == 'True') $data[10] = '3';
		$q = "REPLACE INTO s_user SET
			id_id = '".$uid."',
			u_alias = '".$data[1]."',
			u_pass = '".$data[2]."',
			u_email = '".$data[3]."',
			u_sex = '".$data[4]."', 
			u_birth = '".$data[6]."',
			u_regdate = '".$data[8]."',
			lastlog_date = '".$data[9]."',
			account_date = '".$data[9]."',
			lastonl_date = '".$data[9]."',
			status_id = '".$data[10]."',
			u_pstort = '".$data[13]."'
		";
		$sql->queryUpdate($q);

		//if($data[10] == '1') {
			$birth = explode(' ', $data[6]);
			$birth = $birth[0];
			$age = $user->doage($birth);
			$group = $user->doagegroup($age);
			$sql->queryUpdate("REPLACE INTO s_userlevel SET
			id_id = '".$data[0]."',
			level_id = 'ACTIVE VALID SEX".$data[4]." LEVEL1 BIRTH".$birth." AGEOF".$group." ORT".str_replace(' ', '', strtoupper($data[13]))." LÄN".str_replace(' ', '', strtoupper($data[13]))."'");
		//}
		$sql->queryUpdate("REPLACE INTO s_userbirth SET
		id_id = '".$data[0]."',
		level_id = '".$data[6]."'");

		$user->obj_set('login_offset', 'user_head', $data[0], $data[11]);
		$user->obj_set('user_pres', 'user_profile', $data[0], nl2br($data[5]));
		//slut frans-kod

		if (in_array($file_name, $skip_files)) continue;

		$lastname = getFileLastname($file_name);
		if (!in_array($lastname, $allowed_images)) {
			$skipped++;
			echo 'refused file type: '.$lastname.'<br/>';
			continue;
		}

		//skippar filename dupes
		if (!empty($handled[ $file_name ] )) {
			$skipped++;
			echo 'skipping dupe '.$file_name.'<br/>';
			continue;
		}

		//kontrollera att profilbilden finns på hårddisken
		if ($uid < 10000) $curr_dir = 'p_10000';
		else if ($uid < 20000) $curr_dir = 'p_20000';
		else if ($uid < 30000) $curr_dir = 'p_30000';
		else if ($uid < 40000) $curr_dir = 'p_40000';
		else if ($uid < 50000) $curr_dir = 'p_50000';
		else if ($uid < 60000) $curr_dir = 'p_60000';
		else if ($uid < 70000) $curr_dir = 'p_70000';
		else if ($uid < 80000) $curr_dir = 'p_80000';
		else if ($uid < 90000) $curr_dir = 'p_90000';
		else if ($uid < 100000) $curr_dir = 'p_100000';
		else if ($uid < 110000) $curr_dir = 'p_110000';
		else if ($uid < 120000) $curr_dir = 'p_120000';
		else if ($uid < 130000) $curr_dir = 'p_130000';
		else if ($uid < 140000) $curr_dir = 'p_140000';
		else if ($uid < 150000) $curr_dir = 'p_150000';
		else if ($uid < 160000) $curr_dir = 'p_160000';
		else if ($uid < 170000) $curr_dir = 'p_170000';
		else if ($uid < 180000) $curr_dir = 'p_180000';
		else if ($uid < 190000) $curr_dir = 'p_190000';
		else if ($uid < 200000) $curr_dir = 'p_200000';
		else if ($uid < 210000) $curr_dir = 'p_210000';
		else if ($uid < 220000) $curr_dir = 'p_220000';
		else if ($uid < 230000) $curr_dir = 'p_230000';
		else if ($uid < 240000) $curr_dir = 'p_240000';
		else if ($uid < 250000) $curr_dir = 'p_250000';
		else if ($uid < 260000) $curr_dir = 'p_260000';
		else if ($uid < 270000) $curr_dir = 'p_270000';
		else if ($uid < 280000) $curr_dir = 'p_280000';
		else if ($uid < 290000) $curr_dir = 'p_290000';
		else if ($uid < 300000) $curr_dir = 'p_300000';
		else if ($uid < 310000) $curr_dir = 'p_310000';
		else if ($uid < 320000) $curr_dir = 'p_320000';
		else if ($uid < 330000) $curr_dir = 'p_330000';
		else if ($uid < 340000) $curr_dir = 'p_340000';
		else if ($uid < 350000) $curr_dir = 'p_350000';
		else if ($uid < 360000) $curr_dir = 'p_360000';
		else if ($uid < 370000) $curr_dir = 'p_370000';
		else if ($uid < 380000) $curr_dir = 'p_380000';
		else if ($uid < 390000) $curr_dir = 'p_390000';
		else if ($uid < 400000) $curr_dir = 'p_400000';
		else die('too hi uid '.$uid);

		$src_file = $src_dir.$curr_dir.'/ORG/ORG_'.$file_name;
		$src_thumb = $src_dir.$curr_dir.'/TH/TH_'.$file_name;
		if (!file_exists($src_file)) {
			$skipped++;
			echo 'file not found ('.$skipped.'): '.$src_file.'<br/>';
			continue;
		}
		
		$k++;
		
		$file_time = sql_datetime(filemtime($src_file));
		$file_size = filesize($src_file);

		$i++;
		if ($i >= 10000) {
			$i=0; $fcnt++;
			echo '10k files copied!<br/>';
		}
		if ($fcnt<10) $folder = '0'.$fcnt;
		else $folder = $fcnt;
		
		$pic_id = '04';
		$dst_file = $dst_dir.$folder.'/'.$uid.$pic_id.'.'.$lastname;
		$dst_thumb = $dst_dir.$folder.'/'.$uid.$pic_id.'_2.'.$lastname;

		if (!is_dir($dst_dir.$folder.'/')) {
			mkdir($dst_dir.$folder.'/');
			echo 'Created directory '.$dst_dir.$folder.'/<br/>';
		}

		if (!copy($src_file, $dst_file)) {
			echo '<h1>Failed to copy file! src '.$src_file.', dst '. $dst_file.'</h1>';
			continue;
		}

		if (!copy($src_thumb, $dst_thumb)) {
			echo '<h1>Failed to copy thumb! src '.$src_thumb.', dst '. $dst_thumb.'</h1>';
			continue;
		}


		$q = 'UPDATE s_user SET u_picdate="'.$file_time.'",u_picvalid="1", u_picd="'.$folder.'", u_picid="'.$pic_id.'" WHERE id_id='.$uid;
		$sql->queryInsert($q);
		echo '.';


		//filnamn som slutar med _2, alltså bild_2.jpg är en thumbnail
		

		$handled[ $file_name ] = true;
	} while (!feof($fp));

	$time_spent = microtime(true) - $time_start;
	echo $j.' total users, '.$k.' files processed, '.$skipped.' files skipped in '.round($time_spent, 3).' seconds<br/>';
	echo 'skipped '.$unvalidated.' unvalidated users, '.$banned.' banned users, '.$badusername.' bad usernames.';
?>
