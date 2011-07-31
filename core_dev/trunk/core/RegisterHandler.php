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

class RegisterHandler
{
    static $_instance; ///< singleton

    protected $post_reg_callback; ///< function to execute when user registration was complete

    protected $username_minlen = 3;
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
    function setPasswordMinlen($n) { if (is_numeric($n)) $this->password_minlen = $n; }

    function getUsernameMinlen() { return $this->username_minlen; }
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

        $user = new User();
        if ($user->loadByName($username)) {
            $error->add('Username taken');
            return false;
        }

        $user->create($username);
        if (!$user->getId()) {
            $error->add('Failed to create user');
            return false;
        }

        $user->setPassword($pwd1);

        if ($this->post_reg_callback)
            call_user_func($this->post_reg_callback, $user->getId());

        return true;
    }

}

?>
