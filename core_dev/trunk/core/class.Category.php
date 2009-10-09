<?php
/**
 * $Id$
 */

require_once('constants.php');

class CategoryItem
{
	private $tbl_name;     ///< table name
	private $name;
	private $type;         ///< type as defined in constants.php
	private $owner;
	private $creator;      ///< if set, stores creatorId when categories are created

	private $permissions = PERM_USER;  ///< permission flags as defined in constants.php

	function setTableName($name) { $this->tbl_name = $name; }

	function setName($name) { $this->name = $name; }

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

	function setCreator($id)
	{
		if (!is_numeric($id)) return false;
		$this->creator = $id;
	}

	function setPermissions($flags)
	{
		if (!is_numeric($flags)) return false;
		$this->permissions = $flags;
	}

	function getName($id)
	{
		global $db;
		if (!is_numeric($id)) return false;

		$q = 'SELECT categoryName FROM '.$this->tbl_name.' WHERE categoryId='.$id.' AND categoryType='.$this->type.' ';
		if ($this->owner) $q .= 'AND ownerId='.$this->owner;
		return $db->getOneItem($q);
	}

	/**
	 * Saves the item to database
	 *
	 * @return item id
	 */
	function store()
	{
		global $db;

		$q = 'SELECT categoryId FROM '.$this->tbl_name.' WHERE categoryType='.$this->type.' AND categoryName="'.$db->escape($this->name).'"';
		if ($this->owner) $q .= ' AND ownerId='.$this->owner;
		if ($this->creator) $q .= ' AND creatorId='.$this->creator;
		$id = $db->getOneItem($q);
		if ($id) return $id;

		$q = 'INSERT INTO '.$this->tbl_name.' SET categoryType='.$this->type.',categoryName="'.$db->escape($this->name).'"';
		$q .= ',timeCreated=NOW(),permissions='.$this->permissions;
		if ($this->owner) $q .= ',ownerId='.$this->owner;
		if ($this->creator) $q .= ',creatorId='.$this->creator;
		return $db->insert($q);
	}
}


class CategoryList
{
	private $type;         ///< category type
	private $owner;        ///< owner id, the meaning depends on category type
	private $creator;
	private $tbl_name;     ///< table name

	function __construct($type)
	{
		global $h;
		if (!is_numeric($type)) return false;

		$this->type     = $type;
		$this->tbl_name = 'tblCategories';
		$this->creator  = $h->session->id;
	}

	function setOwner($id)
	{
		if (!is_numeric($id)) return false;
		$this->owner = $id;
	}

	/**
	 * Creates a new category, if it exists return id
	 *
	 * @return category id
	 * @return id of category $name
	 */
	function add($name)
	{
		global $h;

		if (!trim($name)) return false;

		$item = new CategoryItem();
		$item->setTableName($this->tbl_name);
		$item->setType($this->type);
		$item->setOwner($this->owner);
		$item->setCreator($this->creator);
		$item->setName($name);
		return $item->store();
	}

	/**
	 * Returns a list of id->name pairs for the list
	 */
	function getList()
	{
		global $db;

		$q  = 'SELECT categoryId,categoryName FROM '.$this->tbl_name.' WHERE categoryType='.$this->type.' ';
		if ($this->owner) $q .= 'AND ownerId='.$this->owner.' ';
		$q .= 'ORDER BY categoryName ASC';

		return $db->getMappedArray($q);
	}

	function renderList()
	{
		$res = '';

		foreach ($this->getList() as $cat_id => $cat_name)
			$res .= ', <a href="?cat='.$cat_id.'">'.$cat_name.'</a>';

		return substr($res, 2);
	}

}

?>
