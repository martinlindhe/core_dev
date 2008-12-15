<?php
/**
 * Sample program to expose validate_ssn.php features
 */

require_once('config.php');

$months = array('Jan', 'Feb', 'Mar', 'Apr', 'Maj', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec');

$_year = $_month = $_day = $_gender = 0;

if (!empty($_POST['year'])) $_year = $_POST['year'];
if (!empty($_POST['month'])) $_month = $_POST['month'];
if (!empty($_POST['day'])) $_day = $_POST['day'];
if (!empty($_POST['gender'])) $_gender = $_POST['gender'];
$ctrl = '';

if ($_year && $_month && $_day && $_gender) {
	$ctrl = SsnRandomizeSwedish($_year, $_month, $_day, $_gender);
}
$generated = $ctrl ? ($_year.$_month.$_day.'-'.$ctrl) : '';
?>

Välj datum &amp; kön för att generera checksumma:

<form name="idgen" method="post" action="">
<?php

echo '<select name="year">';
for ($i = date('Y')-100; $i <= date('Y'); $i++) {
	if ($_year) {
		echo '<option value="'.$i.'"'.($i==$_year?' selected="selected"':'').'>'.$i.'</option>';
	} else {
		echo '<option value="'.$i.'"'.($i==date('Y')-25?' selected="selected"':'').'>'.$i.'</option>';
	}
}
echo '</select>';

echo '<select name="month">';
for ($i = 1; $i <= 12; $i++) echo '<option value="'.($i<10?'0':'').$i.'"'.($i==$_month?' selected="selected"':'').'>'.($i<10?'0'.$i:$i).' ('.$months[$i-1].')</option>';
echo '</select>';

echo '<select name="day">';
for ($i = 1; $i <= 31; $i++) echo '<option value="'.($i<10?'0':'').$i.'"'.($i==$_day?' selected="selected"':'').'>'.($i<10?'0'.$i:$i).'</option>';
echo '</select>';
?>
<input name="ctrl" type="text" size="4" value="<?=$ctrl?>"/>
<?=$generated?>
<br/>

<input type="radio" name="gender" id="gender2" value="<?=SSN_GENDER_FEMALE?>"<?if($_gender==SSN_GENDER_FEMALE || !$_gender) echo' checked="checked"';?>/>
<label for="gender2">Kvinna</label>
<input type="radio" name="gender" id="gender1" value="<?=SSN_GENDER_MALE?>"<?if($_gender==SSN_GENDER_MALE) echo' checked="checked"';?>/>
<label for="gender1">Man</label>
<br/>
<input type="submit" class="button" value="Generera checksumma"/>
</form>

<br/><br/>
<?php

$ssn = (!empty($_POST['persnr'])) ? $_POST['persnr'] : '19630321-4032';	//randomized (i aplogize if its a real one)
?>
<br/><br/>
Mata in ett personnummer för att verifiera om det är korrekt:
<form method="post" action="">
Personnummer: <input type="text" name="persnr" value="<?=$ssn?>" size="13"/>
<?php

$chkgender = (!empty($_POST['chkgender'])) ? $_POST['chkgender'] : 0;

if (!empty($_POST['persnr'])) {
	$ssn_check = SsnValidateSwedish($ssn, $chkgender);
	if ($ssn_check === true) {
		echo '<span class="okay">Giltigt!</span>';
	} else {
		echo '<span class="critical">Ogiltigt: '.$ssn_error[$ssn_check].'</span>';
	}
}
?>
	<br/>
	Personnummret tillhör en
	<input type="radio" name="chkgender" id="chkgender2" value="<?=SSN_GENDER_FEMALE?>"<?if($chkgender==SSN_GENDER_FEMALE) echo' checked="checked"';?>/>
	<label for="chkgender2">Kvinna</label>
	<input type="radio" name="chkgender" id="chkgender1" value="<?=SSN_GENDER_MALE?>"<?if($chkgender==SSN_GENDER_MALE) echo' checked="checked"';?>/>
	<label for="chkgender1">Man</label>
	<input type="radio" name="chkgender" id="chkgender0" value="<?=SSN_GENDER_UNKNOWN?>"<?if($chkgender==SSN_GENDER_UNKNOWN) echo' checked="checked"';?>/>
	<label for="chkgender0">Vet ej</label><br/>
	<input type="submit" class="button" value="Kontrollera"/>
</form>
<br/><br/><br/>

<form method="post" action="">
<?php

if (!empty($_POST['chkdate'])) {	//YYMMDD
	$chkdate = $_POST['chkdate'];
	$yr = substr($chkdate, 0, 2);
	$yr = ($yr > date('y')) ? '19'.$yr : '20'.$yr;	//years below curryear is considered to be 2000-20xx, otherwise its 1900-19xx
	$mn = intval(substr($chkdate, 2, 2));
	$dy = intval(substr($chkdate, 4, 2));
	if (!checkdate($mn, $dy, $yr)) echo 'Ogiltigt datum';

	$ts = mktime(0, 0, 0, $mn, $dy, $yr);

	echo ((time()-$ts)/31556926).' years<br/>';
}
?>
	Mata in ett datum för att se hur gammal personen är:
	<input type="text" name="chkdate" value="890218"/>
	<input type="submit" class="button" value="Kolla"/>
</form>

</body>
</html>
