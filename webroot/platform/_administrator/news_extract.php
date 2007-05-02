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
	if(!$isCrew) errorNEW('Ingen behörighet.');
	if(empty($_GET['id']) || !is_numeric($_GET['id'])) {
		errorNEW('Felaktigt event.', 'news.php');
	}
	$sql = &new sql();
	$pos = $sql->query("SELECT a.e_name, a.e_cell, a.e_email, u.u_alias FROM {$tab['news']}event a LEFT JOIN {$tab['user']} u ON u.id_id = a.e_user AND u.status_id = '1' WHERE a.e_id = '".$_GET['id']."' ORDER BY a.main_id DESC", 0, 1);
	$totalrow = '';
	$type = $_GET['id'];
	foreach($pos as $row) {
		$row['e_cell'] = (substr($row['e_cell'], 0, 2) == '07')?'46'.substr($row['e_cell'], 1):$row['e_cell'];
		$thisrow = 
((!empty($row['e_name']))?ucwords(strtolower(trim($row['e_name']))):'')
.";".
((!empty($row['e_email']))?strtolower(trim($row['e_email'])):'')
.";".
((!empty($row['e_cell']))?trim($row['e_cell']):'')
.";".
((!empty($row['u_alias']))?trim($row['u_alias']):'')
."\r\n";
		$totalrow .= $thisrow;
	}
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download; charset='ISO-8859-1'");
	header("Content-Disposition: attachment; filename=".$type."_".date("Y-m-d H-i").".txt");
	header("Content-Description: File Transfer");
	print $totalrow;
	exit;
?>