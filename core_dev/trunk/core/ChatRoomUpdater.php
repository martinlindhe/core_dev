<?php
/**
 * $Id$
 *
 * http://yuilibrary.com/yui/docs/io/
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: early wip

//VIEW: core/views/chatroom.php "update"

class ChatRoomUpdater
{
    public static function init()
    {
        $header = XhtmlHeader::getInstance();
        $header->includeJs('http://yui.yahooapis.com/3.4.1/build/yui/yui-min.js');

        $header->registerJsFunction(
        'function chatroom_update(id,target)'.
        '{'.
            'YUI().use("io-base", function(Y)'.
            '{'.
                'var uri = "/iview/chatroom/update/" + id;'.

                /**
                 * Handles the response data
                 * id = Transaction ID
                 * o = The response object
                 * args = Object containing an array (XXXX for mapping data...
                 **/
                'function complete(id, o, args)'.
                '{'.
                    'var data = o.responseText;'. // response data
//                    'alert(data);'.

                    'var t=get_el(target);'.
                    't.innerHTML=data;'.
//                    'alert (t);'.
                '};'.

                // subscribe to event io:complete
                'Y.on("io:complete", complete, Y);'.

                // make the request
                'var request = Y.io(uri);'.
            '});'.
        '}');
    }

}

?>
