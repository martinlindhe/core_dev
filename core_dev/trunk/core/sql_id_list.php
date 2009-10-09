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

	/**
	 * @param $obj object of type sql_id_key
	 */
	function __construct($obj = false)
	{
		if ($obj) $this->child_obj = $obj;
	}

	//table & column names
	function setTableName($n) { $this->tbl_name = $n; }
	function setOwnerName($n) { $this->owner_name = $n; }
	function setChildName($n) { $this->child_name = $n; }
	function setCategoryName($n) { $this->category_name = $n; }

	function setChildId($id)
	{
		if (!$this->child_obj) return false;
		$this->child_obj->setId($id);
	}

	function setOwner($id)
	{
		if (!is_numeric($id)) return false;
		$this->owner = $id;
	}

	function setCategoryId($id)
	{
		if (!is_numeric($id)) return false;
		$this->category = $id;
	}

	function getCategoryId() { return $this->category; }

	function getCategoryName()
	{
		global $h;
		$this->cat = new Category(category::GENERIC);
		$this->cat->setCreator($h->session->id);
		$this->cat->setOwner($h->session->id);
		return $this->cat->getName($this->category);
	}

	function countItems() { return count($this->list); }

	function getChildKey()
	{
		if (!$this->child_obj) return false;
		return $this->child_obj->getKey();
	}

	function getChild() { return $this->child_obj; }

	/**
	 * Loads a list for the owner
	 *
	 * @return array with id=>key values
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
	 *
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
	function addEntry($id)
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
	function removeEntry($id)
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
