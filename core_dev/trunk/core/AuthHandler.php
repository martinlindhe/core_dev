<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: WIP, rewriting

//TODO: cleanup/rewrite handleEvents()

require_once('ViewModel.php');

class AuthHandler extends CoreBase
{
    static $_instance;               ///< singleton class
    var $allow_logins = true;        ///< do app currently allow logins?
    var $allow_registrations = true; ///< do app currently allow registrations?
    private $encrypt_key = '';
    var $check_ip = true;
    private $ip; ///< used for validating if client ip changes

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    function setEncryptKey($key) { $this->encrypt_key = $key; }

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
        global $db;

        $user = new User();
        $id = $user->loadByName($username);

        $enc_password = sha1( $id.sha1($this->encrypt_key).sha1($password) );

        $q = 'SELECT * FROM tblUsers WHERE userName="'.$db->escape($username).'" AND userPass="'.$db->escape($enc_password).'" AND timeDeleted IS NULL';
        $data = $db->getOneRow($q);

        if (!$data) {
            $this->setError( t('Login failed') );
            dp('Failed login attempt: username '.$username, LOGLEVEL_WARNING);
            return false;
        }
//XXXX konfigurera $session härifrån:
/*
$session->setId(xx);
$session->setUserMode(xx);
$session->setUserName(xx);
*/

        if ($data['userMode'] != USERLEVEL_SUPERADMIN) {
            if ($this->mail_activate && !Users::isActivated($data['userId'])) {
                $this->setError( t('This account has not yet been activated.') );
                return false;
            }

            if (!$this->allow_login) {
                $this->setError( t('Logins currently not allowed.') );
                return false;
            }

            $blocked = isBlocked(BLOCK_USERID, $data['userId']);
            if ($blocked) {
                $this->setError( t('Account blocked') );
                dp('Login attempt from blocked user: username '.$username, LOGLEVEL_WARNING);
                return false;
            }
        }
        return $data;
    }

    /**
     * Logs out the user
     */
    function logout($userId)
    {
        $users = new Users();
        $users->logoutTime($userId);

        addEvent(EVENT_USER_LOGOUT, 0, $userId);
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
