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
        return isset($_SESSION['e']) ? count($_SESSION['e']) : 0;
    }

    function add($s)
    {
        $_SESSION['e'][] = $s;
    }

    function reset()
    {
        $_SESSION['e'] = array();
    }

    private function init()
    {
        set_error_handler( array($this, 'internalErrorHandler') );
    }

    function internalErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        // This error code is not included in error_reporting
        if (!(error_reporting() & $errno))
            return;

        // http://se.php.net/manual/en/errorfunc.constants.php
        switch ($errno) {
        case E_ERROR:
        case E_USER_ERROR:
            echo "<b>ERROR</b> $errstr<br />\n";
            echo "  Fatal error on line $errline in file $errfile\n";
            exit(1);
            break;

        case E_WARNING:
        case E_USER_WARNING:
            echo "<b>WARNING</b> $errstr on $errfile:$errline<br/>\n";
            break;

        case E_NOTICE:
        case E_USER_NOTICE:
            echo "<b>Notice</b> $errstr on $errfile:$errline<br/>\n";
            break;

        case E_STRICT:
            echo "<b>Strict warning</b> $errstr on $errfile:$errline<br/>\n";
            break;

        default:
            echo "Unknown error type: [$errno] $errstr on $errfile:$errline<br/>\n";
            break;
        }

        // Don't execute PHP internal error handler
        return true;
    }

    function render($clear_errors = false)
    {
        if (!isset($_SESSION['e']))
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

        foreach ($_SESSION['e'] as $e)
            $res .= '<div class="'.$div_class.'">'.$e.'</div><br/>';

        if ($clear_errors)
            $this->reset();

        return $res;
    }
}

?>
