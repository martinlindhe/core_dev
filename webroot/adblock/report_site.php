<?
	include_once('include_all.php');

	if (!empty($_POST['site'])) {
		$type = 0;
		if (!empty($_POST['type'])) $type = $_POST['type'];
		$siteId = addProblemSite($db, $_POST['site'], $type, $_POST['comment']);
		if ($siteId) {

			include('design_head.php');
?>
The report for site <b><?=htmlentities($_POST['site'])?></b> has been recieved,<br/>
we will look into this site as soon as possible!<br/><br/>
<a href="<?=$_SERVER['PHP_SELF']?>">Report another site</a><br/>
<?
			include('design_foot.php');
			die;

		} else {
			echo 'Error adding problematic site to database!<br/><br/>';
		}
	}

	include('design_head.php');

	echo getInfoField($db, 'page_report_top');
?>
<br/>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>" name="reportform">
<table width="500" cellpadding="0" cellspacing="0" border="0">
	<tr><td width="20">&nbsp;</td><td class="centermenu">
			Site address:<br/>
			<input type="text" name="site" size="86"/><br/>
			<br/>
			<input type="radio" name="type" value="1"/>Site contains advertisement &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
			<input type="radio" name="type" value="2"/>Blocking rules breaks the site<br/><br/><br/>
			Comments (optional):<br/>
			<textarea name="comment" rows="7" cols="84"></textarea><br/><br/>
			<input type="submit" value="Report site"/>
	</td></tr>
</table>
</form>
<br/>
<?
	echo getInfoField($db, 'page_report_bottom');
?>
<script type="text/javascript">
document.reportform.site.focus();
</script>
<?
	include('design_foot.php');
?>