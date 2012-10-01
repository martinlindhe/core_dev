<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2012 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

define('LOGIN_INTERNAL', 1);

class LoginEntry
{

    protected static $tbl_name = 'tblLogins';

    public static function add($user_id, $ip, $user_agent, $type = LOGIN_INTERNAL)
    {
        $q =
        'INSERT INTO '.self::$tbl_name.
        ' SET timeCreated = NOW(), userId = ?, IP = ?, userAgent = ?, type = ?';
        return Sql::pInsert($q, 'issi', $user_id, $ip, $user_agent, $type);
    }

    public static function getHistory($user_id, $type = LOGIN_INTERNAL)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE userId = ? AND type = ? ORDER BY timeCreated DESC';
        return Sql::pSelect($q, 'ii', $user_id, $type);
    }

    /** @return array of user id's associated with $ip */
    public static function getUsersByIP($ip, $type = LOGIN_INTERNAL)
    {
        $q =
        'SELECT DISTINCT(userId) FROM '.self::$tbl_name.
        ' WHERE IP = ? AND type = ?';
        return Sql::pSelect1d($q, 'si', $ip, $type);
    }

    /** @return array of IP's associated with user_id */
    public static function getIPsByUser($user_id, $type = LOGIN_INTERNAL)
    {
        $q =
        'SELECT DISTINCT(IP) FROM '.self::$tbl_name.
        ' WHERE userId = ? AND type = ?';
        return Sql::pSelect1d($q, 'ii', $user_id, $type);
    }

}

?>
