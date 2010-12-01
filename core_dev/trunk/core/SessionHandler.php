<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: remember client ip between requests in order to mitigate session hijacking
//TODO: is setActive() method even nessecary with the RequestHandler blacklist???
//FIXME: session timeout verkar inte funka rätt?!?!? vill kunna ha session i 7 dygn den dör efter nån timme iaf

//TODO: reimplement user-block & ip-block

require_once('User.php');
require_once('class.CoreBase.php');

class SessionHandler extends CoreBase
{
    static $_instance;             ///< singleton

    var $id;
    var $ip;                       /// current IP address
    var $username;
    var $usermode;                 ///< 0=normal user. 1=webmaster, 2=admin, 3=super admin
    var $referer = '';             ///< return to this page after login (if user is browsing a part of the site that is blocked by $this->requireLoggedIn() then logs in)
    var $timeout = 86400;          ///< 24h - max allowed idle time (in seconds) before session times out and user needs to log in again
    var $online_timeout = 1800;    ///< 30m - max idle time before the user is counted as "logged out" in "users online"-lists etc

    var $name = 'core_dev_sid';    ///< session cookie name, needs to be unique for multiple projects on same webhost
    var $start_page;               ///< redirects user to this page (in $config['app']['web_root'] directory) after successful login
    var $logged_out_start_page;
    var $error_page = 'error';     ///< redirects the user to this page to show errors

    var $active = true;            ///< is session active (update user last active?)

    var $isWebmaster;              ///< is user webmaster?
    var $isAdmin;                  ///< is user admin?
    var $isSuperAdmin;             ///< is user superadmin?

    var $allow_logins        = true; ///< do app currently allow logins?
    var $allow_registrations = true; ///< do app currently allow registrations?
    protected $encrypt_key   = '';

    private function __construct() { }
    private function __clone() {}      //singleton: prevent cloning of class

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    function setName($s) { $this->name = $s; }
    function setTimeout($n) { $this->timeout = $n; }
    function setStartPage($s) { $this->start_page = $s; }

    function setActive($b) { $this->active = $b; }

    function setEncryptKey($key) { $this->encrypt_key = $key; }
    function getEncryptKey() { return $this->encrypt_key; }

    function allowLogins($b) { $this->allow_logins = $b; }
    function allowRegistrations($b) { $this->allow_registrations = $b; }

    /**
     * Handles logins
     *
     * @param $username
     * @param $pwd
     * @return true on success
     */
    function login($username, $pwd)
    {
        $db      = SqlHandler::getInstance();
        $error   = ErrorHandler::getInstance();

        if (!$this->allow_logins) {
            $error->add('Logins currently not allowed.');
            return false;
        }

        $username = trim($username);
        $pwd      = trim($pwd);

        $user = new User($username);
        if (!$user->getId()) {
            $error->add('Login failed');
            return false;
        }

        $x = $db->pSelect('SELECT COUNT(*) FROM tblUsers WHERE userId=? AND userName=? AND userPass=? AND timeDeleted IS NULL',
        'iss',
        $user->getId(),
        $username,
        sha1( $user->getId() . sha1($this->encrypt_key) . sha1($pwd) )  // encrypted password
        );

        if (!$x) {
            dp('Failed login attempt: username '.$username);
            $error->add('Login failed');
            return false;
        }

        $this->start($user->getId(), $username);
        dp($this->username.' logged in');

        return true;
    }

    /**
     * Logs out the user
     */
    function logout()
    {
        dp($this->username.' logged out');

        $this->setLogoutTime();
        $this->end();
        $this->showLoggedOutStartPage();
        die;
    }

    /**
     * Resumes the session from previous request
     */
    function resume()
    {
        if (!$this->active) return;

        ini_set('session.gc_maxlifetime', $this->timeout);

        session_name($this->name);
        session_start();

        $this->id           = &$_SESSION['id'];    //if id is set, also means that the user is logged in
        $this->username     = &$_SESSION['username'];
        $this->usermode     = &$_SESSION['usermode'];
        $this->isWebmaster  = &$_SESSION['isWebmaster'];
        $this->isAdmin      = &$_SESSION['isAdmin'];
        $this->isSuperAdmin = &$_SESSION['isSuperAdmin'];
        $this->referer      = &$_SESSION['referer'];
        $this->ip           = &$_SESSION['ip'];

        if (empty($_COOKIE[$this->name]))
            $this->end();

        if (!$this->id)
            return;

        //sets httponly param to mitigate XSS attacks
        $domain = ''; //XXX read domain name from XmlDocumentHandler->getUrl()
        setcookie($this->name, $_COOKIE[$this->name], time()+$this->timeout, '/', $domain, false, true);

        //Logged in: Check user activity - log out inactive user
        //FIXME: redo this- check lastactive timestamp from db when session is resumed instead
        /*
        if ($this->lastActive < (time()-$this->timeout)) {
            $this->log('Session timed out after '.(time()-$this->session->lastActive).' (timeout is '.($this->session->timeout).')', LOGLEVEL_NOTICE);
            $this->end();
            $error->add('Session timed out');
            $this->showErrorPage();
        }*/

        $this->updateActiveTime();
    }

    /**
     * Kills the current session, clearing all session variables
     */
    function end()
    {
        $this->id           = 0;
        $this->ip           = '';
        $this->username     = '';
        $this->usermode     = 0;
        $this->referer      = '';
        $this->isWebmaster  = false;
        $this->isAdmin      = false;
        $this->isSuperAdmin = false;
    }

