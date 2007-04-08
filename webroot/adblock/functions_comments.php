<?
	//functions_comments.php - kommentarer till whatever

	//commentType constants:
	define('COMMENT_INFOFIELD',					120);
	define('COMMENT_ADBLOCKRULE',				121);
	define('COMMENT_PHOTO',							122);
	define('COMMENT_FILE_DESC',					123);
	define('COMMENT_MODERATION_QUEUE',	124);

	define('COMMENT_IP_DETAILS',				150);	//används av AI trackern för att kommentera IP-nummer
	define('COMMENT_IP_RANGE',					151);	//används av AI trackern för att kommentera IP ranges


	function addComment(&$db, $commentType, $ownerId, $commentText, $privateComment=false)
	{
		if (!is_numeric($commentType) || !is_numeric($ownerId) || !is_bool($privateComment)) return false;
		$commentText = dbAddSlashes($db, htmlspecialchars($commentText));

		if ($privateComment) $private = 1;
		else $private = 0;

		$sql = 'INSERT INTO tblComments SET ownerId='.$ownerId.', userId='.$_SESSION['userId'].', userIP='.IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']).', commentType='.$commentType.', commentText="'.$commentText.'", commentPrivate='.$private.', timeCreated=NOW()';
		dbQuery($db, $sql);
	}
	
	function updateComment(&$db, $commentType, $ownerId, $commentId, $commentText)
	{
		if (!is_numeric($commentType) || !is_numeric($ownerId) || !is_numeric($commentId)) return false;
		$commentText = dbAddSlashes($db, htmlspecialchars($commentText));

		$sql  = 'UPDATE tblComments SET commentText="'.$commentText.'",timeCreated=NOW(),userIP='.IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']).' ';
		$sql .= 'WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND userId='.$_SESSION['userId'];

		dbQuery($db, $sql);
	}
	
	function deleteComment(&$db, $commentId)
	{
		if (!is_numeric($commentId)) return false;
		
		dbQuery($db, 'UPDATE tblComments SET deletedBy='.$_SESSION['userId'].',timeDeleted=NOW() WHERE commentId='.$commentId);
	}
	
	/* Deletes all comments for this commentType & ownerId */
	function deleteComments(&$db, $commentType, $ownerId)
	{
		if (!is_numeric($commentType) || !is_numeric($ownerId)) return false;
		
		$sql = 'DELETE FROM tblComments WHERE commentType='.$commentType.' AND ownerId='.$ownerId;
		dbQuery($db, $sql);
	}

	function getComments($commentType, $ownerId, $privateComments=false)
	{
		global $db;

		if (!is_numeric($commentType) || !is_numeric($ownerId) || !is_bool($privateComments)) return array();

		$sql  = 'SELECT t1.*,t2.userName FROM tblComments AS t1 '.
						'LEFT OUTER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) '.
						'WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND deletedBy=0';

		if ($privateComments === false)
			$sql .= ' AND commentPrivate=0';

		$sql .=	' ORDER BY timeCreated DESC';
		return $db->getArray($sql);
	}

	/* returns the last comment posted for $ownerId object. useful to retrieve COMMENT_FILE_DESC where max 1 comment is posted per object */
	function getLastComment(&$db, $commentType, $ownerId, $privateComments=false)
	{
		if (!is_numeric($commentType) || !is_numeric($ownerId) || !is_bool($privateComments)) return false;

		$sql  = 'SELECT * FROM tblComments '.
						'WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND deletedBy=0';

		if ($privateComments === false)
			$sql .= ' AND commentPrivate=0';

		$sql .=	' ORDER BY timeCreated DESC';
		$sql .= ' LIMIT 0,1';

		return dbOneResult($db, $sql);
	}
	

	function getCommentsCount($commentType, $ownerId)
	{
		global $db;

		if (!is_numeric($commentType) || !is_numeric($ownerId)) return 0;

		$sql =	'SELECT COUNT(commentId) FROM tblComments '.
						'WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND deletedBy=0';
		return $db->getOneItem($sql);
	}

?>