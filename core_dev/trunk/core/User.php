<?php
/**
 * $Id$
 *
 * Class to deal with creating and modifying a user
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

    function create($username, $usermode)
    {
        if (!is_numeric($usermode)) return false;

        $db = SqlHandler::getInstance();

        $q = 'INSERT INTO tblUsers SET userName="'.$db->escape($username).'",userMode='.$usermode.',timeCreated=NOW()';
        $this->id   = $db->insert($q);
        $this->name = $username;

        dp('Created user '.$this->id.' with usermode '.$usermode);

        return $this->id;
    }

    /**
     * Marks specified user as "deleted"
     */
    function remove()
    {
        $db = SqlHandler::getInstance();

        $q = 'UPDATE tblUsers SET timeDeleted=NOW() WHERE userId='.$this->id;
        $db->update($q);
    }

    /** Adds the user to a user group */
    function addToGroup($n)
    {
        if (!is_numeric($n)) return false;

        $db = SqlHandler::getInstance();

        $q = 'SELECT COUNT(*) FROM tblGroupMembers WHERE groupId='.$n.' AND userId='.$this->id;
        if ($db->getOneItem($q))
            return true;

        $q = 'INSERT INTO tblGroupMembers SET groupId='.$n.',userId='.$this->id;
        $db->insert($q);
        return true;
    }

    /** Returns a list of UserGroup objects for all groups the user is a member of */
    function getGroups()
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT groupId FROM tblGroupMembers WHERE userId='.$this->id;

        $groups = array();
        foreach ($db->get1dArray($q) as $grp_id)
            $groups[] = new UserGroup($grp_id);

        return $groups;
    }

    /** Returns the highest access level from group membership */
    function getUserLevelByGroup()
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT t2.level FROM tblGroupMembers AS t1'.
        ' INNER JOIN tblUserGroups AS t2 ON (t1.groupId=t2.groupId)'.
        ' WHERE t1.userId='.$this->id.
        ' ORDER BY t2.level DESC LIMIT 1';

        $l = $db->getOneItem($q);
        return $l ? $l : 0;
    }

    /**
     * Sets a new password for the user
     *
     * @param $_id user id
     * @param $_pwd password to set
     */
    function setPassword($_pwd)
    {
        $db = SqlHandler::getInstance();
        $auth = AuthHandler::getInstance();

        $q = 'UPDATE tblUsers SET userPass="'.sha1( $this->id.sha1( $auth->getEncryptKey() ).sha1($_pwd) ).'" WHERE userId='.$this->id;
        $db->update($q);

        return true;
    }

    function render()
    {
        if (!$this->id)
            return t('Anonymous');

        return $this->name;
    }

}

?>
