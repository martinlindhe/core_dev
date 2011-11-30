<?php
/**
 * $Id$
 */

//STATUS: wip

class LoginEntry
{

    protected static $tbl_name = 'tblLogins';

    public static function add($user_id, $ip, $user_agent)
    {
        $q =
        'INSERT INTO '.self::$tbl_name.
        ' SET timeCreated = NOW(), userId = ?, IP = ?, userAgent = ?';
        return Sql::pInsert($q, 'iss', $user_id, $ip, $user_agent);
    }

    public static function getHistory($user_id)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE userId = ? ORDER BY timeCreated DESC';
        return Sql::pSelect($q, 'i', $user_id);
    }

    /** @return array of user id's associated with $ip */
    public static function getUsersByIP($ip)
    {
        $q =
        'SELECT DISTINCT(userId) FROM '.self::$tbl_name.
        ' WHERE IP = ?';
        return Sql::pSelect1d($q, 's', $ip);
    }

    /** @return array of IP's associated with user_id */
    public static function getIPsByUser($user_id)
    {
        $q =
        'SELECT DISTINCT(IP) FROM '.self::$tbl_name.
        ' WHERE userId = ?';
        return Sql::pSelect1d($q, 'i', $user_id);
    }

}

?>
