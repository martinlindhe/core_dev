<?php
/**
 * $Id$
 *
 * Responsible for the application flow,
 * parses request URL and instantiate proper controller and invoke the action method
 *
 * The theory of operation is that URLs follow the format /controller/action/key1/value1/
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: parse $_GET params into $_params
//TODO: special view for login/logout events

require_once('core.php'); //for is_alphanumeric()
require_once('ViewModel.php');

class RequestHandler
{
    static $_instance; ///< singleton

    protected $_controller = 'index'; ///< /CONTROLLER/view/owner/        XXX not actually a controller (yet), its the file to run in the applications /views/ directory
    protected $_view = 'default';     ///< /controller/VIEW/owner/        XXX view parameter for the "controller", or later will be the method to run on the controller
    protected $_owner = '';           ///< /controller/view/OWNER/        alphanumeric id
    protected $_child = '';           ///< /controller/view/owner/CHILD/  alphanumeric id
    protected $_params;
    protected $exclude_session = array();

    public function getView() { return $this->_view; }
    //public function getParams() { return $this->_params; }

    /**
     * Registers a list of controllers that should not invoke $session->resume()
     * XXX this is a hack. dont know how to handle this elegantly.
     * the problem is to mark certain requests as "no session", for example RPC:s
     */
    function excludeSession($arr) { $this->exclude_session = $arr; }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    private function __construct()
    {
        $request = $_SERVER['REQUEST_URI'];

        //exclude application root from parsed request
        $page = XmlDocumentHandler::getInstance();
        $parsed = parse_url($page->getBaseUrl());

        if (substr($request, 0, strlen($parsed['path'])) == $parsed['path'])
            $request = substr($request, strlen($parsed['path']) );

        $arr = explode('/', trim($request, '/'));

        if ($arr && substr($arr[0],0,1) != '?') {
            if (!empty($arr[0]))
                $this->_controller = $arr[0];

            if (!empty($arr[1]))
                $this->_view = $arr[1];

            if (count($arr) <= 2)
                return;

            if (is_alphanumeric($arr[2]))
                $this->_owner = $arr[2];

            if (isset($arr[3]) && is_alphanumeric($arr[3]))
                $this->_child = $arr[3];
        }

//XXX if controller or view contains non-alphanumeric letters, DIE here!

/*
        //XXX FIXME parse params properly
        for ($idx=2, $cnt = count($arr); $idx < $cnt; $idx += 2)
            $res[ $arr[$idx] ] = isset($arr[$idx+1]) ? $arr[$idx+1] : true;

        $this->_params = $res;
*/
    }

    private function __clone() {}      //singleton: prevent cloning of class

    /**
     * Creates a instance of requested controller and invokes requested method on that controller
     */
    public function route()
    {
        $file = 'views/'.$this->_controller.'.php';

        if (!file_exists($file))
            throw new Exception('No file named '.$file );

        if (!in_array($this->_controller, $this->exclude_session) && class_exists('SessionHandler') ) {
            //automatically resumes session unless it is blacklisted
            $session = SessionHandler::getInstance();
            $session->resume();
        }

        if (class_exists('AuthHandler')) {
            //XXX handle login/logout requests to any page. FIXME: use a special view for these
            $auth = AuthHandler::getInstance();
            $auth->handleEvents();
        }

        //expose request params for the view
        $view = new ViewModel($file);
        $view->view   = $this->_view;
        $view->owner  = $this->_owner;
        $view->child  = $this->_child;
        //$view->params = $this->_params;

        $page = XmlDocumentHandler::getInstance();
        $page->attach( $view->render() );
    }

}

?>
