<?php
/**
 * $Id$
 *
 * http://yuilibrary.com/yui/docs/io/
 * http://yuilibrary.com/yui/docs/node/
 * http://yuilibrary.com/yui/docs/json/
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
        // id = room id
        // target = div
        // keep = set to 0 to init, 1 to not clear target div
        'function chatroom_init(id,target,keep)'.
        '{'.
            'YUI().use("io-base","node","json-parse", function(Y)'.
            '{'.
                'var uri = "/iview/chatroom/xhr/update/" + id;'.

                'if (typeof keep === "undefined")'.
                    'var uri = "/iview/chatroom/xhr/init/" + id;'.

                'function complete(id, o)'.
                '{'.
                    'var data = o.responseText;'. // response data
                    'var node = Y.one("#"+target);'.

                    'try {'.
                        'var data = Y.JSON.parse(data);'.
                    '} catch (e) {'.
                        'alert("Invalid data from " + uri);'.
                        'return;'.
                    '}'.

                    'if (typeof keep === "undefined")'.
                        'node.setContent("");'. // clears div

                    'node.append("chat log:<br/>");'.

                    'for (var i = data.length-1; i >= 0; --i) {'.
                        'var p = data[i];'.
                        'node.append(p.microtime + ", " + p.from + " said: " + p.msg + "<br/>");'.
                    '}'.
                '};'.

                // subscribe to event io:complete
                'Y.on("io:complete", complete, Y);'.

                // make the request
                'var request = Y.io(uri);'.
            '});'.

            // registers a timer function
            'var t=setTimeout("chatroom_init("+id+",\'"+target+"\',1)",5000);'.

        '}');
    }

}

?>
