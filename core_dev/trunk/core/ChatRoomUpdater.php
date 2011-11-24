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
        // ts = set to 0 to init, microtime to fetch newer items
        'function chatroom_init(room,target,ts)'.
        '{'.

            'var latest;'.

            'YUI().use("io-base","node","json-parse", function(Y)'.
            '{'.

                'if (typeof ts === "undefined") {'.
                    'var uri = "/iview/chatroom/update/" + room;'.
                '} else {'.
                    'var uri = "/iview/chatroom/update/" + room + "?ts=" + ts;'.
                '}'.

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

                    'if (typeof ts === "undefined") {'.
                        'node.setContent("");'. // clears div
                    '}'.

                    'for (var i = data.length-1; i >= 0; --i) {'.
                        'var p = data[i];'.

                        'var t = new Date( p.microtime * 1000 );'.

                        // XXX if msg is from today, show time. else show full date
                        'var when = t.toUTCString();'.
                        'node.append( when + ", " + p.from + " said: " + p.msg + "<br/>");'.
                    '}'.

                    'if (data[0]) {'.
                        'latest = data[0].microtime;'. // XXX what do if room is empty???
                    '} else {'.
                        'latest = ts;'.
                    '}'.

                    // registers a timer function
                    'var t=setTimeout("chatroom_init("+room+",\'"+target+"\',"+latest+")",2000);'.

//                    'console.log("chat refreshed");'.
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
