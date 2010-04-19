<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: WIP, rewriting

//TODO: maybe rename to LoginHandler (?)

//TODO: cleanup/rewrite handleEvents()

require_once('ViewModel.php');

class AuthHandler extends CoreBase
{
    static $_instance;               ///< singleton
    var $allow_logins = true;        ///< do app currently allow logins?
    var $allow_registrations = true; ///< do app currently allow registrations?
    private $encrypt_key = '';
    var $check_ip = true;
    private $ip;                     ///< to validate if client ip changes

    private function __construct() { }
    private function __clone() {}      //singleton: prevent cloning of class

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
    function login($username, $password)
    {
        $user = new User();
        $id = $user->loadByName($username);

        $enc_password = sha1( $id.sha1($this->encrypt_key).sha1($password) );

        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblUsers WHERE userName="'.$db->escape($username).'" AND userPass="'.$db->escape($enc_password).'" AND timeDeleted IS NULL';
        $data = $db->getOneRow($q);

        if (!$data) {
            $this->setError( t('Login failed') );
            dp('Failed login attempt: username '.$username);
            return false;
        }
//XXXX konfigurera $session härifrån:
/*
$session->setId(xx);
$session->setUserMode(xx);
$session->setUserName(xx);
*/

        if (!$this->allow_logins) {
            $this->setError( t('Logins currently not allowed.') );
            return false;
        }

/*
        if ($data['userMode'] != USERLEVEL_SUPERADMIN) {
            if ($this->mail_activate && !Users::isActivated($data['userId'])) {
                $this->setError( t('This account has not yet been activated.') );
                return false;
            }

            $blocked = isBlocked(BLOCK_USERID, $data['userId']);
            if ($blocked) {
                $this->setError( t('Account blocked') );
                dp('Login attempt from blocked user: username '.$username, LOGLEVEL_WARNING);
                return false;
            }
        }
*/
        return $data;
    }

    /**
     * Logs out the user
     */
    function logout()
    {
        $session = SessionHandler::getInstance();

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

        if ($username != trim($username)) return t('Username contains invalid spaces');

        if (strlen($username) < $this->minlen_username) return t('Username must be at least').' '.$this->minlen_username.' '.t('characters long');
        if (strlen($password1) < $this->minlen_password) return t('Password must be at least').' '.$this->minlen_password.' '.t('characters long');
        if ($password1 != $password2) return t('The passwords doesnt match');

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

            if ($user->loadByName($username)) return t('Username already exists');
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
        //FIXME verify this works:
        /*
        if ($this->ip && isBlocked(BLOCK_IP, $this->ip)) {
            die('You have been blocked from this site.');
        }
        if (!$this->user_agent) $this->user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        */

        $session = SessionHandler::getInstance();

        //Logged in: Check for a logout request. Send GET parameter 'logout' to any page to log out
        if (isset($_GET['logout'])) {
            $this->logout();
        }

        //Check for login request, POST to any page with 'login_usr' & 'login_pwd' variables set to log in
        if (!$session->id && !empty($_POST['login_usr']) && isset($_POST['login_pwd'])) {

            $data = $this->login($_POST['login_usr'], $_POST['login_pwd']);
            if ($data) {

                $session->start($data['userId'], $data['userName'], $data['userMode']);

                //Load custom theme
                /*
                if ($session->allow_themes && $this->user->userdata) {
                    $session->theme = loadUserdataTheme($session->id, $session->default_theme);
                }
                */

                //$session->showStartPage();
            } else {
                $this->setError(t('Login failed'));
            }
        }

/*
        //Handle new user registrations. POST to any page with 'register_usr', 'register_pwd' & 'register_pwd2' to attempt registration
        if (!$session->id && isset($_POST['register_usr']) && isset($_POST['register_pwd']) && isset($_POST['register_pwd2']) && ($this->auth->allow_registration || !Users::cnt())) {
            $preId = 0;
            if (!empty($_POST['preId']) && is_numeric($_POST['preId'])) $preId = $_POST['preId'];
            $check = $this->user->register($_POST['register_usr'], $_POST['register_pwd'], $_POST['register_pwd2'], USERLEVEL_NORMAL, $preId);
            if (is_numeric($check)) {
                Users::setPassword($check, $_POST['register_pwd'], $_POST['register_pwd'], $this->auth->sha1_key);
                if ($this->auth->mail_activate) {
                    $this->auth->sendActivationMail($check);
                } else {
                    $this->auth->login($_POST['register_usr'], $_POST['register_pwd']);
                }
            } else {
                $this->error = t('Registration failed').', '.$check;
            }
        }
*/
        //Logged in: Check if client ip has changed since last request, if so - log user out to avoid session hijacking
        if ($session->id && $this->check_ip && $this->ip && ($this->ip != IPv4_to_GeoIP(client_ip())) ) {
            $msg = t('Client IP changed.').'Client IP changed! Old IP: '.GeoIP_to_IPv4($this->auth->ip).', current: '.GeoIP_to_IPv4(client_ip());
            $this->setError($msg);
die($msg);
            dp($msg, LOGLEVEL_ERROR);
            $session->end();
            $session->errorPage();
        }
    }
}

?>
