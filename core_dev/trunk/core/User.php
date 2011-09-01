<?php
/**
 * $Id$
 *
 * Class to deal with creating and modifying a user
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: wip. REWRITE using static User object

//XXX: move all group stuff to a UserHandler

require_once('SqlObject.php');

require_once('UserHandler.php');
require_once('UserSetting.php');

define('USERLEVEL_NORMAL',      0);
define('USERLEVEL_WEBMASTER',   1);
define('USERLEVEL_ADMIN',       2);
define('USERLEVEL_SUPERADMIN',  3);

define('USER_REGULAR',  1);
define('USER_FACEBOOK', 2);

function getUserLevels()
{
    return array(
    USERLEVEL_NORMAL     => 'Normal',
    USERLEVEL_WEBMASTER  => 'Webmaster',
    USERLEVEL_ADMIN      => 'Admin',
    USERLEVEL_SUPERADMIN => 'Super Admin',
    );
}

function getUserLevelName($n)
{
    $x = getUserLevels();
    return $x[ $n ];
}

function getUserTypes()
{
    return array(
    USER_REGULAR   => 'Regular',
    USER_FACEBOOK  => 'Facebook',
    );
}

class FacebookUser extends User
{
    function __construct($fbid)
    {
        $this->type = USER_FACEBOOK;
        if (!$this->loadByName($fbid)) // tblUsers.userName = facebook id
        {
            // create a new user entry for this facebook id
            $this->create($fbid, USER_FACEBOOK);
            $this->setPassword('');
        }
    }
}

class User
{
    var $id;
    var $type = USER_REGULAR;  ///< user type USER_REGULAR or USER_FACEBOOK
    var $name;                 ///< username
    var $password;
    var $time_created;
    var $time_last_login;
    var $time_last_active;
    var $time_last_logout;
    var $time_deleted;
    var $last_ip;              ///< the IP address used for the most recent login

    protected static $tbl_name = 'tblUsers';

    public static function get($id)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE time_deleted IS NULL AND id = ?';
        $row = Sql::pSelectRow($q, 'i', $id);

        return SqlObject::loadObject($row, __CLASS__);
    }

    public static function getByName($name)
    {
        $q = 'SELECT * FROM tblUsers WHERE time_deleted IS NULL AND name = ?';
        $row = Sql::pSelectRow($q, 's', $name);

        if (!$row) return false;
        return SqlObject::loadObject($row, __CLASS__);
    }






    /**
     * Creates a new user
     */
    function create($username, $type = USER_REGULAR)
    {
        $username = trim($username);

        if (User::getByName($username))
            return false;

        $this->name = $username;
        $this->type = $type;

        $q = 'INSERT INTO tblUsers SET time_created = NOW(), name = ?, type = ?';
        $this->id = Sql::pInsert($q, 'sis', $this->name, $this->type);

        $session = SessionHandler::getInstance();

        dp($session->getUsername().' created user '.$this->name.' ('.$this->id.') of type '.$this->type);

        return $this->id;
    }

    /**
     * Marks specified user as "deleted"
     */
    function remove()
    {
        $q = 'UPDATE tblUsers SET time_deleted = NOW() WHERE id = ?';
        Sql::pUpdate($q, 'i', $this->id);
    }

}

?>
