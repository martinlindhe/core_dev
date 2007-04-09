<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>id generator</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="/css/functions.css" type="text/css"/>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
	<script type="text/javascript" src="/js/functions.js"></script>
	<script type="text/javascript" src="js/idgen.js"></script>
</head>
<body>
<?
	require_once('functions_idgen.php');

	$months = array('Jan', 'Feb', 'Mar', 'Apr', 'Maj', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec');

	$_year = $_month = $_day = $_gender = 0;

	if (!empty($_POST['year']) && !empty($_POST['month']) && !empty($_POST['day']) && !empty($_POST['gender']))
	{
		$_year = $_POST['year'];
		$_month = $_POST['month'];
		$_day = $_POST['day'];
		$_gender = $_POST['gender'];

		$ctrl = generateLastDigits($_year, $_month, $_day, $_gender);
	}

?>
<div id="middle">

	ID generator<br/>
	Välj år:
	<form name="idgen" method="post" action="">

	<select name="year">
<?
	for ($i = date('Y')-100; $i <= date('Y'); $i++) {
		if ($_year) {
			echo '<option value="'.$i.'"'.($i==$_year?' selected="selected"':'').'>'.$i.'</option>';
		} else {
			echo '<option value="'.$i.'"'.($i==date('Y')-20?' selected="selected"':'').'>'.$i.'</option>';
		}
	}
?>
	</select>
	
	<select name="month">
<?
	for ($i = 1; $i <= 12; $i++) {
		echo '<option value="'.($i<10 ? '0'.$i : $i).'"'.($i==$_month?' selected="selected"':'').'>'.$months[$i-1].'</option>';
	}
?>
	</select>

	<select name="day">
<?
	for ($i = 1; $i <= 31; $i++) {
		echo '<option value="'.($i<10 ? '0'.$i : $i).'"'.($i==$_day?' selected="selected"':'').'>'.$i.'</option>';
	}
?>
	</select>
	
	<input name="ctrl" type="text" size="4" value="<?=$ctrl?>"/><br/>
	
	<input type="radio" name="gender" id="gender1" value="1"<?if($_gender==1) echo' checked="checked"';?>/>
	<label for="gender1">Man</label>
	<input type="radio" name="gender" id="gender2" value="2"<?if($_gender==2) echo' checked="checked"';?>/>
	<label for="gender2">Kvinna</label>
	</form>
</div>
</body>
</html>