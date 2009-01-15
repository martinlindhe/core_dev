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

require_once('input_mime.php');
require_once('input_sdp.php');
require_once('output_sdp.php');

define('SIP_UNKNOWN',	0);
define('SIP_INVITE',	1);
define('SIP_ACK',		2);
define('SIP_BYE',		3);
define('SIP_OPTIONS',	4);
define('SIP_CANCEL',	5);
define('SIP_REGISTER',	6);

define('SIP_TRYING',		10);
define('SIP_RINGING',		11);
define('SIP_OK',			12);
define('SIP_UNAUTHORIZED',	13);

class sip_server
{
	var $host, $port;
	var $handle = false;
	var $dst_ip = 0;
	var $nonce_arr = array();	///< array of previously generated nonce's for authentication

	var $auth_realm = 'core_dev SIP server';	///< MUST be globally unique

	function __construct($host, $port = 5060)
	{
		if (!is_numeric($port)) return false;

		$this->host = $host;
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
		$this->handle = stream_socket_server('udp://'.$this->host.':'.$this->port, $errno, $errstr, STREAM_SERVER_BIND);
		if (!$this->handle) {
			die("$errstr ($errno)");
		}

		echo "sip_client ready for connections at ".$this->host.":".$this->port."\n";
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

		if (!in_array($peer_ip, $allowed_ip)) {
			echo "The IP ".$peer_ip." tried to send SIP commands\n";
		}
		*/

		$this->handle_message($peer, $pkt);

		return $pkt;
	}

	/**
	 * Handles incoming SIP messages
	 *
	 * @param $peer client address to send to
	 * @param $msg the raw SIP message
	 */
	function handle_message($peer, $msg)
	{
		$pos = strpos($msg, "\r\n\r\n");
		$sip_msg = trim(substr($msg, 0, $pos));
		$body = trim(substr($msg, $pos));

		$tmp = explode("\r\n", $sip_msg, 2);
		$mime = mimeParseHeader($tmp[1]);

		$tmp = explode(' ', $tmp[0]);	//INVITE sip:xxx@10.10.10.240 SIP/2.0
		$status = $tmp[0];

		switch ($status) {
			case 'INVITE':		$sip_type = SIP_INVITE; break;
			case 'ACK':			$sip_type = SIP_ACK; break;
			case 'BYE':			$sip_type = SIP_BYE; break;
			case 'OPTIONS':		$sip_type = SIP_OPTIONS; break;
			case 'CANCEL':		$sip_type = SIP_CANCEL; break;
			case 'REGISTER':	$sip_type = SIP_REGISTER; break;
			default:
				echo "Unknown SIP command: ".$status."\n";
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
				if ($mime['Content-Type'] == 'application/sdp') {
					echo "Forwarding SIP media streams to IP: ".$this->dst_ip."\n";
					$res_sdp = generate_sdp($body, $this->dst_ip);
					$res = $this->send_message(SIP_OK, $peer, $mime, $res_sdp);

					echo "SDP FROM CALLER:\n".$body."\n\n";
					echo "SDP TO CALLER & STREAMING SERVER:\n".$res_sdp."\n";

					$sdp_file = '/tmp/sip_tmp.sdp';
					file_put_contents($sdp_file, $res_sdp);

					//NOTE: playback live stream locally with VLC:
					//exec('/home/ml/scripts/compile/vlc-git/vlc -vvv '.$sdp_file);
					exec('ffplay '.$sdp_file);

					//XXX dump rtp stream (command not working!!!)
					//exec('ffmpeg -i '.$sdp_file.' -vcodec copy /tmp/out.263');

				} else {
					echo "Error: DIDNT GET SDP FROM CLIENT INVITE MSG\n";
					//FIXME how to handle. hangup?
				}
				break;

			case SIP_BYE:
				echo "Recieved SIP BYE from ".$peer."\n";

				//Send "200 OK" response
				$res = $this->send_message(SIP_OK, $peer, $mime);
				break;

			case SIP_ACK:
				//echo "Recieved SIP ACK from ".$peer."\n";
				//we dont send a response on this message
				break;

			case SIP_OPTIONS:
				echo "Recieved SIP OPTIONS from ".$peer."\n";
				//FIXME more parameters should be set in the OK response (???)
				$res = $this->send_message(SIP_OK, $peer, $mime);
				break;

			//We are acting a SIP Registrar
			case SIP_REGISTER:
				echo "Recieved SIP REGISTER from ".$peer."\n";

				if (empty($mime['Authorization'])) {
					echo "sending auth req!\n";
					//FIXME sip bindings (hur ser dom ut?!) RFC 3261: 10.3
					$res = $this->send_message(SIP_UNAUTHORIZED, $peer, $mime);
				} else {
					$pos = strpos($mime['Authorization'], ' ');
					$auth_type = substr($mime['Authorization'], 0, $pos);
					$auth_response = substr($mime['Authorization'], $pos+1);
					if ($auth_type != "Digest") {
						//FIXME: send error code!
					} else {
						//RFC 2617: "3.2.2.1 Request-Digest":
						$chal = parseAuthRequest($auth_response);

						$a1 = "user".':'.$this->auth_realm.':'."pass";
						$a2 = "REGISTER".':'.$chal['uri'];
						$response = md5( md5($a1).':'.$chal['nonce'].':'.md5($a2) );

						if ($chal['algorithm'] != "MD5" ||
							$chal['realm'] != $this->auth_realm ||
							$chal['nonce'] != $this->get_nonce($peer) ||
							$chal['opaque'] != md5("iam opaque!") ||
							$chal['response'] != $response)
						{
							echo "FAIL!\n";
							//FIXME: send error code!
						} else {
							//FIXME: ska man skicka nåt extra för "auth successful" eller räcker det med en OK ?
							echo "sending OK!\n";
							$res = $this->send_message(SIP_OK, $peer, $mime);
						}
					}
				}
				break;

			case SIP_CANCEL:
				echo "Recieved SIP CANCEL from ".$peer."\n";
				//FIXME vi ska svara!!!
				break;

			default:
				echo "Unknown SIP message type\n";
		}
	}

	/**
	 * Creates a guaranteed unique nonce for the client auth challenge.
	 * If the client previously had a nonce, it will be replaced.
	 */
	function allocate_nonce($peer, $key)
	{
		$nonce = md5(microtime().':'.$key.':'.mt_rand(0, 9999999999999));

		//FIXME verify that genrated nonce is not already in use
		$this->nonce_arr[$peer] = $nonce;

		return $nonce;
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
		"Contact: \"core_dev\" <sip:core_dev@".$this->host.":".$this->port.">\r\n".	//XXX core_dev here??
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
    		" opaque=\"".md5("iam opaque!")."\",".
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
