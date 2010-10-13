<?php
/**
 * $Id$
 *
 * Class to deal with creating and modifying a user
 */

//STATUS: wip

class UserHandler
{
    private $id;
    private $name;

    function __construct($id = 0)
    {
        if (is_numeric($id))
            $this->loadById($id);
    }

    function getId() { return $this->id; }
    function getName() { return $this->name; }

    function loadById($n)
    {
        if (!is_numeric($n))
            return false;

        $user = new User($n);
        $this->id   = $user->getId();
        $this->name = $user->getName();
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

        return $db->getOneItem($q);
    }

/*
    function setUserName($username)
    {
        global $db;

        $q = 'UPDATE tblUsers SET userName="'.$db->escape($username).'" WHERE userId='.$this->id;
        $db->update($q);
    }
*/
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

}

?>
