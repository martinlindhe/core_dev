<?php
/**
 * $Id$
 *
 * Singleton class to perform user registration.
 * Allows for additonal registration steps to be attached to base class
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: should contain "register user" view with ability to extend registration to contain extra fields (like userdata)

require_once('User.php');

class RegisterHandler // XXXX merge with UserHandler?
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

        $o = new User();
        $o->name = $username;
        $o->type = USER_REGULAR;
        $o->time_created = sql_datetime( now() );
        $o->id = User::store($o);

        if (!$o->id) {
            $error->add('Failed to create user');
            return false;
        }

        $o->password = self::encryptPassword($o->id, $password);
        User::store($o);

        $session = SessionHandler::getInstance();

        dp($session->getUsername().' created user '.$username.' ('.$o->id.') of type '.$type);

        if ($this->post_reg_callback)
            call_user_func($this->post_reg_callback, $user->getId());

        return true;
    }

}

?>
