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

    private function __clone() {}      //singleton: prevent cloning of class
    private function __construct() { }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    function setPostRegistrationCallback($s) { $this->post_reg_callback = $s; }

    function register($username, $pwd1, $pwd2)
    {
        $error = ErrorHandler::getInstance();

        $minlen_username = 3;
        $minlen_password = 4;

        $username = trim($username);
        $pwd1     = trim($pwd1);

        if (strlen($username) < $minlen_username) {
            $error->add('Username must be at least '.$minlen_username.' characters long');
            return false;
        }

        if (strlen($pwd1) < $minlen_password) {
            $error->add('Password must be at least '.$minlen_password.' characters long');
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
