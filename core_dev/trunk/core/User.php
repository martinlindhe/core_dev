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

/** XXX WIP: these links should be auto decorated by YuiTooltip */
class UserLink
{
    public static function render($id, $name)
    {
        return '<span class="yui3-hastooltip" id="tt_usr_'.$id.'">'.$name.'</span>';
    }
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
        ' WHERE id = ?'.
        ' AND time_deleted IS NULL';
        $row = Sql::pSelectRow($q, 'i', $id);

        return SqlObject::loadObject($row, __CLASS__);
    }

    public static function getByName($name)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE name = ?'.
        ' AND time_deleted IS NULL';
        $row = Sql::pSelectRow($q, 's', $name);

        if (!$row) return false;
        return SqlObject::loadObject($row, __CLASS__);
    }

    /** See UserHandler::create() */
    static function store($obj)
    {
        return SqlObject::store($obj, self::$tbl_name, 'id');
    }

    /**
     * Used by SessionHandler::login() and others
     */
    static function getExact($type, $id, $name, $pwd)
    {
        $q =
        'SELECT * FROM tblUsers'.
        ' WHERE id = ? AND name = ? AND password = ? AND type = ? AND time_deleted IS NULL';

        $row = Sql::pSelectRow($q,
        'issi',
        $id,
        $name,
        UserHandler::encryptPassword($id, $pwd),
        $type
        );

        if (!$row) return false;
        return SqlObject::loadObject($row, __CLASS__);
    }

    /**
     * Marks specified user as "deleted"
     */
    function remove()
    {
        throw new Exception ('XXX update code');

        $q = 'UPDATE tblUsers SET time_deleted = NOW() WHERE id = ?';
        Sql::pUpdate($q, 'i', $this->id);
    }

}

?>
