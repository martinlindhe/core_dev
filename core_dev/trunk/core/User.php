<?php
/**
 * $Id$
 *
 * Class to deal with creating and modifying a user
 *
 * @author Martin Lindhe, 2009-2012 <martin@startwars.org>
 */

//STATUS: wip

require_once('constants.php');
require_once('SqlObject.php');
require_once('UserHandler.php');
require_once('UserSetting.php');
require_once('Password.php');

/** XXX WIP: these links should be auto decorated by YuiTooltip */
class UserLink
{
    public static function render($id)
    {
        $u = User::get($id);
        if (!$u)
            return 'no such user';

        $res = '';

        switch ($u->type) {
        case SESSION_REGULAR:
            //$res .= '(reg)';
            break;
        case SESSION_FACEBOOK:
//            '<fb:name uid="'.$u->name.'" useyou="false"></fb:name>';
            //$pic = UserSetting::get($u->id, 'fb_picture');
            $name = UserSetting::get($u->id, 'fb_name');
            $res .= $name.' (facebook)';
            break;
        default: throw new Exception ('hm');
        }

        // $res .= '<span class="yui3-hastooltip" id="tt_usr_'.$u->id.'">'.$u->name.'</span>';
        $res .= ahref('u/profile/'.$u->id, $u->name);

        return $res;
    }
}

class FacebookUser extends User
{
    function __construct($fbid)
    {
        $this->type = SESSION_FACEBOOK;
        if (!$this->getByName($fbid)) // tblUsers.userName = facebook id
        {
            // create a new user entry for this facebook id
            $this->create($fbid, $this->type);
            $this->setPassword('');
        }
    }
}

class User
{
    var $id;
    var $type = SESSION_REGULAR; ///< user type, SESSION_REGULAR or SESSION_FACEBOOK
    var $name;                   ///< username
    var $password;
    var $time_created;
    var $time_last_login;
    var $time_last_active;
    var $time_last_logout;
    var $time_deleted;
    var $last_ip;                ///< the IP address used for the most recent login

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

    public static function getName($id)
    {
        $q =
        'SELECT name FROM '.self::$tbl_name.
        ' WHERE id = ?'.
        ' AND time_deleted IS NULL';
        return Sql::pSelectItem($q, 'i', $id);
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
        ' WHERE id = ? AND name = ? AND type = ? AND time_deleted IS NULL';

        $row = Sql::pSelectRow($q,
        'isi',
        $id,
        $name,
        $type
        );

        if (!$row)
            return false;

        $x = explode(':', $row['password']);
        if (count($x) == 2) {
            $algo = $x[0];
            $pwd2 = $x[1];
        } else {
            // auto fallback to old default (sha1)
            $algo = 'sha1';
            $pwd2 = $row['password'];
        }

        if (!Password::encrypt($id, $pwd, $algo) == $pwd2)
            return false;

        return SqlObject::loadObject($row, __CLASS__);
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
