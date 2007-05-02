<?
session_start();
#ob_start();
#    ob_implicit_flush(0);
#    ob_start('ob_gzhandler');
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}
	if(!$isCrew && strpos($_SESSION['u_a'][1], 'news_send') === false) errorNEW('Ingen behörighet.');

	$doevent = false;
	$totalrow = '';
	$where = '';


	if(empty($_GET['t']) && !empty($_GET['id']) && (is_numeric($_GET['id']) || is_md5($_GET['id']))) {
		$doid = $_GET['id'];
		$type = 'send';
	}
	if($type == 'send' && is_md5($doid)) {
		$visit = (empty($_GET['r']))?false:true;
		$del = (empty($_GET['d']))?false:true;
		if($visit) {
			$type = 'visit';
			$sql = "SELECT unique_id, date_cnt FROM s_sendvisit WHERE category_id = '".secureINS($doid)."' AND unique_id != '' AND site_visit = '1' ORDER BY date_cnt DESC";
		} elseif($del) {
			$type = 'del';
			$sql = "SELECT unique_id, date_cnt FROM s_senddelete WHERE category_id = '".secureINS($doid)."' AND unique_id != '' ORDER BY date_cnt DESC";
		} else {
			$type = 'read';
			$sql = "SELECT unique_id, date_cnt FROM s_sendvisit WHERE category_id = '".secureINS($doid)."' AND unique_id != '' ORDER BY date_cnt DESC";
		}
		$sql = mysql_query($sql);
		if(mysql_num_rows($sql) > 0) {
			while($row = mysql_fetch_assoc($sql)) {
				$thisrow = 
((!empty($row['unique_id']))?strtolower($row['unique_id']):'')
.';'.
((!empty($row['date_cnt']))?$row['date_cnt']:'')
."\r\n";
				$totalrow .= $thisrow;
			}
		}
	} elseif($type == 'all') {

	}

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download; charset=ISO-8859-1");
	header("Content-Disposition: attachment; filename=".$type."_".date("Y-m-d H-i").".txt");
	header("Content-Description: File Transfer");
	print $totalrow;
	exit;
?>