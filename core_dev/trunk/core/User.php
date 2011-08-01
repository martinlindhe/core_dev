<?php
/**
 * $Id$
 *
 * Class to deal with creating and modifying a user
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: wip

define('USERLEVEL_NORMAL',      0);
define('USERLEVEL_WEBMASTER',   1);
define('USERLEVEL_ADMIN',       2);
define('USERLEVEL_SUPERADMIN',  3);

require_once('UserSetting.php');

class User
{
    var $id;
    var $name;
    var $time_created;
    var $time_last_active;
    var $last_ip;            ///< the IP address used for the most recent login
    var $email;
    var $userlevel = 0;
    var $is_online = false;

    function __construct($s = 0)
    {
        if ($s && is_numeric($s))
            $this->loadById($s);
        else if (is_string($s))
            $this->loadByName($s);
    }

    static function getUserLevels()
    {
        return array(
        USERLEVEL_NORMAL     => 'Normal',
        USERLEVEL_WEBMASTER  => 'Webmaster',
        USERLEVEL_ADMIN      => 'Admin',
        USERLEVEL_SUPERADMIN => 'Super Admin',
        );
    }

    function getId() { return $this->id; }
    function getName() { return $this->name; }
    function getTimeCreated() { return $this->time_created; }
    function getTimeLastActive() { return $this->time_last_active; }
    function getLastIp() { return $this->last_ip; }
    function getEmail() { return $this->email; }

    function isOnline()
    {
        if (!$this->id)
            throw new Exception ('no id set');

        return $this->is_online;
    }

    function loadFromSql($row)
    {
        $this->id               = $row['userId'];
        $this->name             = $row['userName'];
        $this->time_created     = $row['timeCreated'];
        $this->time_last_active = $row['timeLastActive'];
        $this->last_ip          = $row['lastIp'];

        $this->email = $this->loadSetting('email');

        $this->is_online = false;

        $session = SessionHandler::getInstance();

        if (ts($this->time_last_active) > time() - $session->online_timeout)
            $this->is_online = true;

        $db = SqlHandler::getInstance();

        $q = 'SELECT t2.level FROM tblGroupMembers AS t1'.
        ' INNER JOIN tblUserGroups AS t2 ON (t1.groupId=t2.groupId)'.
        ' WHERE t1.userId = ?'.
        ' ORDER BY t2.level DESC LIMIT 1';

        $l = $db->pSelectItem($q, 'i', $this->id);
        $this->userlevel = $l ? $l : 0;
    }

    function loadSetting($name)
    {
        return UserSetting::get($this->id, $name);
    }

    function saveSetting($name, $val)
    {
        return UserSetting::set($this->id, $name, $val);
    }

    function deleteSetting($name)
    {
        return UserSetting::delete($this->id, $name);
    }

    function loadById($id)
    {
        if (!is_numeric($id)) return false;

        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblUsers WHERE timeDeleted IS NULL AND userId = ?';
        $row = $db->pSelectRow($q, 'i', $id);

        if (!$row) return false;
        $this->loadFromSql($row);

        return $this->name;
    }

    function loadByName($name)
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblUsers WHERE timeDeleted IS NULL AND userName = ?';
        $row = $db->pSelectRow($q, 's', $name);

        if (!$row) return false;
        $this->loadFromSql($row);

        return $this->id;
    }

    /**
     * Creates a new user
     */
    function create($username)
    {
        $db = SqlHandler::getInstance();
        $username = trim($username);

        $user = new User();
        if ($user->loadByName($username))
            return false;

        $q = 'INSERT INTO tblUsers SET timeCreated=NOW(),userName = ?';
        $this->id   = $db->pInsert($q, 's', $username);
        $this->name = $username;

        $session = SessionHandler::getInstance();

        dp($session->getUsername().' created user '.$this->name.' ('.$this->id.')');

        return $this->id;
    }

    /**
     * Marks specified user as "deleted"
     */
    function remove()
    {
        $db = SqlHandler::getInstance();

        $q = 'UPDATE tblUsers SET timeDeleted=NOW() WHERE userId = ?';
        $db->pUpdate($q, 'i', $this->id);
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

    function removeFromGroup($n)
    {
        if (!is_numeric($n)) return false;

        $db = SqlHandler::getInstance();

        $q = 'DELETE FROM tblGroupMembers WHERE groupId='.$n.' AND userId='.$this->id;
        $db->delete($q);
        return true;
    }

    /** Returns a list of UserGroup objects for all groups the user is a member of */
    function getGroups()
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT groupId FROM tblGroupMembers WHERE userId = ?';
        $res = $db->pSelect1d($q, 'i', $this->id);

        $groups = array();
        foreach ($res as $grp_id)
            $groups[] = new UserGroup($grp_id);

        return $groups;
    }

    /** Returns the highest access level from group membership */
    function getUserLevel() { return $this->userlevel; }

    function getUserLevelName()
    {
        $x = User::getUserLevels();

        return $x[ $this->userlevel ];
    }

    /**
     * Sets a new password for the user
     *
     * @param $_id user id
     * @param $_pwd password to set
     */
    function setPassword($pwd)
    {
        $db = SqlHandler::getInstance();
        $session = SessionHandler::getInstance();

        $db->pUpdate(
        'UPDATE tblUsers SET userPass = ? WHERE userId = ?',
        'si',
        sha1( $this->id.sha1( $session->getEncryptKey() ).sha1($pwd) ),
        $this->id
        );

        return true;
    }

    function getLoginHistory()
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblLogins WHERE userId = ? ORDER BY timeCreated DESC';
        return $db->pSelect($q, 'i', $this->id);
    }

    function render()
    {
        if (!$this->id)
            return t('Anonymous');

        return $this->name;
    }

}

?>
