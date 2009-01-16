<?php
/**
 * $Id$
 *
 * Basic SIP/2.0 server implementation
 *
 * SIP RFC: http://www.ietf.org/rfc/rfc3261.txt
 * HTTP Authentication RFC: http://www.faqs.org/rfcs/rfc2617.html
 * http://tools.ietf.org/id/draft-ietf-sip-call-flows-05.txt
 *
 * @author Martin Lindhe, 2007-2009
 */

//TODO (maybe??): merge get_nonce / allocate_nonce & port allocation code into child class ($x = new child() )?

//TODO: debug output: show sip messages
//TODO: return correct error codes on failures

require_once('input_mime.php');
require_once('input_sdp.php');
require_once('output_sdp.php');

//Client requests
define('SIP_INVITE',	1);
define('SIP_ACK',		2);
define('SIP_BYE',		3);
define('SIP_OPTIONS',	4);
define('SIP_CANCEL',	5);
define('SIP_REGISTER',	6);

//Server responses
define('SIP_TRYING',		10);
define('SIP_RINGING',		11);
define('SIP_OK',			12);
define('SIP_UNAUTHORIZED',	13);

class sip_server
{
	var $interface, $port;
	var $handle = false;
	var $dst_ip = 0;
	var $nonce_arr = array();		///< array of previously generated nonce's for authentication
	var $allocated_ports = array();	///< array of currently allocated ports for A/V RTP streams

	var $auth_realm   = 'core_dev SIP server';	///< MUST be globally unique
	var $auth_opaque  = 'iam opaque!';			///< the content of this string is irrelevant
	var $auth_handler = false;					///< defines custom authentication handler

	/**
	 * Constructor
	 *
	 * @param $interface IP address to listen to
	 * @param $port port to listen to
	 */
	function __construct($interface = '0.0.0.0', $port = 5060)
	{
		if (!is_numeric($port)) return false;

		$this->interface = $interface;
		$this->port = $port;

		$this->start_server();
	}

	function __destruct()
	{
		if ($this->handle) {
			fclose($this->handle);
		}
	}

	/**
	 * Starts to listen for SIP messages over UDP
	 */
	function start_server()
	{
		$this->handle = stream_socket_server('udp://'.$this->interface.':'.$this->port, $errno, $errstr, STREAM_SERVER_BIND);
		if (!$this->handle) {
			die("$errstr ($errno)");
		}

		echo "sip_server ready for connections at ".$this->interface.":".$this->port."\n";
	}

	function auth_callback($cb)
	{
		$this->auth_handler = $cb;
	}

	/**
	 * Sample SIP "Digest" authentication
	 *
	 * @return true if credentials are correct
	 */
	function auth_default_handler($username, $realm, $uri, $nonce, $response)
	{
		if ($this->auth_handler) {
			return call_user_func($this->auth_handler, $username, $realm, $uri, $nonce, $response);
		}

		$a1 = $username.':'.$realm.':'."password";	//XXX fetch password from somewhere
		$a2 = "REGISTER".':'.$uri;

		if (md5(md5($a1).':'.$nonce.':'.md5($a2)) == $response) return true;
		return false;
	}

	/**
	 * Allocates a free port to be used for current peer
	 * The port must be even numbered and come in blocks of 4
	 *
	 * @return allocated port or false if no more ports are available
	 */
	function allocate_port($peer)
	{
		for ($i = 20000; $i < 64000; $i += 4) {
			if (empty($this->allocated_ports[$i])) {
				$this->allocated_ports[$i] = $peer;
				echo "DEBUG: Port range ".$i." - ".($i+3)." allocated for ".$peer."\n";
				return $i;
			}
		}
		return false;
	}

	/**
	 * Frees allocated port associated with peer
	 *
	 * @return true on success
	 */
	function free_port($peer)
	{
		for ($i = 20000; $i < 64000; $i += 4) {
			if (!empty($this->allocated_ports[$i]) && $this->allocated_ports[$i] == $peer) {
				unset($this->allocated_ports[$i]);
				echo "DEBUG: Port range ".$i." - ".($i+3)." freed for ".$peer."\n";
				return true;
			}
		}
		return false;
	}

	/**
	 * Creates a guaranteed unique nonce for the client auth challenge.
	 * If the client previously had a nonce, it will be replaced.
	 */
	function allocate_nonce($peer, $key)
	{
		for (;;) {
			$nonce = md5(microtime().':'.$key.':'.mt_rand(0, 9999999999999));
			$conflict = false;

			//verifies that generated nonce is not already in use
			foreach ($this->nonce_arr as $old_peer => $old_nonce) {
				if ($old_nonce == $nonce) $conflict = true;
			}

			if (!$conflict) {
				$this->nonce_arr[$peer] = $nonce;
				return $nonce;
			}
		}
	}

