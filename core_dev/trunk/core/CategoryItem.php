<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: wip, used in PollManager

require_once('constants.php');

class CategoryItem
{
    // category types
    const POLL_OPTIONS = 1;  ///< used by PollWidget where it is a list of poll options

    var $id;
    var $type;         ///< type as defined in constants.php
    var $owner;
    var $title;

    private $creator;     ///< if set, stores creatorId when categories are created
    public  $TimeCreated; ///< Timestamp object

    private $permissions = PERM_USER;  ///< permission flags as defined in constants.php

    function __construct($type)
    {
        if (!is_numeric($type))
            return false;

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

?>
