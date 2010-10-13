<?php
/**
 * $Id$
 *
 * User object
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

//STATUS: wip, is replacing class.Users.php

class User
{
    private $id;
    private $name;
    private $time_created;
    private $time_last_active;

    function __construct($s = 0)
    {
        if ($s && is_numeric($s))
            $this->loadById($s);
        else if (is_string($s))
            $this->loadByName($s);
    }

    function getId() { return $this->id; }
    function getName() { return $this->name; }
    function getTimeCreated() { return $this->time_created; }
    function getTimeLastActive() { return $this->time_last_active; }

    function loadFromSql($row)
    {
        $this->id               = $row['userId'];
        $this->name             = $row['userName'];
        $this->time_created     = $row['timeCreated'];
        $this->time_last_active = $row['timeLastActive'];
    }

    function loadById($id)
    {
        if (!is_numeric($id)) return false;

        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblUsers WHERE userId='.$id;
        $q .= ' AND timeDeleted IS NULL';

        $row = $db->getOneRow($q);
        if (!$row) return false;
        $this->loadFromSql($row);

        return $this->name;
    }

    function loadByName($name)
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblUsers WHERE userName="'.$db->escape($name).'"';
        $q .= ' AND timeDeleted IS NULL';

        $row = $db->getOneRow($q);
        if (!$row) return false;
        $this->loadFromSql($row);

        return $this->id;
    }

    function render()
    {
        if (!$this->id)
            return t('Anonymous');

        return $this->name;
    }

}

?>
