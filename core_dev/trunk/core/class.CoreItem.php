<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//STATUS: wip

require_once('constants.php');
require_once('class.CoreBase.php');

class CoreItem extends CoreBase
{
	var $id;
	var $type;         ///< type as defined in constants.php
	var $owner;
	var $title;

	function setId($id)
	{
		if (!is_numeric($id)) return false;
		$this->id = $id;
	}

	function setType($id)
	{
		if (!is_numeric($id)) return false;
		$this->type = $id;
	}

	function setOwner($id)
	{
		if (!is_numeric($id)) return false;
		$this->owner = $id;
	}

	function setTitle($s) { $this->title = $s; }

	function getId() { return $this->id; }
	function getTitle() { return $this->title; }
}


?>
