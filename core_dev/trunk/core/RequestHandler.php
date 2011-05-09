<?php
/**
 * $Id$
 *
 * Responsible for the application flow,
 * parses request URL and instantiate proper controller and invoke the action method
 *
 * The theory of operation is that URLs follow the format /controller/action/key1/value1/
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('core.php'); //for is_alphanumeric()
require_once('ViewModel.php');

class RequestHandler
{
    static $_instance; ///< singleton

    protected $_controller = 'index'; ///< /CONTROLLER/view/owner/        XXX not actually a controller (yet), its the file to run in the applications /views/ directory
    protected $_view = 'default';     ///< /controller/VIEW/owner/        XXX view parameter for the "controller", or later will be the method to run on the controller
    protected $_owner = '';           ///< /controller/view/OWNER/        alphanumeric id
    protected $_child = '';           ///< /controller/view/owner/CHILD/  alphanumeric id
    protected $exclude_session = array();

    private function __clone() {}      //singleton: prevent cloning of class

    public function getController() { return $this->_controller; }
    public function getView() { return $this->_view; }

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
        // REDIRECT_URL holds the (public) url the page was redirected to (when using mod_rewrite), also it dont mangle utf8 in url
        if (isset($_SERVER['REDIRECT_URL']))
            $request = $_SERVER['REDIRECT_URL'];
        else
            $request = $_SERVER['REQUEST_URI'];

        // exclude application root from parsed request
        $page = XmlDocumentHandler::getInstance();

        $parsed = parse_url($page->getUrl());

        if (substr($request, 0, strlen($parsed['path'])) == $parsed['path'])
            $request = substr($request, strlen($parsed['path']) );

        $arr = explode('/', trim($request, '/'));

        if ($arr && substr($arr[0],0,1) != '?')
        {
            if (!empty($arr[0])) {
                if (!is_alphanumeric($arr[0]))
                    die('XXX controller');
                $this->_controller = $arr[0];
            }

            if (!empty($arr[1])) {
                if (!is_alphanumeric($arr[1]))
                    die('XXX view');
                $this->_view = $arr[1];
            }

            if (count($arr) <= 2)
                return;

            if ($arr[2]) {
                if (!is_alphanumeric($arr[2]))
                    die('XXX owner');
                $this->_owner = $arr[2];
            }

            if (!empty($arr[3])) {
                if (!is_alphanumeric($arr[3]))
                    die('XXX child');
                $this->_child = $arr[3];
            }
        }
    }

    /**
     * Creates a instance of requested controller and invokes requested method on that controller
     */
    public function route()
    {
        $page  = XmlDocumentHandler::getInstance();
        $error = ErrorHandler::getInstance();

        // automatically resumes session unless it is blacklisted
        if (class_exists('SessionHandler') && !in_array($this->_controller, $this->exclude_session))
        {
            // resume session & handle login/logout/register requests to any page
            $view = new ViewModel('views/handle_request.php', $this);
            $page->attach( $view->render() );
        }

        if ($error->getErrorCount())
            $page->attach( $error->render(true) );

        if ($this->_controller == 'coredev')
            $file = $page->getCoreDevInclude().'views/coredev.php';
        else
            $file = 'views/'.$this->_controller.'.php';

        if (!file_exists($file))
            $file = 'views/error_404.php';

        // expose request params for the view
        $view = new ViewModel($file);
        $view->view   = $this->_view;
        $view->owner  = $this->_owner;
        $view->child  = $this->_child;

        $page->attach( $view->render() );
    }

}

?>
