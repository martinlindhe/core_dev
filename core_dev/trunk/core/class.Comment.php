<?php
/**
 * $Id$
 *
 * Class to create/delete/display one comment
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('class.User.php');
require_once('prop_Timestamp.php');

class Comment
{
	const NEWS       =  1;
	const BLOG       =  2; ///< anonymous or registered users comments on a blog
	const FILE       =  3; ///< anonymous or registered users comments on a image
	const TODOLIST   =  4; ///< todolist item comments
	const GENERIC    =  5; ///< generic comment type
	const PASTEBIN   =  6; ///< "pastebin" text. anonymous submissions are allowed
	const SCRIBBLE   =  7; ///< scribble board
	const CUSTOMER   =  8; ///< customer comments
	const FILEDESC   =  9; ///< this is a file description, only one per file can exist
	const ADMIN_IP   = 10; ///< a comment on a specific IP number, written by an admin (only shown to admins), ownerId=geoip number
	const WIKI       = 11; ///< a comment to a wiki article

	//Comment types only meant for the admin's eyes
	const MODERATION = 30; ///< owner = tblModeration.queueId
	const USER       = 31; ///< owner = tblUsers.userId, admin comments for a user

	private $type, $id, $userId, $ownerId;

	private $tbl_name = 'tblComments';
	private $error;

	private $add_interval = 30; ///< max time in seconds to look for duplicate comment adds

	function __construct($type = 0, $id = 0)
	{
		global $h, $db;

		if (!$db) {
			echo "Comment() ERROR: no db available\n";
			return false;
		}

		if ($type) $this->setType($type);
		if ($id) $this->setId($id);

		if ($h) $this->userId = $h->session->id;
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

	function getError() { return $this->error; }
	function setError($e) { $this->error = $e; }

	function add($text, $private = false)
	{
		global $db;
		if (!is_bool($private)) return false;

		$ip_num = IPv4_to_GeoIP(client_ip());

		$q =
		'SELECT COUNT(*) FROM '.$this->tbl_name.
		' WHERE commentType='.$this->type.
		' AND ownerId='.$this->ownerId.
		($this->userId ? ' AND userId='.$this->userId : '').
		' AND userIP='.$ip_num.
		' AND commentText="'.$db->escape($text).'"'.
		' AND timeCreated >= DATE_SUB(NOW(), INTERVAL '.$this->add_interval.' SECOND)';

		if ($db->getOneItem($q)) {
			$this->setError('Your comment has already been stored.');
			return false;
		}

		$q =
		'INSERT INTO '.$this->tbl_name.' SET commentType='.$this->type.
		',ownerId='.$this->ownerId.',userId='.$this->userId.
		',userIP='.$ip_num.',timeCreated=NOW()'.
		',commentText="'.$db->escape($text).'"';
		if ($private) $q .= ',commentPrivate=1';

		$this->id = $db->insert($q);
		return $this->id;
	}

	//XXX snygga till utseendet
	function render($row = false)
	{
		global $db, $h;

		if (!$row) {
			$q = 'SELECT * FROM tblComments';
			$q .= ' WHERE commentId='.$this->id;
			if ($this->type)    $q .= ' AND commentType='.$this->type;
			if ($this->ownerId) $q .= ' AND ownerId='.$this->ownerId;
			$q .= ' AND deletedBy=0 LIMIT 1';
			$row = $db->getOneRow($q);
			if (!$row) return false;
		}

		$this->setId($row['commentId']);
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

		$user = new User($row['userId']);

		$res = '<div class="'.$style_head.'">';
		//echo makeThumbLink($row['ownerId']);

		$res .= $user->link();

		$txt = formatUserInputText($row['commentText']);

		$time = new Timestamp($row['timeCreated']);

		$res .= ', <font size="1">'.$time->getRelative().'</font>';
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
