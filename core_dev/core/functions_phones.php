<?php
/**
 * $Id$
 *
 * Utility functions for working with phone numbers
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

$e164_cc[1] = 'USA, Canada';
$e164_cc[7] = 'Russia';	//& Kazakhstan
$e164_cc[20] = 'Egypt';
$e164_cc[27] = 'South Africa';
$e164_cc[30] = 'Greece';
$e164_cc[31] = 'Netherlands';
$e164_cc[32] = 'Belgium';
$e164_cc[33] = 'France';
$e164_cc[34] = 'Spain';	//& Ceuta and Melilla, Canary Islands
$e164_cc[36] = 'Hungary';
$e164_cc[39] = 'Italy';	//& Vatican City #2
$e164_cc[40] = 'Romania';
$e164_cc[41] = 'Switzerland';
$e164_cc[43] = 'Austria';
$e164_cc[44] = 'United Kingdom';	//& Guernsey, Isle of Man, Jersey
$e164_cc[45] = 'Denmark';
$e164_cc[46] = 'Sweden';
$e164_cc[47] = 'Norway';	//& Svalbard and Jan Mayen
$e164_cc[48] = 'Poland';
$e164_cc[49] = 'Germany';
$e164_cc[51] = 'Peru';
$e164_cc[52] = 'Mexico';
$e164_cc[53] = 'Cuba';
$e164_cc[54] = 'Argentina';
$e164_cc[55] = 'Brazil';
$e164_cc[56] = 'Chile';
$e164_cc[57] = 'Colombia';
$e164_cc[58] = 'Venezuela';
$e164_cc[60] = 'Malaysia';
$e164_cc[61] = 'Australia';	//& Christmas Island, Cocos Islands
$e164_cc[62] = 'Indonesia';
$e164_cc[63] = 'Philippines';
$e164_cc[64] = 'New Zealand';
$e164_cc[65] = 'Singapore';
$e164_cc[66] = 'Thailand';
$e164_cc[81] = 'Japan';
$e164_cc[82] = 'South Korea';
$e164_cc[84] = 'Vietnam';
$e164_cc[86] = "People's Republic of China";
$e164_cc[90] = 'Turkey';	//& Turkish Republic of Northern Cyprus
$e164_cc[91] = 'India';
$e164_cc[92] = 'Pakistan';
$e164_cc[93] = 'Afghanistan';
$e164_cc[94] = 'Sri Lanka';
$e164_cc[95] = 'Burma';
$e164_cc[98] = 'Iran';
$e164_cc[212] = 'Morocco';	//& Western Sahara
$e164_cc[213] = 'Algeria';
$e164_cc[216] = 'Tunisia';
$e164_cc[218] = 'Libya';
$e164_cc[220] = 'The Gambia';
$e164_cc[221] = 'Senegal';
$e164_cc[222] = 'Mauritania';
$e164_cc[223] = 'Mali';
$e164_cc[224] = 'Guinea';
$e164_cc[225] = "Côte d'Ivoire";
$e164_cc[226] = 'Burkina Faso';
$e164_cc[227] = 'Niger';
$e164_cc[228] = 'Togo';
$e164_cc[229] = 'Benin';
$e164_cc[230] = 'Mauritius';
$e164_cc[231] = 'Liberia';
$e164_cc[232] = 'Sierra Leone';
$e164_cc[233] = 'Ghana';
$e164_cc[234] = 'Nigeria';
$e164_cc[235] = 'Chad';
$e164_cc[236] = 'Central African Republic';
$e164_cc[237] = 'Cameroon';
$e164_cc[238] = 'Cape Verde';
$e164_cc[239] = 'São Tomé and Príncipe';
$e164_cc[240] = 'Equatorial Guinea';
$e164_cc[241] = 'Gabon';
$e164_cc[242] = 'Republic of Congo';
$e164_cc[243] = 'Democratic Republic of Congo';
$e164_cc[244] = 'Angola';
$e164_cc[245] = 'Guinea-Bissau';
$e164_cc[246] = 'British Indian Ocean Territory';
$e164_cc[247] = 'Ascension Island';
$e164_cc[248] = 'Seychelles';
$e164_cc[249] = 'Sudan';
$e164_cc[250] = 'Rwanda';
$e164_cc[251] = 'Ethiopia';
$e164_cc[252] = 'Somalia, Somaliland';
$e164_cc[253] = 'Djibouti';
$e164_cc[254] = 'Kenya';
$e164_cc[255] = 'Tanzania';
$e164_cc[256] = 'Uganda';
$e164_cc[257] = 'Burundi';
$e164_cc[258] = 'Mozambique';
$e164_cc[260] = 'Zambia';
$e164_cc[261] = 'Madagascar';
$e164_cc[262] = 'Réunion, Mayotte';
$e164_cc[263] = 'Zimbabwe';
$e164_cc[264] = 'Namibia';
$e164_cc[265] = 'Malawi';
$e164_cc[266] = 'Lesotho';
$e164_cc[267] = 'Botswana';
$e164_cc[268] = 'Swaziland';
$e164_cc[269] = 'Comoros';
$e164_cc[290] = 'Saint Helena, Tristan da Cunha';
$e164_cc[291] = 'Eritrea';
$e164_cc[297] = 'Aruba';
$e164_cc[298] = 'Faroe Islands';
$e164_cc[299] = 'Greenland';
$e164_cc[350] = 'Gibraltar';
$e164_cc[351] = 'Portugal';
$e164_cc[352] = 'Luxembourg';
$e164_cc[353] = 'Republic of Ireland';
$e164_cc[354] = 'Iceland';
$e164_cc[355] = 'Albania';
$e164_cc[356] = 'Malta';
$e164_cc[357] = 'Cyprus';
$e164_cc[358] = 'Finland';	//& Aland Islands
$e164_cc[359] = 'Bulgaria';
$e164_cc[370] = 'Lithuania';
$e164_cc[371] = 'Latvia';
$e164_cc[372] = 'Estonia';
$e164_cc[373] = 'Moldova';
$e164_cc[374] = 'Armenia, Nagorno-Karabakh #1';
$e164_cc[375] = 'Belarus';
$e164_cc[376] = 'Andorra';
$e164_cc[377] = 'Monaco';
$e164_cc[378] = 'San Marino';
$e164_cc[379] = 'Vatican City #1';
$e164_cc[380] = 'Ukraine';
$e164_cc[381] = 'Serbia';
$e164_cc[382] = 'Montenegro';
$e164_cc[385] = 'Croatia';
$e164_cc[386] = 'Slovenia';
$e164_cc[387] = 'Bosnia and Herzegovina';
$e164_cc[388] = 'SPECIAL';	//http://en.wikipedia.org/wiki/European_Telephony_Numbering_Space
$e164_cc[389] = 'Republic of Macedonia';
$e164_cc[420] = 'Czech Republic';
$e164_cc[421] = 'Slovakia';
$e164_cc[423] = 'Liechtenstein';
$e164_cc[500] = 'Falkland Islands';
$e164_cc[501] = 'Belize';
$e164_cc[502] = 'Guatemala';
$e164_cc[503] = 'El Salvador';
$e164_cc[504] = 'Honduras';
$e164_cc[505] = 'Nicaragua';
$e164_cc[506] = 'Costa Rica';
$e164_cc[507] = 'Panama';
$e164_cc[508] = 'St. Pierre and Miquelon';
$e164_cc[509] = 'Haiti';
$e164_cc[590] = 'Guadeloupe';
$e164_cc[591] = 'Bolivia';
$e164_cc[592] = 'Guyana';
$e164_cc[593] = 'Ecuador';
$e164_cc[594] = 'French Guiana';
$e164_cc[595] = 'Paraguay';
$e164_cc[596] = 'Martinique';
$e164_cc[597] = 'Suriname';
$e164_cc[598] = 'Uruguay';
$e164_cc[599] = 'Netherlands Antilles';
$e164_cc[670] = 'East Timor';
$e164_cc[672] = 'Norfolk Island';	//& Australian Antarctic Territory
$e164_cc[673] = 'Brunei Darussalam';
$e164_cc[674] = 'Nauru';
$e164_cc[675] = 'Papua New Guinea';
$e164_cc[676] = 'Tonga';
$e164_cc[677] = 'Solomon Islands';
$e164_cc[678] = 'Vanuatu';
$e164_cc[679] = 'Fiji';
$e164_cc[680] = 'Palau';
$e164_cc[681] = 'Wallis and Futuna';
$e164_cc[682] = 'Cook Islands';
$e164_cc[683] = 'Niue';
$e164_cc[685] = 'Samoa';
$e164_cc[686] = 'Kiribati';
$e164_cc[687] = 'New Caledonia';
$e164_cc[688] = 'Tuvalu';
$e164_cc[689] = 'French Polynesia';
$e164_cc[690] = 'Tokelau';
$e164_cc[691] = 'Federated States of Micronesia';
$e164_cc[692] = 'Marshall Islands';
$e164_cc[800] = 'SPECIAL';	//http://en.wikipedia.org/wiki/Universal_international_freephone_number
$e164_cc[808] = 'SPECIAL';	//http://en.wikipedia.org/wiki/Shared_Cost_Service
$e164_cc[850] = 'North Korea';
$e164_cc[852] = 'Hong Kong';
$e164_cc[853] = 'Macau';
$e164_cc[855] = 'Cambodia';
$e164_cc[856] = 'Laos';
$e164_cc[870] = 'SPECIAL';	//http://en.wikipedia.org/wiki/Inmarsat
$e164_cc[871] = 'SPECIAL';	//http://en.wikipedia.org/wiki/Inmarsat
$e164_cc[872] = 'Pitcairn Islands';	//& http://en.wikipedia.org/wiki/Inmarsat
$e164_cc[873] = 'SPECIAL';	//http://en.wikipedia.org/wiki/Inmarsat
$e164_cc[874] = 'SPECIAL';	//http://en.wikipedia.org/wiki/Inmarsat
$e164_cc[878] = 'SPECIAL';	//http://en.wikipedia.org/wiki/Universal_Personal_Telecommunications
$e164_cc[880] = 'Bangladesh';
$e164_cc[881] = 'SPECIAL';	//http://en.wikipedia.org/wiki/Global_Mobile_Satellite_System
$e164_cc[882] = 'SPECIAL';	//http://en.wikipedia.org/wiki/International_Networks_(country_code)
$e164_cc[883] = 'SPECIAL';	//"International National Rate Service"
$e164_cc[886] = 'Republic of China (Taiwan)';
$e164_cc[888] = 'SPECIAL';	//http://en.wikipedia.org/wiki/OCHA
$e164_cc[960] = 'Maldives';
$e164_cc[961] = 'Lebanon';
$e164_cc[962] = 'Jordan';
$e164_cc[963] = 'Syria';
$e164_cc[964] = 'Iraq';
$e164_cc[965] = 'Kuwait';
$e164_cc[966] = 'Saudi Arabia';
$e164_cc[967] = 'Yemen';
$e164_cc[968] = 'Oman';
$e164_cc[970] = 'Palestinian Authority #1';
$e164_cc[971] = 'United Arab Emirates';
$e164_cc[972] = 'Israel, Palestinian Authority #2';
$e164_cc[973] = 'Bahrain';
$e164_cc[974] = 'Qatar';
$e164_cc[975] = 'Bhutan';
$e164_cc[976] = 'Mongolia';
$e164_cc[977] = 'Nepal';
$e164_cc[979] = 'SPECIAL';	//http://en.wikipedia.org/wiki/International_premium_rate_service
$e164_cc[991] = 'SPECIAL';	//http://en.wikipedia.org/wiki/ITPCS
$e164_cc[992] = 'Tajikistan';
$e164_cc[993] = 'Turkmenistan';
$e164_cc[994] = 'Azerbaijan';	//& Nagorno-Karabakh #2
$e164_cc[995] = 'Georgia';
$e164_cc[996] = 'Kyrgyzstan';
$e164_cc[998] = 'Uzbekistan';

/**
 * Returns textual representation of country code
 * @param $cc E.164 country code
 */
