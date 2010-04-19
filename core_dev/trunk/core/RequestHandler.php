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

class RequestHandler
{
    static $_instance; ///< singleton
    protected $_controller, $_action, $_params, $_body;

    public function getParams() { return $this->_params; }
    public function getController() { return $this->_controller; }
    public function getAction() { return $this->_action; }
    public function getBody() { return $this->_body; }
    public function setBody($body) { $this->_body = $body; }
    public function addBody($body) { $this->_body .= $body; }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    private function __construct()
    {
        $request = $_SERVER['REQUEST_URI'];
        $request = str_replace('?', '/', $request);
        $request = str_replace('&', '/', $request);
        $request = str_replace('=', '/', $request);

        $arr = explode('/', trim($request, '/'));
        $this->_controller = !empty($arr[0]) ? $arr[0] : 'index';
        $this->_action     = !empty($arr[1]) ? $arr[1] : 'render';

        if (count($arr) <= 2)
            return;

        for ($idx=2, $cnt = count($arr); $idx < $cnt; $idx += 2)
            $res[ $arr[$idx] ] = isset($arr[$idx+1]) ? $arr[$idx+1] : true;

        $this->_params = $res;
    }

    private function __clone() {}      //singleton: prevent cloning of class

    /**
     * Creates a instance of requested controller and invokes requested method on that controller
     */
    public function route()
    {
        if (!class_exists($this->getController()))
            throw new Exception("No controller named ".$this->getController() );

        $rc = new ReflectionClass($this->getController());
        if (!$rc->implementsInterface('IController'))
            throw new Exception("Controller dont implement IController");

        if (!$rc->hasMethod($this->getAction()))
            throw new Exception("No action named ".$this->getAction()." on controller ".$this->getController() );

        $controller = $rc->newInstance();
        $method = $rc->getMethod($this->getAction());
        $method->invoke($controller);
    }

}

?>

