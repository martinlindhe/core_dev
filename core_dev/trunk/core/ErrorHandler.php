<?php
/**
 * $Id$
 *
 * Stores errors in a $_SESSION array to keep them persistent
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: use anonymous function for set_error_handler(), requires PHP 5.3

class ErrorHandler
{
    static $_instance; ///< singleton

    private function __construct()
    {
        $this->init();
    }

    private function __clone() {}      //singleton: prevent cloning of class

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    function getErrorCount()
    {
        return isset($_SESSION['cd_errors']) ? count($_SESSION['cd_errors']) : 0;
    }

    function add($s)
    {
        $_SESSION['cd_errors'][] = $s;
    }

    function reset()
    {
        $_SESSION['cd_errors'] = array();
    }

    private function init()
    {
        set_error_handler( array($this, 'internalErrorHandler') );
    }

    function internalErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        // http://se.php.net/manual/en/errorfunc.constants.php
        switch ($errno) {
        case E_ERROR:
        case E_USER_ERROR:
            $type = 'ERROR';
            $s = "$errstr on $errfile:$errline";
            break;

        case E_WARNING:
        case E_USER_WARNING:
            $type = 'WARNING';
            $s = "$errstr on $errfile:$errline";
            break;

        case E_NOTICE:
        case E_USER_NOTICE:
            $type = 'NOTICE';
            $s = "$errstr on $errfile:$errline";
            break;

        case E_STRICT:
            $type = 'STRICT';
            $s = "$errstr on $errfile:$errline";
            break;

        default:
            $type = 'UNKNOWN';
            $s = "Unknown error type: [$errno] $errstr on $errfile:$errline";
            break;
        }

        dp('PHP ERROR: '.$type.' '.$s);

        // Dont display errors that is hidden by error_reporting setting
        if (!(error_reporting() & $errno))
            return;

        echo '<b>'.$type.':</b> '.$s."<br/>\n";

        // Don't execute PHP internal error handler
        return true;
    }

    function render($clear_errors = false)
    {
        if (empty($_SESSION['cd_errors']))
            return '';

        $div_class = 'error_'.mt_rand(0,99999);

        $header = XhtmlHeader::getInstance();

        $header->embedCss(
        '.'.$div_class.' {'.
            'border: 1px dashed;'.
            'color: #000;'.
            'padding: 5px;'.
            'line-height: 20px;'.
            'background-color: #FB9B9B;'.
        '}');

        $res = '';

        foreach ($_SESSION['cd_errors'] as $e)
            $res .= '<div class="'.$div_class.'">'.$e.'</div><br/>';

        if ($clear_errors)
            $this->reset();

        return $res;
    }
}

?>
