<?php
/**
 * $Id$
 *
 * Singleton class to perform user registration and user handling.
 * Allows for additonal registration steps to be attached to base class
 *
 * @author Martin Lindhe, 2009-2012 <martin@ubique.se>
 */

//TODO code needs updating in session_forgot_pwd

//TODO: should contain "register user" view with ability to extend registration to contain extra fields (like userdata)

namespace cd;

require_once('User.php');
require_once('UserGroup.php');
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

        if (Password::isForbidden($pwd1)) {
            $error->add('Your password is a very weak one and is forbidden to use');
            return false;
        }

        $user_id = self::create($username, $pwd1);

        if (!$user_id) {
            $error->add('Failed to create user');
            return false;
        }

        if ($this->post_reg_callback)
            call_user_func($this->post_reg_callback, $user_id);

        return $user_id;
    }

    public static function create($username, $password, $type = SESSION_REGULAR, $algo = 'sha512')
    {
        $username = trim($username);

        if (User::getByName($username))
            return false;

        $o = new User();
        $o->name = $username;
        $o->type = $type;
        $o->time_created = sql_datetime( now() );
        $o->id = $o->store();
        if (!$o->id)
            return false;

        $session = SessionHandler::getInstance();

        $o->password = Password::encrypt($o->id, $session->getEncryptKey(), $password, $algo);
        $o->store(); // write again with password encoded using the user id

        dp($session->getUsername().' created user '.$username.' ('.$o->id.') of type '.$type);
        return $o->id;
    }

    public static function isOnline($id)
    {
        $u = User::get($id);
        if (!$u)
            throw new \Exception ('wat');

        $session = SessionHandler::getInstance();

        if (ts($u->time_last_active) > time() - $session->online_timeout)
            return true;

        return false;
    }

    public static function setUsername($id, $username)
    {
        $u = User::get($id);
        if (!$u)
            throw new \Exception ('wat');

        $u->name = $username;
        $u->store();
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
            throw new \Exception ('wat');

        $session = SessionHandler::getInstance();

        $u->password = Password::encrypt($id, $session->getEncryptKey(), $pwd, $algo);
        $u->store();
    }

}

?>
