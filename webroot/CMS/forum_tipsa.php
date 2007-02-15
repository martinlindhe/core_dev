<?
	include("include_all.php");
	
	if (isset($_GET['id']) && $_SESSION['loggedIn']) {
		$itemId = $_GET['id'];
	} else {
		header('Location: '.$config['start_page']); die;
	}

	include('design_head.php');
	include('design_forum_head.php');

	/* Lägg till en kommentar till anmälan */
	if (isset($_POST['mail'])) {

		if (ValidEmail($_POST['mail'])) {
			
			$item = getForumItem($db, $itemId);
			
			if (isset($_POST['namn']) && $_POST['namn']) {
				$mail = "Hej ".$_POST["namn"]."!\n\n";
			} else {
				$mail = "Hej!\n\n";
			}

			$mail .= $_SESSION["userName"]." har skickat dig den här länken till dig från communityt\n";
			$mail .= "på vår sajt, ".$config['site_url']."/.\n\n";
			
			if ($item["authorId"]) {
				$mail .= $item["itemSubject"]." av ".$item["authorName"].", ".getRelativeTimeLong($item["timestamp"]).":\n";
			} else {
				$mail .= $item["itemSubject"]." av gäst, ".getRelativeTimeLong($item["timestamp"])."\n";
			}

			$mail .= "För att läsa inlägget i sin helhet, klicka på länken nedan:\n";
			$mail .= $config['site_url']."/forum.php?id=".$itemId."#".$itemId."\n\n";

			if (isset($_POST["comment"]) && $_POST["comment"]) {
				$mail .= "\n";
				$mail .= "Din kompis lämnade även följande hälsning:\n";
				$mail .= $_POST["comment"]."\n\n";
			}
			
			$mail .= "Vänliga hälsningar\n";
			$mail .= "Reply Solutions.";
			
			$subject = "Meddelande från communityt";
			if (sendMail($_POST['mail'], $subject, $mail, "uReply <info@tack.se>") == true) {

				echo 'Tipset ivägskickat<br>';
				echo getInfoField($db, 'hjalp-tipsa_om_inlagg_klar');

			} else {
				echo 'Problem med utskicket<br>';
				echo getInfoField($db, 'hjalp-tipsa_om_inlagg_misslyckat');
			}
			echo '<br><br>';
			echo '<a href="forum.php?id='.$itemId.'#'.$itemId.'">'.$config['text']['link_return'].'</a>';
			
		} else {
			echo 'Ogiltig mailaddress!';
		}

	} else {

		echo '<b>Tipsa om inl&auml;gg</b><br>';

		echo getInfoField($db, 'hjalp-tipsa_om_inlagg');
		echo '<br><br>';
		
		
		$data = getForumItem($db, $itemId);
		echo showForumPost($db, $data).'<br>';
	
		echo '<form name="tipsa" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';
		echo 'Din kompis namn: <input name="namn" type="text" maxlength=30 size=20><br>';
		echo 'E-post: <input name="mail" type="text" maxlength=50 size=40><br>';
		echo '<br>';
		echo 'H&auml;lsning:<br>';
		echo '<textarea name="comment" cols=40 rows=6></textarea><br>';
		echo '<input type="submit" class="button" value="Tipsa">';
		echo '</form>';

		echo '<a href="forum.php?id='.$itemId.'#'.$itemId.'">'.$config['text']['link_return'].'</a>';
	}

	include('design_forum_foot.php');
	include('design_foot.php');
?>