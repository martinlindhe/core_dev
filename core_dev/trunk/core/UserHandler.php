<?php
/**
 * $Id$
 *
 * Singleton class to perform user registration and user handling.
 * Allows for additonal registration steps to be attached to base class
 *
 * @author Martin Lindhe, 2009-2012 <martin@startwars.org>
 */

//TODO code needs updating in session_forgot_pwd

//TODO: should contain "register user" view with ability to extend registration to contain extra fields (like userdata)

require_once('User.php');
require_once('UserGroup.php');
require_once('UserGroupList.php');
require_once('ReservedWord.php');
require_once('Password.php');

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

        if (ReservedWord::isReservedUsername($username)) {
            $error->add('Username is reserved');
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

    public static function create($username, $password, $type = SESSION_REGULAR)
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

        $o->password = Password::encrypt($o->id, $password);
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
     * Sets a new password for the user
     *
     * @param $id user id
     * @param $pwd password to set
     * @param $algo hash algorithm to use
     */
    public static function setPassword($id, $pwd, $algo = 'sha512')
    {
        $u = User::get($id);
        if (!$u)
            throw new Exception ('wat');

        $u->password = $algo.':'.Password::encrypt($id, $pwd, $algo);
        User::store($u);
        return true;
    }

}

?>
