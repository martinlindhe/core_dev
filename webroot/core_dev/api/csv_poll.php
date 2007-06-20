<?
	/* csv_poll.php - returns the specified polls result as csv data. requires admin */

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	require_once('find_config.php');
	
	$session->requireAdmin();

	$_id = $_GET['id'];
	
	$outfile = 'poll #'.$_id.' - '.date('Y-m-d H:i').'.txt';		//default extension for csv files in spss

	header('Content-type: text/csv');
	header('Content-Disposition: attachment; filename="'.$outfile.'"');
	
	
	echo '"Option","Votes"'."\n";
	$votes = getPollStats($_id);
	$tot_votes = 0;
	foreach ($votes as $row) $tot_votes += $row['cnt'];

	foreach ($votes as $row) {
		$pct = 0;
		if ($tot_votes) $pct = (($row['cnt'] / $tot_votes)*100);
		echo '"'.$row['categoryName'].'","'.$row['cnt'].'"'."\n";
	}

?>