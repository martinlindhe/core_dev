<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2012 <martin@startwars.org>
 */

class UserGroupHandler
{
    protected static $tbl_name = 'tblGroupMembers';

    public static function getUserLevel($id)
    {
        $q =
        'SELECT t2.level FROM '.self::$tbl_name.' AS t1'.
        ' INNER JOIN tblUserGroups AS t2 ON (t1.groupId=t2.groupId)'.
        ' WHERE t1.userId = ?'.
        ' ORDER BY t2.level DESC LIMIT 1';

        $l = Sql::pSelectItem($q, 'i', $id);
        return $l ? $l : 0;
    }

    /** Returns a list of UserGroup objects for all groups the user is a member of */
    public static function getGroups($user_id)
    {
        $q =
        'SELECT groupId FROM '.self::$tbl_name.
        ' WHERE userId = ?';
        $res = Sql::pSelect1d($q, 'i', $user_id);

        $groups = array();
        foreach ($res as $grp_id)
            $groups[] = new UserGroup($grp_id);

        return $groups;
    }

    /** Adds the user to a user group */
    public static function addToGroup($user_id, $grp_id)
    {
        $q =
        'SELECT COUNT(*) FROM '.self::$tbl_name.
        ' WHERE groupId = ? AND userId = ?';
        if (Sql::pSelectItem($q, 'ii', $grp_id, $user_id))
            return true;

        $q =
        'INSERT INTO '.self::$tbl_name.
        ' SET groupId = ?, userId = ?';
        Sql::pInsert($q, 'ii', $grp_id, $user_id);
        return true;
    }

    public static function removeFromGroup($user_id, $grp_id)
    {
        $q =
        'DELETE FROM '.self::$tbl_name.
        ' WHERE groupId = ? AND userId = ?';
        Sql::pDelete($q, 'ii', $grp_id, $user_id);
        return true;
    }

}

?>
