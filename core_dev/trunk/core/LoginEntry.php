<?php
/**
 * $Id$
 *
 * Writes to tblLogins
 */

//STATUS: early wip

class LoginEntry
{

    public static function add($user_id, $user_ip, $user_agent)
    {
        $q = 'INSERT INTO tblLogins SET timeCreated = NOW(), userId = ?, IP = ?, userAgent = ?';
        return Sql::pInsert($q, 'iss', $user_id, $user_ip, $user_agent);
    }

    public static function getHistory($user_id)
    {
        $q = 'SELECT * FROM tblLogins WHERE userId = ? ORDER BY timeCreated DESC';
        return Sql::pSelect($q, 'i', $user_id);
    }


}

?>