    /**
     * Sets up a session. Called from the auth class
     *
     * @param $id user id
     * @param $username user name
     */
    function start($id, $username)
    {
        $this->id = $id;
        $this->ip = client_ip();
        $this->username = $username;

        $user = new User($id);
        $this->usermode = $user->getUserLevel();

        if ($this->usermode >= USERLEVEL_WEBMASTER)  $this->isWebmaster  = true;
        if ($this->usermode >= USERLEVEL_ADMIN)      $this->isAdmin      = true;
        if ($this->usermode >= USERLEVEL_SUPERADMIN) $this->isSuperAdmin = true;

        $this->updateLoginTime();

        $db = SqlHandler::getInstance();

        $error = ErrorHandler::getInstance();
        $error->reset(); /// remove previous errors

        $q = 'INSERT INTO tblLogins SET timeCreated=NOW(), userId = ?, IP = ?, userAgent = ?';
        $db->pInsert($q, 'iss', $this->id, client_ip(), $_SERVER['HTTP_USER_AGENT'] );
    }

    function getUserLevelName()
    {
        $u = new User($this->id);
        return $u->getUserLevelName();
    }

    /**
     * @return array of ApiCustomer objects that is owned by groups that current user is a member of
     */
    function getApiAccounts()
    {
        $u = new User($this->id);

        $res = array();

        foreach ($u->getGroups() as $grp)
        {
            $api_accts = new ApiCustomerList( $grp->getId() );
            foreach ($api_accts->getCustomers() as $acc)
                $res[] = $acc;
        }

        return $res;
    }

    /**
     * @return array with api account id:s that is owned by groups that current user is a member of
     */
    function getApiAccountIds()
    {
        $u = new User($this->id);

        $res = array();

        foreach ($u->getGroups() as $grp)
        {
            $api_accts = new ApiCustomerList( $grp->getId() );
            foreach ($api_accts->getCustomers() as $acc)
                $res[] = $acc->getId();
        }

        return $res;
    }

    /**
     * @return true if user is member of a UserGroup which owns api account $id
     */
    function ownsApiAccount($id)
    {
        if ($this->isSuperAdmin)
            return true;

        foreach ($this->getApiAccounts() as $acc)
            if ($acc->getId() == $id)
                return true;

        return false;
    }

    private function updateActiveTime()
    {
        if (!$this->id || !$this->active) return;

        $db = SqlHandler::getInstance();
        $db->pUpdate('UPDATE tblUsers SET timeLastActive=NOW() WHERE userId = ?', 'i', $this->id);
    }

    private function updateLoginTime()
    {
        $db = SqlHandler::getInstance();
        $db->pUpdate('UPDATE tblUsers SET timeLastLogin=NOW(), timeLastActive=NOW() WHERE userId = ?', 'i', $this->id);
    }

    function setLogoutTime()
    {
        $db = SqlHandler::getInstance();
        $db->pUpdate('UPDATE tblUsers SET timeLastLogout=NOW() WHERE userId = ?', 'i', $this->id);
    }

    /**
     * Locks registered users out from certain pages, such as registration page
     */
    function requireLoggedOut()
    {
        if (!$this->id) return;
        $this->showStartPage();
    }

    /**
     * Locks unregistered users out from certain pages
     */
    function requireLoggedIn()
    {
        if ($this->id) return;

        $error = ErrorHandler::getInstance();
        $error->add('The page you requested requires you to be logged in.');

        $this->referer = $_SERVER['REQUEST_URI'];
        $this->showErrorPage();
    }

    /**
     * Locks normal users out from certain pages
     */
    function requireWebmaster()
    {
        if ($this->isWebmaster) return;

        $error = ErrorHandler::getInstance();
        $error->add('The page you requested requires webmaster rights to view.');
        $this->showErrorPage();
    }

    /**
     * Locks normal users & webmasters out from certain pages
     */
    function requireAdmin()
    {
        if ($this->isAdmin) return;

        $error = ErrorHandler::getInstance();
        $error->add('The page you requested requires admin rights to view.');
        $this->showErrorPage();
    }

    /**
     * Locks out everyone except for super-admin from certain pages
     */
    function requireSuperAdmin()
    {
        if ($this->isSuperAdmin) return;

        $error = ErrorHandler::getInstance();
        $error->add('The page you requested requires superadmin rights to view.');
        $this->showErrorPage();
    }

    /**
     * Locks out everyone not from localhost (for setup scripts)
     */
    function requireLocalhost()
    {
        if ($this->ip == '127.0.0.1') return;
        die;
    }

    /**
     * Redirects user to error page
     */
    function showErrorPage()
    {
        $db = SqlHandler::getInstance();

        if ($db->getErrorCount()) {
            echo "DEBUG: session->redirect aborted due to error".ln();
            return;
        }

        js_redirect($this->error_page);
    }

    /**
     * Redirects user to default start page (logged in)
     */
    function showStartPage()
    {
        if (!empty($this->referer))
            js_redirect($this->referer);

        js_redirect($this->start_page);
    }

    /**
     * Redirects user to default start page (logged out)
     */
    function showLoggedOutStartPage()
    {
       js_redirect($this->logged_out_start_page);
    }

    function renderLoginForm()
    {
        $view = new ViewModel('views/auth_login_form.php');
        return $view->render();
    }

}

?>
