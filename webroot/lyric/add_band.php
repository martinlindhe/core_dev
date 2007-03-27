<?
	include ('include_all.php');
	include('body_header.php');

	if ($_SESSION['loggedIn'] && !empty($_POST['bandname']) && isset($_POST['bandinfo']))
	{
		$band_name = trim($_POST['bandname']);
		$band_info = $_POST['bandinfo'];
		
		$band_id = addBand($db, $_SESSION['userId'], $band_name);

		if ($band_id)
		{
			if (!updateBandInfo($db, $band_id, $band_info))
			{
				echo 'Update of band info failed.<br>';
			}
			
			echo '<b>'.$band_name.'</b> added.<br><br>';
			
			if ($_SESSION['userMode'] == 0) {
				/* Add bandId to moderation queue */
				addModerationItem($db, $band_id, MODERATION_BAND);
				echo 'Band addition added to moderation queue aswell<br><br>';
			}
			
			echo '<a href="show_band.php?id='.$band_id.'">Click here to go to '.$band_name.' page</a><br>';
			echo '<br><br>';
			die;
		}
		else
		{
			echo 'A band with that name ('.$band_name.') already exists in database, add failed.<br>';
		}
	}

?>

<table width=400 cellpadding=0 cellspacing=0 border=0>
<form name="addband" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
<tr><td width=100>Band name:</td><td><input type="text" name="bandname"></td></tr>
<tr><td valign="top">Band info:</td><td><textarea name="bandinfo" cols=40 rows=6></textarea></td></tr>
<tr><td colspan=2><input type="submit" value="Add" class="buttonstyle"></td></tr>
</form>
</table>

<script type="text/javascript">
document.addband.bandname.focus();
</script>

<?
	include('body_footer.php');
?>