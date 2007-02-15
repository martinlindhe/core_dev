<?
	//fixme: gör användbar

	die;

		/* Radera sig själv */
/*
		echo '<b>Radera dig sj&auml;lv</b><br><br>';
		echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$show.'&deleteme=1">Klicka h&auml;r</a> f&ouml;r att radera dig sj&auml;lv samt alla anv&auml;ndaruppgifter som har med dig att g&ouml;ra.<br>';
		echo '<br>';
*/


	if (isset($_GET['deleteme'])) {
		if ($_GET['deleteme'] == 1) { //bekräfta

			echo 'Vill du verkligen radera dig sj&auml;lv?<br><br>';

			echo '<table width="40%" cellpadding=0 cellspacing=0 border=0><tr>';
			echo '<td align="center"><a href="'.$_SERVER['PHP_SELF'].'?id='.$show.'&deleteme=2">'.$config['text']['prompt_yes'].'</a></td>';
			echo '<td align="center"><a href="'.$_SERVER['PHP_SELF'].'?id='.$show.'">'.$config['text']['prompt_no'].'</a></td>';
			echo '</tr></table>';

		} else if ($_GET['deleteme'] == 2) { //genomföra

			removeUser($db, $show);
						
			if ($show == $_SESSION['userId']) {
				echo 'Du &auml;r nu raderad.<br><br>';
				logoutUser($db, $show);
				$_SESSION = array();
				session_destroy();
			} else {
				echo 'Anv&auml;ndaren &auml;r nu raderad.<br><br>';
			}

			echo '<a href="'.$config['start_page'].'">Till startsidan</a>';
		}

?>