
var chat_link_chatreq_clicked = 1;
var chat_link_chatreq = '';
var chat_link_chatreq_org = '';
var chat_link_chatreq_nonew = '';
var chat_link_chat = '';
var chat_poll_cnt = 0;

function chat_request_start(link_chatreq, link_chat)
{
	chat_link_chatreq_org = link_chatreq;
	chat_link_chatreq_nonew = link_chatreq+'?nonewchat';
	chat_link_chatreq = link_chatreq;
	chat_link_chat = link_chat;
	chat_request();
}

var chat_request_request = new AJAX();
function chat_request()
{
	if (!chat_request_request._busy) {
		chat_request_request.GET_raw(chat_link_chatreq, chat_request_callback);
	}
	if (++chat_poll_cnt >= 100) return;

	setTimeout("chat_request()", 10 * 1000);	//refresh stats every 10 seconds
}

function chat_request_callback()
{
	if (!chat_request_request || !chat_request_request.ResultReady()) return;

 	// TODO: Detta kan utvecklas till att bli en generell funktion.. om man använder flera rader osv.. eller byter till json eller xml...
 	// Men det är ett senare projekt...
 	// Nu är formatet: userid;otheruserid;otherusername
 	results = chat_request_request._request.responseText.split(";");

	if (results[1]>0) {
		chat_link_chatreq_clicked = 0;
		chat_link_chatreq = chat_link_chatreq_nonew;
		html =  '<b>Chatförfrågan</b><br/><br/>'+results[2]+' vill'+
				' chatta med dig.<br/>Vill du chatta med '+results[2]+'?'+
				'<br/><a href="javascript:return false;" onClick="chat_link_chatreq_clicked=1;'+
				'window.open(\''+chat_link_chat+'?otherid='+
				results[1]+'\',\'chatwindow'+results[1]+'\',\'status=1\')">Ja</a> / '+
				'<a href="javascript:return false;" onClick="set_invisible_by_'+
				'name(\'popup_chat\');chat_link_chatreq_clicked=1;">Nej</a>';
		if (getElementById('popup_chat').style.display == 'none') {
			show_element_by_name('popup_chat');
		}
		set_div_content('popup_chat', html);
		set_visible_by_name('popup_chat');
	} else if (chat_link_chatreq_clicked) {
		set_invisible_by_name('popup_chat');
		chat_link_chatreq = chat_link_chatreq_org;
	}
	chat_request_request = new AJAX();
}

var chat_otherId = 0;
var chat_myId = 0;
var chat_otherName = '';
var chat_myName = '';


function chat_onload(otherId, myId, otherName, myName)
{
	chat_otherId = otherId;
	chat_myId = myId;
	chat_otherName = otherName;
	chat_myName = myName;
	chat();
}

var chat_request_q = new AJAX();
function chat()
{
	if (!chat_request_q._busy) {
		chat_request_q.GET_raw(_ext_core+'ajax_chat_1on1.php?otherid='+chat_otherId, chat_callback);
	}
	setTimeout("chat()", 2 * 1000);	//refresh stats every 2 seconds
}

function chat_callback()
{
	if (!chat_request_q || !chat_request_q.ResultReady()) return;

	// TODO: gör så den skickas ngn vacker xml istället...
 	results = chat_request_q._request.responseText.split("\n");

	if (results[0] != 0) {
		for (i = 0; i < results.length; i++) {
			if (results[i] != '') {
				row = results[i].split("|;");
				span = add_span(document.getElementById('chat_div'));
				span.innerHTML = '['+row[2]+'] &lt;'+row[0]+'&gt; '+row[1]+'<br/>';
			}
		}
	}

	document.getElementById('chat_div').scrollTop = document.getElementById('chat_div').scrollHeight;

	chat_request_q = new AJAX();
}

var chat_send_request = new AJAX();
function chat_send(input)
{
	if (!chat_send_request._busy) {
		chat_send_request.GET_raw(_ext_core+'ajax_chat_1on1_send.php?otherid='+chat_otherId+'&msg='+input.value, chat_send_callback);
		span = add_span(document.getElementById('chat_div'));
		//TODO: Fult fult, borde abstraheras med en datum-tjofräs i javascript
		var today = new Date();
		var y = today.getFullYear();
		var mm= today.getMonth()+1;
		var d = today.getDate();
		var h = today.getHours();
		var m = today.getMinutes();
		var s = today.getSeconds();
		span.innerHTML = '['+y+'-'+padTwoDigs(mm)+'-'+padTwoDigs(d)+' '+padTwoDigs(h)+':'+padTwoDigs(m)+':'+padTwoDigs(s)+'] &lt;'+chat_myName+'&gt; '+input.value+'<br/>';
		input.value = '';
//		span.innerHTML = '['+row[2]+'] <'+row[0]+'>'+row[1]+'<br/>';
	} else {
		return false;
	}

}

// TODO: Gör så att callback kan ta parametrar, så att callback-funktionen kan avgöra om texten ska visas eller inte.
function chat_send_callback()
{
	if (!chat_send_request || !chat_send_request.ResultReady()) return;

	document.chat_form.chat_message.focus();
	document.getElementById('chat_div').scrollTop = document.getElementById('chat_div').scrollHeight;

	chat_send_request = new AJAX();

//	if (results == 1) {
//		span = add_span(document.getElementById('chat_div'), '', '');
//		span.innerHTML =
//	}
}

function padTwoDigs(i)
{
	if (i<10) {
		i='0' + i;
	}
	return i;
}
