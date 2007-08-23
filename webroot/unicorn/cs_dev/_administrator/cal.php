<?
session_start();
ob_start();
    ob_implicit_flush(0);
    ob_start('ob_gzhandler');
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');

	require("./set_onl.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}

	$limit = 10;
	$ip_limit = 20;
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
error_reporting(E_ALL);


	$months = array("1" => "Januari", "2" => "Februari", "3" => "Mars", "4" => "April", "5" => "Maj", "6" => "Juni", "7" => "Juli", "8" => "Augusti", "9" => "September", "10" => "Oktober", "11" => "November", "12" => "December");
	$cal = array();
	$year = (!empty($_GET['year']) && is_numeric($_GET['year']))?$_GET['year']:date("Y");
	$month_o = (!empty($_GET['month']) && is_numeric($_GET['month']))?$_GET['month']:stripZero(date("m"));
	$month = (!empty($_GET['month']) && is_numeric($_GET['month']))?((strlen($_GET['month']) < 2)?'0'.$_GET['month']:$_GET['month']):date("m");
	$h_change = false;
	$c_change = false;
	$this_month = stripZero(date("m"));
	$this_day = stripZero(date("d"));
	$this_year = date("Y");

	if(!empty($_POST['dopost'])) {
		foreach($_POST as $key => $val) {

			if(strpos($key, 'take_id') !== false) {
				$kid = explode(":", $key);
				$un = $kid[2];
				$kid = $kid[1];
				if(!empty($_POST['name_id:' . $kid . ':' . $un])) {
					$town = (isset($_POST['town_id:' . $kid . ':' . $un]))?$_POST['town_id:' . $kid . ':' . $un]:'';
					$status = (!empty($_POST['status_id:' . $kid . ':' . $un]) && $_POST['status_id:' . $kid . ':' . $un] == '1')?'1':'2';
					$price = (isset($_POST['price_id:' . $kid . ':' . $un]))?$_POST['price_id:' . $kid . ':' . $un]:'0';
					$price = str_replace('.', '', $price);

					$_POST['take_id:' . $kid . ':' . $un] = (strlen($_POST['take_id:' . $kid . ':' . $un]) < 2)?'0'.$_POST['take_id:' . $kid . ':' . $un]:$_POST['take_id:' . $kid . ':' . $un];
					if(!empty($_POST['day_id:' . $kid . ':' . $un])) {
						mysql_query("UPDATE $cal_tab SET cal_name = '".secureINS($_POST['name_id:' . $kid . ':' . $un])."', cal_town= '".secureINS($town)."', cal_date = '".secureINS($year.'-'.$month.'-'.$_POST['take_id:' . $kid . ':' . $un])."', cal_price = '".secureINS($price)."', status_id = '$status' WHERE main_id = '".secureINS($_POST['day_id:' . $kid . ':' . $un])."' LIMIT 1");
					} else {
						mysql_query("INSERT INTO $cal_tab SET cal_name = '".secureINS($_POST['name_id:' . $kid . ':' . $un])."', cal_town= '".secureINS($town)."', cal_date = '".secureINS($year.'-'.$month.'-'.$_POST['take_id:' . $kid . ':' . $un])."', cal_price = '".secureINS($price)."', status_id = '$status'");
					}
				} elseif(!empty($_POST['day_id:' . $kid . ':' . $un])) {
					mysql_query("DELETE FROM $cal_tab WHERE main_id = '".secureINS($_POST['day_id:' . $kid . ':' . $un])."' LIMIT 1");
				}
			}
		}
		header("Location: cal.php?month=$month&year=$year");
		exit;
	}

	if(!empty($_POST['donotes'])) {
		$check = mysql_result(mysql_query("SELECT COUNT(*) as count FROM $notes_tab WHERE notes_month = '".$year."-".$month."-01'"), 0, 'count');
		if($check > 0) {

			if(!empty($_POST['notes_text'])) {
				mysql_query("UPDATE $notes_tab SET notes_text = '".secureINS($_POST['notes_text'])."', notes_month = '".secureINS($year.'-'.$month.'-01')."', notes_date = NOW() WHERE notes_month = '".$year."-".$month."-01' LIMIT 1");
			} else {
				mysql_query("DELETE FROM $notes_tab WHERE notes_month = '".$year."-".$month."-01' LIMIT 1");
			}
		} elseif(!empty($_POST['notes_text'])) {
			mysql_query("INSERT INTO $notes_tab SET notes_text = '".secureINS($_POST['notes_text'])."', notes_month = '".secureINS($year.'-'.$month.'-01')."', notes_date = NOW()");
		}
		header("Location: cal.php?month=$month&year=$year");
		exit;
	}

	$sql = mysql_query("SELECT main_id, status_id, cal_date, cal_name, cal_town, cal_price FROM $cal_tab WHERE cal_date LIKE '%-".$month."-%' ORDER BY cal_date ASC");

	$notes = mysql_query("SELECT notes_text FROM $notes_tab WHERE notes_month = '".$year."-".$month."-01' LIMIT 1");
	$notes = (mysql_num_rows($notes) > 0)?mysql_result($notes, 0, 'notes_text'):'';

	$o_date = '';
	while($row = mysql_fetch_assoc($sql)) {
		if($row['cal_date'] == $o_date) {
			$cal[$row['cal_date']][] = array("id" => $row['main_id'], "date" => $row['cal_date'], "name" => $row['cal_name'], "town" => $row['cal_town'], "price" => $row['cal_price'], "status" => $row['status_id']);
		} else {
			$cal[$row['cal_date']] = array();
			$cal[$row['cal_date']][] = array("id" => $row['main_id'], "date" => $row['cal_date'], "name" => $row['cal_name'], "town" => $row['cal_town'], "price" => $row['cal_price'], "status" => $row['status_id']);
		}
		$o_date = $row['cal_date'];
	}

	require("./_tpl/admin_head.php");
?>
<script type="text/javascript" src="fnc_adm.js"></script>
<script type="text/javascript">
function compareText(v) {
	c = document.getElementById('notes_compare');
	h = document.getElementById('notes_head');
	if(c.value != v.value) {
		h.className = 'bg_blk txt_look txt_cnt';
		v.title = 'Ej sparad.';
	} else {
		h.className = 'bg_blk txt_cnt';
		v.title = '';
	}
}
function change_view(id) {
	obj = document.getElementById('status_id:' + id);
	img = document.getElementById('img_id:' + id);

	if(obj.value == '1') {
// stänga av!
		img.src = './_img/status_red.gif';
		obj.value = '2';
	} else {
// sätta check
		img.src = './_img/status_none.gif';
		obj.value = '1';
	}
}
</script>
	<table>
	<tr><td height="25"><a href="cal.php">Kalender</a></td></tr>
	<tr>
		<td width="39%" style="padding: 0 0 15px 0;">
			<table width="100%">
			<tr class="bg_blk">
				<td <?=($this_year == $year && $month_o == $this_month)?'title="Aktuell månad" class="txt_look':'class="txt_wht txt_bld';?>" height="30" style="vertical-align: middle; padding-left: 10px;"><?=strtoupper(strftime("%B", strtotime($year.'-'.$month.'-01')))?> MÅNAD <?=$year?></td>
				<td align="right" style="vertical-align: middle;"><input type="button" class="inp_realbtn" value="Uppdatera" style="width: 80px; margin: 0 10px 0 0;" onclick="document.cal.submit();"></td>
			</tr>
			<tr><td colspan="2" class="bg_gray" style="padding: 10px 0 10px 10px;">
				<form name="cal" action="cal.php?month=<?=$month?>&year=<?=$year?>" method="post">
				<input type="hidden" name="dopost" value="1">
				<table width="100%" cellspacing="0">
<?
	$total = 0;
	for($i = 1; $i < 32; $i++) {
		$show = false;
		if(strlen($i) < 2) $i = '0'.$i;
		if(!empty($cal[$year.'-'.$month.'-'.$i]) && is_array($cal[$year.'-'.$month.'-'.$i]) && count($cal[$year.'-'.$month.'-'.$i]) > 0) {
			$show = true;
		}
		$today = false;
		if($this_year == $year && $month_o == $this_month && $i == $this_day) {
			$today = true;
		}
		if($show) { foreach($cal[$year.'-'.$month.'-'.$i] as $val) {
			$total += ($val['status'] == '1')?$val['price']:'0';
			$unique = md5(microtime());
?>
				<tr>
					<td><input type="hidden" name="day_id:<?=$i?>:<?=$unique?>" value="<?=$val['id']?>"><input type="text" style="padding: 3px 0 0 2px; height: 18px; width: 30px;" name="take_id:<?=$i?>:<?=$unique?>" class="inp_nrm<?=($val['status'] == '2')?' txt_shead':(($today)?' txt_look':'');?>" onfocus="this.select();" value="<?=stripZero(date("d", strtotime($val['date'])))?>"></td>
					<td><input type="text" name="name_id:<?=$i?>:<?=$unique?>" class="inp_nrm<?=($val['status'] == '2')?' txt_shead':(($today)?' txt_look':'');?>" style="padding: 3px 0 0 2px; height: 18px; width: 140px;" onfocus="this.select();" value="<?=secureOUT($val['name'])?>"></td>
					<td><input type="text" name="town_id:<?=$i?>:<?=$unique?>" style="padding: 3px 0 0 2px; height: 18px; width: 140px;" class="inp_nrm<?=($val['status'] == '2')?' txt_shead':(($today)?' txt_look':'');?>" onfocus="this.select();" value="<?=secureOUT($val['town'])?>"></td>
					<td><input type="text" name="price_id:<?=$i?>:<?=$unique?>" style="padding: 2px 0 0 2px; height: 18px; width: 40px;" class="inp_nrm txt_shead" onfocus="this.select();" value="<?=secureOUT($val['price'])?>"></td>
					<td>
<input type="hidden" name="status_id:<?=$i?>:<?=$unique?>" id="status_id:<?=$i?>:<?=$unique?>" value="<?=$val['status']?>">
<img src="./_img/status_<?=($val['status'] == '1')?'none':'red';?>.gif" style="margin: 5px 2px 0 0;" onclick="change_view('<?=$i?>:<?=$unique?>');" id="img_id:<?=$i?>:<?=$unique?>">
					</td>
				</tr>
<?
		} } else {
			$unique = md5(microtime());
?>
				<tr>
					<td><input type="hidden" name="day_id:<?=$i?>:<?=$unique?>" value=""><input type="text" style="padding: 3px 0 0 2px; height: 18px; width: 30px;" name="take_id:<?=$i?>:<?=$unique?>" class="inp_nrm<?=($today)?' txt_look':'';?>" onfocus="this.select();" value="<?=stripZero(date("d", strtotime($year.'-'.$month.'-'.$i)));?>"></td>
					<td><input type="text" name="name_id:<?=$i?>:<?=$unique?>" class="inp_nrm<?=($today)?' txt_look':'';?>" style="padding: 3px 0 0 2px; height: 18px; width: 140px;" onfocus="this.select();" value=""></td>
					<td><input type="text" name="town_id:<?=$i?>:<?=$unique?>" style="padding: 3px 0 0 2px; height: 18px; width: 140px;" class="inp_nrm<?=($today)?' txt_look':'';?>" onfocus="this.select();" value=""></td>
					<td style="padding-right: 4px;"><input type="text" name="price_id:<?=$i?>:<?=$unique?>" style="padding: 2px 0 0 2px; height: 18px; width: 40px;" class="inp_nrm txt_shead" onfocus="this.select();" value="0"></td>
					<td>
<input type="hidden" name="status_id:<?=$i?>:<?=$unique?>" id="status_id:<?=$i?>:<?=$unique?>" value="1">
<img src="./_img/status_none.gif" style="margin: 5px 2px 0 0;" onclick="change_view('<?=$i?>:<?=$unique?>');" id="img_id:<?=$i?>:<?=$unique?>">
					</td>
				</tr>
<?
		}
	}
?>
				<tr><td colspan="5" align="right" style="padding: 4px 7px;">Totalt: <input type="text" name="price_id:<?=$i?>" readonly style="padding: 2px 0 0 2px; margin: 0 0 0 0; height: 18px; width: 40px;" class="inp_nrm txt_bld" onfocus="this.select();" value="<?=$total?>"></td>
				</table>
				</form>
			</td></tr>
			<tr class="bg_blk">
				<td colspan="2" height="30" align="right" style="vertical-align: middle;"><input type="button" class="inp_realbtn" value="Uppdatera" style="width: 80px; margin: 0 10px 0 0;" onclick="document.cal.submit();"></td>
			</tr>
			</table>
		</td>
		<td width="180" style="padding: 0 10px 0 15px;">
			<form name="notes" action="cal.php?month=<?=$month?>&year=<?=$year?>" method="post">
			<input type="hidden" name="donotes" value="1">
			<input type="hidden" id="notes_compare" value="<?=secureOUT($notes)?>">
			<table width="100%" class="txt_wht txt_bld">
			<tr><td colspan="2" height="35" class="bg_blk txt_cnt" style="vertical-align: middle;"><a href="cal.php?year=<?=$year - 1?>&month=<?=$month?>">«</a>&nbsp;&nbsp;&nbsp;ÅRGÅNG <?=$year?>&nbsp;&nbsp;&nbsp;<a href="cal.php?year=<?=$year + 1?>&month=<?=$month?>">»</a></td></tr>
			<tr class="bg_blk">
				<td style="padding: 0 5px 5px 15px;">
<ul class="list">
<?
	$i = 1;
	foreach($months as $key => $val) {
		if($i < 7) {
			$selected = ($i == $month_o)?' txt_look':'';
			print '<li class="list_item"><a href="cal.php?month='.$i.'&year='.$year.'" class="txt_wht'.$selected.'">'.$val.'</a></li>';
			$i++;
		}
	}
?>
</ul>
				</td>
				<td style="padding: 0 5px 5px 5px;">
<ul class="list">
<?
	$i = 0;
	foreach($months as $key => $val) {
		$i++;
		if($i > 6 && $i < 13) {
			$selected = ($i == $month_o)?' txt_look':'';
			print '<li class="list_item"><a href="cal.php?month='.$i.'&year='.$year.'" class="txt_wht'.$selected.'">'.$val.'</a></li>';
		}
	}
?>
</ul>
				</td>
			</tr>
			<tr><td colspan="2" height="10">&nbsp;</td></tr>
			<tr><td colspan="2" height="35" class="bg_blk txt_cnt" style="vertical-align: middle;" id="notes_head">ANTECKNINGAR<br><?=strtoupper(strftime("%B", strtotime($year.'-'.$month.'-01')))?></td></tr>
			<tr><td colspan="2" class="bg_blk txt_cnt"><textarea name="notes_text" class="inp_nrm" style="overflow: auto; width: 99%; height: 150px;" onchange="compareText(this);" onkeyup="compareText(this);" onkeydown="compareText(this);"><?=secureOUT($notes)?></textarea></td></tr>
			<tr><td colspan="2" height="30" align="right" class="bg_blk" style="vertical-align: middle;"><input type="button" class="inp_realbtn" value="Spara" style="width: 60px; margin: 0 10px 0 0;" onclick="document.notes.submit();"></td></tr>
			</table>
			</form>

		</td>
	</tr>
	</table>
</body>
</html>
