<?php
/**
 * $Id$
 *
 * http://yuilibrary.com/yui/docs/io/
 * http://yuilibrary.com/yui/docs/node/
 * http://yuilibrary.com/yui/docs/json/
 * http://yuilibrary.com/yui/docs/datatype/
 * http://yuilibrary.com/yui/docs/event/
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip

//VIEW: core/views/chatroom.php "update"

//TODO: dont hardcode locale to sv-SE
//XXX: can YUI scroll focus to bottom? use that & drop scroll_to_bottom() in that case

class ChatRoomUpdater
{
    public static function init($room_id, $div_name, $form_id)
    {
        $header = XhtmlHeader::getInstance();
        $header->includeJs('http://yui.yahooapis.com/3.4.1/build/yui/yui-min.js');

        $session = SessionHandler::getInstance();

        $interval = 5 * 1000; // milliseconds
        $locale = 'sv-SE';

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


        $header->embedJs(
        'YUI({lang:"'.$locale.'"}).use("io-form","node","json-parse","datatype-date", function(Y)'.
        '{'.

            'Y.on("load", function() {'.
                // initialize chat room
                'Init();'.
            '});'.

            // ts = set to 0 to init, microtime to fetch newer items
            'function Init(ts)'.
            '{'.
                'var latest;'.

                'if (typeof ts === "undefined") {'.
                    'var uri = "/iview/chatroom/update/" + '.$room_id.';'.
                '} else {'.
                    'var uri = "/iview/chatroom/update/" + '.$room_id.' + "?ts=" + ts;'.
                '}'.

                'function complete(id, o)'.
                '{'.
                    'var data = o.responseText;'. // response data
                    'var node = Y.one("#'.$div_name.'");'.

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
                            'node.append(chatmsg_render(p));'.
                    '}'.

                    'if (data.length)'.
                        'scroll_to_bottom("'.$div_name.'");'.

                    'latest = data[0] ? data[0].microtime : ts;'.

                    // registers timer
                    'setTimeout(Init,'.$interval.',latest);'.
//                    'console.log("completed " + id);'.
                '};'.

                // subscribe once to event io:complete
                'Y.once("io:complete",complete,Y);'.

                // make the request
                'var request = Y.io(uri);'.
            '}'.

            'Y.one("#'.$form_id.'").on("submit", function(e)'.
            '{'.
                // stop the event's default behavior
                'e.preventDefault();'.

                // stop the event from bubbling up the DOM tree
                'e.stopPropagation();'.

                'frm = get_el( this.get("id") );'.

                'if (!frm.msg.value)'.
                    'return false;'.

                'var uri = "/iview/chatroom/send/" + '.$room_id.' + "?m=" + frm.msg.value;'.

                'var request = Y.io(uri);'.

                'var node = Y.one("#'.$div_name.'");'.

                // append sent message to <div>
                'var p = {'.
                    '"from":'.$session->id.','.
                    '"msg":frm.msg.value,'.
                    '"microtime":new Date().getTime()/1000'.
                '};'.
                'node.append(chatmsg_render(p));'.

                'scroll_to_bottom("'.$div_name.'");'.

                'frm.msg.value = "";'.

                'return false;'. // return false so form dont refresh
            '});'.

            // renders a chat message
            'function chatmsg_render(p)'.
            '{'.
                'var d = new Date(p.microtime*1000);'.  // to milliseconds

                'var today = new Date( new Date().getFullYear(), new Date().getMonth(), new Date().getDate(),0,0,0);'.

                // http://yuilibrary.com/yui/docs/api/classes/DataType.Date.html#method_format
                'if (d >= today) {'.
                    'return Y.DataType.Date.format(d, {format:"%H:%M"}) + ", "+p.from+" said: "+p.msg+"<br/>";'.
                '} else {'.
                    'return Y.DataType.Date.format(d, {format:"%a %d %b %H:%M"}) + ", "+p.from+" said: "+p.msg+"<br/>";'.
                '}'.
            '}'.

        '});'
        );
    }

}

?>
