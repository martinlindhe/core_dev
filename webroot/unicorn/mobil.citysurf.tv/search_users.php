<?
	require_once('config.php');
	$user->requireLoggedIn();

	require('design_head.php');
?>

	<div class="h_search"></div>

	<div class="mid_content">
	<form method="post" action="search_users_result.php">
		<table width="100%">
			<tr>
				<td>Kön:</td>
				<td>
					<input type="hidden" name="sex" value="0"/>
					<input type="radio" name="sex" id="sexM" value="M"/><label for="sexM">Killar</label>
					<input type="radio" name="sex" id="sexF" value="F"/><label for="sexF">Tjejer</label><br/>
				</td>
			</tr>
			<td>
				<!--	<input type="checkbox" name="online" id="online" value="1"/><label for="online">Online nu</label> -->
				<!--	<input type="checkbox" name="pic" id="pic" value="1" checked="true"/><label for="pic">Har bild</label><br/> -->
				Alias:</td>
			 <td>
			 	<input type="text" name="alias" size="15"/>
			</td>
		</tr>
		<tr>
			<td>Ålder:</td>
			<td>
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
			</td>
		</tr>
	</table>

		<select name="lan">
			<option value="0">Alla län</option>
			<? optionLan(@$result['lan']); ?>
		</select><br/><br/>
		
		<input type="submit" value="Sök"/>
	</form>
</div>

<?
	require('design_foot.php');
?>
