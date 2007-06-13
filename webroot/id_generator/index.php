<?
	require_once('config.php');
	
	require_once('functions_idgen.php');
	
	$months = array('Jan', 'Feb', 'Mar', 'Apr', 'Maj', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec');

	$_year = $_month = $_day = $_gender = 0;
	
	$ctrl = '';

	if (!empty($_POST['year']) && !empty($_POST['month']) && !empty($_POST['day']) && !empty($_POST['gender']))
	{
		$_year = $_POST['year'];
		$_month = $_POST['month'];
		$_day = $_POST['day'];
		$_gender = $_POST['gender'];

		$ctrl = generateLastDigits($_year, $_month, $_day, $_gender);
	}
	
	createXHTMLHeader();
?>
<div id="middle">

	ID generator<br/>
	Välj datum &amp; kön för att generera checksumma:

	<form name="idgen" method="post" action="">
<?
	echo '<select name="year">';
	for ($i = date('Y')-100; $i <= date('Y'); $i++) {
		if ($_year) {
			echo '<option value="'.$i.'"'.($i==$_year?' selected="selected"':'').'>'.$i.'</option>';
		} else {
			echo '<option value="'.$i.'"'.($i==date('Y')-20?' selected="selected"':'').'>'.$i.'</option>';
		}
	}
	echo '</select>';

	echo '<select name="month">';
	for ($i = 1; $i <= 12; $i++) echo '<option value="'.($i<10?'0':'').$i.'"'.($i==$_month?' selected="selected"':'').'>'.$months[$i-1].'</option>';
	echo '</select>';

	echo '<select name="day">';
	for ($i = 1; $i <= 31; $i++) echo '<option value="'.($i<10?'0':'').$i.'"'.($i==$_day?' selected="selected"':'').'>'.$i.'</option>';
	echo '</select>';
?>
	<input name="ctrl" type="text" size="4" value="<?=$ctrl?>"/><br/>

	<input type="radio" name="gender" id="gender1" value="1"<?if($_gender==1) echo' checked="checked"';?>/>
	<label for="gender1">Man</label>
	<input type="radio" name="gender" id="gender2" value="2"<?if($_gender==2) echo' checked="checked"';?>/>
	<label for="gender2">Kvinna</label><br/>
	<input type="submit" class="button" value="Generera checksumma"/>
	</form>
	
	<br/><br/>
<?

	$check_id = '800724-0131';	//default sample (randomzied id. aplogize if its a real one)

	if (!empty($_POST['persnr'])) {
		$check_id = $_POST['persnr'];
		if (ValidPersNr($check_id)) {
			echo '<div class="okay">Giltigt personnummer!</div>';
		} else {
			echo '<div class="critical">Ogiltigt personnummer</div>';
		}
	}
?>
	<br/><br/>
	Mata in personnummer för att verifiera om det är korrekt:
	<form method="post" action="">
		Personnummer: <input type="text" name="persnr" value="<?=$check_id?>" size="12"/><br/>
		<input type="submit" class="button" value="Kontrollera"/>
	</form>
	
</div>
</body>
</html>