<?php
/**
 * $Id$
 *
 * Singleton class to perform user registration and user handling.
 * Allows for additonal registration steps to be attached to base class
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//TODO code needs updating in session_forgot_pwd

//TODO: should contain "register user" view with ability to extend registration to contain extra fields (like userdata)

//XXXX: move userlevel & usergroup stuff to separate classes (UserGroupHandler)

require_once('User.php');

class UserHandler
{
    static $_instance; ///< singleton

    protected $post_reg_callback; ///< function to execute when user registration was complete

    protected $username_minlen = 3;
    protected $username_maxlen = 20;

    protected $password_minlen = 6;

    private function __clone() {}      //singleton: prevent cloning of class
    private function __construct() { }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    function setUsernameMinlen($n) { if (is_numeric($n)) $this->username_minlen = $n; }
    function setUsernameMaxlen($n) { if (is_numeric($n)) $this->username_maxlen = $n; }

    function setPasswordMinlen($n) { if (is_numeric($n)) $this->password_minlen = $n; }

    function getUsernameMinlen() { return $this->username_minlen; }
    function getUsernameMaxlen() { return $this->username_maxlen; }

    function getPasswordMinlen() { return $this->password_minlen; }

    function setPostRegistrationCallback($s) { $this->post_reg_callback = $s; }

    function register($username, $pwd1, $pwd2)
    {
        $error = ErrorHandler::getInstance();

        $username = trim($username);
        $pwd1     = trim($pwd1);

        if (strlen($username) < $this->username_minlen) {
            $error->add('Username must be at least '.$this->username_minlen.' characters long');
            return false;
        }

        if (strlen($username) > $this->username_maxlen) {
            $error->add('Username cant be longer than '.$this->username_maxlen.' characters long');
            return false;
        }

        if (strlen($pwd1) < $this->password_minlen) {
            $error->add('Password must be at least '.$this->password_minlen.' characters long');
            return false;
        }

        if ($pwd1 != $pwd2) {
            $error->add('Passwords dont match');
            return false;
        }

        if ($username == $pwd1) {
            $error->add('Username and password must be different');
            return false;
        }

        if (User::getByName($username)) {
            $error->add('Username taken');
            return false;
        }

        if (!self::create($username, $pwd1)) {
            $error->add('Failed to create user');
            return false;
        }

        if ($this->post_reg_callback)
            call_user_func($this->post_reg_callback, $user->getId());

        return true;
    }

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
        if (!$o->id)
            return false;

        $o->password = self::encryptPassword($o->id, $password);
        User::store($o);

        $session = SessionHandler::getInstance();

        dp($session->getUsername().' created user '.$username.' ('.$o->id.') of type '.$type);
        return $o->id;
    }

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

    public static function encryptPassword($id, $pwd)
    {
        $session = SessionHandler::getInstance();
        return sha1( $id . sha1( $session->getEncryptKey() ) . sha1($pwd) );
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
