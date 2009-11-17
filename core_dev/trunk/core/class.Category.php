<?php
/**
 * $Id$
 */

//STATUS: need work

//XXX rewrite list->init() function to initialize this->items with CategoryItem objects
//XXX2: extend CoreList

//TODO: some general methods for ui category management (edit, remove)

require_once('class.CoreList.php');
require_once('constants.php');

class CategoryItem extends CoreBase
{
	private $name;
	private $id;
	private $type;         ///< type as defined in constants.php
	private $owner;
	private $creator;      ///< if set, stores creatorId when categories are created

	private $permissions = PERM_USER;  ///< permission flags as defined in constants.php

	function __construct($type)
	{
		global $h;
		if (!is_numeric($type)) return false;

		$this->type     = $type;
	}

	function setName($name) { $this->name = $name; }
	function setId($id) { $this->id = $id; }

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

	function getId() { return $this->id; }

	function getName()
	{
		global $db;

		$q = 'SELECT categoryName FROM tblCategories WHERE categoryId='.$this->id.' AND categoryType='.$this->type.' ';
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

		$q = 'SELECT categoryId FROM tblCategories WHERE categoryType='.$this->type.' AND categoryName="'.$db->escape($this->name).'"';
		if ($this->owner) $q .= ' AND ownerId='.$this->owner;
		if ($this->creator) $q .= ' AND creatorId='.$this->creator;
		$this->id = $db->getOneItem($q);
		if ($this->id) return $this->id;

		$q = 'INSERT INTO tblCategories SET categoryType='.$this->type.',categoryName="'.$db->escape($this->name).'"';
		$q .= ',timeCreated=NOW(),permissions='.$this->permissions;
		if ($this->owner) $q .= ',ownerId='.$this->owner;
		if ($this->creator) $q .= ',creatorId='.$this->creator;
		$this->id = $db->insert($q);
		return $this->id;
	}
}


class CategoryList extends CoreBase
{
	private $type;         ///< category type
	private $owner;        ///< owner id, the meaning depends on category type
	private $creator;

	function __construct($type)
	{
		global $h;
		if (!is_numeric($type)) return false;

		$this->type     = $type;
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

		$item = new CategoryItem($this->type);
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
	private function init() //XXX rewrite function to initialize this->items with CategoryItem objects
	{
		global $db;

		$q  = 'SELECT categoryId,categoryName FROM tblCategories WHERE categoryType='.$this->type.' ';
		if ($this->owner) $q .= 'AND ownerId='.$this->owner.' ';
		$q .= 'ORDER BY categoryName ASC';

		return $db->getMappedArray($q);
	}

	function renderList()
	{
		$res = '';

		foreach ($this->init() as $cat_id => $cat_name)
			$res .= ', <a href="?cat='.$cat_id.'">'.$cat_name.'</a>';

		return '<a href="?cat=0">'.t('Overview').'</a>, '.substr($res, 2);
	}

}

?>
