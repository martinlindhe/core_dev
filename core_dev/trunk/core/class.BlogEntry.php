<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

//STATUS: wip

require_once('prop_Timestamp.php');

class BlogEntry
{
	private $id;
	private $owner;
	private $category;

	public $timeCreated, $timeUpdated, $timeDeleted; ///< Timestamp objects
	private $isPrivate = false;
	private $subject;
	private $body;
	private $deletedBy;
	private $rating;
	private $countRatings, $countReads;

	function isDeleted() { return $this->deletedBy ? true : false; }
	function isUpdated() { return $this->timeUpdated->get() ? true : false; }

	function setId($id)
	{
		if (!is_numeric($id)) return false;
		$this->id = $id;
	}

	function setOwner($id)
	{
		if (!is_numeric($id)) return false;
		$this->owner = $id;
	}

	function setCategory($id)
	{
		if (!is_numeric($id)) return false;
		$this->category = $id;
	}

	function setSubject($s) { $this->subject = $s; }
	function setBody($s) { $this->body = $s; }

	function getId() { return $this->id; }
	function getOwner() { return $this->owner; }
	function getCategory() { return $this->category; }
	function getSubject() { return $this->subject; }
	function getBody() { return $this->body; }

	function get()
	{
		global $db;

		if (!$this->id) return false;

		$q =
		'SELECT * FROM tblBlogs'.
		' WHERE blogId='.$this->id;
		if ($this->owner) $q .= ' AND userId='.$this->owner;
		$row = $db->getOneRow($q);
		if (!$row) return false;

		$this->owner       = $row['userId']; //XXX rename to ownerId
		$this->category    = $row['categoryId'];
		$this->isPrivate   = $row['isPrivate'];
		$this->subject     = $row['subject'];
		$this->body        = $row['body'];
		$this->timeCreated = new Timestamp($row['timeCreated']);
		$this->timeUpdated = new Timestamp($row['timeUpdated']);
		$this->timeDeleted = new Timestamp($row['timeDeleted']);
		$this->deletedBy   = $row['deletedBy'];
		$this->rating      = $row['rating'];
		$this->countRatings= $row['ratingCnt'];
		$this->countReads  = $row['readCnt'];

		return $this;
	}

	function update()
	{
		global $db;
		$q = 'UPDATE tblBlogs SET subject="'.$db->escape($this->subject).'",body="'.$db->escape($this->body).'",categoryId='.$this->category.' WHERE blogId='.$this->id;
		$db->update($q);
	}

}

?>
