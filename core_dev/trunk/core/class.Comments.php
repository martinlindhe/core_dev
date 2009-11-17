<?php
/**
 * $Id$
 *
 * CommentList is a list of CommentItem objects
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('class.CoreList.php');
require_once('class.User.php');
require_once('client_captcha.php');
require_once('prop_Timestamp.php');
require_once('xhtml_form.php');

//STATUS: mostly ok, needs more testing

//FIXME: implement and use oo-pager for CommentList

class CommentItem extends CoreBase
{
	var $type, $id, $owner;
	var $text;
	var $isPrivate = false; ///< boolean
	var $TimeCreated, $TimeDeleted; ///< Timestamp objects
	var $deletedBy; ///< userId
	var $creator, $creatorIP;

	private $add_interval = 30; ///< max time in seconds to look for duplicate comment adds

	function __construct($type = 0)
	{
		$this->setType($type);
	}

	function setType($type)
	{
		if (!is_numeric($type)) return false;
		$this->type = $type;
	}

	function setOwner($id)
	{
		if (!is_numeric($id)) return false;
		$this->owner = $id;
	}

	function setCreator($id)
	{
		if (!is_numeric($id)) return false;
		$this->creator = $id;
	}

	function setId($id)
	{
		if (!is_numeric($id)) return false;
		$this->id = $id;
	}

	function setText($txt) { $this->text = $txt; }
	function setPrivate($bool) { $this->isPrivate = $bool; }

	/**
	 * Creates/updates db entry for this object
	 * @return item Id
	 */
	function store()
	{
		global $db;

		if ($this->id) {
			//XXX update
			return $this->id;
		}

		$ip_num = IPv4_to_GeoIP(client_ip());

		$q =
		'SELECT COUNT(*) FROM tblComments'.
		' WHERE commentType='.$this->type.
		' AND ownerId='.$this->owner.
		($this->creator ? ' AND userId='.$this->creator : '').
		' AND userIP='.$ip_num.
		' AND commentText="'.$db->escape($this->text).'"'.
		' AND timeCreated >= DATE_SUB(NOW(), INTERVAL '.$this->add_interval.' SECOND)';

		if ($db->getOneItem($q)) {
			$this->setError('Your comment has already been stored.');
			return false;
		}

		$q =
		'INSERT INTO tblComments SET commentType='.$this->type.
		',ownerId='.$this->owner.',userId='.$this->creator.
		',userIP='.$ip_num.',timeCreated=NOW()'.
		',commentText="'.$db->escape($this->text).'"';
		if ($this->isPrivate) $q .= ',commentPrivate=1';

		$this->id = $db->insert($q);
		return $this->id;
	}

	function delete($deleted_by)
	{
		global $db;
		if (!is_numeric($deleted_by)) return false;

		$q = 'UPDATE tblComments SET deletedBy='.$deleted_by.',timeDeleted=NOW() WHERE commentId='.$this->id;
		$q .= ' AND commentType='.$this->type;
		if ($this->owner) $q .= ' AND ownerId='.$this->owner;
		return $db->update($q);
	}
}

class CommentList extends CoreList
{
	const NEWS       =  1;
	//XXX: only enable types when they are used. some should be depreacated
/*
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

	//XXX: use id >= 50 for project-specific types
*/

	private $owner;
	private $type;
	private $showDeleted = false;   ///< shall deleted comments be included?
	private $showPrivate = false;   ///< shall private comments be included?
	private $allowAnon   = false;   ///< do we allow anonymous comments?
	private $use_captcha = true;    ///< shall we use captchas for anonymous comments?
	private $limit       = 0;       ///< number of items per page
	private $private_comments = true;

	private $Captcha;               ///< Captcha object

	function __construct($type)
	{
		if (!is_numeric($type)) return false;
		$this->type = $type;

		$this->Captcha = new Captcha();

		$this->Captcha->setPrivKey('6LfqDQQAAAAAAKOMPfoJYcpqfZBlWQZf1BYiq7qt');
		$this->Captcha->setPubKey( '6LfqDQQAAAAAAMF-GaCBYHRJFetLd_BrjO8-2HBW');
	}

	function setOwner($id)
	{
		if (!is_numeric($id)) return false;
		$this->owner = $id;
	}

	function setAnonAccess($bool) { $this->allowAnon = $bool; }
	function disableCaptcha() { $this->use_captcha = false; }

	function disablePrivate() { $this->private_comments = false; }

	function showDeleted() { $this->showDeleted = true; }
	function showPrivate() { $this->showPrivate = true; }