function E164cc($cc)
{
	global $e164_cc;

	if (!empty($e164_cc[$cc])) return '+'.$cc.' ('. $e164_cc[$cc].')';
	return $cc;
}

/**
 * Separate country code (if any) from phone number
 * Defaults to swedish (+46) in case of missing country code
 *
 * Implements all country codes from:
 * http://www.itu.int/publ/T-SP-E.164D-2007/en
 * as of 2008.10.29
 *
 * See also:
 * http://en.wikipedia.org/wiki/Country_calling_code
 * http://en.wikipedia.org/wiki/E.164
 */
function parseAnr($anr)
{
	global $e164_cc;

	if (!is_numeric($anr)) {
		//SIP user address
		//FIXME users could perform call spoofing if we allow numerical usernames from sip callers
		$res['country'] = 'SIP';
		$res['anr'] = $anr;
		return $res;
	}

	if (substr($anr, 0, 1) == '0') {
		//Swedish numer without country code
		$res['country'] = '46';
		$res['anr'] = substr($anr, 1);
		return $res;
	}

	if (substr($anr, 0, 1) == '1') {
		//US, Canada
		//FIXME: identify correct country: http://en.wikipedia.org/wiki/List_of_NANP_area_codes
		$res['country'] = '1';
		$res['anr'] = substr($anr, 1);
		return $res;
	}

	if (substr($anr, 0, 1) == '7') {
		//Russia, Kazakhstan
		//FIXME: identify correct country: http://en.wikipedia.org/wiki/+7
		$res['country'] = '7';
		$res['anr'] = substr($anr, 1);
		return $res;
	}

	//2-digit country codes
	$res['country'] = substr($anr, 0, 2);
	$res['anr'] = substr($anr, 2);
	if (!empty($e164_cc[$res['country']])) return $res;

	//3-digit country codes
	$res['country'] = substr($anr, 0, 3);
	$res['anr'] = substr($anr, 3);
	if (!empty($e164_cc[$res['country']])) return $res;

	//XXX This should not be able to happen. All existing country codes are handled above
	echo "Unknown country code: ".$anr."\n";
	return $res;
}

?>
