<?
	include_once("community_includes.php");

	if ($_SESSION["loggedIn"] === false) {
		header("Location: ".SITE_STARTPAGE);
		die;
	}
	
	if (userAccess($db, $_SESSION["userId"], "forum_global_can_modify_all_folders")) {
		if (isset($_GET["id"])) {
			$itemId = $_GET["id"];
		
			if (!forumItemExists($db, $itemId)) {
				header("Location: ".SITE_STARTPAGE);
				die;
			}
		} else {
			header("Location: ".SITE_STARTPAGE);
			die;
		}

		if (isset($_POST["subject"]) && isset($_POST["body"])) {
			
			forumUpdateItem($db, $itemId, $_POST["subject"], $_POST["body"]);
			header("Location: forum.php?id=".$itemId);
			die;
		}

	} else {
		header("Location: ".SITE_STARTPAGE);
		die;
	}
	
	
	
	include(PATH_DESIGN."body_header.php");
	
	echo makeTitle(SITE_NAME . TITLE_PATH_SEPARATOR . "<a href=\"".PATH_PREFIX."/forum.php\">Forum</a>". TITLE_PATH_SEPARATOR ."Redigera" )."<br>";
	
	$item = getForumItem($db, $itemId);
	
	echo "<form name=\"change\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?id=".$itemId."\">";
	echo "Rubrik: <input name=\"subject\" size=60 value=\"".$item["itemSubject"]."\"><br>";
	echo "<textarea name=\"body\" cols=70 rows=20>".$item["itemBody"]."</textarea><br>";
	echo "<input type=\"submit\" value=\"Spara\" class=\"buttonstyle\">";
	echo "</form>";	

	include(PATH_DESIGN."body_footer.php");
?>