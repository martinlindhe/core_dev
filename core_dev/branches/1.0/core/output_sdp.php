<?php
/**
 * $Id$
 *
 * Helper functions to generate SDP packets
 *
 * @author Martin Lindhe, 2007-2009
 */

require_once('input_sdp.php');
require_once('functions_time.php');	//for ntptime()


//SDP media stream types
define('SDP_VIDEO', 1);
define('SDP_AUDIO', 2);

//Recognized "RTP/AVP" payload types
$rtpavp_payload['video'] = array(
	'H264/90000'      => 20,  //VLC & ffmpeg handles H264 over RTP well
	//'H263/90000'      => 10,  //TESTED: ffmpeg visar perfekt bild med lagg vid 7fps. this is the format that comes from the 3GGW
	'H263-1998/90000' => 9,   //ffplay visar en svart bild, verkar ej ta emot videoframes
	//'H261/90000'      => 8    //TESTED: ffmpeg does not support H.261 packetization (image shows but distorted!)
);

$rtpavp_payload['audio'] = array(
	'PCMA/8000'       => 11,  //VLC & ffmpeg handles PCMA over RTP well. this is the format that comes from the 3GGW
	'PCMU/8000'       => 10,  //VLC & ffmpeg handles PCMU over RTP well
	//'G723/8000'       => 2,   //ffmpeg: [sdp @ 0x79cb60]Could not find codec parameters (Audio: 0x0000)
	//'G7221/32000'     => 1    //ffmpeg: [sdp @ 0x79cb60]Could not find codec parameters (Audio: 0x0000) (VLC also dont support it)
);

//lists static payloads. these should not need "a=rtpmap" generated. See RFC 3551
//Note: H264 NEEDS a=rtpmap to be set for ffmpeg to work correctly
$rtpavp_payload['static'] = array(
	0,	//PCMU audio
	8	//PCMA audio
);


/**
 * Takes raw SDP header and use it to generate a .sdp file describing the RTP stream
 * Primary use is to tell the caller to send RTP data to $dst_ip
 *
 * @param $raw_sdp unparsed sdp header
 * @param $dst_ip ip address to tell the client to send media streams to
 * @param $port specifies lower port number to use. this +3 ports will be allocated
 * @return generated sdp header to use as response for $raw_sdp
 */
function generate_sdp($raw_sdp, $dst_ip, $port)
{
	$sdp_arr = parse_sdp($raw_sdp);

	//Session description
	$sdp =
	"v=0\r\n".
	"o=- ".ntptime()." 0 IN IP4 ".$sdp_arr['ip']."\r\n".	//Origin (this string is used as a "session identifier")
	"s=core_dev\r\n".										//XXX core_dev version
	"c=IN IP4 ".$dst_ip."\r\n".								//Connection data (send RTP data to this IP)
	//Time description
	"t=0 0\r\n";	//Time active. "0 0" means the session is regarded as permanent

	if (!empty($sdp_arr['video'])) {
		$sdp .= generate_sdp_media_tag(SDP_VIDEO, $sdp_arr['video'], $port);
		$port += 2;
	}
	if (!empty($sdp_arr['audio'])) {
		$sdp .= generate_sdp_media_tag(SDP_AUDIO, $sdp_arr['audio'], $port);
	}

	return $sdp;
}

/**
 * Generates a sdp media tag
 *
 * @param $type SDP_VIDEO or SDP_AUDIO
 * @param $sdp_media data describing the media formats of specified media type
 * @return a sdp media tag
 */
function generate_sdp_media_tag($type, $sdp_media, $port)
{
	global $rtpavp_payload;

	switch ($type) {
		case SDP_VIDEO:
			$m_tag = 'video';
			$codecs = $rtpavp_payload['video'];
			break;
		case SDP_AUDIO:
			$m_tag = 'audio';
			$codecs = $rtpavp_payload['audio'];
			break;
		default:
			echo "generate_sdp_media_tag() unknown type: ".$type."\n";
			return false;
	}

	echo "DEBUG: Using port ".$port." for ".$m_tag."\n";

	//Media description
	$prio = 0;
	$sdp = '';

	foreach ($sdp_media['params'] as $key => $param_id) {
		if (array_key_exists($sdp_media[$param_id]['encoding'], $codecs)) {
			if ($codecs[ $sdp_media[$param_id]['encoding'] ] <= $prio)
				continue;

			$prio = $codecs[ $sdp_media[$param_id]['encoding'] ];

			$sdp = "m=".$m_tag." ".$port." ".$sdp_media['protocol']." ".$param_id."\r\n";

			//skip a=rtpmap for standard payload types who should not need it
			if (!in_array($param_id, $rtpavp_payload['static'])) {
				$sdp .= "a=rtpmap:".$param_id." ".$sdp_media[$param_id]['encoding']."\r\n";
			}

			if (!empty($sdp_media[$param_id]['params'])) {
				//Maps RTP payload-specific parameters to the generated .sdp (not sure if this is nessecary)
				$sdp .= "a=fmtp:".$param_id." ".$sdp_media[$param_id]['params']."\r\n";
			}
		} else {
			if ($sdp_media[$param_id]['encoding'] != 'telephone-event/8000') {
				echo "Unrecognized ".$m_tag." format: ".$sdp_media[$param_id]['encoding']."\n";
			}
		}
	}
	return $sdp;
}

?>
