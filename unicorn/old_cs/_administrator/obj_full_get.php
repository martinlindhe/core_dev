<?
session_start();
ob_start();
    ob_implicit_flush(0);
    ob_start('ob_gzhandler');
	ini_set("max_execution_time", 0);
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}

	$status = (!empty($_GET['status']))?$_GET['status']:'0';
	$sql = &new sql();
	$thispage = 'obj.php?status=full';
	$view_full = 0;
	if(!empty($_GET['all'])) {
		$view_full = 1;
	}

	if(!empty($_GET['get'])) {
		$pic = $sql->queryLine("SELECT main_id, topic_id, id, p_pic, statusID, status_id FROM {$tab['pic']} WHERE main_id = '".secureINS($_GET['get'])."' LIMIT 1");
		if($pic[5] == '2')
			$file = ADMIN_IMAGE_DIR.$pic[1].'/'.$pic[2].'-full'.GALLERY_CODE.'-'.$pic[6].'.'.$pic[3];
		else
			$file = ADMIN_IMAGE_DIR.$pic[1].'/'.$pic[2].'-full'.GALLERY_CODE.'.'.$pic[3];
		if(file_exists($file)) {
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/force-download; charset=ISO-8859-1");
			header("Content-Disposition: attachment; filename=".strtoupper(NAME_FILE).'_'.$pic[0].'.'.$pic[3]);
			header("Content-Description: File Transfer");
			readfile($file);
			exit;
		} else errorACT('Filen finns inte!', $thispage);
	}
?>