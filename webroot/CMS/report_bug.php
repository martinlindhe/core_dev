<?
	include_once('include_all.php');

	include('design_head.php');
	
	if (isset($_POST['desc'])) {

		addBugReport($db, $_POST['desc']);

		echo getInfoField($db, 'page_report_bug_complete');
		
	} else {

		echo getInfoField($db, 'page_report_bug').'<br>';

		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
		echo 'Please describe the problem you are reporting:<br>';
		echo '<textarea name="desc" cols=60 rows=8></textarea><br><br>';
		echo '<input type="submit" class="button" value="Submit bugreport"><br>';
		echo '</form>';
		echo '<br>';
	}
	
	include('design_foot.php');
?>