<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2012 <martin@ubique.se>
 */

//STATUS: wip

//TODO-later: rename table columns

namespace cd;

define('LOGIN_INTERNAL', 1);

class LoginEntry
{
    var $entryId;
    var $userId;
    var $timeCreated;
    var $IP;
    var $userAgent;
    var $type;

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
        ' WHERE userId = ? AND type = ?'.
        ' ORDER BY timeCreated DESC';
        return Sql::pSelect($q, 'ii', $user_id, $type);
    }

    /** @return login entries of all types for this user */
    public static function getAllHistory($user_id)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE userId = ?'.
        ' ORDER BY timeCreated DESC';
        return Sql::pSelect($q, 'i', $user_id);
    }

    public static function getAllBetween($time_start, $time_end)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE timeCreated BETWEEN "'.sql_datetime($time_start).'" AND "'.sql_datetime($time_end).'"'.
        ' ORDER BY timeCreated DESC';
        return Sql::pSelect($q);
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
