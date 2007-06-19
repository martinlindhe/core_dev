<?
session_start();
	ini_set("max_execution_time", 0);
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	require("./set_tmb.php");
	require("./lib_zip.php");
	require("./lib_dir.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}
	$vimmel = &new vimmel();
    $start = execSt();	
	$ttl = 'POSTLOAD';
	$image_dir = ADMIN_IMAGE_DIR;

if(!empty($_GET['do_gen'])) {
		$doit = false;
		if(!empty($_GET['id'])) {
			$sql = mysql_query("SELECT main_id, owner_id FROM $topic_tab WHERE main_id = '".secureINS($_GET['id'])."' LIMIT 1");
			if(mysql_num_rows($sql) > 0) {
				$doit = true;
				$movefiles = 0;
				$got_movie = 0;
				$if_go = mysql_result(mysql_query("SELECT COUNT(*) as count FROM $pic_tab WHERE topic_id = '".secureINS($_GET['id'])."'"), 0, 'count');

				if($if_go) $movefiles = 1;
				$row = mysql_fetch_assoc($sql);
				$p_own = $row['owner_id'];
				if(!empty($_GET['owner'])) $p_owner = $_GET['owner']; else $p_owner = '';
			}
		}
		if(!$doit) {
			$msg = 'Felaktig session.';
			$js_ex = 'window.close()';
			require("./_tpl/notice_admin.php");
			exit;
		}
		if(empty($_GET['file']) || !is_zip($_GET['file']) || !file_exists($in_dir.$_GET['file'])) {
			$msg = 'Felaktig fil.';
			$js_ex = 'window.close()';
			require("./_tpl/notice_admin.php");
			exit;
		} else $file_sent = $_GET['file'];

		$archive = new PclZip($in_dir.$file_sent);
		$file = substr($file_sent, 0, strrpos($file_sent, "."));
		$madenew = false;
		if(!file_exists($in_dir.$file) || !is_dir($in_dir.$file)) {
			$madenew = true;
			@mkdir($in_dir.$file, 0755);
		}
		if(!$archive->extract(PCLZIP_OPT_PATH, $in_dir.$file)) {
			if($madenew) @rmdir($in_dir.$file);
			$msg = 'Error: '.$archive->errorInfo(true);
			$js_ex = 'window.close()';
			require("./_tpl/notice_admin.php");
			exit;
		} else {
			if(gettxt('admin_vimmeldeletezip')) unlink($in_dir.$file_sent);
		}
		$img_dir = fix_dirlist($in_dir.$file);
		# Rensa från icke bilder
		if(count($img_dir['dirs']) > 0) { foreach($img_dir['dirs'] as $val) {
			$dir_val  = substr($val, 0, strrpos($val, "/") + 1);
			$file_val = substr(strrchr($val, "/"), 1);
			#system("rm -rf " . $dir_val . $file_val);
			delete_tree($dir_val . $file_val);
		} }
		header("Location: pics_generate_SAME.php?id=".$row['main_id']."&dir=".$file."&mf=".$movefiles."&gm=".$got_movie."&owner=".((!empty($p_owner))?$p_owner:$p_own));
		exit;
} elseif(!empty($_GET['id']) && !empty($_GET['dir']) && isset($_GET['mf'])) {
		$doit = false;
		if(!empty($_GET['id'])) {
			$sql = mysql_query("SELECT main_id, owner_id, p_exclusive FROM $topic_tab WHERE main_id = '".secureINS($_GET['id'])."' LIMIT 1");
			if(mysql_num_rows($sql) > 0) {
				$doit = true;
				$movefiles = (!empty($_GET['mf']))?'1':'0';
				$got_movie = (!empty($_GET['gm']))?'1':'0';
				$movie = '';

				$row = mysql_fetch_assoc($sql);
				if($movefiles) {
					if(!file_exists(ADMIN_IMAGE_DIR.$row['main_id'].'/')) { 
						$oldunmask = umask(0); 
						mkdir(ADMIN_IMAGE_DIR.$row['main_id'], 0777); 
						umask($oldunmask);
					}
				} else {
					if(!$got_movie && !file_exists(ADMIN_IMAGE_DIR.$row['main_id'].'/')) { 
						#delete_tree(ADMIN_IMAGE_DIR.$row['main_id']);
						$oldunmask = umask(0); 
						mkdir(ADMIN_IMAGE_DIR.$row['main_id'], 0777); 
						umask($oldunmask);
					}
				}
				$p_own = $row['owner_id'];
				if(!empty($_GET['owner'])) $p_owner = $_GET['owner']; else $p_owner = '';
			}
		}
		if(!$doit) {
			$msg = 'Felaktig session.';
			#$js_ex = 'window.close()';
			require("./_tpl/notice_admin.php");
			exit;
		}
		if(empty($_GET['dir']) || !is_dir($in_dir.$_GET['dir']) || !file_exists($in_dir.$_GET['dir'])) {
			$msg = 'Felaktig mapp.';
			#$js_ex = 'window.close()';
			require("./_tpl/notice_admin.php");
			exit;
		} else $dir = $_GET['dir'];

		if(!file_exists($in_dir.$dir.'/'.$row['main_id'].'/') || !is_dir($in_dir.$dir.'/'.$row['main_id'])) { 
			$oldunmask = umask(0); 
			@mkdir($in_dir.$dir.'/'.$row['main_id'], 0777); 
			@umask($oldunmask);
			$gen_dir = $in_dir.$dir.'/'.$row['main_id'].'/';
		} else $gen_dir = $in_dir.$dir.'/'.$row['main_id'].'/';

		$img_dir = fix_dirlist($in_dir.$dir);
		$i = 0;
		if(count($img_dir['files']) > 0) { foreach($img_dir['files'] as $val) {
			$img_info = getimagesize($val);
			switch($img_info[2]) {
			case 2:
				$p_name = '.jpg';
				$file_stripped = substr(strrchr($val, "/"), 1);
				$file_stripped = explode('.', $file_stripped);
				unset($file_stripped[count($file_stripped)-1]);
				$file_stripped = implode('.', $file_stripped);
				$unique = microtime();
				$file_id = md5($unique . rand(1, 999999));
				$errors = 0;
		      		$errors += doThumb($val, $gen_dir.$file_stripped.$p_name, $upl_full[0], $upl_full[1]);
				$errors += doThumb($gen_dir.$file_stripped.$p_name, $gen_dir.$file_stripped.'-thumb'.$p_name, $upl_thumb[0], $upl_thumb[1], 90);
				$errors += doWM($gen_dir.$file_stripped.$p_name, $gen_dir.$file_stripped.$p_name, 93, $row['p_exclusive']);

				rename($val, $gen_dir.$file_stripped.'-full1537'.$p_name);
				#if($movefiles) {
					rename($gen_dir.$file_stripped.$p_name, ADMIN_IMAGE_DIR.$row['main_id'].'/'.$file_stripped.$p_name);
					rename($gen_dir.$file_stripped.'-full1537'.$p_name, ADMIN_IMAGE_DIR.$row['main_id'].'/'.$file_stripped.'-full1537'.$p_name);
					rename($gen_dir.$file_stripped.'-thumb'.$p_name, ADMIN_IMAGE_DIR.$row['main_id'].'/'.$file_stripped.'-thumb'.$p_name);
				#}
				mysql_query("INSERT INTO $pic_tab SET status_id = '".(($movefiles)?'0':'1')."', topic_id = '".secureINS($row['main_id'])."', id = '$file_stripped', p_pic = '".substr($p_name, 1)."', p_date = NOW(), owner_id = '".secureINS(((!empty($p_owner))?$p_owner:$p_own))."'");
				$vimmel->vimmelAdd('pic', $row['main_id'], (($movefiles)?'0':'1'));
				if($errors == 0) { @unlink($val); }  else { $log = ' |error| '; exit; }
				$i++;
	$msg = 'Bild genererad!<br><br><b>Bilder kvar: '.(count($img_dir['files'])-1).'.</b><br><br>Vänta...';
	$mv = 'pics_generate_SAME.php?id='.$row['main_id'].'&dir='.$dir.'&mf='.$movefiles.'&gm='.$got_movie.'&owner='.$p_owner;
	$time = '1000';
	require("./_tpl/notice_apopup.php");
	exit;
				break;
			default:
				@unlink($val);	
			}
		} }
		if($movefiles) {
			delete_tree($in_dir.$dir);
			@rmdir($indir.$dir);
		} else {
		#	if($got_movie) {
		#		rename(ADMIN_IMAGE_DIR.$row['main_id'].'/'.$movie.'.wmv', $gen_dir.$movie.'.wmv');
		#		rename(ADMIN_IMAGE_DIR.$row['main_id'].'/'.$movie.'.jpg', $gen_dir.$movie.'.jpg');
		#	}
			#if(file_exists(ADMIN_IMAGE_DIR.$row['main_id'].'/')) { 
			#	delete_tree(ADMIN_IMAGE_DIR.$row['main_id']);
			#}
			#@rename($gen_dir, ADMIN_IMAGE_DIR.$row['main_id']);
			delete_tree($in_dir.$dir);
			@rmdir($indir.$dir);
		}
		$msg = 'Färdig!';
		$js_sex = 'window.opener.location.href = \'pics.php?id='.$row['main_id'].'\';';
		$ex = 'window.close();';
		require("./_tpl/notice_apopup.php");
		exit;
}

?>