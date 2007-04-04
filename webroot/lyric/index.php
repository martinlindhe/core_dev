<?
	require_once('config.php');
	require('design_head.php');

	if ($session->id) {
		echo '<a href="add_band.php">Add band</a><br/>';
		echo '<a href="add_record.php">Add normal record</a><br/>';
		echo '<a href="add_record_comp.php">Add compilation / split</a><br/>';
		echo '<br/>';
	}
?>
<a href="list_bands.php">List bands</a><br/>
<br/>

<a href="missing_lyrics.php">List missing lyrics</a><br/>
<a href="incomplete_lyrics.php">List incomplete lyrics</a><br/>
<br/>

<form name="search" method="post" action="search.php">
	<input type="text" name="query"/>
	<input type="submit" value="Search" class="buttonstyle"/>
</form>
<script type="text/javascript">
document.search.query.focus();
</script>
<?
	echo 'There are '.bandCount().' bands, '.recordCount().' records, '.trackCount().' tracks and '.lyricCount().' lyrics in database.<br/>';
	echo '<br/>';

	if ($session->id) {
		if ($session->isAdmin) {
			echo 'You are a administrator<br/>';
		} else {
			echo 'You are a normal user<br/>';
		}
		echo '<a href="logout.php">Log out</a><br/>';
	} else {
		echo '<a href="login.php">Log in</a><br/>';
	}

	require('design_foot.php');
?>
