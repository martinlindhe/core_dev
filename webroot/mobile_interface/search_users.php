<?
	require('design_head.php');
?>

	SÖK ANVÄNDARE<br/>
	<br/>

	<form method="post" action="search_users_result.php">
		<input type="checkbox"/>Killar
		<input type="checkbox"/>Tjejer
		<input type="checkbox"/>Online nu<br/>
		Fritext: <input type="text"/><br/>
		
		Stad:
		<select name="xx">
			<option>Alla städer
			<option>Stockholm
		</select>
		
		<input type="submit" value="Sök"/>
	</form>

<?
	require('design_foot.php');
?>