	/**
	 * Returns previously generated nonce for client, or false if none is found
	 */
	function get_nonce($peer)
	{
		if (!empty($this->nonce_arr[$peer])) return $this->nonce_arr[$peer];

		return false;
	}

	/**
	 * Listens for SIP messages
	 */
	function listen()
	{
		$pkt = stream_socket_recvfrom($this->handle, 1500, 0, $peer);	//XXX bigger buffer?

		echo "Data incoming from ".$peer."\n";

		/*
		$pos = strpos($peer, ':');
		$peer_ip = substr($peer, 0, $pos);
		$peer_port = substr($peer, $pos + 1);
		echo "Data incoming from  ".$peer_ip." on port ".$peer_port."\n";
		*/

		$this->handle_message($peer, $pkt);

		return $pkt;
	}

	/**
	 * Handles incoming SIP messages
	 *
	 * @param $peer client address to send to
	 * @param $msg the raw SIP message
	 * @return true if message was handled
	 */
	function handle_message($peer, $msg)
	{
		$pos = strpos($msg, "\r\n\r\n");
		$head = explode("\r\n", trim(substr($msg, 0, $pos)), 2);
		$mime = mimeParseHeader($head[1]);
		$body = trim(substr($msg, $pos));

		$tmp = explode(' ', $head[0]);	//INVITE sip:xxx@10.10.10.240 SIP/2.0

		if ($tmp[2] != 'SIP/2.0') {
			//XXX return error code!
			echo "ERROR: Unsupported SIP version: ".$tmp[2]."\n";
			return false;
		}

		switch ($tmp[0]) {
			case 'INVITE':		$sip_type = SIP_INVITE; break;
			case 'ACK':			$sip_type = SIP_ACK; break;
			case 'BYE':			$sip_type = SIP_BYE; break;
			case 'OPTIONS':		$sip_type = SIP_OPTIONS; break;
			case 'CANCEL':		$sip_type = SIP_CANCEL; break;
			case 'REGISTER':	$sip_type = SIP_REGISTER; break;
			default:
				echo "Unknown SIP command: ".$tmp[0]."\n";
				return false;
		}

		switch ($sip_type) {
			case SIP_INVITE:
				//echo "Recieved SIP INVITE from ".$peer."\n";

				//Send "100 TRYING" response
				$res = $this->send_message(SIP_TRYING, $peer, $mime);

				//Send "180 RINGING" response
				$res = $this->send_message(SIP_RINGING, $peer, $mime);

				//Send "200 OK" response
				if ($mime['Content-Type'] != 'application/sdp') {
					echo "ERROR: DIDNT GET SDP FROM CLIENT INVITE MSG\n";
					//FIXME how to handle. hangup?
					break;
				}

				echo "Forwarding SIP media streams to IP: ".$this->dst_ip."\n";

				$port = $this->allocate_port($peer);

				$res_sdp = generate_sdp($body, $this->dst_ip, $port);
				$res = $this->send_message(SIP_OK, $peer, $mime, $res_sdp);

				echo "SDP FROM CALLER:\n".$body."\n\n";
				echo "SDP TO CALLER & STREAMING SERVER:\n".$res_sdp."\n";

				$sdp_file = '/tmp/sip_tmp.sdp';
				file_put_contents($sdp_file, $res_sdp);

				//NOTE: playback live stream locally with VLC:
				//exec('/home/ml/scripts/compile/vlc-git/vlc -vvv '.$sdp_file);
				//exec('ffplay '.$sdp_file);

				//XXX dump rtp stream (command not working!!!)
				//exec('ffmpeg -i '.$sdp_file.' -vcodec copy /tmp/out.263');
				break;

			case SIP_BYE:
				echo "Recieved SIP BYE from ".$peer."\n";
				//Send "200 OK" response
				$res = $this->send_message(SIP_OK, $peer, $mime);
				$this->free_port($peer);
				break;

			case SIP_ACK:
				//echo "Recieved SIP ACK from ".$peer."\n";
				//we dont send a response on this message
				break;

			case SIP_OPTIONS:
				//FIXME more parameters should be set in the OK response (???)
				echo "Recieved SIP OPTIONS from ".$peer."\n";
				$res = $this->send_message(SIP_OK, $peer, $mime);
				break;

			//We are acting a SIP Registrar
			case SIP_REGISTER:
				echo "Recieved SIP REGISTER from ".$peer."\n";

				if (empty($mime['Authorization'])) {
					//FIXME sip bindings (how does that work?!) RFC 3261: 10.3
					echo "Sending auth request!\n";
					$res = $this->send_message(SIP_UNAUTHORIZED, $peer, $mime);
					break;
				}

				$pos = strpos($mime['Authorization'], ' ');
				$auth_type = substr($mime['Authorization'], 0, $pos);
				$auth_response = substr($mime['Authorization'], $pos+1);

				if ($auth_type != "Digest") {
					//FIXME: send error code! 400 Bad request (???)
					echo "ERROR: SIP/2.0 only support Digest authentication\n";
					break;
				}

				//RFC 2617: "3.2.2.1 Request-Digest"
				$chal = parseAuthRequest($auth_response);

				if ($chal['algorithm'] != "MD5" ||
					$chal['realm'] != $this->auth_realm ||
					$chal['nonce'] != $this->get_nonce($peer) ||
					$chal['opaque'] != md5($this->auth_opaque) ||
					empty($chal['username']))
				{
					//FIXME: send error code! 400 Bad request (???)
					echo "ERROR: Authentication details for user ".$chal['username']." is invalid!\n";
					break;
				}

				$check = $this->auth_default_handler($chal['username'], $this->auth_realm, $chal['uri'], $chal['nonce'], $chal['response']);

				if ($check) {
					echo "DEBUG: Authentication of user ".$chal['username']." SUCCESSFUL!\n";
					$res = $this->send_message(SIP_OK, $peer, $mime);
				} else {
					//FIXME: send error code! 403 Forbidden (???)
					echo "ERROR: Authentication of user ".$chal['username']." FAILED!\n";
				}
				break;

			case SIP_CANCEL:
				echo "Recieved SIP CANCEL from ".$peer."\n";
				//FIXME what should we respond?
				break;

			default:
				echo "Unknown SIP message type\n";
				return false;
		}

		return true;
	}

