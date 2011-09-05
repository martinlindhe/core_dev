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

class UserHandler  /// XXX rename, UserHelper ??
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

    /** Returns a list of UserGroup objects for all groups the user is a member of */
    public static function getGroups($user_id)
    {
        $q = 'SELECT groupId FROM tblGroupMembers WHERE userId = ?';
        $res = Sql::pSelect1d($q, 'i', $user_id);

        $groups = array();
        foreach ($res as $grp_id)
            $groups[] = new UserGroup($grp_id);

        return $groups;
    }

    /** Adds the user to a user group */
    public static function addToGroup($user_id, $grp_id)
    {
        $q = 'SELECT COUNT(*) FROM tblGroupMembers WHERE groupId = ? AND userId = ?';
        if (Sql::pSelectItem($q, 'ii', $grp_id, $user_id))
            return true;

        $q = 'INSERT INTO tblGroupMembers SET groupId = ?, userId = ?';
        Sql::pInsert($q, 'ii', $grp_id, $user_id);
        return true;
    }

    public static function removeFromGroup($user_id, $grp_id)
    {
        $q = 'DELETE FROM tblGroupMembers WHERE groupId = ? AND userId = ?';
        Sql::pDelete($q, 'ii', $grp_id, $user_id);
        return true;
    }

    public static function getLoginHistory($id)
    {
        $q = 'SELECT * FROM tblLogins WHERE userId = ? ORDER BY timeCreated DESC';
        return Sql::pSelect($q, 'i', $id);
    }

    /**
     * Sets a new password for the user
     *
     * @param $_id user id
     * @param $_pwd password to set
     */
    public static function setPassword($id, $pwd)
    {
        $u = User::get($id);
        if (!$u)
            throw new Exception ('wat');

        $u->password = self::encryptPassword($id, $pwd);
        User::store($u);
        return true;
    }

    private static function encryptPassword($id, $s)
    {
        $session = SessionHandler::getInstance();
        return sha1( $id.sha1( $session->getEncryptKey() ).sha1($s) );
    }

    public static function setUsername($id, $username)
    {
        $u = User::get($id);
        if (!$u)
            throw new Exception ('wat');

        $u->name = $username;
        User::store($u);
        return true;
    }

    /**
     * Creates a new user
     */
    public static function create($username, $password, $type = USER_REGULAR)
    {
        $username = trim($username);

        if (User::getByName($username))
            return false;

        $o = new User();
        $o->name = $username;
        $o->type = $type;
        $o->time_created = sql_datetime( now() );
        $o->id = User::store($o);

        $o->password = self::encryptPassword($o->id, $password);
        User::store($o);

        $session = SessionHandler::getInstance();

        dp($session->getUsername().' created user '.$username.' ('.$o->id.') of type '.$type);

        return $o->id;
    }


/*
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
*/


}

?>
