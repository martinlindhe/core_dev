<?php
/**
 * $Id$
 *
 * Functions for reading and writing of vCard's
 *
 * http://en.wikipedia.org/wiki/Vcard
 *
 * See also output_ical.php
 */

//STATUS: INCOMPLETE DRAFT


//TODO: parse vcard from the web, example http://www.agigen.se/agigen.vcf

//TODO: support multiple input & output formats for contact information

function renderVcard()
{
	$ln = "\n";

	//XXX mime type is "text/x-vcard"

	$res =
	'BEGIN:VCARD'.$ln.
	'VERSION:3.0'.$ln.
	'N:Efternamn;Förnamn'.$ln.
	'FN:Förnamn Efternamn'.$ln.    //  FN;LANGUAGE=en;CHARSET=UTF-8:Agigen Ltd.
	'ORG:Organisations namn'.$ln.  //  ORG;CHARSET=UTF-8:Agigen Ltd.
	//'TITLE:Mister masterman'.$ln. //-optional?
	'TEL:+46-733-445315'.$ln.
	'ADR:;;Kungsgatan 56;Stockholm;;11122;Sweden'.$ln.  //ADR;LANGUAGE=en;CHARSET=UTF-8:;;Kungsgatan 56;Stockholm;;11122;Sweden
	'EMAIL:hello@agigen.se'.$ln.

	'PHOTO:http://xxx'.$ln. /// ???? tar den en url?
	'LOGO:http://XXX'.$ln. /// ??? 	an image or graphic of the logo of the organization that is associated with the individual to which the vCard belongs
	'BDAY:xxx'.$ln. //XXX date of birth of the individual associated with the vCard
	'TZ:xxx'.$ln. //XXX information related to the standard time zone of the vCard object
	'GEO:xxx'.$ln. //XXX 	The property specifies a latitude and longitude

	'REV:20080424T195243Z'.$ln. //XX 	combination of the calendar date and time of day of the last update to the vCard object
	'URL:xxx'.$ln. //XXX en länk som returnerar DETTA vcard (senaste versionen)
	'UID:xxx'.$ln. //XXX 	specifies a value that represents a persistent, globally unique identifier associated with the object
	'KEY:xxx'.$ln. //XXX  	the public encryption key associated with the vCard object

	//'SOURCE:http://www.agigen.se/'.$ln.
	//'NAME:Agigen Ltd.'.$ln.

	'X-MSN:xxx'.$ln.
	'X-SKYPE-USERNAME:xxx'.$ln.

	'END:VCARD'.$ln;
	return $res;
}

?>
