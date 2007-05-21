<?
	require_once('config.php');
	if (!$l) die;	//user not logged in

	require('design_head.php');
?>

	SÖK ANVÄNDARE<br/><br/>

	<div class="mid_content">
	<form method="post" action="search_users_result.php">
		Kön:
		<input type="hidden" name="sex" value="0"/>
		<input type="radio" name="sex" id="sexM" value="M"/><label for="sexM">Killar</label>
		<input type="radio" name="sex" id="sexF" value="F"/><label for="sexF">Tjejer</label><br/>
		
<!--		<input type="checkbox" name="online" id="online" value="1"/><label for="online">Online nu</label> -->
		<input type="checkbox" name="pic" id="pic" value="1" checked="true"/><label for="pic">Har bild</label><br/>
		Alias: <input type="text" name="alias" size="15"/><br/>
		Ålder:
		<select name="age">
			<option value="0">alla åldrar</option>
			<option value="1">mellan 0-20 år</option>
			<option value="2">mellan 21-25 år</option>
			<option value="3">mellan 26-30 år</option>
			<option value="4">mellan 31-35 år</option>
			<option value="5">mellan 36-40 år</option>
			<option value="6">mellan 41-45 år</option>
			<option value="7">mellan 46-50 år</option>
			<option value="8">mellan 51-55 år</option>
			<option value="9">56 år och äldre</option>			
		</select>
		<br/>

		<select name="lan">
			<option value="0">Alla län</option>
<?		optionLan($result['lan']); ?>
		</select>
		
		<input type="submit" value="Sök"/>
	</form>
</div>

<?
	require('design_foot.php');
?>