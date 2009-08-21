<?php
/**
 * $Id$
 *
 * Helper functions to parse SDP packets
 *
 * @author Martin Lindhe, 2007-2008
 */


/**
 * Parses SDP message & returns it as a easy-to-handle array
 *
 * @param $raw_sdp unparsed sdp header
 * @return array with parsed sdp elements
 */
function parse_sdp($raw_sdp)
{
	$sdp = explode("\r\n", $raw_sdp);

	$result = array();

	foreach ($sdp as $row) {
		$pos = strpos($row, '=');
		$key = substr($row, 0, $pos);
		$val = substr($row, $pos+1);
		//echo "key = '".$key."', val = '".$val."'\n";

		switch ($key) {
			//Session description
			case 'v': break;			//SDP version
			case 'o': $result['origin'] = $val; break;		//Origin (the full string is used as unique session identifier)
			case 's': break;			//Session name
			case 'i': break;			//?
			case 'c':	//c=<nettype> <addrtype> <connection-address>
				$conn = explode(" ", $val);
				$result['ip'] = $conn[2];
				break;

			case 'b': break;			//Bandwidth

			//Time description
			case 't': break;			//Time active

			//Media description
			case 'm':
				$media = explode(" ", $val);
				$current_media = $media[0];
				$params = @trim($media[3].' '.$media[4].' '.$media[5].' '.$media[6].' '.$media[7].' '.$media[8].' '.$media[9]);
				//echo 'MEDIA TYPE '.$media[0]." AT PORT ".$media[1]." protocol ".$media[2]." params ".$params."\n";
				$result[ $current_media ]['ip'] = $result['ip'];
				$result[ $current_media ]['port'] = $media[1];
				$result[ $current_media ]['protocol'] = $media[2];
				$result[ $current_media ]['params'] = explode(' ', $params);
				break;

			case 'a': //Media attributes
				$words = explode(" ", $val);
				$pos = strpos($words[0], ':');
				$attribute = substr($words[0], 0, $pos);
				$value = substr($words[0], $pos+1);
				$params = @trim($words[1].' '.$words[2].' '.$words[3].' '.$words[4].' '.$words[5].' '.$words[6].' '.$words[7]);
				//echo "MEDIA ATTRIBUTE: ".$attribute." = '".$value."'\n";

				switch ($attribute) {
					case 'rtpmap':	//a=rtpmap:<payload type> <encoding name>/<clock rate> [/<encoding parameters>]
						//echo "rtpmap ".$value.", encodingname = ".$words[1]."\n";	//FIXME vi ignorerar om det finns några <encoding parameters>. de används ej av PSE-MS
						$result[ $current_media ][ $value ]['encoding'] = $words[1];
						break;

					case 'fmtp':	//a=fmtp:<format> <format specific parameters>
						//echo "fmtp ".$value.", params = ".$params."\n";
						$result[ $current_media ][ $value ]['params'] = $params;
						break;

					case 'encryption': break;
					case 'x-mpdp': break;		//mirial extension?

					default:
						echo "Unknown SDP media attribute: ".$attribute." = '".$value."'\n";
						break;
				}
				break;

			default:
				echo "UNKNOWN SDP field ".$key."\n";
				break;
		}
	}

	return $result;
}

?>
