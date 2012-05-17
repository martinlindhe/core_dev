<?php
/**
 * $Id$
 *
 * Stores errors in a $_SESSION array to keep them persistent
 *
 * @author Martin Lindhe, 2010-2012 <martin@startwars.org>
 */

//STATUS: wip

//TODO: use anonymous function for set_error_handler(), requires PHP 5.3

require_once('IXmlComponent.php');

class ErrorHandler implements IXmlComponent
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
        register_shutdown_function( array($this, 'internalShutdownHandler') );
    }

    /** will catch FATAL errors (type = E_ERROR) */
    function internalShutdownHandler()
    {
        $a = error_get_last();
        if (!$a)
            return;

        $out = ob_get_contents();

        // clear all previous output in order to avoid having error output hidden in a opened html tag
        ob_end_clean();

        echo '</div>'; // HACK to not end up inside a <div style="display:none">
        echo '<pre>';
        echo strip_tags($out);
        echo '</pre>';

        $this->internalErrorHandler($a['type'], $a['message'], $a['file'], $a['line']);
    }

    function internalErrorHandler($errno, $errstr, $errfile, $errline, $errcontext = '')
    {
        $s = $errstr;

        // http://se.php.net/manual/en/errorfunc.constants.php
        switch ($errno) {
        case E_ERROR:             $type = 'E_ERROR'; break;
        case E_WARNING:           $type = 'E_WARNING'; break;
        case E_PARSE:             $type = 'E_PARSE'; break;
        case E_NOTICE:            $type = 'E_NOTICE'; break;
        case E_COMPILE_ERROR:     $type = 'E_COMPILE_ERROR'; break;
        case E_USER_ERROR:        $type = 'E_USER_ERROR'; break;
        case E_USER_WARNING:      $type = 'E_USER_WARNING'; break;
        case E_USER_NOTICE:       $type = 'E_USER_NOTICE'; break;
        case E_STRICT:            $type = 'E_STRICT'; break;
        case E_RECOVERABLE_ERROR: $type = 'E_RECOVERABLE_ERROR'; break;
        case E_DEPRECATED:        $type = 'E_DEPRECATED'; break;

        default:
            $type = 'UNKNOWN';
            $s = 'Unknown error type '.$errno.': '.$errstr;
            break;
        }

        dp('PHP ERROR: '.$type.' ('.$errno.') '.$s.' on '.$errfile.':'.$errline);

        // Dont display errors that is hidden by error_reporting setting
        if (!(error_reporting() & $errno))
            return;

        echo '</div>'; // HACK to not end up inside a <div style="display:none">
        echo '<pre>';
        echo '<b>'.$type.':</b> '.$s.' on '.$errfile.':'.$errline;
        echo '</pre>';

        // Don't execute PHP internal error handler
        return true;
    }

    function render()
    {
        if (empty($_SESSION['cd_errors']))
            return '';

        $div_class = 'error_'.mt_rand();

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

        // clear errors
        $this->reset();

        return $res;
    }
}

?>
