<?
/*
todo: lista låtar som inte finns på nån skiva (admin)
todo: lista låtar som inte tillhör nåt band (t.ex ogiltigt bandId) (admin)
todo: dumpa databasen dagligen automatiskt
*/

	include('include_all.php');
	include('body_header.php');

	if ($_SESSION['loggedIn'] == true) {
		echo '<a href="add_band.php">Add band</a><br>';
		echo '<a href="add_record.php">Add normal record</a><br>';
		echo '<a href="add_record_comp.php">Add compilation / split</a><br>';
		echo '<br>';
	}
?>
<a href="list_bands.php">List bands</a><br>
<br>

<a href="missing_lyrics.php">List missing lyrics</a><br>
<a href="incomplete_lyrics.php">List incomplete lyrics</a><br>
<br>


<form name="search" method="post" action="search.php">
<input type="text" name="query">
<input type="submit" value="Search" class="buttonstyle">
</form>
<script type="text/javascript">
document.search.query.focus();
</script>
<?

	echo 'There are '.bandCount($db).' bands, '.recordCount($db).' records, '.trackCount($db).' tracks and '.lyricCount($db).' lyrics in database.<br>';
	echo '<br>';

	if ($_SESSION['loggedIn'] == true) {
		if ($_SESSION['userMode'] == 1) {
			echo 'You are a administrator<br>';
		} else {
			echo 'You are a normal user<br>';
		}
		echo '<a href="logout.php">Log out</a><br>';
	} else {
		echo '<a href="login.php">Log in</a><br>';
	}
	

	include('body_footer.php');
?>
