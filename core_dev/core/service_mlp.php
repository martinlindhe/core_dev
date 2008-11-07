<?php
/**
 * $Id$
 *
 * Implementation of lookups to telia sonera's MLP
 * server for geographic positioning of their msid's
 *
 * The code has only been tested with telia but the
 * protocol is standardized so it should work with
 * other providers aswell.
 *
 * Addverbations used:
 *
 * "PG" = Programmers Guide Telia Positioning Access API V4.0.pdf
 * "slir" = Standard Location Immediate Request
 * "slia" = Standard Location Immediate Answer (PG p.14)
 * "MSID" = cellphone id (phone number)
 *
 * World Geodetic System (WGS84):
 * http://en.wikipedia.org/wiki/WGS84
 *
 * Mobile Location Protocol (MLP) 3.0.0: "LIF TS 101"
 * http://www.openmobilealliance.org/tech/affiliates/lif/lifindex.html
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */


$positioning_server = 'https://pooh.sun.telia.se:9221/LocationQueryService';
$invitations_service = 'https://pooh.sun.telia.se:9261/Service/Invitation';

/**
 * @param $msid MSID in the format 46701234567
 */
function mlpSLIR($username, $password, $msid)
{
	$service_id = $username;	//PG p.6 says "Service name": To be stated on making
								//positioning queries (serviceid). Same value as Username.

	$out =
	'<?xml version="1.0" encoding="ISO-8859-1"?>'.
	'<!DOCTYPE svc_init SYSTEM "MLP_SVC_INIT_300.DTD">'.
	'<svc_init>'.
		'<hdr>'.
			'<client>'.
				'<id>'.$username.'</id>'.
				'<pwd>'.$password.'</pwd>'.
				'<serviceid>'.$service_id.'</serviceid>'.
			'</client>'.
		'</hdr>'.
		'<slir>'.
			'<msids>'.	//up to 10 msid's can be queried in one request (PG p.12)
				'<msid>'.$msid.'</msid>'.
			'</msids>'.
			//'<prio type="HIGH"/>'.	//"NORMAL" is default (PG p.14)
		'</slir>'.
	'</svc_init>';

	return $out;
}

/**
 * Parses a SLIA
 */
function parseSLIA($xml)
{
	global $slia_arr, $tag_name, $pos_cnt;

	$msid_pos = array();
	$pos_cnt = 0;

	$parser = xml_parser_create('ISO-8859-1');
	xml_parse_into_struct($parser, $xml, $vals, $index);
	xml_parser_free($parser);

	foreach ($index as $key=>$val) {
		if ($key != "POS") continue;

		// each contiguous pair of array entries are the
		// lower and upper range for each molecule definition
		for ($i=0; $i < count($val); $i+=2) {
			$offset = $val[$i] + 1;
			$len = $val[$i + 1] - $offset;
			$msid_pos[] = parseSliaPos(array_slice($vals, $offset, $len));
		}
	}

	return $msid_pos;
}

//FIXME gör en ordentlig parser
function parseSliaPos($values)
{
	$res = array();

	foreach ($values as $val) {
		if ($val['type'] != 'complete') continue;

		switch ($val['tag']) {
				case 'MSID': $res['msid'] = $val['value']; break;
				case 'X': $res['x'] = $val['value']; break;	//XXX detta funkar bara för CircularArcArea coordinates
				case 'Y': $res['y'] = $val['value']; break;
		}
	}
	return $res;
}

?>
