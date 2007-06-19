<?
session_start();
#ob_start();
#    ob_implicit_flush(0);
#    ob_start('ob_gzhandler');
	ini_set("max_execution_time", 0);
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
function fixSemi($a) { return str_replace(';', ':', $a); }
	if(notallowed()) {
		header("Location: ./");
		exit;
	}
	if(!$isCrew) errorNEW('Ingen behörighet.');
	$sql = &new sql();

	$error = false;
	$type = 'all';
	$where = '';
	$tpass = $sql->queryResult("SELECT user_pass FROM s_admin WHERE main_id = '".secureINS($_SESSION['u_i'])."' LIMIT 1");
	$pass = @$_POST['pass'];
	$gotsec = false;
		$try = $sql->queryLine("SELECT id_id, status_id, u_pass FROM s_user WHERE id_id = '".secureINS($_SESSION['u_i'])."'", 1);
		//$try = @mysql_fetch_assoc($try);
		if($try['status_id'] == '4') errorNEW('Ingen behörighet.');
	if(substr($pass, 0, 1) == '!') { $pass = substr($pass, 1); $gotsec = true; }
	if(!empty($pass) && $pass === $tpass) {
		if(isset($_POST['level']) && is_numeric($_POST['level'])) {
			$level = $_POST['level'];
			if($level == '10' || $level == '30' || $level == '50' || $level == '60' || $level == '100') {
				$where = "WHERE level_id = '".secureINS((intval($_POST['level'])/10))."' AND status_id = '1'";
				$sqlord = 'u.id_id ASC';
				$type = 'NIVÅ_'.($level/10);
			} elseif($level == '99') {
				$where = "WHERE status_id = '1'";
				$sqlord = 'u_fname ASC, u_sname ASC';
				$type = 'MEDLEMSLISTA';
			} elseif($level == '2') {
				$where = "WHERE u_picvalid = '1' AND status_id = '1'";
				$sqlord = 'u_email ASC';
				$type = 'HAR_BILD';
			} elseif($level == '3') {
				$where = "WHERE u_picvalid != '1' AND status_id = '1'";
				$sqlord = 'u_email ASC';
				$type = 'HAR_INTE_BILD';
			} elseif($level == '4') {
				$where = "WHERE u_sex = 'F' AND status_id = '1'";
				$sqlord = 'u_alias ASC';
				$type = 'TJEJER';
			} elseif($level == '22') {
				$where = "LEFT JOIN s_obj s ON s.content_type = 'send_email' AND s.owner_id = u.id_id WHERE u.status_id = '1' AND (s.content = '0' OR s.content IS NULL)";
				$sqlord = 'u_alias ASC';
				$type = 'EPOST_JA';
			} elseif($level == '23') {
				$where = "LEFT JOIN s_obj s ON s.content_type = 'send_email' AND s.owner_id = u.id_id WHERE u.status_id = '1' AND (s.content = '1')";
				$sqlord = 'u_alias ASC';
				$type = 'EPOST_NEJ';
			} elseif($level == '33') {
				$where = "LEFT JOIN s_obj s ON s.content_type = 'send_cell' AND s.owner_id = u.id_id WHERE u.status_id = '1' AND (s.content = '0' OR s.content IS NULL)";
				$sqlord = 'u_alias ASC';
				$type = 'SMS_JA';
			} elseif($level == '34') {
				$where = "LEFT JOIN s_obj s ON s.content_type = 'send_cell' AND s.owner_id = u.id_id WHERE u.status_id = '1' AND (s.content = '1')";
				$sqlord = 'u_alias ASC';
				$type = 'SMS_NEJ';
			} elseif($level == '6') {
				$where = "WHERE u_sex = 'M' AND status_id = '1'";
				$sqlord = 'u_alias ASC';
				$type = 'KILLAR';
			} else {
				$where = "WHERE status_id = '1'";
				$sqlord = 'u_alias ASC';
				$type = 'ALLA';
			}
		} else {
			$lan = (!empty($_POST['lan']))?1:0;
			$level = '0';
			if($lan)
				$where = "WHERE u_pstlan = '".secureINS($_POST['lan'])."'";
			else
				$where = '';
			$sqlord = 'u_regdate DESC';
		}

		if($level == '22' || $level == '23')
			$select = 'u_email, city_id';
		elseif($level == '33' || $level == '34')
			$select = 'u_cell, city_id';
		else
			$select = 'u_alias, u_pass, u_email, u_fname, u_sname, u_sex, reg_ip, reg_sess, u_birth, u_birth_x, u_cell, u_regdate, u_street, u_pstnr, u_pstort, u_pstlan';
		if($level == '99') $select = 'u_alias, u_fname, u_sname';
		$res = "SELECT $select FROM s_user u LEFT JOIN s_userinfo i ON i.id_id = u.id_id $where ORDER BY $sqlord";
		$res = $sql->query($res, 0, 1);
		$totalrow = '';
#$res = array_map('fixSemi', $res);
		if($level == '99') {
			foreach($res as $row) {
				$thisrow = 
((!empty($row['u_fname']))?fixSemi(trim(ucwords(strtolower($row['u_fname'])))):'')
." ".
((!empty($row['u_sname']))?fixSemi(trim(ucwords(strtolower($row['u_sname'])))):'')
.";".
((!empty($row['u_alias']))?fixSemi(trim(ucwords(strtolower($row['u_alias'])))):'')
.";".
((!empty($row['city_id']))?fixSemi(trim($row['city_id'])):'')
."\r\n";
				$totalrow .= $thisrow;
			}
		} else {
			if($level == '2' || $level == '3') {
				foreach($res as $row) {
					$thisrow = 
((!empty($row['u_email']))?fixSemi(trim(strtolower($row['u_email']))):'')
.";".
((!empty($row['u_cell']))?fixSemi(trim($row['u_cell'])):'')
.";".
((!empty($row['city_id']))?fixSemi(trim($row['city_id'])):'')
."\r\n";
					$totalrow .= $thisrow;
				}
			} elseif($level == '22' || $level == '23') {
				foreach($res as $row) {
					$thisrow = 
((!empty($row['u_email']))?fixSemi(trim(strtolower($row['u_email']))):'')
.";".
((!empty($row['city_id']))?fixSemi(trim($row['city_id'])):'')
."\r\n";
					$totalrow .= $thisrow;
				}
			} elseif($level == '33' || $level == '34') {
				foreach($res as $row) {
if(!empty($row['u_cell'])) {
					$row['u_cell'] = str_replace(' ', '', $row['u_cell']);
					$row['u_cell'] = str_replace('-', '', $row['u_cell']);
					$row['u_cell'] = (substr($row['u_cell'], 0, 2) == '07')?'46'.substr($row['u_cell'], 1):$row['u_cell'];
					$thisrow = 
((!empty($row['u_cell']))?'+'.fixSemi(trim($row['u_cell'])):'')
.";".
((!empty($row['city_id']))?fixSemi(trim($row['city_id'])):'')
."\r\n";
					$totalrow .= $thisrow;
}
				}
			} else {
			foreach($res as $row) {
			$row['u_cell'] = str_replace(' ', '', $row['u_cell']);
			$row['u_cell'] = str_replace('-', '', $row['u_cell']);
			$row['u_cell'] = (substr($row['u_cell'], 0, 2) == '07')?'46'.substr($row['u_cell'], 1):$row['u_cell'];
				$thisrow = 
((!empty($row['u_alias']))?fixSemi(trim($row['u_alias'])):'')
.";".
((!empty($row['u_pass']))?fixSemi(trim($row['u_pass'])):'')
.";".
((!empty($row['u_email']))?fixSemi(trim(strtolower($row['u_email']))):'')
.";".
((!empty($row['u_fname']))?fixSemi(trim($row['u_fname'])):'')
.";".
((!empty($row['u_sname']))?fixSemi(trim($row['u_sname'])):'')
.";".
((!empty($row['u_sex']))?fixSemi(trim($row['u_sex'])):'')
.";".
((!empty($row['u_birth']))?fixSemi(trim($row['u_birth'])):'')
.";".
(($gotsec)?((!empty($row['u_birth_x']))?fixSemi(trim($row['u_birth_x'])).";":''):'').
((!empty($row['u_cell']))?fixSemi(trim($row['u_cell'])):'')
.";".
((!empty($row['u_street']))?fixSemi(trim($row['u_street'])):'')
.";".
((!empty($row['u_pstnr']))?fixSemi(trim($row['u_pstnr'])):'')
.";".
((!empty($row['u_pstort']))?fixSemi(trim($row['u_pstort'])):'')
.";".
((!empty($row['u_regdate']))?fixSemi(trim($row['u_regdate'])):'')
.";".
((!empty($row['reg_ip']))?fixSemi(trim($row['reg_ip'])):'')
.";".
((!empty($row['reg_sess']))?fixSemi(trim($row['reg_sess'])):'')
.";".
((!empty($row['city_id']))?fixSemi(trim($row['city_id'])):'')
."\r\n";
				$totalrow .= $thisrow;
			}
			}
		}
		sesslogADD($_SESSION['u_u'], '', 'C-EXTRACT!!!');
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download; charset=ISO-8859-1");
		header("Content-Disposition: attachment; filename=".date("ymd_Hi").'_'.$type.".".$_POST['extype']);
		header("Content-Description: File Transfer");
		print $totalrow;
		exit;
	} else {
		sesslogADD($_SESSION['u_u'], $_POST['pass'], 'C-EXTRACTFAIL!!!');
		header('Location: user.php');
	}
?>