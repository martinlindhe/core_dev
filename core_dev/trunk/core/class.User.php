<?php
/**
 * $Id$
 *
 * User object
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

//STATUS: wip   -will replace class.Users.php

require_once('class.CoreBase.php');

class User extends CoreBase
{
    private $id, $name;
    private $exclude_deleted = true;

    function __construct($id = 0)
    {
        $this->loadById($id);
    }

    function setId($id)
    {
        $this->loadById($id);
    }

    function getId() { return $this->id; }
    function getName() { return $this->name; }

    function loadById($id)
    {
        global $db;
        if (!is_numeric($id)) return false;

        $q = 'SELECT userName FROM tblUsers WHERE userId='.$id;
        if ($this->exclude_deleted) $q .= ' AND timeDeleted IS NULL';
        $res = $db->getOneItem($q);
        if (!$res) return false;

        $this->id   = $id;
        $this->name = $res;

        return $this->name;
    }

    function loadByName($name)
    {
        global $db;

        $q = 'SELECT userId FROM tblUsers WHERE userName="'.$db->escape($name).'"';
        if ($this->exclude_deleted) $q .= ' AND timeDeleted IS NULL';
        $res = $db->getOneItem($q);
        if (!$res) return false;

        $this->id   = $res;
        $this->name = $name;

        return $this->id;
    }

    function render()
    {
        if (!$this->id)
            return t('Anonymous');

        return '<a '.($class?' class="'.$class.'"':'').'href="user.php?id='.$this->id.'">'.$this->name.'</a>';
    }

}

?>
