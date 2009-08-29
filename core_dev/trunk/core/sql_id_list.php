<?php
/**
 * Modifies a table with multiple "child" id's connected to a owner id
 */

class sql_id_list
{
	private $tbl_name, $owner_name, $child_name;
	private $owner;
	private $list = array();

	function setTable($n) { $this->tbl_name = $n; }
	function setOwnerName($n) { $this->owner_name = $n; }
	function setChildName($n) { $this->child_name = $n; }

	function countItems() { return count($this->list); }
	function getList() { return $this->list; }

	/**
	 * Loads a list for the owner
	 *
	 * @param $owner Owner id
	 */
	function load($owner)
	{
		global $db;
		if (!is_numeric($owner)) return false;
		$this->owner = $owner;

		$q = 'SELECT '.$this->child_name.' FROM '.$this->tbl_name.' WHERE '.$this->owner_name.'='.$this->owner;
		$this->list = $db->get1dArray($q);
		return true;
	}

	/**
	 * Adds a entry to the list
	 */
	function add($child)
	{
		global $db;
		if (!is_numeric($child)) return false;

		$this->list[] = $child;

		$q = 'INSERT INTO '.$this->tbl_name.' SET '.$this->owner_name.'='.$this->owner.','.$this->child_name.'='.$child;
		return $db->insert($q);
	}

}
?>
