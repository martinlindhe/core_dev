<?
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

	//$data = file('harem_vipgallery.csv');
	$data = file('harem_gallery.csv');

	$fcnt = 6;

	$src_dir = 'cs/';
	$dst_dir = '_input/usergallery/';

	$i = 0;
	$handled = array();
	$notfound = 0;

	$allowed_images = array('jpg', 'gif', 'png');

	foreach ($data as $row)
	{
		list($main_id,$user_id,$file_name,$pht_cmt) = explode(';', $row);
		$pht_cmt=eregi_replace("\"",'',$pht_cmt);
		$file_name = str_replace('"', '', $file_name);

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

		$src_file = $src_dir.'ORG_'.strtolower($file_name);
		if (!file_exists($src_file)) {
			$notfound++;
			echo 'file not found ('.$notfound.'): '.$src_file.'<br/>';
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
		
		$dst_file = $dst_dir.$folder.'/'.$main_id.'.'.$lastname;

		if (!is_dir($dst_dir.$folder.'/')) {
			mkdir($dst_dir.$folder.'/');
			echo 'Created directory '.$dst_dir.$folder.'/<br/>';
		}

		if (!copy($src_file, $dst_file)) {
			echo 'failed to copy file! src '.$src_file.', dst '. $dst_file.'<br/>';
			continue;
		}

		#echo "$main_id | $user_id | $fcnt | $file_name | $pht_cmt<br />";

		//query for "galleri X" pics, status_id = 2 for "galleri X"
		//$q = "REPLACE INTO s_userphoto SET status_id='2', pht_date='$file_time', main_id='$main_id', hidden_value='', user_id='$user_id', picd='$folder', old_filename='$file_name', pht_cmt='$pht_cmt'";
		
		//query for normal "galleri" pics, status_id = 1
		$q = "REPLACE INTO s_userphoto SET status_id='1', pht_date='$file_time', pht_size='$file_size', main_id='$main_id', hidden_value='', user_id='$user_id', picd='$folder', old_filename='$file_name', pht_cmt='$pht_cmt'";
		$sql->queryInsert($q);
		
		echo '.';

		$handled[ $file_name ] = true;
	}

	$time_spent = microtime(true) - $time_start;
	echo $i.' files imported in '.round($time_spent, 3).' seconds';
?>
