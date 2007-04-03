<?
	require('design_head.php');
	
	/*
		todo: slå ihop som en "skriv nya brev" + "svara på brev" (skicka med mail-id som vi ska svara på isåfall
	
		todo: dropdown med folk från kompislistan
	
	*/
?>

	SKIV NYTT MAIL<br/>
	<br/>

	<form method="post" action="">
		Till: <input type="text"/><br/>
		Rubrik: <input type="text"/><br/>
		Meddelande:<br/>
		<textarea></textarea><br/>
		<input type="submit" value="Skicka"/>
	</form>


<?
	require('design_foot.php');
?>