	/**
	 * Initializes the object from database
	 */
	private function init()
	{
		global $db;

		//XXX this sql will break some times
		$q  = 'SELECT * FROM tblComments WHERE';
		if ($this->owner) $q .= ' ownerId='.$this->owner.' AND';
		if ($this->type) $q .= ' commentType='.$this->type.' AND';
		if (!$this->showPrivate) $q .= ' commentPrivate=0 AND';
		if (!$this->showDeleted) $q .= ' deletedBy=0';
		$q .= ' ORDER BY timeCreated DESC';

		$list = $db->getArray($q);
		foreach ($list as $row) {
			$comment = new CommentItem($this->type);
			$comment->id          = $row['commentId'];
			$comment->owner       = $row['ownerId'];
			$comment->text        = $row['commentText'];
			$comment->isPrivate   = $row['commentPrivate'];
			$comment->TimeCreated = new Timestamp($row['timeCreated']);
			$comment->TimeDeleted = new Timestamp($row['timeDeleted']);
			$comment->deletedBy   = $row['deletedBy'];
			$comment->creator     = $row['userId']; ///< XXX currently "tblComments.userId", should be renamed to creatorId
			$comment->creatorIP   = $row['userIP']; ///< XXX currently "tblComments.userIP", should be renamed to creatorIP

			$this->addItem($comment);
		}
	}

	/**
	 * Handles form POST
	 */
	function handleSubmit($p, $caller)
	{
		global $h;
		if (empty($p['comment_'.$this->type])) {
			$caller->setError('No text entered.');
			return false;
		}

		if ($h->session->id || //logged in
			(!$h->session->id && $this->allowAnon && !$this->use_captcha) || //anon + captcha disabled
			(!$h->session->id && $this->allowAnon && $this->captcha->verify()) //anon + captcha accepted
			) {

			$comment = new CommentItem($this->type);
			$comment->setOwner($this->owner);
			$comment->setCreator($h->session->id);
			$comment->setText($p['comment_'.$this->type]);

			if ($this->private_comments)
				$comment->setPrivate( $p['comment_priv_'.$this->type]);

			$id = $comment->store();

			if (!$id) $caller->setError( $comment->getError() );

			unset($p['comment_'.$this->type]);
			return $id;
		}

		if (!$h->session->id && $this->allowAnon && !$this->captcha->verify()) {
			$caller->setError('Incorrect captcha');
			return false;
		}

		$caller->setError('Unauthorized submit');
		return false;
	}

	function render()
	{
		global $h;

		if (!empty($_GET['cmt_delete']) && is_numeric($_GET['cmt_delete'])) {
			if ($h->session->isAdmin) {
				$item = new CommentItem($this->type);
				$item->setId($_GET['cmt_delete']);
				$item->delete($h->session->id);
				unset($_GET['cmt_delete']);
			}
		}

		if ($h->session->id || $this->allowAnon) {
			$form = new xhtml_form('addcomment');
			$form->addTextarea('comment_'.$this->type, 'Write a comment', '', 30, 6);

			if ($this->private_comments)
				$form->addCheckbox('comment_priv_'.$this->type, 'Private comment?');

			if ($this->use_captcha && !$h->session->id)
				$form->addCaptcha($this->Captcha);

			$form->addSubmit('Add comment');
			$form->setHandler('handleSubmit', $this);
		}

		$this->init(); //load items from db

		$res =
		'<div class="comment_header" onclick="toggle_element(\'comments_holder\')">'.
			count($this->items).' '.(count($this->items) == 1 ? t('comment') : t('comments')).
		'</div>';

		$res .= '<div id="comments_holder">';
		$res .= '<div id="comments_only">';

		foreach ($this->items as $item) {

			$user = new User($item->creator);

			$res .= '<div class="comment_details">';
			$res .= $user->link();
			$res .= ', <font size="1">'.$item->TimeCreated->render().'</font>';
			$res .= ($item->isPrivate ? 'Comment is private (only visible to owner and admins)' : '');
			$res .= '</div>';

			$res .= '<div class="comment_text">'.formatUserInputText($item->text);

			if ($h->session->id && (
				$h->session->isAdmin ||
				//allow users to delete their own comments
				$h->session->id == $item->creator
				)
			) {
				$res .= ' | ';
				$res .= coreButton('Delete', URLadd('cmt_delete', $item->id) );

			}
			$res .= '</div>';
		}
		$res .= '</div>'; //id="comments_only"

		if ($h->session->id || $this->allowAnon) {
			//add comment form
			$res .= $form->render();
		}

		$res .= '</div>'; //id="comments_holder"

		return $res;
	}

}


/*
		//XXX make use of this info
		$this->tbl_layout = array(
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
?>
