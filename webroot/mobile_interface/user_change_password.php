<?
	require('design_head.php');
?>

	ÄNDRA LÖSENORD<br/>
	<br/>

	<form method="post">
		Gammalt lösenord: <input type="password"/><br/>
		Nytt lösenord: <input type="password"/><br/>
		<br/>
		Bekräfta lösenord: <input type="password"/><br/>
		Snabbingloggnings PIN: <input type="text"/><br/>
		<input type="submit" value="Spara"/>
	</form>

<?
	require('design_foot.php');
?>