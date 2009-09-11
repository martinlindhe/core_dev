<?php
/**
 * $Id$
 *
 * Object is a list of comments, usually attached to another object
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('class.Comment.php');

class Comments
{
	private $tbl_layout;
	private $tbl_name = 'tblComments';
	private $sql_limit = ''; ///< used by the pager

	private $ownerId;
	private $type;
	private $showDeleted = false; ///< shall deleted comments be included?
	private $showPrivate = false; ///< shall private comments be included?
	private $limit       = 15;    ///< number of items per page

	function __construct($type)
	{
		if (!is_numeric($type)) return false;
		$this->type = $type;
	}

	function setOwner($id)
	{
		if (!is_numeric($id)) return false;
		$this->ownerId = $id;
	}

	function setLimit($l)
	{
		if (!is_numeric($l)) return false;
		$this->limit = $l;
	}

	function setShowDeleted() { $this->showDeleted = true; }
	function setShowPrivate() { $this->showPrivate = true; }

	function getList()
	{
		global $db;

		$q  = 'SELECT * FROM '.$this->tbl_name.' WHERE ';

		//XXX this sql will break some times
		if ($this->ownerId) $q .= 'ownerId='.$this->ownerId.' AND';
		if ($this->type) $q .= ' commentType='.$this->type.' AND';
		if (!$this->showPrivate) $q .= ' commentPrivate=0 AND';
		if (!$this->showDeleted) $q .= ' deletedBy=0';

		$q .= ' ORDER BY timeCreated DESC'.$this->sql_limit;
		return $db->getArray($q);
	}

	function getCount()
	{
		global $db;

		$q  = 'SELECT COUNT(*) FROM '.$this->tbl_name.' WHERE ';

		//XXX this sql will break some times
		if ($this->ownerId) $q .= 'ownerId='.$this->ownerId.' AND';
		if ($this->type) $q .= ' commentType='.$this->type.' AND';
		if (!$this->showPrivate) $q .= ' commentPrivate=0 AND';
		if (!$this->showDeleted) $q .= ' deletedBy=0';
		return $db->getOneItem($q);
	}

	function render()
	{
		global $h;

		$col_w = 30;
		$col_h = 6;

		$comment = new Comment();
		$comment->setType($this->type);
		$comment->setOwner($this->ownerId);

		if (!empty($_POST['cmt_'.$this->type])) {
//XXX same check as for show form!
			//addComment();

			$comment->add($_POST['cmt_'.$this->type]);
			die('add comment!');


			unset($_POST['cmt_'.$this->type]);
		}

		$cnt = $this->getCount();

		$res = '<div class="comment_header" onclick="toggle_element(\'comments_holder\')">'.$cnt.' '.($cnt == 1 ? t('comment'):t('comments')).'</div>';

		$res .= '<div id="comments_holder">';
		$res .= '<div id="comments_only">';
		$pager = makePager($cnt, $this->limit);

		$this->sql_limit = $pager['limit'];

		$res .= $pager['head'];
		foreach ($this->getList() as $row) {
			$res .= $comment->render($row);
		}
		if ($cnt >= 5) $res .= $pager['head'];
		$res .= '</div>'; //id="comments_only"

		if ($h->session->id) {  //XXX allow anonym post
			$res .= '<form method="post" action="">';
			$res .= xhtmlTextarea('cmt_'.$this->type, '', $col_w, $col_h).'<br/>';
			$res .= xhtmlSubmit('Add comment');
			$res .= '</form>';
		}

		$res .= '</div>';	//id="comments_holder"

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