	/**
	 * Generates a SIP message
	 *
	 * @param $type type of sip message to generat
	 * @param $peer client address to send to
	 * @param $prev array of Mime header data from previous recieved SIP message
	 * @param $sdp_data SDP data to attach to message if needed
	 */
	function send_message($type, $peer, $prev, $sdp_data = '')
	{
		switch ($type) {
			case SIP_OK:           $res = "SIP/2.0 200 OK\r\n"; break;
			case SIP_TRYING:       $res = "SIP/2.0 100 TRYING\r\n"; break;
			case SIP_RINGING:      $res = "SIP/2.0 180 RINGING\r\n"; break;
			case SIP_UNAUTHORIZED: $res = "SIP/2.0 401 Unauthorized\r\n"; break;
			default:
				echo "sip_client->send_message() unknown type ".$type."\n";
				return false;
		}

		$res .=
		"From: ".$prev['From']."\r\n".		//append "epid="
		"Call-ID: ".$prev['Call-ID']."\r\n".//echo
		"CSeq: ".$prev['CSeq']."\r\n".		//echo
		"Via: ".$prev['Via']."\r\n".		//append "recieved="
		"To: ".$prev['To']."\r\n".			//append "tag="
		"Allow: MESSAGE, INVITE, CANCEL, ACK, OPTIONS, SUBSCRIBE, NOTIFY, BYE\r\n".	//XXX "MESSAGE" ?
		"Contact: \"core_dev\" <sip:core_dev@".$this->interface.":".$this->port.">\r\n".	//XXX core_dev here??
		"User-Agent: core_dev\r\n";			//XXX core_dev version

		if ($type == SIP_UNAUTHORIZED) {
			//RFC 3261 #22.4
			//Based on HTTP Digest authentication: http://www.faqs.org/rfcs/rfc2617.html

			$nonce = $this->allocate_nonce($peer, $prev['Call-ID']);

			$res .=
			"WWW-Authenticate: Digest".
			" realm=\"".$this->auth_realm."\",".	//text string
    		" domain=\"sip:sip.example.com\",".		//FIXME: set domain according to the "To:" field set by client
			" nonce=\"".$nonce."\",".
    		" opaque=\"".md5($this->auth_opaque)."\",".
			" stale=FALSE,".	//XXX A flag, indicating that the previous request from the client was rejected because the nonce value was stale.
			" algorithm=MD5\r\n";
		}

		if ($sdp_data) {
			$res .=
			"Content-Type: application/sdp\r\n".
			"Content-Length: ".strlen($sdp_data)."\r\n\r\n".$sdp_data;
		} else {
			$res .=
			"Content-Length: 0\r\n\r\n";
		}

		stream_socket_sendto($this->handle, $res, 0, $peer);
		return true;
	}

}

?>
