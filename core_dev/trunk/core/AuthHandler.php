<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: WIP

//TODO: maybe rename to LoginHandler (?)
//TODO: reimplement user-block & ip-block

//TODO: cleanup/rewrite handleEvents()

require_once('ViewModel.php');
require_once('User.php');

class AuthHandler extends CoreBase
{
    static  $_instance;                  ///< singleton
    var     $allow_logins = true;        ///< do app currently allow logins?
    var     $allow_registrations = true; ///< do app currently allow registrations?
    private $encrypt_key = '';
    var     $check_ip = true;
    private $ip;                         ///< to validate if client ip changes

    private function __construct() { }
    private function __clone() {}        ///< singleton: prevent cloning of class

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    function setEncryptKey($key) { $this->encrypt_key = $key; }
    function getEncryptKey() { return $this->encrypt_key; }

    function allowLogins($b) { $this->allow_logins = $b; }
    function allowRegistrations($b) { $this->allow_registrations = $b; }

    function renderLoginForm()
    {
        $view = new ViewModel('views/auth_login_form.php');
        return $view->render();
    }

    /**
     * Handles logins
     *
     * @param $username
     * @param $password
     * @return true on success
     */
    private function login($username, $password)
    {
        $db      = SqlHandler::getInstance();
        $error   = ErrorHandler::getInstance();
        $session = SessionHandler::getInstance();

        if (!$this->allow_logins) {
            $error->add('Logins currently not allowed.');
            return false;
        }

        $user = new User($username);
        if (!$user->getId()) {
            $error->add('Login failed');
            return false;
        }

        $enc_password = sha1( $user->getId() . sha1($this->encrypt_key) . sha1($password) );

        $q = 'SELECT * FROM tblUsers WHERE userId='.$user->getId().' AND userName="'.$db->escape($username).'" AND userPass="'.$db->escape($enc_password).'" AND timeDeleted IS NULL';
        $row = $db->getOneRow($q);
        if (!$row) {
            dp('Failed login attempt: username '.$username);
            $error->add('Login failed');
            return false;
        }

        $session->start($row['userId'], $row['userName'], $row['userMode']);
        dp($session->username.' logged in');

/*
        if ($data['userMode'] != USERLEVEL_SUPERADMIN) {
            if ($this->mail_activate && !Users::isActivated($data['userId'])) {
                $error->add('This account has not yet been activated.');
                return false;
            }
        }
*/
        return true;
    }

    /**
     * Logs out the user
     */
    private function logout()
    {
        $session = SessionHandler::getInstance();

        dp($session->username.' logged out');

        //addEvent(EVENT_USER_LOGOUT, 0, $session->id);
        $session->setLogoutTime();
        $session->end();
        $session->showLoggedOutStartPage();
        die;
    }


///used in register() method
    //var $reserved_usercheck = true;     ///< check if username is listed as reserved username, requires tblStopwords
    //var $userdata = true;               ///< shall we use tblUserdata for required userdata fields?

    var $minlen_username = 3;           ///< minimum length for valid usernames
    var $minlen_password = 4;           ///< minimum length for valid passwords

    /**
     * Register new user in the database
     *
     * @param $username user name
     * @param $password1 password
     * @param $password2 password (repeat)
     * @param $mode user mode
     * @return the user ID of the newly created user
     */
    function register($username, $password1, $password2, $usermode = USERLEVEL_NORMAL)
    {
        if (!is_numeric($usermode)) return false;

        $error = ErrorHandler::getInstance();

        if ($username != trim($username)) {
            $error->add('Username contains invalid spaces');
            return false;
        }

        if (strlen($username) < $this->minlen_username) {
            $error->add('Username must be at least '.$this->minlen_username.' characters long');
            return false;
        }

        if (strlen($password1) < $this->minlen_password) {
            $error->add('Password must be at least '.$this->minlen_password.' characters long');
            return false;
        }
        if ($password1 != $password2) {
            $error->add('The passwords doesnt match');
            return false;
        }

//        if ($this->reserved_usercheck && isReservedUsername($username)) return t('Username is not allowed');
/*
        //Checks if email was required, and if so if it was correctly entered
        if ($this->userdata) {
            $chk = verifyRequiredUserdataFields();
            if ($chk !== true) return $chk;
        }
*/
        $userlist = new UserList();

        if ($userlist->getCount()) {
            $user = new User();

            if ($user->loadByName($username)) {
                $error->add('Username already exists');
                return false;
            }
        } else {
            //No users exists, give this user superadmin status
            $_mode = USERLEVEL_SUPERADMIN;
        }

        $userhandler = new UserHandler();
        $userhandler->create($username, $usermode);
        $userhandler->setPassword($password1);

        dp('Registered user: '.$username.', id '.$userhandler->getId());

/*
        //Stores the additional data from the userdata fields that's required at registration
        if ($this->userdata) {
            handleRequiredUserdataFields($newUserId);
        }
*/
        return $userhandler->getId();
    }

    /**
     * Handles login, logout & register user requests
     */
    function handleEvents()
    {
        $session = SessionHandler::getInstance();
        $error   = ErrorHandler::getInstance();

        //Logged in: Check for a logout request. Send GET parameter 'logout' to any page to log out
        if (isset($_GET['logout']))
            $this->logout();

        //Check for login request, POST to any page with 'login_usr' & 'login_pwd' variables set to log in
        if (!$session->id && !empty($_POST['login_usr']) && isset($_POST['login_pwd']))
            $this->login($_POST['login_usr'], $_POST['login_pwd']);


        //Handle new user registrations. POST to any page with 'register_usr', 'register_pwd' & 'register_pwd2' to attempt registration
        if (!$session->id && isset($_POST['register_usr']) && isset($_POST['register_pwd']) && isset($_POST['register_pwd2'])) {

            $userlist = new UserList();

            if ($this->allow_registrations || !$userlist->getCount()) {
                $check = $this->register($_POST['register_usr'], $_POST['register_pwd'], $_POST['register_pwd2'], USERLEVEL_NORMAL);
                if ($check) {
/*
                    if ($this->auth->mail_activate) {
                        $this->auth->sendActivationMail($check);
                    } else {
*/
                        $this->login($_POST['register_usr'], $_POST['register_pwd']);
//                    }
                } else {
                    $error->add('Registration failed');
                }
            }
        }


        //Logged in: Check if client ip has changed since last request, if so - log user out to avoid session hijacking
        if ($session->id && $this->check_ip && $this->ip && ($this->ip != IPv4_to_GeoIP(client_ip())) ) {
            $msg = t('Client IP changed.').'Client IP changed! Old IP: '.GeoIP_to_IPv4($this->auth->ip).', current: '.GeoIP_to_IPv4(client_ip());
            $error->add($msg);
die($msg);
            dp($msg);
            $session->end();
            $session->errorPage();
        }
    }
}

?>
