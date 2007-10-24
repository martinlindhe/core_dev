<?
	require_once('config.php');

	if (!empty($_POST['site'])) {
		$type = 0;
		if (!empty($_POST['type'])) $type = $_POST['type'];
		//$siteId = addProblemSite($_POST['site'], $type, $_POST['comment']);
		$siteId  = saveFeedback($type, $_POST['site'], $_POST['comment']);

		require('design_head.php');

		if ($siteId) {
			echo 'The report for site <b>'.htmlentities($_POST['site'], ENT_QUOTES, 'UTF-8').'</b> has been recieved,<br/>';
			echo 'we will look into this site as soon as possible!<br/><br/>';
			echo '<a href="'.$_SERVER['PHP_SELF'].'">Report another site</a><br/>';

		} else {
			echo '<span class="critical">Error adding problematic site to database!</span>';
		}

		require('design_foot.php');
		die;
	}

	require('design_head.php');

	wiki('Report_top');
?>
<br/>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>" name="reportform">
<table width="500" cellpadding="0" cellspacing="0" border="0">
	<tr><td width="20">&nbsp;</td><td class="centermenu">
			Site address:<br/>
			<input type="text" name="site" size="86"/><br/>
			<br/>
			<input type="radio" name="type" id="radio1" value="<?=FEEDBACK_ADBLOCK_ADS?>"/>
			<label for="radio1">Site contains advertisement</label> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
			<input type="radio" name="type" id="radio2" value="<?=FEEDBACK_ADBLOCK_BROKEN_RULE?>"/>
			<label for="radio2">Blocking rules breaks the site</label><br/><br/><br/>
			Comments (optional):<br/>
			<textarea name="comment" rows="7" cols="60"></textarea><br/><br/>
			<input type="submit" class="button" value="Submit report"/>
	</td></tr>
</table>
</form>
<br/>
<?
	wiki('Report_bottom');
?>
<script type="text/javascript">
document.reportform.site.focus();
</script>
<?
	require('design_foot.php');
?>