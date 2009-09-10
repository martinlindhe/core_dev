<?php
/**
 * $Id$
 *
 * Class to create/delete/display one comment
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//TODO: create class.Comments.php for a list of comment objects

class Comment
{
	const NEWS     = 1;
	const BLOG     = 2; ///< anonymous or registered users comments on a blog
	const FILE     = 3; ///< anonymous or registered users comments on a image
	const TODOLIST = 4;	///< todolist item comments
	const GENERIC  = 5; ///< generic comment type
	const PASTEBIN = 6; ///< "pastebin" text. anonymous submissions are allowed
	const SCRIBBLE = 7; ///< scribble board
	const CUSTOMER = 8; ///< customer comments
	const FILEDESC = 9; ///< this is a file description, only one per file can exist
	const ADMIN_IP = 10;///< a comment on a specific IP number, written by an admin (only shown to admins), ownerId=geoip number
	const WIKI     = 11;///< a comment to a wiki article

	/* Comment types only meant for the admin's eyes */
	const MODERATION = 30; ///< owner = tblModeration.queueId
	const USER       = 31; ///< owner = tblUsers.userId, admin comments for a user

	private $type, $id, $userId, $ownerId;

	var $table_layout;
	private $tbl_name = 'tblComments';

	function __construct($type, $id = 0)
	{
		global $h, $db;

		if (!$db) {
			echo "Comment() ERROR: no db available\n";
			return false;
		}

		$this->setType($type);
		if ($id) $this->setId($id);

		if ($h) $this->userId = $h->session->id;
/*
		//XXX make use of this info
		$this->table_name = 'tblComments';
		$this->table_layout = array(
		'commentId'      => array('bigint:20:unsigned','null:NO','key:PRI','extra:auto_increment'),
		'commentType'    => array('tinyint:3:unsigned','null:NO'),
		'commentText'    => array('text'),
		'commentPrivate' => array('tinyint:3:unsigned','null:NO'),
		'timeCreated'    => array('datetime','null:YES'),
		'timeDeleted'    => array('datetime','null:YES'),
		'deletedBy'      => array('bigint:20:unsigned','null:NO'),
		'ownerId'        => array('bigint:20:unsigned','null:NO'),
		'userId'         => array('bigint:20:unsigned','null:NO'),
		'userIP'         => array('bigint:20:unsigned','null:NO')
		);
*/
	}

	function setType($type)
	{
		if (!is_numeric($type)) return false;
		$this->type = $type;
	}

	function setOwner($id)
	{
		if (!is_numeric($id)) return false;
		$this->ownerId = $id;
	}

	function setId($id = 0)
	{
		if (!is_numeric($id)) return false;

		$this->id = $id;
	}

	function newComment($text, $private = false)
	{
		global $db;
		if (!is_bool($private)) return false;

		$q =
		'INSERT INTO '.$this->tbl_name.' SET ownerId='.$this->ownerId.',userId='.$this->userId.
		',userIP='.IPv4_to_GeoIP(client_ip()).',commentType='.$this->type.
		',commentText="'.$db->escape($text).'",timeCreated=NOW()';
		if ($private) $q .= ',commentPrivate=1';

		$this->id = $db->insert($q);
		return $this->id;
	}

	//XXX snygga till utseendet
	function render()
	{
		global $db, $h;
		if (!$this->id) return false;

		$q = 'SELECT * FROM tblComments';
		$q .= ' WHERE commentId='.$this->id;
		if ($this->type)    $q .= ' AND commentType='.$this->type;
		if ($this->ownerId) $q .= ' AND ownerId='.$this->ownerId;
		$q .= ' AND deletedBy=0 LIMIT 1';
		$row = $db->getOneRow($q);
		if (!$row) return false;

		$this->setType($row['commentType']);
		$this->setOwner($row['ownerId']);

		$style_head = 'comment_details';
		$style_body = 'comment_text';

		if (!empty($_GET['cmt_delete']) && is_numeric($_GET['cmt_delete']) && ($_GET['cmt_delete'] == $this->id) ) {
			//let users delete comments belonging to their files
			if ($h->session->isAdmin ||
				($this->type == Comment::FILE && $h->files->getOwner($this->ownerId) == $h->session->id)
			) {
				$q = 'UPDATE tblComments SET deletedBy='.$this->userId.',timeDeleted=NOW() WHERE commentId='.$this->id;
				if ($this->type)    $q .= ' AND commentType='.$this->type;
				if ($this->ownerId) $q .= ' AND ownerId='.$this->ownerId;
				$db->update($q);

				unset($_GET['cmt_delete']);
				return false;
			}
		}

		$res = '<div class="'.$style_head.'">';
		//echo makeThumbLink($row['ownerId']);

		$res .= $row['userId'] ? Users::link($row['userId']) : t('Anonymous');

		$txt = formatUserInputText($row['commentText']);

		$res .= ', <font size="1">'.formatTime($row['timeCreated']).'</font>';
		$res .= '</div>';
		$res .= '<div class="'.$style_body.'">'.$txt;
		if ($h->session->id && ($h->session->isAdmin ||
			//allow users to delete their own comments
			$h->session->id == $row['userId'] ||
			//allow users to delete comments on their files
			($this->type == Comment::FILE && $h->files->getOwner($this->ownerId) == $h->session->id)
			)
		) {
			$res .= ' | ';
			$res .= coreButton('Delete', URLadd('cmt_delete', $this->id) );

		}
		$res .= '</div>';
		return $res;
	}

}

?>
