<?php
/**
 * $Id$
 *
 * Writes to tblLogins
 */

//STATUS: early wip

class LoginEntry
{

    public static function add($user_id, $ip, $user_agent)
    {
        $q = 'INSERT INTO tblLogins SET timeCreated = NOW(), userId = ?, IP = ?, userAgent = ?';
        return Sql::pInsert($q, 'iss', $user_id, $ip, $user_agent);
    }

    public static function getHistory($user_id)
    {
        $q = 'SELECT * FROM tblLogins WHERE userId = ? ORDER BY timeCreated DESC';
        return Sql::pSelect($q, 'i', $user_id);
    }

    /** @return array of user id's associated with $ip */
    public static function getUsersByIP($ip)
    {
        $q = 'SELECT DISTINCT(userId) FROM tblLogins WHERE IP = ?';
        return Sql::pSelect1d($q, 's', $ip);
    }

    /** @return array of IP's associated with user_id */
    public static function getIPsByUser($user_id)
    {
        $q = 'SELECT DISTINCT(IP) FROM tblLogins WHERE userId = ?';
        return Sql::pSelect1d($q, 'i', $user_id);
    }

}

?>
