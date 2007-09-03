<?
# Behövde inte vara rekursiv
function getDirList($base) {
	$retval['dirs'] = array();
	$retval['files'] = array();

	if(is_dir($base)) {
		$dh = opendir($base);
		while (false !== ($dir = readdir($dh))) {
			if (is_dir($base ."/". $dir) && $dir !== '.' && $dir !== '..')  {
				$subs = $dir;
				$subbase = $base ."/". $dir;
			/*Check the creation date of the directory*/
			#if(filemtime($subbase) > $base_date)
			#{
				$getDirList_alldirs[] = $subbase;
			#}
			#	getDirList($subbase);
			} elseif(is_file($base ."/". $dir) && $dir !== '.' && $dir !== '..') {
				$getDirList_allfiles[] = $base ."/". $dir;
			}
		}
		closedir($dh);
		$retval['dirs'] = (!empty($getDirList_alldirs))?$getDirList_alldirs:array();
		$retval['files'] = (!empty($getDirList_allfiles))?$getDirList_allfiles:array();
	} else {
		mkdir($base);
		
	}

	return $retval;
}

function delete_tree($base) {
	if(is_dir($base)) {
		$dh = opendir($base);
		while (false !== ($dir = readdir($dh))) {
			if(file_exists($base ."/". $dir) && $dir !== '.' && $dir !== '..' && !is_dir($base ."/". $dir)) {
				unlink($base ."/". $dir);
			} elseif(is_dir($base ."/". $dir) && $dir !== '.' && $dir !== '..')  {
				delete_tree($base ."/". $dir);
			}
		}
		closedir($dh);
	}
	@rmdir($base);
}

function fix_dirlist($base_dir) {

	$retval = getDirList($base_dir);
	return $retval;

}

function checkDIRS($id, $val, $type = 1) {
	if($type == '1') {
		if(!file_exists($id .'/'. $val) && !is_dir($id .'/'. $val)) @mkdir($id .'/'. $val);
		if(!file_exists($id .'/'. $val .'/normal') && !is_dir($id .'/'. $val .'/normal')) @mkdir($id .'/'. $val .'/normal');
		if(!file_exists($id .'/'. $val .'/full') && !is_dir($id .'/'. $val .'/full')) @mkdir($id .'/'. $val .'/full');
		if(!file_exists($id .'/'. $val .'/thumb') && !is_dir($id .'/'. $val .'/thumb')) @mkdir($id .'/'. $val .'/thumb');
		if(!file_exists($id .'/'. $val .'/movie') && !is_dir($id .'/'. $val .'/movie')) @mkdir($id .'/'. $val .'/movie');
	} else {
		if(!file_exists($id .'/'. $val) && !is_dir($id .'/'. $val)) @mkdir($id .'/'. $val);
		if(!file_exists($id .'/'. $val .'/movie') && !is_dir($id .'/'. $val .'/movie')) @mkdir($id .'/'. $val .'/movie');
	}
	return true;
}


?>