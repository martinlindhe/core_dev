<?php
/**
 * $Id$
 *
 * Basic SIP client/server implementation
 *
 * SIP RFC: http://www.ietf.org/rfc/rfc3261.txt
 * http://tools.ietf.org/id/draft-ietf-sip-call-flows-05.txt
 *
 * @author Martin Lindhe, 2007-2009
 */

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
		$sip['sdp'] = trim(substr($msg, $pos));

		$sip_msg = explode("\r\n", $sip_msg);

		$sip_type = SIP_UNKNOWN;
		$sdp_present = false;
		$auth_req = false;

//XXX maybe reuse mime header parse code
		foreach ($sip_msg as $row) {
			$cmd = explode(' ', $row);
			$params = @trim($cmd[1].' '.$cmd[2].' '.$cmd[3].' '.$cmd[4].' '.$cmd[5].' '.$cmd[6]);
			switch ($cmd[0]) {
				case 'INVITE':		$sip_type = SIP_INVITE; break;
				case 'ACK':			$sip_type = SIP_ACK; break;
				case 'BYE':			$sip_type = SIP_BYE; break;
				case 'OPTIONS':		$sip_type = SIP_OPTIONS; break;
				case 'CANCEL':		$sip_type = SIP_CANCEL; break;
				case 'REGISTER':	$sip_type = SIP_REGISTER; break;

				case 'Via:':	//Contains IP & port which we should send response to
					$sip['via'] = $params;
					break;

				case 'To:':		//Display name of called person (me)
					$sip['to'] = $params;
					$sip['to'] = str_replace('<', '', $sip['to']);
					$sip['to'] = str_replace('>', '', $sip['to']);
					break;

				case 'From:':	//Display name of the caller
					$sip['from'] = $params;
					break;

				case 'Call-ID:':
					$sip['callid'] = $params;
					break;

				case 'CSeq:':
					$sip['cseq'] = $params;
					break;

				case 'Subject:': break;
				case 'Contact:': break;
				case 'User-Agent:': break;
				case 'Max-Forwards:': break;
				case 'Allow:': break;
				case 'Expires:': break;
				case 'Supported:': break;
				case 'Content-Length:': break;
				case 'Authorization:': $auth_req = true; break;

				case 'Content-Type:':
					if ($params == 'application/sdp') $sdp_present = true;
					break;

				default:
					echo "Unknown SIP header: ".$cmd[0]." ".$params."\n";
					echo "full SIP message follows:\n";
					print_r($sip_msg);
					echo "\n-----------\n";
					break;
			}
		}

		switch ($sip_type) {
			case SIP_INVITE:
				//echo "Recieved SIP INVITE from ".$peer."\n";

				//Send "100 TRYING" response
				$res = $this->send_message(SIP_TRYING, $peer, $sip);

				//Send "180 RINGING" response
				$res = $this->send_message(SIP_RINGING, $peer, $sip);

				//Send "200 OK" response
				if ($sdp_present) {
					echo "Forwarding SIP media streams to IP: ".$this->dst_ip."\n";
					$res_sdp = generate_sdp($sip['sdp'], $this->dst_ip);
					$res = $this->send_message(SIP_OK, $peer, $sip, $res_sdp);

					echo "SDP FROM CALLER:\n".$sip['sdp']."\n\n";
					echo "SDP TO CALLER & STREAMING SERVER:\n".$res_sdp."\n";

					$sdp_file = '/tmp/sip_tmp.sdp';
					file_put_contents($sdp_file, $res_sdp);

					//NOTE: playback live stream locally with VLC:
					//exec('/home/ml/scripts/compile/vlc-git/vlc -vvv '.$sdp_file);
					//exec('ffplay '.$sdp_file);

				} else {
					echo "Error: DIDNT GET SDP FROM CLIENT INVITE MSG\n";
					//FIXME how to handle. hangup?
				}
				break;

			case SIP_BYE:
				echo "Recieved SIP BYE from ".$peer."\n";

				//Send "200 OK" response
				$res = $this->send_message(SIP_OK, $peer, $sip);
				break;

			case SIP_ACK:
				//echo "Recieved SIP ACK from ".$peer."\n";
				//we dont send a response on this message
				break;

			case SIP_OPTIONS:
				echo "Recieved SIP OPTIONS from ".$peer."\n";
				//FIXME more parameters should be set in the OK response (???)
				$res = $this->send_message(SIP_OK, $peer, $sip);
				break;

			//We are acting a SIP Registrar
			case SIP_REGISTER:
				echo "Recieved SIP REGISTER from ".$peer."\n";
				d($sip_msg);

				if (!$auth_req) {
					echo "sending auth req!\n";
					//FIXME sip bindings (hur ser dom ut?!) RFC 3261: 10.3
					$res = $this->send_message(SIP_UNAUTHORIZED, $peer, $sip);
				} else {
					echo "sending OK!\n";
					$res = $this->send_message(SIP_OK, $peer, $sip);
				}
				break;

			case SIP_CANCEL:
				echo "Recieved SIP CANCEL from ".$peer."\n";
				//FIXME ska vi svara????
				break;

			default:
				echo "Unknown SIP message type\n";
		}
	}

	/**
	 * Generates a SIP message
	 *
	 * @param $type type of sip message to generat
	 * @param $peer client address to send to
	 * @param $prev array of values from previous recieved SIP message
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
		"From: ".$prev['from']."\r\n".		//append "epid="
		"Call-ID: ".$prev['callid']."\r\n".	//echo
		"CSeq: ".$prev['cseq']."\r\n".		//echo
		"Via: ".$prev['via']."\r\n".		//append "recieved="
		"To: <".$prev['to'].">\r\n".		//append "tag="
		"Allow: MESSAGE, INVITE, CANCEL, ACK, OPTIONS, SUBSCRIBE, NOTIFY, BYE\r\n".	//XXX "MESSAGE" ?
		"Contact: \"core_dev\" <sip:core_dev@".$this->host.":".$this->port.">\r\n".	//XXX core_dev here??
		"User-Agent: core_dev\r\n";			//XXX core_dev version

		if ($type == SIP_UNAUTHORIZED) {
			$res .=
			"WWW-Authenticate: Digest".
			" realm=\"MCI WorldCom SIP\",".
    		" domain=\"sip:ss2.wcom.com\",".
			" nonce=\"ea9c8e88df84f1cec4341ae6cbe5a359\",".
    		" opaque=\"\",".
			" stale=FALSE,".
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
