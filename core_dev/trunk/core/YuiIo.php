<?php
/**
 * $Id$
 *
 * http://yuilibrary.com/yui/docs/io/
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: early wip

class YuiIo
{
    public $uri;

    function __construct($s = '')
    {
        $this->uri = $s;
    }

    function render()
    {
        if (!$this->uri)
            throw new Exception ('no uri set');

        $header = XhtmlHeader::getInstance();
        $header->includeJs('http://yui.yahooapis.com/3.4.1/build/yui/yui-min.js');

        // Create a YUI instance using io-base module.
        $js =
        'YUI().use("io-base", function(Y)'.
        '{'.
            'var uri = "'.$this->uri.'";'.

            /**
             * Handles the response data.
             * id = Transaction ID
             * o = The response object
             * args = Object containing an array (XXXX for mapping data... P)=
             **/
            'function complete(id, o, args)'.
            '{'.
                'var data = o.responseText;'. // Response data.
                'alert(data);'.
            '};'.

            // Subscribe to event "io:complete"
            'Y.on("io:complete", complete, Y);'.

            // Make an HTTP request to 'get.php'.
            'var request = Y.io(uri);'.
        '});';

        return js_embed($js);
    }

}

?>
