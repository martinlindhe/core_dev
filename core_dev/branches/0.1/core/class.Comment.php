<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('atom_comments.php');

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

	/* Comment types only meant for the admin's eyes */
	const MODERATION = 30; ///< owner = tblModeration.queueId
	const USER       = 31; ///< owner = tblUsers.userId, admin comments for a user

	var $type   = false; ///< comment type
	var $id     = 0;     ///< commentId
	var $userId = 0;     ///< current userId

	var $table_layout;

	function __construct($type, $id = 0)
	{
		global $h, $db;

		if (!$db) {
			echo "Comment: no db available\n";
			return false;
		}

		$this->type = $type;
		if ($id) $this->load($id);

		if ($h) $this->userId = $h->session->id;

		$this->table_name = 'tblComments'; //XXX make use of this info
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
	}

	function create($parentId, $text, $private = false)
	{
		return addComment($this->type, $parentId, $text, $private);
	}

	function load($id = 0, $type = 0)
	{
		if ($id)   $this->id   = $id;
		if ($type) $this->type = $type;

		return getComment($this->id);
	}

	function children($parentId, $private = false)
	{
		return getComments($this->type, $parentId, $private = false);
	}

	function delete($id = 0, $type = 0)
	{
		if ($id)   $this->id   = $id;
		if ($type) $this->type = $type;

		return deleteComment($this->id);
	}
}

?>
