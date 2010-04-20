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

class RequestHandler
{
    static $_instance; ///< singleton

    protected $_controller; ///< /CONTROLLER/view/id/   XXX not actually a controller (yet), its the file to run in the applications /views/ directory
    protected $_view;       ///< /controller/VIEW/id/  XXX view parameter for the "controller", or later will be the method to run on the controller
    protected $_id;         ///< /controller/view/ID/
    protected $_params;

    public function getParams() { return $this->_params; }
    public function getController() { return $this->_controller; }
    public function getView() { return $this->_view; }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    private function __construct()
    {
        $request = $_SERVER['REQUEST_URI'];

        $arr = explode('/', trim($request, '/'));
        $this->_controller = !empty($arr[0]) ? $arr[0] : 'index';
        $this->_view       = !empty($arr[1]) ? $arr[1] : 'default';

//XXX if controller or view contains non-alphanumeric letters, DIE here!

        //$request = str_replace('?', '/', $request);
        //$request = str_replace('&', '/', $request);
        //$request = str_replace('=', '/', $request);


        if (count($arr) <= 2)
            return;

        if (is_numeric($arr[2]))
            $this->_id = $arr[2];
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
        $view_file = 'views/'.$this->getController().'.php';

        if (!file_exists($view_file))
            throw new Exception('No file named '.$view_file );

        //expose request params for the view
        $view = new ViewModel($view_file);
        $view->view   = $this->_view;
        $view->id     = $this->_id;
        //$view->params = $this->_params;

        $page = XMLDocumentHandler::getInstance();
        $page->attach( $view->render() );
    }

}

?>
