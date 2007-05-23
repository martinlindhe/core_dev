<?
	require_once('config.php');

	if (!isset($_GET['id']) || !is_numeric($_GET['id'])) die;
	$roomId = $_GET['id'];

	$room = getChatRoom($roomId);
	if (!$room) die;

	if ($session->id) {
		setChatRoomUser($roomId);
	}

	require('design_head.php');

	echo '<div id="ajax_chat_holder">';
	echo '<div id="ajax_chat_room1">'.$room['roomName'].'</div>';
	echo '<div id="ajax_chat"></div>';
	echo '<div id="ajax_chat_members"></div>';
	if ($session->id) {
		echo '<form name="chat_form" action="">';
		echo 'Type here: <input name="chat_text" type="text" size="60" maxlength="'.$config['chat']['max_text_length'].'" onkeyup="chars_left(this.form);" onkeydown="chars_left(this.form); return send_chat(event,'.$roomId.');"/> ';
		echo '<input name="chat_charsleft" readonly="readonly" type="text" size="3" value="'.$config['chat']['max_text_length'].'"/>';
		echo '<div id="ajax_send_status" class="ajax_status_idle"></div>';
		echo '<div id="ajax_recv_status" class="ajax_status_idle"></div>';
		echo '</form>';
	}
	echo '</div>';

?>



<script type="text/javascript">
<!--
if (document.chat_form) document.chat_form.chat_text.focus();

function chars_left(form)
{
	document.chat_form.chat_charsleft.value = <?=$config['chat']['max_text_length']?>-document.chat_form.chat_text.value.length;
}

/************************
** AJAX CHAT FUNCTIONS **
************************/

var get_request = null;
var send_request = null;
var chat_members = new Array();

function send_chat(e, room)
{
	if (e.keyCode == 13 && document.chat_form.chat_text.value.length) {
		//skicka xml data
		set_class('ajax_send_status', 'ajax_status_sending');
		send_request = AJAX_Request('chat_post.php', 'TEXT', send_chat_handler, 'POST', 'r='+room+'&t='+document.chat_form.chat_text.value);
		return false;
	}

	return true;
}

function send_chat_handler()
{
	if (!send_request || send_request.readyState != 4) return;

	if (send_request.status == 200) {
		var e = document.getElementById('ajax_chat');
		var time = display_timestamp();
		var msg = document.chat_form.chat_text.value;
		var txt = time + ' you said: ' + msg;
		add_node_and_focus(e, txt, 'chat_line_mine');

		document.chat_form.chat_text.value = '';
		document.chat_form.chat_charsleft.value = <?=$config['chat']['max_text_length']?>;

		set_class('ajax_send_status', 'ajax_status_idle');
	} else {
		alert('There was a problem with the request.');
	}

	send_request = null;
}

function reload_chat(room)
{
	empty_element_by_name('ajax_chat');
	set_class('ajax_recv_status', 'ajax_status_loading');

	get_request = AJAX_Request('chat_get.php', 'XML', load_chat_handler, 'POST', 'r='+room+'&all=1');
}

function load_chat(room)
{
	reload_chat(room);

	setTimeout("update_chat(true,"+room+")", 1000);
}


function update_chat(go,room)
{
	set_class('ajax_recv_status', 'ajax_status_loading');
	get_request = AJAX_Request('chat_get.php', 'XML', load_chat_handler, 'POST', 'r='+room);

	if (go) setTimeout("update_chat(true,"+room+")", 1000);
}

function load_chat_handler()
{
	if (!get_request || get_request.readyState != 4) return;

	if (get_request.status == 200) {

		var e = document.getElementById('ajax_chat');

		var root_node = get_request.responseXML.getElementsByTagName('x').item(0);
		if (!root_node) return;

		//each chat "item" is encapsulated in a <l>, now we loop through them
		var items = root_node.childNodes;
		for (var i=0; i<items.length; i++)
		{
			var time,uName,uId,msg,txt;
			var cur = items[i].childNodes;
			if (!cur) continue;

			if (items[i].nodeName == 'l') {	//<l> is chat buffer lines
				//loop through all of this <l>'s tags

				for (var j=0; j<cur.length; j++)
				{
					if (!cur[j].firstChild) continue;
					var nodeName = cur[j].nodeName;
					if (nodeName == 't') time = display_timestamp(cur[j].firstChild.nodeValue);
					if (nodeName == 'u') uName = cur[j].firstChild.nodeValue;
					if (nodeName == 'i') uId = cur[j].firstChild.nodeValue;
					if (nodeName == 'm') msg = cur[j].firstChild.nodeValue;
				}

				if (uId==<?=$session->id?>) {
					txt = time + ' you said: ' + msg;
					add_node_and_focus(e, txt, 'chat_line_mine');
				} else {
					txt = time + ' by ' + uName + ': ' + msg;
					add_node_and_focus(e, txt, 'chat_line_other');
				}
			} else if (items[i].nodeName == 's') {	//<s> is chat room uSer joined entries

				for (var j=0; j<cur.length; j++)
				{
					if (!cur[j].firstChild) continue;
					var nodeName = cur[j].nodeName;
					if (nodeName == 'u') uName = cur[j].firstChild.nodeValue;
					if (nodeName == 'i') uId = cur[j].firstChild.nodeValue;
				}

				if (!in_arr(chat_members, uName)) {
					txt = display_timestamp() + ': ' + uName + ' entered the chat room.';
					add_node_and_focus(e, txt, 'chat_line_user_entered');

					e = document.getElementById('ajax_chat_members');
					add_node(e, uName, 'chat_member_normal');
					chat_members.push(uName);
				}
			} else if (items[i].nodeName == 'e') {	//<e> is chat room uSer left entries

				for (var j=0; j<cur.length; j++)
				{
					if (!cur[j].firstChild) continue;
					var nodeName = cur[j].nodeName;
					if (nodeName == 'u') uName = cur[j].firstChild.nodeValue;
					if (nodeName == 'i') uId = cur[j].firstChild.nodeValue;
				}

				if (in_arr(chat_members, uName)) {
					txt = display_timestamp() + ': ' + uName + ' left the chat room.';
					add_node_and_focus(e, txt, 'chat_line_user_left');

					arr_del(chat_members, uName);
				}
			}
		}

		uName = '<?=$session->username?>';
		if (!in_arr(chat_members, uName)) {
			e = document.getElementById('ajax_chat_members');
			add_node(e, uName, 'chat_member_normal');
			chat_members.push(uName);
		}

		set_class('ajax_recv_status', 'ajax_status_idle');

	} else {
		alert('There was a problem with the request.');
	}
	get_request = null;
}

window.onload = function()
{
	load_chat(<?=$roomId?>);
}
-->
</script>

<span style="cursor: pointer; text-decoration: underline;" onclick="reload_chat(<?=$roomId?>)">refresh chat buffer</span>
<br/>
<br/>
<span style="cursor: pointer; text-decoration: underline;" onclick="empty_element('ajax_chat')">empty chat buffer</span>

<?
	require('design_foot.php');
?>