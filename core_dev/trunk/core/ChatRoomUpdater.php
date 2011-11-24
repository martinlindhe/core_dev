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

//STATUS: wip

//VIEW: core/views/chatroom.php "update"

class ChatRoomUpdater
{
    public static function init()
    {
        $header = XhtmlHeader::getInstance();
        $header->includeJs('http://yui.yahooapis.com/3.4.1/build/yui/yui-min.js');

        $session = SessionHandler::getInstance();

        $interval = 5 * 1000; // milliseconds


        $header->registerJsFunction(
        // scrolls focus to bottom of a <div style="overflow:auto">
        'function scroll_to_bottom(div)'.
        '{'.
            'var elm = get_el(div);'.
            'try {'.
                'elm.scrollTop = elm.scrollHeight;'.
            '} catch(e) {'.
                // for older browsers
                'var f = document.createElement("input");'.
                'if (f.setAttribute) f.setAttribute("type","text");'.
                'if (elm.appendChild) elm.appendChild(f);'.
                'f.style.width = "0px";'.
                'f.style.height = "0px";'.
                'if (f.focus) f.focus();'.
                'if (elm.removeChild) elm.removeChild(f);'.
            '}'.
        '}');

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

                    'if (typeof ts === "undefined")'.
                        'node.setContent("");'. // clears div

                    'for (var i = data.length-1; i >= 0; --i) {'.
                        'var p = data[i];'.

                        'if ((typeof ts === "undefined") || p.from != '.$session->id.')'.
                            'node.append( chatmsg_render(p) );'.
                    '}'.

                    'scroll_to_bottom(target);'. //XXX can YUI scroll focus to bottom?

                    'latest = data[0] ? data[0].microtime : ts;'.

                    // registers a timer function
                    'var t=setTimeout("chatroom_init("+room+",\'"+target+"\',"+latest+")",'.$interval.');'.

//                    'console.log("chat refreshed");'.
                '};'.

                // subscribe to event io:complete
                'Y.on("io:complete", complete, Y);'.

                // make the request
                'var request = Y.io(uri);'.
            '});'.

        '}');

        $header->registerJsFunction(
        'function chatmsg_render(p)'.
        '{'.
            'var t = new Date(p.microtime*1000);'. // to milliseconds

            // XXX if msg is from today, show time. else show full date
            'var when = t.toUTCString();'.

            'return when + ", " + p.from + " said: " + p.msg + "<br/>";'.
        '}');

        $header->registerJsFunction(
        'function chatroom_send(frm,room,target)'.
        '{'.
            'if (!frm.msg.value)'.
                'return false;'.

            'YUI().use("io-form","node", function(Y)'.
            '{'.
                'var uri = "/iview/chatroom/send/" + room + "?m=" + frm.msg.value;'.

                'var request = Y.io(uri);'.

                'var node = Y.one("#"+target);'.

                // append sent message to <div>
                'var p = {"from":'.$session->id.',"msg":frm.msg.value,"microtime": new Date().getTime()/1000 };'.
                'node.append( chatmsg_render(p) );'.

                'scroll_to_bottom(target);'. //XXX can YUI scroll focus to bottom?

                'frm.msg.value = "";'.
            '});'.

            'return false;'. // never return true! so form wont refresh
        '}');

    }

}

?>
