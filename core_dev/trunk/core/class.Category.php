<?php
/**
 * $Id$
 */

//STATUS: wip

//TODO: some general methods for ui category management (edit, remove) - not here!

require_once('class.CoreItem.php');
require_once('class.CoreList.php');
require_once('prop_Timestamp.php');

class CategoryItem extends CoreItem
{
	private $creator;      ///< if set, stores creatorId when categories are created
	var $TimeCreated;  ///< Timestamp object

	private $permissions = PERM_USER;  ///< permission flags as defined in constants.php

	function __construct($type)
	{
		if (!is_numeric($type))
			return false;

		$this->type = $type;
	}

	function setId($id)
	{
		if (!is_numeric($id)) return false;
		$this->id = $id;

		global $db;
		$q = 'SELECT * FROM tblCategories WHERE categoryType='.$this->type.' AND categoryId='.$this->id;
		$row = $db->getOneRow($q);

		$this->setTitle($row['categoryName']);
		$this->setOwner($row['ownerId']);
		$this->setPermissions($row['permissions']);
		$this->setCreator($row['creatorId']);
		$this->TimeCreated = new Timestamp($row['timeCreated']);
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

	/**
	 * Saves the item to database
	 *
	 * @return item id
	 */
	function store()
	{
		global $db;

		if ($this->id) {
			die('XXX UPDATE CATEGORY '.$this->id);
			return $this->id;
		}

		$q = 'INSERT INTO tblCategories SET '.
		'timeCreated=NOW(),'.
		'categoryType='.$this->type.','.
		'categoryName="'.$db->escape($this->title).'",'.
		'permissions='.$this->permissions;
		if ($this->owner) $q .= ',ownerId='.$this->owner;
		if ($this->creator) $q .= ',creatorId='.$this->creator;

		$this->id = $db->insert($q);
		return $this->id;
	}
}


class CategoryList extends CoreList
{
	private $type;         ///< category type
	private $owner;        ///< owner id, the meaning depends on category type
	private $creator;

	function __construct($type)
	{
		global $h;
		if (!is_numeric($type)) return false;

		$this->type    = $type;
		$this->creator = $h->session->id;
	}

	function setOwner($id)
	{
		if (!is_numeric($id)) return false;
		$this->owner = $id;
		$this->init(); /// XXX remove hack
	}

	/**
	 * Returns a list of id->name pairs for the list
	 */
	private function init()
	{
		global $db;

		$q  = 'SELECT * FROM tblCategories WHERE categoryType='.$this->type.' ';
		if ($this->owner) $q .= 'AND ownerId='.$this->owner;

		$list = $db->getArray($q);
		foreach ($list as $row) {
			$cat = new CategoryItem($this->type);
			$cat->setId($row['categoryId']);
			$cat->setTitle($row['categoryName']);
			$cat->setOwner($row['ownerId']);
			$cat->setPermissions($row['permissions']);
			$cat->setCreator($row['creatorId']);
			$cat->TimeCreated = new Timestamp($row['timeCreated']);

			$this->addItem($cat);
		}
	}

	function renderList()
	{
		$res = '';
		$this->init();

		foreach ($this->getKeyVals() as $id => $name)
			$res .= ', <a href="?cat='.$id.'">'.$name.'</a>';

		return '<a href="?cat=0">'.t('Overview').'</a>, '.substr($res, 2);
	}

}

?>
