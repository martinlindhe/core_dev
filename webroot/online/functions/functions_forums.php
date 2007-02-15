<?
	/*
		functions_forums.php - Funktioner för forum

		2002.12.19
			* Fixade getForumMostReadMessagesHere() så att den inte returnerar diskussioner

		2002.11.08
			* Upprensad (kollar parametrar)
	*/



	/* Forum customization options */
	define("FORUM_SHOW_BODY_IN_THREAD",	false); //visa även inlägget i trådad vy
	define("FORUM_USE_SUBJECT_IN_POSTS",1); //0=gör rubrik av en del av inlägget
	define("FORUM_ALLOW_VOTE",			false);
	define("FORUM_SHOW_READCOUNT",		false);	//visa antal läsningar av ett inlägg
	define("FORUM_VOTE_TOP",			10); //possible to score between 1 and 10
	define("FORUMS_CAN_CREATE_DISCUSSIONS_IN_ROOT", false);

	define("FORUMS_REPLY_PREFIX", "Re: ");
	
	define("ALLOWABLE_TAGS",			"<b><i><br><hr><center><font><h1><h2><h3><h4><h5><body><table><tr><td><pre>");
	define("ALLOW_HTML_IN_FORUMS",		false);

	define("FORUMS_ROOTNAME",		"Forum");
	define("FORUM_MAXSIZE_BODY",	4000); //maxlängd på inlägg och diskussioner
	define("MESSAGE_MAXSIZE_BODY",	1000); //maxlängd på instant messages
	define("USERINFOFIELD_MAXSIZE",	4000); //maxlängd på userinfo-textfält

	define("FORUM_BGCOLOR_DARK",	"#FFFFFF");
	define("FORUM_BGCOLOR_LIGHT",	"#FFFFFF");
	define("FORUM_BGCOLOR_CURRENT",	"#EBEBEB");
	define("FORUM_FRAMECOLOR",		"#000000");
	define("FORUM_SHOW_AUTHOR_THUMBNAIL",	false);
	define("TITLE_PATH_SEPARATOR",	" &raquo; "); // för tm
	define("PATH_PREFIX",			"/online");	//utan avslutande /
	define("FORUM_QUOUTE_BODY",		true);













	/* Forum-itemtypes */
	define("FORUM_FOLDER",				1);
	define("FORUM_MESSAGE",				2);


	/* Returns all items inside $itemId */
	function getForumItems($db, $itemId) {

		if (!is_numeric($itemId)) return false;

		$sql  = "SELECT tblForums.*,tblUsers.userName AS authorName ";
		$sql .= "FROM tblForums ";
		$sql .= "LEFT OUTER JOIN tblUsers ON (tblUsers.userId=tblForums.authorId) ";
		$sql .= "WHERE tblForums.parentId=".$itemId." ";
		$sql .= "ORDER BY tblForums.itemType ASC,tblForums.timestamp ASC,tblForums.itemSubject ASC";

		return dbArray($db, $sql);
	}

	/* Returns an array with arrays of items, 'level' is added to it */
	function getForumItemsRecursive($db, $itemId, $level=0) {

		if (!is_numeric($itemId) || !is_numeric($level)) return false;

		$level++;

		$list = getForumItems($db, $itemId);
		for ($i=0; $i<count($list); $i++) {
			$result[$i] = $list[$i];
			$result[$i]["level"] = $level;
			$result[$i]["sub"] = getForumItemsRecursive($db, $list[$i]["itemId"], $level);
		}
		if (isset($result)) {
			return $result;
		} else {
			return;
		}
	}

	/* Deletes itemId and everything below it. also deletes associated moderation queue entries */
	function deleteForumItemRecursive($db, $itemId, $loop = false) {

		if (!is_numeric($itemId)) return false;

		$sql = "SELECT itemId FROM tblForums WHERE parentId=".$itemId;
		$check = dbQuery($db, $sql);
		$cnt = dbNumRows($check);

		for ($i=0; $i<$cnt; $i++) {
			$row = dbFetchArray($check);

			$sql = "DELETE FROM tblForums WHERE itemId=".$row["itemId"];
			dbQuery($db, $sql);

			deleteForumItemRecursive($db, $row["itemId"], true);
		}

		if ($loop != true) {
			$sql = "DELETE FROM tblForums WHERE itemId=".$itemId;
			dbQuery($db, $sql);
		}
	}

	function getForumFolderDepth($db, $itemId) {

		if (!is_numeric($itemId)) return false;

		if ($itemId != 0) {

			$sql = "SELECT itemSubject,parentId FROM tblForums WHERE itemId=".$itemId." AND itemType=".FORUM_FOLDER;
			$check = dbQuery($db, $sql);
			$row = dbFetchArray($check);
			if ($row["itemSubject"]) {
				$result = " - ".$row["itemSubject"];
			} else {
				$result = "";
			}
			$result = getForumFolderDepth($db, $row["parentId"]).$result;
			return $result;

		} else {
			$result = FORUMS_ROOTNAME;
			return $result;
		}
	}



	/* Return the number of messages inside $itemId, recursive (default) */
	function getForumMessageCount($db, $itemId, $recursive = true, $mecnt = 0) {

		if (!is_numeric($itemId) || !is_numeric($mecnt)) return false;

		$sql = "SELECT itemId FROM tblForums WHERE parentId=".$itemId." AND itemType=".FORUM_MESSAGE;
		$check = dbQuery($db, $sql);
		$cnt = dbNumRows($check);

		for ($i=0; $i<$cnt; $i++) {
			$row = dbFetchArray($check);
			$mecnt++;
			if ($recursive === true) {
				$mecnt = getForumMessageCount($db, $row["itemId"], $recursive, $mecnt);
			}
		}
		return $mecnt;
	}

	/* Returns the number of folders inside $itemId, recursive */
	function getForumFolderCount($db, $itemId, $mecnt = 0) {

		if (!is_numeric($itemId) || !is_numeric($mecnt)) return false;

		$sql = "SELECT itemId FROM tblForums WHERE parentId=".$itemId." AND itemType=".FORUM_FOLDER;

		$check = dbQuery($db, $sql);
		$cnt = dbNumRows($check);

		for ($i=0; $i<$cnt; $i++) {
			$row = dbFetchArray($check);
			$mecnt++;
			$mecnt = getForumFolderCount($db, $row["itemId"], $mecnt);
		}
		return $mecnt;
	}

	/* Return the number of items (folders & messages & discussions) inside $itemId, recursive */
	function getForumItemCount($db, $itemId, $mecnt = 0) {

		if (!is_numeric($itemId) || !is_numeric($mecnt)) return false;

		$sql = "SELECT itemId FROM tblForums WHERE parentId=".$itemId;

		$check = dbQuery($db, $sql);
		$cnt = dbNumRows($check);

		for ($i=0; $i<$cnt; $i++) {
			$row = dbFetchArray($check);
			$mecnt++;
			$mecnt = getForumItemCount($db, $row["itemId"], $mecnt);
		}
		return $mecnt;
	}

	function forumItemIsFolder($db, $itemId) {

		if (!is_numeric($itemId)) return false;

		if ($itemId == 0) return true; //root folder

		$sql = "SELECT itemType FROM tblForums WHERE itemId=".$itemId;
		$check = dbQuery($db, $sql);
		$row = dbFetchArray($check);

		if ($row["itemType"] == FORUM_FOLDER) {
			return true;
		} else {
			return false;
		}
	}

	function forumItemIsMessage($db, $itemId) {
		/* Returns false if item is a message but parent is a folder (item is a discussion then) */

		if (!is_numeric($itemId)) return false;

		$sql = "SELECT itemType, parentId FROM tblForums WHERE itemId=".$itemId;
		$check = dbQuery($db, $sql);
		$row = dbFetchArray($check);

		if ($row["itemType"] == FORUM_MESSAGE) {
			if (forumItemIsFolder($db, $row["parentId"])) {
				return false;
			} else {
				return true;
			}
			return true;
		} else {
			return false;
		}
	}

	function forumItemIsDiscussion($db, $itemId) {
		/* If the parentId is a folder and itemId is a message, then it is a discussion! */

		if (!is_numeric($itemId)) return false;

		$sql = "SELECT itemType, parentId FROM tblForums WHERE itemId=".$itemId;
		$check = dbQuery($db, $sql);
		$row = dbFetchArray($check);

		if ($row["itemType"]==FORUM_MESSAGE) {

			if (forumItemIsFolder($db, $row["parentId"])) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function getForumItemParent($db, $itemId) {

		if (!is_numeric($itemId)) return false;

		$sql = "SELECT parentId FROM tblForums WHERE itemId=".$itemId;
		$check = dbQuery($db, $sql);
		if (dbNumRows($check)) {
			$row = dbFetchArray($check);
			return $row["parentId"];
		} else {
			return 0; //root
		}
	}

	function setForumItemParent($db, $itemId, $parentId) {

		if (!is_numeric($itemId) || !is_numeric($parentId)) return false;

		$sql = "UPDATE tblForums SET parentId=".$parentId." WHERE itemId=".$itemId;
		dbQuery($db, $sql);
	}


	/* Recursive, returns the nearest folderId above itemId (which is a message) */
	function getForumFolderParent($db, $itemId) {

		if (!is_numeric($itemId)) return false;
		$parentId = getForumItemParent($db, $itemId);
		if ($parentId == 0) return $parentId;

		$sql = "SELECT itemType FROM tblForums WHERE itemId=".$parentId;
		$check = dbQuery($db, $sql);
		$row = dbFetchArray($check);

		if ($row["itemType"] == FORUM_FOLDER) {
			return $parentId;
		} else {
			$parentId = getForumFolderParent($db, $parentId);
		}
		return $parentId;
	}

	/* Returns the root message id of $itemId */
	function getForumMessageRoot($db, $itemId) {

		if (!is_numeric($itemId)) return false;
		$parentId = getForumItemParent($db, $itemId);
		if ($parentId == 0) return $itemId;

		$sql = "SELECT itemType FROM tblForums WHERE itemId=".$parentId;
		$check = dbQuery($db, $sql);
		$row = dbFetchArray($check);

		if ($row["itemType"] == FORUM_FOLDER) {
			return $itemId;
		} else {
			$parentId = getForumMessageRoot($db, $parentId);
		}
		return $parentId;
	}


	function getForumItem($db, $itemId) {

		if (!is_numeric($itemId)) return false;

		$sql  = "SELECT tblForums.*,tblUsers.userName AS authorName ";
		$sql .= "FROM tblForums ";
		$sql .= "LEFT OUTER JOIN tblUsers ON (tblForums.authorId=tblUsers.userId) ";
		$sql .= "WHERE tblForums.itemId=".$itemId;

		$check = dbQuery($db, $sql);
		return dbFetchArray($check);
	}

	function addForumFolder($db, $ownerId, $parentId, $folderName, $folderDesc = "") {

		if (!is_numeric($ownerId) || !is_numeric($parentId)) return false;

		if (ALLOW_HTML_IN_FORUMS) {
			$folderDesc = strip_tags($folderDesc, ALLOWABLE_TAGS);
		} else {
			$folderDesc = strip_tags($folderDesc);
		}
		$folderName = addslashes(strip_tags($folderName));
		$folderDesc = addslashes($folderDesc);

		$sql = "INSERT INTO tblForums SET itemType=".FORUM_FOLDER.",authorId=".$ownerId.",parentId=".$parentId.",itemSubject='".$folderName."',itemBody='".$folderDesc."',timestamp=".time();
		$query = dbQuery($db, $sql);
		return dbInsertId($query);
	}

	function addForumMessage($db, $ownerId, $parentId, $subject, $body) {

		if (!is_numeric($ownerId) || !is_numeric($parentId)) return false;

		if (ALLOW_HTML_IN_FORUMS) {
			$body = strip_tags($body, ALLOWABLE_TAGS);
		} else {
			$body = strip_tags($body);
		}
		$subject = addslashes(strip_tags($subject));

		$body = substr($body, 0, FORUM_MAXSIZE_BODY);
		$body = addslashes($body);

		$sql = "INSERT INTO tblForums SET itemType=".FORUM_MESSAGE.",authorId=".$ownerId.",parentId=".$parentId.",itemSubject='".$subject."',itemBody='".$body."',timestamp=".time();
		$query = dbQuery($db, $sql);
		$itemId = dbInsertId($query);
		
		return $itemId;
	}


	/* Returns the $count last posts by $userId, or all if $count is skipped */
	function getUserLastForumPosts($db, $authorId, $count="") {

		if (!is_numeric($authorId)) return false;

		$sql = "SELECT * FROM tblForums WHERE authorId=".$authorId." AND itemType=".FORUM_MESSAGE." ORDER BY timestamp DESC";
		if (is_numeric($count)) {
			$sql .= " LIMIT 0,".$count;
		}

		return dbArray($db, $sql);
	}

	function getForumPostsCount($db, $authorId) {

		if (!is_numeric($authorId)) return false;

		$sql = "SELECT COUNT(itemId) FROM tblForums WHERE authorId=".$authorId." AND itemType=".FORUM_MESSAGE;
		$check = dbQuery($db, $sql);

		$row = dbFetchArray($check);
		return $row[0];
	}


	/* Returns the timestamp of the newest forum entry inside $itemId, recursive */
	function getForumNewestItem($db, $itemId, $currtop="") {

		if (!$currtop) $currtop=0;
		if (!is_numeric($itemId) || !is_numeric($currtop)) return false;

		$sql = "SELECT itemId, timestamp FROM tblForums WHERE parentId=".$itemId;
		$list = dbArray($db, $sql);
		for ($i=0; $i<count($list); $i++) {
			if ($list[$i]["timestamp"]>$currtop) {
				$currtop = $list[$i]["timestamp"];
			}
			$currtop = getForumNewestItem($db, $list[$i]["itemId"], $currtop);
		}

		return $currtop;
	}


	/* Returns a list of all folder paths, ie folder1 - folder_in_folder1 etc... + folderid, used for now in accessgroup admin */
	function getForumFolderStructure($db, $parentId, $arr="", $pre="") {

		if (!is_numeric($parentId)) return false;

		$sql = "SELECT itemSubject, itemId FROM tblForums WHERE itemType=".FORUM_FOLDER." AND parentId=".$parentId." ORDER BY itemSubject";
		$list = dbArray($db, $sql);

		/* Lägg först till allt på samma nivå */
		for ($i=0; $i<count($list); $i++) {
			if ($pre != "") {
				$arr[] = array("name" => $pre." - ".$list[$i]["itemSubject"], "itemId" => $list[$i]["itemId"]);
			} else {
				$arr[] = array("name" => $list[$i]["itemSubject"], "itemId" => $list[$i]["itemId"]);
			}
		}

		/* Sen rekursiva */
		for ($i=0; $i<count($list); $i++) {
			if ($pre != "") {
				$pre = $pre." - ".$list[$i]["itemSubject"];
			} else {
				$pre = $list[$i]["itemSubject"];
			}

			$arr = getForumFolderStructure($db, $list[$i]["itemId"], $arr, $pre);
			$pre="";
		}

		return $arr;
	}

	function updateForumReadCounter($db, $itemId) {

		if (!is_numeric($itemId)) return false;

		$sql = "UPDATE tblForums SET itemRead=itemRead+1 WHERE itemId=".$itemId;
		dbQuery($db, $sql);
	}

	function addForumVote($db, $itemId, $value) {

		if (!is_numeric($itemId) || !is_numeric($value)) return false;

		$sql  = "UPDATE tblForums ";
		$sql .= "SET itemVote=itemVote+".$value.",itemVoteCnt=itemVoteCnt+1 ";
		$sql .= "WHERE itemId=".$itemId;
		dbQuery($db, $sql);
	}


	function getMostActivePosters($db, $limit="") {

		$sql  = "SELECT COUNT(tblForums.authorId) AS cnt,tblForums.authorId AS userId,tblUsers.userName AS userName ";
		$sql .= "FROM tblForums ";
		$sql .= "INNER JOIN tblUsers ON (tblForums.authorId=tblUsers.userId) ";
		$sql .= "GROUP BY tblForums.authorId ";
		$sql .= "ORDER BY cnt DESC";

		if (is_numeric($limit)) {
			$sql .= " LIMIT 0,".$limit;
		}

		return dbArray($db, $sql);
	}

	/* item is a forum or folder or whatever! */
	function getForumItemDepthHTML($db, $itemId) {

		if (!is_numeric($itemId)) return false;

		if ($itemId != 0) {

			$sql = "SELECT itemSubject,parentId FROM tblForums WHERE itemId=".$itemId;
			$check = dbQuery($db, $sql);
			$row = dbFetchArray($check);
			if ($row["itemSubject"]) {
				$result = " - <a href=\"".PATH_PREFIX."/forum.php?id=".$itemId."\">".$row["itemSubject"]."</a>";
			} else {
				$result = "";
			}
			$result = getForumItemDepthHTML($db, $row["parentId"]).$result;
			return $result;

		} else {
			$result = "<a href=\"".PATH_PREFIX."/forum.php\">".FORUMS_ROOTNAME."</a>";
			return $result;
		}
	}

	/* Returns the $count last posts */
	function getLastForumPosts($db, $count) {

		if (!is_numeric($count)) return false;

		$sql  = "SELECT ";
		$sql .= "t1.itemId,t1.authorId,t1.itemSubject,t1.itemBody,t1.timestamp,t2.userName AS authorName ";
		$sql .= "FROM tblForums AS t1 ";
		$sql .= "INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ";
		$sql .= "WHERE itemType=".FORUM_MESSAGE." ";
		$sql .= "ORDER BY t1.timestamp DESC ";
		$sql .= "LIMIT 0,".$count;

		return dbArray($db, $sql);
	}

	/* Returns the $count most read posts (on whole forum) */
	function getForumMostReadMessages($db, $count) {

		if (!is_numeric($count)) return false;

		$sql  = "SELECT ";
		$sql .= "t1.itemId,t1.authorId,t1.itemSubject,t1.itemBody,t1.timestamp,t2.userName AS authorName ";
		$sql .= "FROM tblForums AS t1 ";
		$sql .= "LEFT OUTER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ";
		$sql .= "WHERE itemType=".FORUM_MESSAGE." ";
		$sql .= "ORDER BY itemRead DESC ";
		$sql .= "LIMIT 0,".$count;

		return dbArray($db, $sql);
	}


	/* Returns true/false */
	function forumItemExists($db, $itemId) {

		if (!is_numeric($itemId)) return false;

		$sql = "SELECT itemId FROM tblForums WHERE itemId=".$itemId;
		$check = dbQuery($db, $sql);
		if (dbNumRows($check)) {
			return true;
		} else {
			return false;
		}
	}


	/* Returns TRUE if $itemParent is parent to $itemChild */
	function forumIsItemParent($db, $itemParent, $itemChild) {

		if (!is_numeric($itemParent) || !is_numeric($itemChild)) return false;

		while (1) {

			$sql = "SELECT parentId FROM tblForums WHERE itemId=".$itemChild;
			$check = dbQuery($db, $sql);
			$row = dbFetchArray($check);

			if ($row["parentId"] == $itemParent) {
				return true;
			} else if ($row["parentId"] == 0) {
				return false;
			}
			$itemChild = $row["parentId"];
		}
	}

	/* Sparar ändringar i ett inlägg/folder/whatever */
	function forumUpdateItem($db, $itemId, $subject, $body) {
		
		if (!is_numeric($itemId)) return false;
		$subject = addslashes($subject);
		$body = addslashes($body);

		dbQuery($db, "UPDATE tblForums SET itemSubject='".$subject."',itemBody='".$body."' WHERE itemId=".$itemId);
	}



	/* Returns a list of all folder paths, ie folder1 - folder_in_folder1 etc... + folderid, used for now in accessgroup admin */
	function getForumStructure($db, $parentId=0, $arr="", $pre="") {

		$parentId = addslashes($parentId);
		if (!is_numeric($parentId)) return false;

		$sql = "SELECT itemSubject,itemId FROM tblForums WHERE parentId=".$parentId." ORDER BY itemSubject";
		$list = dbArray($db, $sql);

		/* Lägg först till allt på samma nivå */
		for ($i=0; $i<count($list); $i++) {
			if ($pre != "") {
				$arr[] = array("name" => $pre." - ".$list[$i]["itemSubject"], "itemId" => $list[$i]["itemId"]);
			} else {
				$arr[] = array("name" => $list[$i]["itemSubject"], "itemId" => $list[$i]["itemId"]);
			}
		}

		/* Sen rekursiva */
		for ($i=0; $i<count($list); $i++) {
			if ($pre != "") {
				$pre = $pre." - ".$list[$i]["itemSubject"];
			} else {
				$pre = $list[$i]["itemSubject"];
			}

			$arr = getForumStructure($db, $list[$i]["itemId"], $arr, $pre);
			$pre="";
		}

		return $arr;
	}

























	define("TEXT_FORUM_TOP",		"<img src=\"gfx/forum_top.gif\" width=14 height=14>");
	define("TEXT_FORUM_DOWN",		"<img src=\"gfx/forum_down.gif\" width=14 height=\"100%\">");

	define("TEXT_FORUM_T",			"<img src=\"gfx/forum_t.gif\" width=14 height=14>");
	define("TEXT_FORUM_L",			"<img src=\"gfx/forum_l.gif\" width=14 height=14>");
	define("TEXT_FORUM_END1",		"<img src=\"gfx/forum_end1.gif\" width=14 height=14>");
	define("TEXT_FORUM_END2",		"<img src=\"gfx/forum_end2.gif\" width=14 height=14>");
	define("TEXT_FORUM_SPACE",		"<img src=\"gfx/forum_space.gif\" width=14 height=\"100%\">");


	/* Recursive function that rebuilds the message tree */
	function printForumTree($db, $list, $lastactive, $itemId, $root, $thread="", $bgcol="") {

		if (!is_numeric($itemId)) return false;

		if ($root == true) {
			$messageRootId = getForumMessageRoot($db, $itemId);

			echo "<table width=\"100%\" height=\"100%\" cellpadding=0 cellspacing=0 border=0 bgcolor=\"".FORUM_BGCOLOR_DARK."\"><tr>";
				echo "<td width=16>";
					echo TEXT_FORUM_TOP."<br>";
					echo TEXT_FORUM_DOWN;
				echo "</td>";

				echo "<td>";
					if ($messageRootId == $itemId) {
						echo showForumPost($db, $messageRootId, FORUM_BGCOLOR_CURRENT, true, 0);
					} else {

						$rootData = getForumItem($db, $messageRootId);
						echo "<b><a href=\"".$_SERVER["PHP_SELF"]."?id=".$messageRootId."#".$messageRootId."\">";
						echo $rootData["itemSubject"]."</a></b>";
						echo " av <a href=\"/user.php?id=".$rootData["authorId"]."\">".$rootData["authorName"]."</a>";
					}

				echo "</td>";

			echo "</tr></table>";

			$thread[1]="";
		}

		for ($i=0; $i<count($list); $i++) {

			if ($root == true) {
				if (isset($pos) && ($pos % 2)) {
					$bgcol = FORUM_BGCOLOR_DARK;
				} else {
					$bgcol = FORUM_BGCOLOR_LIGHT;
					$pos=0;
				}
				$pos++;
			}

			$level = $list[$i]["level"];
			if ($list[$i]["itemId"] == $itemId) {
				echo "<table width=\"100%\" cellpadding=0 cellspacing=0 border=0 bgcolor=".FORUM_BGCOLOR_DARK.">";
			} else {
				echo "<table width=\"100%\" cellpadding=0 cellspacing=0 border=0 bgcolor=".$bgcol.">";
			}

			echo "<tr>";
			echo "<td width=\"".(($level*14)+14)."\" height=\"100%\" valign=\"top\">";

				echo "<table width=\"100%\" height=\"100%\" cellpadding=0 cellspacing=0 border=0><tr>";
				//Rensa upp tråden neråt
				$xcnt = count($thread);
				for ($j=$level; $j<$xcnt+10; $j++) {
					$thread[$j]="";
				}

				//Visa en tråd för detta inlägg neråt
				if ($level>1) {
					echo "<td height=\"100%\">";
					echo "<table width=\"100%\" height=\"100%\" cellpadding=0 cellspacing=0 border=0><tr>";
					for ($j=1; $j<$level; $j++) {
						echo "<td>";
						if (isset($thread[$j]) && $thread[$j] == "t") {
							echo TEXT_FORUM_DOWN;
						} else {
							echo TEXT_FORUM_SPACE;
						}
						echo "</td>";
					}
					echo "</tr></table>";
					echo "</td>";
				}

				echo "<td valign=\"top\" height=\"100%\">";
				if ( isset($list[$i+1]) ) {
					echo TEXT_FORUM_T."<br>";
					echo TEXT_FORUM_DOWN;
					$thread[$level] = "t";
				} else {
					echo TEXT_FORUM_L;
				}
				echo "</td>";


				echo "<td valign=\"top\" height=\"100%\">";
				if ( !isset($list[$i]["sub"]) ) {
					//om inlägget har 0 svar, visas detta:
					echo TEXT_FORUM_END1;
				} else {
					echo TEXT_FORUM_END2."<br>";
					echo TEXT_FORUM_DOWN;
				}
				echo "</td>";

				echo "</tr></table>";

			echo "</td>";

			echo "<td width=\"*\" height=\"*\" valign=\"top\">";
				echo "<a name=\"".$list[$i]["itemId"]."\">";
				if ($list[$i]["itemId"] == $itemId || FORUM_SHOW_BODY_IN_THREAD==1) {
					if ($list[$i]["itemId"] == $itemId) {
						$usecol = FORUM_BGCOLOR_CURRENT;
					} else {
						$usecol = $bgcol;
					}
					echo showForumPost($db, $itemId, $usecol, true, $level);
				} else {

					echo "<b><a href=\"".$_SERVER["PHP_SELF"]."?id=".$list[$i]["itemId"]."#".$list[$i]["itemId"]."\">";
					$subj = substr($list[$i]["itemBody"],0,50);
					if (FORUM_USE_SUBJECT_IN_POSTS==1) {
						if ($list[$i]["itemSubject"]) {
							echo $list[$i]["itemSubject"];
						} else {
							echo $subj;
						}
					} else {
						$subj = substr($list[$i]["itemBody"],0,50);
						if ($subj) {
							echo $subj;
						} else {
							echo "&nbsp;";
						}
					}
					echo "</a></b> ";

					if ($list[$i]["authorId"]==0) {
						echo "av gäst";
					} else {
						echo "av <a href=\"".PATH_PREFIX."/user.php?id=".$list[$i]["authorId"]."\">".$list[$i]["authorName"]."</a>";
					}


					if (!isset($_SESSION["forum".$list[$i]["itemId"]])) $_SESSION["forum".$list[$i]["itemId"]]=false;

					/*
					if ($root == true) {
						echo " (".getForumMessageCount($db, $list[$i]["itemId"])." svar)";
					}
					*/

					echo "<td width=120 valign=\"top\" align=\"right\">".getDateStringDefault($list[$i]["timestamp"])."</td>";
				}
			echo "</td>";
			echo "</tr>";



			echo "</table>";
			if ($list[$i]["sub"]) {
				printForumTree($db, $list[$i]["sub"], $lastactive, $itemId, false, $thread, $bgcol);
			}
		}
		return;
	}

	function showForumPost($db, $itemId, $color=FORUM_BGCOLOR_CURRENT, $normal=true, $threadpos=0) {

		if (!is_numeric($itemId)) return false;

		if (!isset($_SESSION["forum".$itemId])) $_SESSION["forum".$itemId]=false;

		$item = getForumItem($db, $itemId);

		$result  = "<table width=\"100%\" border=0 cellspacing=0 cellpadding=1><tr><td>";
		$result .= "<table width=\"100%\" border=0 cellspacing=0 cellpadding=1 bgcolor=\"".FORUM_FRAMECOLOR."\"><tr><td>";
		$result .= "<table width=\"100%\" border=0 cellspacing=0 cellpadding=2>";
		if (FORUM_ALLOW_VOTE && $normal==true) {
			$result .= "<form method=\"post\" action=\"forum.php?id=".$itemId."\">";
		}
		$result .= "<tr bgcolor=".$color.">";
		$result .= "<td valign=\"top\">";
		$result .= "<table width=\"100%\" cellspacing=0 cellpadding=0 border=0><tr><td>";
		if (FORUM_USE_SUBJECT_IN_POSTS == 1) {

			if ($item["itemType"] == FORUM_FOLDER) {
				$result .= TEXT_FORUMFOLDER;
			} else if (forumItemIsFolder($db, $item["parentId"])) {
				$result .= "<img src=\"gfx/forum_post.png\">";
			}

			$result .= "<b><a href=\"".PATH_PREFIX."/forum.php?id=".$itemId."#".$itemId."\">".$item["itemSubject"]."</a></b> ";

			if ($item["authorId"] == 0) {
				$result .= "av gäst";
			} else {
				$result .= "av <a href=\"".PATH_PREFIX."/user.php?id=".$item["authorId"]."\">".$item["authorName"]."</a>";
			}

			$result .= "<br>";
		}
		$wrap = 64-($threadpos*2);

		$result .= wordwrap(nl2br(stripslashes($item["itemBody"])),$wrap," ",1);
		$result .= "</td></tr></table>";

		$result .= "</td>"; //slut på top left (inlägget)

		$result .= "<td width=120 valign=\"top\" align=\"right\">";
			$result .= getDateStringDefault($item["timestamp"])."<br>";
			
			if (FORUM_SHOW_READCOUNT) {
				$result .= $item["itemRead"]." ";
				if ($item["itemRead"] == 1)
					$result .= "läsning<br>";
				else
					$result .= " läsningar<br>";
			}

			if (FORUM_ALLOW_VOTE) {
				if ($item["itemVoteCnt"]>0) {
					$val = $item["itemVote"]/$item["itemVoteCnt"];
				} else {
					$val = 0;
				}
				$result .= round($val,2)."/".$item["itemVoteCnt"]."<br>";
				if ($normal==true) {
					$result .= "<input type=\"hidden\" name=\"voteId\" value=\"".$itemId."\">";
					$result .= "<select name=\"vote\">";
					for ($i=1; $i<=FORUM_VOTE_TOP; $i++) {
						$result .= "<option value=\"".$i."\">".$i;
					}
					$result .= "</select>";
					$result .= "<input type=\"submit\" value=\"Rösta\" class=\"buttonstyle\">";
				}
			}

		$result .= "</td>";
		$result .= "</tr>"; //slut på högerglumpen

		if (FORUM_SHOW_AUTHOR_THUMBNAIL === true) {
			$result .= "<td align=\"right\" valign=\"top\" width=".THUMBNAIL_MINI_WIDTH.">";
			$result .= getMiniThumbnail($db, $item["authorId"], USERFIELD_PICTURE);
			$result .= "</td>";
		}
		if (FORUM_ALLOW_VOTE && $normal==true) {
			$result .= "</form>";
		}


		if ($_SESSION["userId"] && $normal==true) {

			$result .= "<tr bgcolor=".$color.">";
			$result .= "<td colspan=2>";
			$result .= "<img src=\"gfx/black.gif\" width=\"100%\" height=1>";

			$result .= "<table width=\"100%\" cellpadding=0 cellspacing=0 border=0>";
			$result .= "<tr><td width=150 valign=\"top\">";
			
				$result .= "<a href=\"".PATH_PREFIX."/forum.php?id=".$itemId."#write\">Svara &raquo;</a><br>";

				$result .= "<a href=\"".PATH_PREFIX."/forum_anmal.php?id=".$itemId."\">Anmäl &raquo;</a><br>";

				$result .= "<a href=\"".PATH_PREFIX."/tipsa_om_inlagg.php?id=".$itemId."\">Tipsa kompis &raquo;</a><br>";

			$result .= "</td><td valign=\"top\">";

			/* Modererings-kommandon */
			if ($_SESSION["superUser"]) {
				$result .= "<a href=\"".PATH_PREFIX."/forum_redigera.php?id=".$itemId."\">Redigera &raquo;</a><br>";
				$result .= "<a href=\"".PATH_PREFIX."/forum.php?id=".getForumItemParent($db, $itemId)."&remove=".$itemId."\">Radera &raquo;</a><br>";
				$result .= "<a href=\"".PATH_PREFIX."/forum_flytta.php?id=".$itemId."\">Flytta &raquo;</a><br>";
			}

			$result .= "</td></tr>";
			$result .= "</table>";

			$result .= "</td></tr>";
		}

		$result .= "</table>";
		$result .= "</table>";
		$result .= "</table>";
		return $result;
	}


	//Item is a folder
	function getForumFolderDepthHTML($db, $separator, $itemId) {

		if (!is_numeric($itemId)) return false;

		if ($itemId != 0) {

			$sql = "SELECT itemSubject,parentId FROM tblForums WHERE itemId=".$itemId." AND itemType=".FORUM_FOLDER;
			$check = dbQuery($db, $sql);
			$row = dbFetchArray($check);
			if ($row["itemSubject"]) {
				$result = $separator."<a href=\"".PATH_PREFIX."/forum.php?id=".$itemId."\">".$row["itemSubject"]."</a>";
			} else {
				$result = "";
			}
			$result = getForumFolderDepthHTML($db, $separator, $row["parentId"]).$result;
			return $result;

		} else {
			$result = SITE_NAME . $separator."<a href=\"".PATH_PREFIX."/forum.php\">".FORUMS_ROOTNAME."</a>";
			return $result;
		}
	}
	
	/* same as getForumFolderDepthHTML but all text in uppercase */
	function getForumFolderDepthHTMLupper($db, $separator, $itemId) {

		if (!is_numeric($itemId)) return false;

		if ($itemId != 0) {

			$sql = "SELECT itemSubject,parentId FROM tblForums WHERE itemId=".$itemId." AND itemType=".FORUM_FOLDER;
			$check = dbQuery($db, $sql);
			$row = dbFetchArray($check);
			if ($row["itemSubject"]) {
				$result = " ".$separator." <a href=\"".PATH_PREFIX."/forum.php?id=".$itemId."\">".strtoupper($row["itemSubject"])."</a>";
			} else {
				$result = "";
			}
			$result = getForumFolderDepthHTMLupper($db, $separator, $row["parentId"]).$result;
			return $result;

		} else {
			$result = SITE_NAME.": <a href=\"".PATH_PREFIX."/forum.php\">".strtoupper(FORUMS_ROOTNAME)."</a>";
			return $result;
		}
	}



















	//misc functions:
	
	function getDateStringDefault($timestamp) {
		//return date("Y-m-d \k\l H.i",$timestamp); //2002.11.02 kl 15.20
		return getRelativeTime($timestamp);
	}



	/* Returnerar en sträng formaterad relativt utifrån aktuell tid */
	function getRelativeTime($timestamp) {
		global $month, $day, $weekday;
		
		if (!is_numeric($timestamp) || !$timestamp) return "Aldrig";

		$yesterday    = mktime (0,0,0,date("m") ,date("d")-1,  date("Y"));
		$usryesterday = mktime (0,0,0,date("m",$timestamp), date("d",$timestamp), date("Y",$timestamp));
		$lastweek     = mktime (0,0,0,date("m"), date("d")-7, date("Y"));
	
		
		if ( date("Y-m-d",$timestamp) == date("Y-m-d")) {
			$result = $day["today"].date(" H:i",$timestamp);

		} else if ($usryesterday == $yesterday) {
			$result = $day["yesterday"].date(" H:i",$timestamp);

		} else if ($timestamp >= $lastweek) {

			$wd = $weekday["last"][date("w",$timestamp)];
			$result = "I ".$wd.date(" H:i",$timestamp);

		} else {

			$wd = $weekday["short"][date("w",$timestamp)];
			$mn = $month["short"][date("n",$timestamp)];
			$dy = $day["pron"][date("j",$timestamp)];
			$result = $wd." ".$dy." ".$mn;
		}

		return $result;
	}

	


?>