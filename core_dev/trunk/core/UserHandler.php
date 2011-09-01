<?php
/**
 * $Id$
 *
 * User handler class, used to change user stuff such as group membership
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: ripped out from User
// XXX code needs updating in coredev user admin, user registration & user login & user forgot password

class UserHandler
{
    public static function isOnline($id)
    {
        $session = SessionHandler::getInstance();
        $u = User::get($id);

        if (ts($u->time_last_active) > time() - $session->online_timeout)
            return true;

        return false;
    }

    public static function getUserLevel($id)
    {
        $q =
        'SELECT t2.level FROM tblGroupMembers AS t1'.
        ' INNER JOIN tblUserGroups AS t2 ON (t1.groupId=t2.groupId)'.
        ' WHERE t1.userId = ?'.
        ' ORDER BY t2.level DESC LIMIT 1';

        $l = Sql::pSelectItem($q, 'i', $id);
        return $l ? $l : 0;
    }


    /** Adds the user to a user group */
    function addToGroup($n)
    {
        if (!is_numeric($n)) return false;

        $q = 'SELECT COUNT(*) FROM tblGroupMembers WHERE groupId = ? AND userId = ?';
        if (Sql::pSelectItem($q, 'ii', $n, $this->id))
            return true;

        $q = 'INSERT INTO tblGroupMembers SET groupId = ?, userId = ?';
        Sql::pInsert($q, 'ii', $n, $this->id);
        return true;
    }

    function removeFromGroup($n)
    {
        if (!is_numeric($n)) return false;

        $q = 'DELETE FROM tblGroupMembers WHERE groupId = ? AND userId = ?';
        Sql::pDelete($q, 'ii', $n, $this->id);
        return true;
    }

    /** Returns a list of UserGroup objects for all groups the user is a member of */
    function getGroups()
    {
        $q = 'SELECT groupId FROM tblGroupMembers WHERE userId = ?';
        $res = Sql::pSelect1d($q, 'i', $this->id);

        $groups = array();
        foreach ($res as $grp_id)
            $groups[] = new UserGroup($grp_id);

        return $groups;
    }

    function getLoginHistory()
    {
        $q = 'SELECT * FROM tblLogins WHERE userId = ? ORDER BY timeCreated DESC';
        return Sql::pSelect($q, 'i', $this->id);
    }

    function render() /// XXXX move to a view
    {
        if (!$this->id)
            return t('Anonymous');

        switch ($this->type) {
        case USER_REGULAR: return $this->name;
        case USER_FACEBOOK:
            $name = UserSetting::get($this->id, 'fb_name');
            //$pic = UserSetting::get($this->id, 'fb_picture');
            return $name.' (facebook)';
//            return '<fb:name uid="'.$this->name.'" useyou="false"></fb:name>';

        default: throw new Exception ('hm');
        }
    }

}

?>
