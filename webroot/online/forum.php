<?
	include_once("functions/include_all.php");

	if (isset($_GET["id"])) {
		$itemId = $_GET["id"];

		if (!forumItemExists($db, $itemId)) {
			$itemId = 0;
		}

	} else {
		$itemId = 0; //root folder
	}

	include("design_head.php");

	if (isset($_GET["remove"])) {
		/* Radera meddelande/folder och allt under den */
		$removeId = $_GET["remove"];

		if ($_SESSION["superUser"]) {
			deleteForumItemRecursive($db, $removeId);
		}
	}

	/* Starta/avsluta bevakning */
	if ($_SESSION["loggedIn"] == true) {
		if (isset($_GET["subscribe"])) {
			/* Starta bevakning här */
			addSubscription($db, $_SESSION["userId"], $_GET["subscribe"], SUBSCRIBE_MAIL);
		} else if (isset($_GET["unsubscribe"])) {
			/* Avsluta bevakning här */
			removeSubscription($db, $_SESSION["userId"], $_GET["unsubscribe"], SUBSCRIBE_MAIL);
		}
	}

	$writeMessage = "Nytt inlägg";
	$writeSubject = "";
	$writeBody = "";


	if (isset($_POST["itemType"])) {
		$itemType = $_POST["itemType"];

   		$msgBody    = $_POST["msgBody"];
   		if (isset($_POST["msgSubject"])) {
   			$msgSubject = $_POST["msgSubject"];
   		} else {
   			$msgSubject = "";
   		}

		if ($itemType == "folder" && $msgSubject) {
    		if ($_SESSION["superUser"]) {
    			/* skapa ny folder */
    			$itemId = addForumFolder($db, $_SESSION["userId"], $itemId, $msgSubject, $msgBody);
    			header("Location: forum.php?id=".$itemId); die;
    		}
    	} else if ($itemType == "discussion" && $msgSubject) {
    		if (forumItemIsFolder($db, $itemId) && ($_SESSION["loggedIn"] == true)) {
    			/* Skapa ny diskussion */
    			if (strlen($msgBody) <= FORUM_MAXSIZE_BODY) {
    				$itemId = addForumMessage($db, $_SESSION["userId"], $itemId, $msgSubject, $msgBody);
    				header("Location: forum.php?id=".$itemId); die;
    			} else {
    				$writeSubject = $msgSubject;
    				$writeBody = $msgBody;
    				$forum_error = "Diskussionstexten är för lång, den tillåtna maxlängden är ".FORUM_MAXSIZE_BODY." tecken, var god försök att korta ner texten lite.";
    			}
    		}
    	} else if ($itemType == "message") {
    		if ((forumItemIsMessage($db, $itemId) || forumItemIsDiscussion($db, $itemId)) && ($_SESSION["loggedIn"] == true)) {
    			/* skapa nytt inlägg */
    			if ($msgSubject && $msgBody && (strlen($msgBody) <= FORUM_MAXSIZE_BODY)) {
    				$itemId = addForumMessage($db, $_SESSION["userId"], $itemId, $msgSubject, $msgBody);
    				header("Location: forum.php?id=".$itemId); die;
    			} else if (strlen($msgBody) > FORUM_MAXSIZE_BODY) {
    				$writeSubject = $msgSubject;
    				$writeBody = $msgBody;
    				$forum_error = "Inlägget är för långt, den tillåtna maxlängden är ".FORUM_MAXSIZE_BODY." tecken, var god försök att korta ner texten lite.";
    			}
    		}
    	}

    	if (isset($_POST["subscribehere"]) && $_POST["subscribehere"]) {
    		/* Slå på bevakning för det nyskapade inlägget/diskussionen */
    		addSubscription($db, $_SESSION["userId"], $itemId, SUBSCRIBE_MAIL);
    	}

	}


	$item = getForumItem($db, $itemId);

	if (($itemId == 0) || forumItemIsFolder($db, $itemId)) {

		//echo makeTitle(getForumFolderDepthHTML($db, TITLE_PATH_SEPARATOR, $itemId))."<br>"; //todo: maketitle

		echo "<table width=\"100%\" cellpadding=0 cellspacing=0 border=0>";
		if ($itemId == 0) { //Rootlevel
			echo "<tr><td>";
				echo "forum-help here";
			echo "</td></tr>";
		} else {
			echo "<tr><td>".nl2br($item["itemBody"])."<br>";

			if ($_SESSION["superUser"]) {
				echo "<a href=\"".PATH_PREFIX."/forum_redigera.php?id=".$itemId."\">Edit &raquo;</a><br>";
				echo "<a href=\"".$_SERVER["PHP_SELF"]."?id=".getForumItemParent($db, $itemId)."&remove=".$itemId."\">Remove &raquo;</a><br>";
			}
			echo "</td></tr>";
		}
		echo "</table>";
		echo "<br>";

		/* Show content in current dir */
		$list = getForumItems($db, $itemId);

		if (count($list)) {
			
			$xx  = "<table width=\"100%\" cellpadding=0 cellspacing=0 border=0 class=\"link_white\"><tr>";
			if ($itemId == 0) {
				$xx .= "<td width=\"*\">Ämnen</td>";
			} else {
				$xx .= "<td width=\"*\">Diskussioner</td>";
			}
			$xx .= "<td width=150>Senaste inlägget</td>";
			$xx .= "<td width=40>Antal</td>";
			$xx .= "</tr></table>";
			//echo makeTitle($xx); //todo


			echo "<table width=\"100%\" cellpadding=0 cellspacing=0 border=0>";

			for ($i=0; $i<count($list); $i++) {
				if ($i % 2) {
					echo "<tr bgcolor=".FORUM_BGCOLOR_DARK.">";
				} else {
					echo "<tr bgcolor=".FORUM_BGCOLOR_LIGHT.">";
				}

				echo "<td width=\"*\" valign=\"top\">";
				$subject = $list[$i]["itemSubject"];
				if (strlen($subject)>35) $subject = substr($subject,0,35);
				switch ($list[$i]["itemType"]) {
					case FORUM_FOLDER:  echo "<img src=\"gfx/forum_folder.png\"><b><a href=\"".$_SERVER["PHP_SELF"]."?id=".$list[$i]["itemId"]."\">".$subject."</a></b>"; break;
					case FORUM_MESSAGE: echo "<img src=\"gfx/forum_post.png\"><b><a href=\"".$_SERVER["PHP_SELF"]."?id=".$list[$i]["itemId"]."\">".$subject."</a></b>"; break;
				}
				if (strlen($list[$i]["itemSubject"])>strlen($subject)) echo "..";
				
				if ($list[$i]["itemType"] == FORUM_MESSAGE) {
					 echo " by <a href=\"".PATH_PREFIX."/user.php?id=".$list[$i]["authorId"]."\">".$list[$i]["authorName"]."</a>";
				}

				echo "</td>";

				echo "<td width=150 valign=\"top\">";
				$last = getForumNewestItem($db, $list[$i]["itemId"]);
				if ($last) {
					echo getDateStringDefault($last);
				} else {
					echo "&nbsp;";
				}
				echo "</td>";
				echo "<td width=40 valign=\"top\">".getForumMessageCount($db, $list[$i]["itemId"],false)."</td>";
				echo "</tr>";
			}
			echo "</table>";
		}

	} else { //msg eller discussion

		$parentFolderId = getForumFolderParent($db, $itemId);
		$messageRootId = getForumMessageRoot($db, $itemId);

		//echo makeTitle(getForumFolderDepthHTML($db, TITLE_PATH_SEPARATOR, $parentFolderId))."<br>"; //todo

		if ($itemId != $messageRootId) {
			$writeMessage = "Svara på inlägg";

			if (substr($item["itemSubject"],0,4)!= FORUMS_REPLY_PREFIX) {
				$writeSubject = FORUMS_REPLY_PREFIX.$item["itemSubject"];
			} else {
				$writeSubject = $item["itemSubject"];
			}

			if ($item["itemBody"] && FORUM_QUOUTE_BODY) {
				$writeBody = "> ".$item["itemBody"];
				$writeBody = strip_tags($writeBody);
				$writeBody = str_replace("\n", "\n> ", $writeBody)."\n\n";
			} else {
				$writebody = "";
			}
		}
		$list = getForumItemsRecursive($db, $messageRootId);
		printForumTree($db, $list, 0, $itemId, true);
	}


	if (isset($forum_error) && $forum_error) {
		echo "<font color=\"red\">".$forum_error."</font><br>";
	}

	if (forumItemIsFolder($db, $itemId)) {
		if ($_SESSION["loggedIn"] == true) {
			if (($itemId == 0) && (FORUMS_CAN_CREATE_DISCUSSIONS_IN_ROOT === true)) {
				$discussion=true;
			} else if ($itemId != 0) {
				$discussion=true;
			}
		}
		if ($_SESSION["superUser"]) {
			$folder=true;
		}
		if ((isset($folder) && $folder) || (isset($discussion) && $discussion)) {
			echo "<br>";
			//echo makeTitle("Nytt ämne / diskussion"); //todo
			echo "<table cellpadding=2 cellspacing=0 border=0>";
			echo "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?id=".$itemId."\">";
			echo "<tr><td>";
			if (isset($discussion)) {
				echo "<input type=\"radio\" class=\"radiostyle\" name=\"itemType\" value=\"discussion\" checked>Ny diskussion ";
			}

			if (isset($folder)) {
				echo "<input type=\"radio\" class=\"radiostyle\" name=\"itemType\" value=\"folder\" checked>Nytt ämne";
			}
			echo "</td></tr>";
			echo "<tr><td>Subject: &nbsp;<input type=\"text\" size=51 maxlength=50 name=\"msgSubject\" value=\"".$writeSubject."\"></td></tr>";
			echo "<tr><td><textarea cols=59 rows=6 name=\"msgBody\" class=\"textareastyle\">".$writeBody."</textarea></td></tr>";
			echo "<tr><td>";

				echo "<table width=\"100%\" cellpadding=0 cellspacing=0 border=0><tr>";
				echo "<td valign=\"top\">";
					echo "<input type=\"submit\" value=\"Save\" class=\"buttonstyle\"> ";
				echo "</td>";
				echo "<td width=5><img src=\"\" width=5></td>";

				echo "</tr></table>";

			echo "</td></tr>";
			echo "</form>";
			echo "</table>";
		}

	} else {

		if ($_SESSION["loggedIn"] == true) {

			//nytt MESSAGE!
			echo "<a name=\"write\"></a>";
			echo "<br>";
			//echo makeTitle($writeMessage)."<br>"; //todo

			echo "<table cellpadding=2 cellspacing=0 border=0>";
			echo "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?id=".$itemId."\">";
			echo "<input type=\"hidden\" name=\"itemType\" value=\"message\">";
			if (FORUM_USE_SUBJECT_IN_POSTS==1) {
				echo "<tr><td>Subject: &nbsp;<input type=\"text\" size=\"51\" maxlength=\"100\" name=\"msgSubject\" value=\"".$writeSubject."\"></td></tr>";
			}
			echo "<tr><td>";
				echo "<textarea cols=59 rows=6 name=\"msgBody\" class=\"textareastyle\">".$writeBody."</textarea>";
			echo "</td></tr>";
			echo "<tr><td>";

				echo "<table width=\"100%\" cellpadding=0 cellspacing=0 border=0><tr>";
				echo "<td valign=\"top\">";
					echo "<input type=\"submit\" value=\"Save\" class=\"buttonstyle\"> ";
				echo "</td>";
				echo "<td width=5><img src=\"\" width=5></td>";

				echo "</tr></table>";


			echo "</td></tr>";
			echo "</form>";
			echo "</table>";
		}
	}
	
	include("design_foot.php");
?>