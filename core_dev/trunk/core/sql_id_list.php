<?php
/**
 * Modifies a table with multiple "child" id's connected to a owner id
 */

class sql_id_list
{
	private $tbl_name, $owner_name, $child_name;
	private $owner;
	private $list = array();
	private $child_obj = false; ///< child id table is described by this sql_id_key object

	function __construct($obj = false)
	{
		if ($obj) $this->child_obj = $obj;
	}

	function setTableName($n) { $this->tbl_name = $n; }
	function setOwnerName($n) { $this->owner_name = $n; }
	function setChildName($n) { $this->child_name = $n; }

	function countItems() { return count($this->list); }
	function getList() { return $this->list; }

	/**
	 * Loads a list for the owner
	 *
	 * @param $owner Owner id
	 */
	function load($owner = 0)
	{
		global $db;
		if (!is_numeric($owner)) return false;
		$this->owner = $owner;

		$q = 'SELECT '.$this->child_name;
		if ($this->child_obj)
			$q .= ','.$this->child_obj->getKeyName();

		$q .= ' FROM '.$this->tbl_name;
		if ($this->child_obj) {
			$q .= ' LEFT JOIN '.$this->child_obj->getTableName().' ON';
			$q .= ' ('.$this->tbl_name.'.'.$this->child_name.'='.$this->child_obj->getTableName().'.'.$this->child_obj->getIdName().')';
		}
		if ($owner) $q .= ' WHERE '.$this->owner_name.'='.$this->owner;

		$this->list = $db->getMappedArray($q);
		return true;
	}

	/**
	 * Adds a entry to the list
	 */
	function add($id)
	{
		global $db;
		if (!is_numeric($id)) return false;

		$this->list[] = $id;

		$q = 'INSERT INTO '.$this->tbl_name.' SET '.$this->owner_name.'='.$this->owner.','.$this->child_name.'='.$id;
		return $db->insert($q);
	}

	/**
	 * Removes a entry from the list
	 */
	function remove($id)
	{
		global $db;
		if (!is_numeric($id)) return false;

		$key = array_search($id, $this->list);
		unset($this->list[$key]);

		$q = 'DELETE FROM '.$this->tbl_name.' WHERE '.$this->owner_name.'='.$this->owner.' AND '.$this->child_name.'='.$id;
		return $db->delete($q);
	}

}
?>
