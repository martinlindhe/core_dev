<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: wip, used by polls

require_once('constants.php');

class CategoryItem
{
    var $id;
    var $type;                           ///< type as defined in constants.php
    var $owner;
    var $title;
    var $TimeCreated;                    ///< Timestamp object

    protected $creator;                  ///< if set, stores creatorId when categories are created
    protected $permissions = PERM_USER;  ///< permission flags as defined in constants.php

    function __construct($type = 0)
    {
        if (!is_numeric($type))
            throw new Exception ('non-numeric type');

        $this->type = $type;
    }

    function getId() { return $this->id; }
    function getTitle() { return $this->title; }

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
            $q =
            'UPDATE tblCategories SET categoryType = ?, categoryName = ?,'.
            ' permissions = ?, ownerId = ?, creatorId = ? WHERE categoryId = ?';
            return $db->pUpdate($q, 'isiiii', $this->type, $this->title, $this->permissions, $this->owner, $this->creator, $this->id);
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

?>
