<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: remember client ip between requests in order to mitigate session hijacking
//FIXME: session timeout verkar inte funka rätt?!?!? vill kunna ha session i 7 dygn den dör efter nån timme iaf

//TODO: reimplement user-block & ip-block

//TODO: rework tblUsers.userPass to store value as "sha1:xxxxx", "sha512:xxx" in order to simplify future hash change
//      XXX also increase tblUsers.userPass size to 64(512 bits) + 10 (algorithm name) characters

//TODO: calculate password hash by feeding first generated hash to hash algorithm for a number of times (10?)
//      XXX will break all currently used passwords


require_once('CoreBase.php');
require_once('User.php');
require_once('ErrorHandler.php');

class SessionHandler extends CoreBase
{
    static $_instance;             ///< singleton

    var $id;
    var $ip;                       /// current IP address
    var $username;
    var $usermode;                 ///< 0=normal user. 1=webmaster, 2=admin, 3=super admin
    var $referer;                  ///< return to this page after login (if user is browsing a part of the site that is blocked by $this->requireLoggedIn() then logs in)
    var $timeout        = 86400;   ///< 24h - max allowed idle time (in seconds) before session times out and user needs to log in again
    var $online_timeout = 1800;    ///< 30m - max idle time before the user is counted as "logged out" in "users online"-lists etc
    var $facebook_app_id;          ///< facebook app id, enables "login with facebook"?
    var $facebook_secret;          ///< facebook secret
    var $facebook_id;              ///< "fbid" facebook user id

    var $name;                     ///< session cookie name, needs to be unique for multiple projects on same webhost
    var $start_page;               ///< redirects user to this page (in $config['app']['web_root'] directory) after successful login
    var $logged_out_start_page;
    var $error_page = 'coredev/error';  ///< redirects the user to this page to show errors

    var $isWebmaster;              ///< is user webmaster?
    var $isAdmin;                  ///< is user admin?
    var $isSuperAdmin;             ///< is user superadmin?
    protected $last_active;        ///< timestamp of last activity

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
    function setTimeout($n)
    {
        if (!is_duration($n))
            throw new Exception ('bad timeout: '.$n);

        $this->timeout = parse_duration($n);
    }
    function setStartPage($s) { $this->start_page = $s; }
    function setLastActive()
    {
        $this->last_active = time();
        SqlHandler::getInstance()->pUpdate('UPDATE tblUsers SET timeLastActive=NOW() WHERE userId = ?', 'i', $this->id);
    }

    function setEncryptKey($key) { $this->encrypt_key = $key; }
    function getEncryptKey() { return $this->encrypt_key; }

    function getUsername() { return $this->username; }
    function getLastActive() { return $this->last_active; }

    function allowLogins($b) { $this->allow_logins = $b; }
    function allowRegistrations($b) { $this->allow_registrations = $b; }

    /**
     * App must be registed at http://developers.facebook.com/setup
     */
    function setFacebookAuth($app_id, $secret)
    {
        $this->facebook_app_id = $app_id;
        $this->facebook_secret = $secret;
    }

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

        $x = $db->pSelectItem('SELECT COUNT(*) FROM tblUsers WHERE userId=? AND userName=? AND userPass=? AND timeDeleted IS NULL',
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

        $this->id = $user->getId();
        $this->ip = client_ip();
        $this->username = $username;

        $this->usermode = $user->getUserLevel();

        if ($this->usermode >= USERLEVEL_WEBMASTER)  $this->isWebmaster  = true;
        if ($this->usermode >= USERLEVEL_ADMIN)      $this->isAdmin      = true;
        if ($this->usermode >= USERLEVEL_SUPERADMIN) $this->isSuperAdmin = true;

        $db->pUpdate('UPDATE tblUsers SET timeLastLogin=NOW(), timeLastActive=NOW(), lastIp = ? WHERE userId = ?', 'si', client_ip(), $this->id);

        $q = 'INSERT INTO tblLogins SET timeCreated=NOW(), userId = ?, IP = ?, userAgent = ?';
        $db->pInsert($q, 'iss', $this->id, client_ip(), $_SERVER['HTTP_USER_AGENT'] );

        $_SESSION['id']           = $this->id;
        $_SESSION['username']     = $this->username;
        $_SESSION['usermode']     = $this->usermode;
        $_SESSION['isWebmaster']  = $this->isWebmaster;
        $_SESSION['isAdmin']      = $this->isAdmin;
        $_SESSION['isSuperAdmin'] = $this->isSuperAdmin;
        $_SESSION['referer']      = $this->referer;
        $_SESSION['ip']           = $this->ip;
        $_SESSION['last_active']  = time();
        session_write_close();

        dp($this->username.' logged in');

        $error->reset(); // remove previous errors

        return true;
    }

    /**
     * Starts session & loads previous session data if found
     * must be called at beginning of each page request
     */
    function start()
    {
        if (!$this->name)
            throw new Exception ('session name not set');

        session_name($this->name);

        ini_set('session.cookie_lifetime', $this->timeout); // in seconds
        ini_set('session.gc_maxlifetime', $this->timeout);  // in seconds

        if (!session_start())
            throw new Exception ('failed to start session');

        if (empty($_SESSION['id']))
            return;

        $page = XmlDocumentHandler::getInstance();

        setcookie($this->name, session_id(), time() + $this->timeout, $page->getRelativeUrl() );

        $this->id           = &$_SESSION['id'];
        $this->username     = &$_SESSION['username'];
        $this->usermode     = &$_SESSION['usermode'];
        $this->isWebmaster  = &$_SESSION['isWebmaster'];
        $this->isAdmin      = &$_SESSION['isAdmin'];
        $this->isSuperAdmin = &$_SESSION['isSuperAdmin'];
        $this->referer      = &$_SESSION['referer'];
        $this->ip           = &$_SESSION['ip'];
        $this->last_active  = &$_SESSION['last_active'];
    }

    /** Logs out the user */
    function logout()
    {
        dp($this->username.' logged out');

        $db = SqlHandler::getInstance();
        $db->pUpdate('UPDATE tblUsers SET timeLastLogout=NOW() WHERE userId = ?', 'i', $this->id);

        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 604800, $params['path'], $params['domain'], $params['secure'], $params['httponly']);

        $this->end();

        $this->showLoggedOutStartPage();
        die;
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

        if (!empty($_SESSION))
            $_SESSION = array();  //XXXX FIXME: dont destroy $_SESSION['cd_errors'] (error handler messages)

        session_destroy();
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

        if ($db instanceof DatabaseMysqlProfiler && $db->getErrorCount()) {
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
        $view = new ViewModel('views/session_login.php');
        return $view->render();
    }

}

?>
