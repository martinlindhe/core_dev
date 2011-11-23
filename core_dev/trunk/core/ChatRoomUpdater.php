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
            'YUI().use("io-base", "node", "json-parse", function(Y)'.
            '{'.
                'var uri = "/iview/chatroom/update/" + id;'.

                'function complete(id, o)'.
                '{'.
                    'var data = o.responseText;'. // response data

                    'var node = Y.one("#"+target);'.
                    'node.setContent("");'. // clears div

'
                    try {
                        var data = Y.JSON.parse(data);
                    }
                    catch (e) {
                        alert("Invalid data");
                    }

                    for (var i = data.m.length - 1; i >= 0; --i) {
                        var p = data.m[i];
                        node.append("<p>" + p.microtime + ", " + p.from + " said: " + p.msg + "</p>");
                    }
'.

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
