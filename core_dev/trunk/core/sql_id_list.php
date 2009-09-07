<?php
/**
 * Modifies a table with multiple "child" id's connected to a owner id
 */

class sql_id_list
{
	private $tbl_name, $owner_name, $child_name, $category_name;
	private $owner;             ///< list entry owner
	private $category  = false; ///< list entry category
	private $child_obj = false; ///< child id table is described by this sql_id_key object

	function __construct($obj = false)
	{
		if ($obj) $this->child_obj = $obj;
	}

	function setTableName($n) { $this->tbl_name = $n; }
	function setOwnerName($n) { $this->owner_name = $n; }
	function setChildName($n) { $this->child_name = $n; }
	function setCategoryName($n) { $this->category_name = $n; }

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

	function countItems() { return count($this->list); }

	function getChild() { return $this->child_obj; }

	/**
	 * Loads a list for the owner
	 */
	function getList()
	{
		global $db;

		$q = 'SELECT '.$this->child_name;
		if ($this->child_obj)
			$q .= ','.$this->child_obj->getKeyName();

		$q .= ' FROM '.$this->tbl_name;
		if ($this->child_obj) {
			$q .= ' LEFT JOIN '.$this->child_obj->getTableName().' ON';
			$q .= ' ('.$this->tbl_name.'.'.$this->child_name.'='.$this->child_obj->getTableName().'.'.$this->child_obj->getIdName().')';
		}
		if ($this->owner || $this->category !== false) $q .= ' WHERE ';
		if ($this->owner) $q .= $this->owner_name.'='.$this->owner;
		if ($this->owner && $this->category !== false) $q .= ' AND ';
		if ($this->category !== false) $q .= $this->category_name.'='.$this->category;

		return $db->getMappedArray($q);
	}

	/**
	 * Returns list with subscribed links & number of subscribers
	 * @return array with indexes 'id', 'name' and 'cnt'
	 */
	function getSummary()
	{
		global $db;
		if (!$this->child_obj) {
			die('summary without child not implemented');
		}

		$q = 'SELECT COUNT('.$this->owner_name.') AS cnt,'.
			$this->child_name.' AS id, '.
			$this->child_obj->getKeyName().' AS name'.
			' FROM '.$this->tbl_name.
			' LEFT JOIN '.$this->child_obj->getTableName().
			' ON ('.$this->child_name.'='.$this->child_obj->getTableName().'.'.$this->child_obj->getIdName().')';

		if ($this->category) $q .= 'WHERE '.$this->category_name.'='.$this->category;

		$q .= ' GROUP BY '.$this->child_obj->getIdName();

		return $db->getArray($q);
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
		if ($this->category !== false) $q .= ','.$this->category_name.'='.$this->category;
		return $db->insert($q);
	}

	/**
	 * Removes a entry from the list
	 */
	function remove($id)
	{
		global $db;
		if (!is_numeric($id)) return false;

		unset($this->list[$id]);

		$q = 'DELETE FROM '.$this->tbl_name.' WHERE '.$this->owner_name.'='.$this->owner.' AND '.$this->child_name.'='.$id;
		if ($this->category !== false) $q .= ' AND '.$this->category_name.'='.$this->category;
		return $db->delete($q);
	}

}
?>
