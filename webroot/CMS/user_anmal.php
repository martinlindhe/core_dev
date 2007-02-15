<?
	include('include_all.php');

	if (empty($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$show = $_GET['id'];
	/* Rapportera användaren till abuse */
	$queueId = addToModerationQueue($db, $show, MODERATION_REPORTED_USER);

	include('design_head.php');

	/* Lägg till en kommentar till anmälan */
	if (isset($_POST['motivation'])) {
		addModerationQueueComment($db, $queueId, $_POST['motivation'], $_SESSION['userId']);

		echo 'Anm&auml;l '.getUserName($db, $show).'<br><br>';

		echo getInfoField($db, 'anmäl_användare_färdig').'<br><br>';

		echo '<a href="user_show.php?id='.$show.'">'.$config['text']['link_return'].'</a>';

	} else {

		echo 'Anm&auml;l '.getUserName($db, $show).'<br><br>';
		echo getInfoField($db, 'anmäl_användare');
		echo '<br><br>';

		echo '<form name="abuse" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$show.'">';
		echo 'Motivera anm&auml;lningen:<br>';
		echo '<textarea name="motivation" cols=44 rows=5></textarea><br>';
		echo '<input type="submit" class="button" value="'.$config['text']['link_report'].'">';
		echo '</form>';

		echo '<a href="user_show.php?id='.$show.'">'.$config['text']['link_return'].'</a><br>';
	}

	include('design_foot.php');
?>