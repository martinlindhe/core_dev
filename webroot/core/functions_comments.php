<?
	//functions_comments.php - kommentarer till whatever

	define('COMMENT_NEWS',					1);

	define('COMMENT_ADBLOCKRULE',		20);

	function addComment($commentType, $ownerId, $commentText, $privateComment = false)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($commentType) || !is_numeric($ownerId) || !is_bool($privateComment)) return false;
		$commentText = $db->escape(htmlspecialchars($commentText));

		if ($privateComment) $private = 1;
		else $private = 0;

		$q = 'INSERT INTO tblComments SET ownerId='.$ownerId.', userId='.$session->id.', userIP='.IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']).', commentType='.$commentType.', commentText="'.$commentText.'", commentPrivate='.$private.', timeCreated=NOW()';
		$db->query($q);
	}
	
	function updateComment($commentType, $ownerId, $commentId, $commentText)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($commentType) || !is_numeric($ownerId) || !is_numeric($commentId)) return false;
		$commentText = $db->escape(htmlspecialchars($commentText));

		$q  = 'UPDATE tblComments SET commentText="'.$commentText.'",timeCreated=NOW(),userIP='.IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']).' ';
		$q .= 'WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND userId='.$session->id;

		$db->query($q);
	}
	
	function deleteComment($commentId)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($commentId)) return false;

		$db->query('UPDATE tblComments SET deletedBy='.$session->id.',timeDeleted=NOW() WHERE commentId='.$commentId);
	}
	
	/* Deletes all comments for this commentType & ownerId */
	function deleteComments($commentType, $ownerId)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($commentType) || !is_numeric($ownerId)) return false;
		
		$q = 'DELETE FROM tblComments WHERE commentType='.$commentType.' AND ownerId='.$ownerId;
		$db->query($q);
	}

	function getComments($commentType, $ownerId, $privateComments = false)
	{
		global $db;

		if (!is_numeric($commentType) || !is_numeric($ownerId) || !is_bool($privateComments)) return array();

		$q  = 'SELECT t1.*,t2.userName FROM tblComments AS t1 '.
					'LEFT OUTER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) '.
					'WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND deletedBy=0';

		if ($privateComments === false) $q .= ' AND commentPrivate=0';

		$q .=	' ORDER BY timeCreated DESC';
		return $db->getArray($q);
	}

	/* returns the last comment posted for $ownerId object. useful to retrieve COMMENT_FILE_DESC where max 1 comment is posted per object */
	function getLastComment($commentType, $ownerId, $privateComments = false)
	{
		global $db;

		if (!is_numeric($commentType) || !is_numeric($ownerId) || !is_bool($privateComments)) return false;

		$q  = 'SELECT * FROM tblComments '.
					'WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND deletedBy=0';

		if ($privateComments === false) $q .= ' AND commentPrivate=0';

		$q .=	' ORDER BY timeCreated DESC';
		$q .= ' LIMIT 0,1';

		return $db->getOneRow($q);
	}
	
	function getCommentsCount($commentType, $ownerId)
	{
		global $db;

		if (!is_numeric($commentType) || !is_numeric($ownerId)) return 0;

		$q =	'SELECT COUNT(commentId) FROM tblComments '.
					'WHERE ownerId='.$ownerId.' AND commentType='.$commentType.' AND deletedBy=0';
		return $db->getOneItem($q);
	}
?>