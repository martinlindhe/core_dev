<?
	include('include_all.php');
	
	$show = '';
	if (isset($_GET['id']) && $_GET['id'] != $_SESSION['userId']) {
		$show = $_GET['id'];
		$showname = getUserName($db, $show);
	} else if ($_SESSION['userId']) {
		$show     = $_SESSION['userId'];
		$showname = $_SESSION['userName'];
	}

	if (!$_SESSION['loggedIn'] || !$showname || ($show == $_SESSION['userId']) || ($show == $config['messages']['system_id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (substr($showname, -1) == 's') {
		$niceshowname = $showname."'";
	} else {
		$niceshowname = $showname.'s';
	}

	if (!empty($_GET['removerelation']) && is_numeric($_GET['removerelation'])) {
		removeFriend($db, $_GET['removerelation']);
		header('Location: user_relations.php?id='.$_GET['removerelation']);
		die;
	}

	include('design_head.php');
	include('design_user_head.php');
	
		$content = '';

		if (isset($_POST['msg'])) {
			addFriendRequest($db, $show, $_POST['msg']);
			
			$content .= 'En foresp&oslash;rsel har blitt sendt til '.$showname.'<br>';
			$content .= 'Du vil f&aring; en melding n&aring;r mottakeren har svart p&aring; foresp&oslash;rselen.<br><br>';

			$content .= '<a href="user_show.php?id='.$show.'">'.$config['text']['link_return'].'</a>';
			
		} else {
			
			if (hasPendingFriendRequest($db, $show)) {

				$content .= 'Du har allerede sendt en foresp&oslash;rsel til '.$showname.'<br><br>';
				$content .= 'Du kan slette foresp&oslash;rselen <a href="user_relations.php?id='.$_SESSION['userId'].'">h&auml;r</a>.';

				//$content .= 'Du har redan en p&aring;g&aring;ende f&ouml;rfr&aring;gan om att bli v&auml;n med '.$showname.'.<br><br>';
				//$content .= 'Du kan radera din f&ouml;rfr&aring;gan <a href="user_relations.php?id='.$_SESSION['userId'].'">h&auml;r</a>.';
			} else {
	
				/* Skapa relation */
				if (!isFriend($db, $show)) {

					$content .= 'Her kan du sp&oslash;rre om '.$showname.' vil bli en av dine venner.<br>';
					$content .= $showname.' m&aring; godkjenne foresp&oslash;rselen f&oslash;r dere blir listet i hverandres "Mine venner"-liste.<br><br>';

					$content .= 'Send en hilsen:<br>';
					$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$show.'">';
					$content .= '<textarea name="msg" cols=40 rows=5></textarea><br><br>';
					$content .= '<input type="submit" class="button" value="Send">';
					$content .= '</form><br>';
				} else {
					$content .= 'Du har en relation med '.$showname.'<br><br>';
					$content .= '<a href="'.$_SERVER['PHP_SELF'].'?id='.$show.'&removerelation='.$show.'">Avbryt relationen</a><br>';
				}
			}
		}

		echo '<div id="user_misc_content">';
		echo MakeBox('Venner', $content);
		echo '</div>';

	include('design_user_foot.php');
	include('design_foot.php');
?>