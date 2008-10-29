<?php
/**
 * $Id$
 *
 * Utility functions for working with phone numbers
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

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
	switch ($res['country']) {
		case '20':	//Egypt
		case '27':	//South Africa
		case '30':	//Greece
		case '31':	//Netherlands
		case '32':	//Belgium
		case '33':	//France
		case '34':	//Spain, Ceuta and Melilla, Canary Islands
		case '36':	//Hungary
		case '39':	//Italy, Vatican City #2
		case '40':	//Romania
		case '41':	//Switzerland
		case '43':	//Austria
		case '44':	//United Kingdom, Guernsey, Isle of Man, Jersey
		case '45':	//Denmark
		case '46':	//Sweden
		case '47':	//Norway, Svalbard and Jan Mayen
		case '48':	//Poland
		case '49':	//Germany
		case '51':	//Peru
		case '52':	//Mexico
		case '53':	//Cuba
		case '54':	//Argentina
		case '55':	//Brazil
		case '56':	//Chile
		case '57':	//Colombia
		case '58':	//Venezuela
		case '60':	//Malaysia
		case '61':	//Australia, Christmas Island, Cocos Islands
		case '62':	//Indonesia
		case '63':	//Philippines
		case '64':	//New Zealand
		case '65':	//Singapore
		case '66':	//Thailand
		case '81':	//Japan
		case '82':	//South Korea
		case '84':	//Vietnam
		case '86':	//People's Republic of China
		case '90':	//Turkey, Turkish Republic of Northern Cyprus
		case '91':	//India
		case '92':	//Pakistan
		case '93':	//Afghanistan
		case '94':	//Sri Lanka
		case '95':	//Burma
		case '98':	//Iran
			return $res;
	}

	//3-digit country codes
	$res['country'] = substr($anr, 0, 3);
	$res['anr'] = substr($anr, 3);
	switch ($res['country']) {
		case '212':	//Morocco & Western Sahara
		case '213':	//Algeria
		case '216':	//Tunisia
		case '218':	//Libya
		case '220':	//The Gambia
		case '221':	//Senegal
		case '222':	//Mauritania
		case '223':	//Mali
		case '224':	//Guinea
		case '225':	//Côte d'Ivoire
		case '226':	//Burkina Faso
		case '227':	//Niger
		case '228':	//Togo
		case '229':	//Benin
		case '230':	//Mauritius
		case '231':	//Liberia
		case '232':	//Sierra Leone
		case '233':	//Ghana
		case '234':	//Nigeria
		case '235':	//Chad
		case '236':	//Central African Republic
		case '237':	//Cameroon
		case '238':	//Cape Verde
		case '239':	//São Tomé and Príncipe
		case '240':	//Equatorial Guinea
		case '241':	//Gabon
		case '242':	//Republic of Congo
		case '243':	//Democratic Republic of Congo
		case '244':	//Angola
		case '245':	//Guinea-Bissau
		case '246':	//British Indian Ocean Territory
		case '247':	//Ascension Island
		case '248':	//Seychelles
		case '249':	//Sudan
		case '250':	//Rwanda
		case '251':	//Ethiopia
		case '252':	//Somalia, Somaliland
		case '253':	//Djibouti
		case '254':	//Kenya
		case '255':	//Tanzania
		case '256':	//Uganda
		case '257':	//Burundi
		case '258':	//Mozambique
		case '260':	//Zambia
		case '261':	//Madagascar
		case '262':	//Réunion, Mayotte
		case '263':	//Zimbabwe
		case '264':	//Namibia
		case '265':	//Malawi
		case '266':	//Lesotho
		case '267':	//Botswana
		case '268':	//Swaziland
		case '269':	//Comoros
		case '290':	//Saint Helena, Tristan da Cunha
		case '291':	//Eritrea
		case '297':	//Aruba
		case '298':	//Faroe Islands
		case '299':	//Greenland
		case '350':	//Gibraltar
		case '351':	//Portugal
		case '352':	//Luxembourg
		case '353':	//Republic of Ireland
		case '354':	//Iceland
		case '355':	//Albania
		case '356':	//Malta
		case '357':	//Cyprus
		case '358':	//Finland, Aland Islands
		case '359':	//Bulgaria
		case '370':	//Lithuania
		case '371':	//Latvia
		case '372':	//Estonia
		case '373':	//Moldova
		case '374':	//Armenia, Nagorno-Karabakh #1
		case '375':	//Belarus
		case '376':	//Andorra
		case '377':	//Monaco
		case '378':	//San Marino
		case '379':	//Vatican City #1
		case '380':	//Ukraine
		case '381':	//Serbia
		case '382':	//Montenegro
		case '385':	//Croatia
		case '386':	//Slovenia
		case '387':	//Bosnia and Herzegovina
		case '388':	//http://en.wikipedia.org/wiki/European_Telephony_Numbering_Space
		case '389':	//Republic of Macedonia
		case '420':	//Czech Republic
		case '421':	//Slovakia
		case '423':	//Liechtenstein
		case '500':	//Falkland Islands
		case '501':	//Belize
		case '502':	//Guatemala
		case '503':	//El Salvador
		case '504':	//Honduras
		case '505':	//Nicaragua
		case '506':	//Costa Rica
		case '507':	//Panama
		case '508':	//St. Pierre and Miquelon
		case '509':	//Haiti
		case '590':	//Guadeloupe
		case '591':	//Bolivia
		case '592':	//Guyana
		case '593':	//Ecuador
		case '594':	//French Guiana
		case '595':	//Paraguay
		case '596':	//Martinique
		case '597':	//Suriname
		case '598':	//Uruguay
		case '599':	//Netherlands Antilles
		case '670':	//East Timor
		case '672':	//Norfolk Island, Australian Antarctic Territory
		case '673':	//Brunei Darussalam
		case '674':	//Nauru
		case '675':	//Papua New Guinea
		case '676':	//Tonga
		case '677':	//Solomon Islands
		case '678':	//Vanuatu
		case '679':	//Fiji
		case '680':	//Palau
		case '681':	//Wallis and Futuna
		case '682':	//Cook Islands
		case '683':	//Niue
		case '685':	//Samoa
		case '686':	//Kiribati
		case '687':	//New Caledonia
		case '688':	//Tuvalu
		case '689':	//French Polynesia
		case '690':	//Tokelau
		case '691':	//Federated States of Micronesia
		case '692':	//Marshall Islands
		case '800':	//http://en.wikipedia.org/wiki/Universal_international_freephone_number
		case '808':	//http://en.wikipedia.org/wiki/Shared_Cost_Service
		case '850':	//North Korea
		case '852':	//Hong Kong
		case '853':	//Macau
		case '855':	//Cambodia
		case '856':	//Laos
		case '870':	//http://en.wikipedia.org/wiki/Inmarsat
		case '871':	//http://en.wikipedia.org/wiki/Inmarsat
		case '872':	//http://en.wikipedia.org/wiki/Inmarsat, Pitcairn Islands
		case '873':	//http://en.wikipedia.org/wiki/Inmarsat
		case '874':	//http://en.wikipedia.org/wiki/Inmarsat
		case '878':	//http://en.wikipedia.org/wiki/Universal_Personal_Telecommunications
		case '880':	//Bangladesh
		case '881':	//http://en.wikipedia.org/wiki/Global_Mobile_Satellite_System
		case '882':	//http://en.wikipedia.org/wiki/International_Networks_(country_code)
		case '883':	//"International National Rate Service"
		case '886':	//Republic of China (Taiwan)
		case '888':	//http://en.wikipedia.org/wiki/OCHA
		case '960':	//Maldives
		case '961':	//Lebanon
		case '962':	//Jordan
		case '963':	//Syria
		case '964':	//Iraq
		case '965':	//Kuwait
		case '966':	//Saudi Arabia
		case '967':	//Yemen
		case '968':	//Oman
		case '970':	//Palestinian Authority #1
		case '971':	//United Arab Emirates
		case '972':	//Israel, Palestinian Authority #2
		case '973':	//Bahrain
		case '974':	//Qatar
		case '975':	//Bhutan
		case '976':	//Mongolia
		case '977':	//Nepal
		case '979':	//http://en.wikipedia.org/wiki/International_premium_rate_service
		case '991':	//http://en.wikipedia.org/wiki/ITPCS
		case '992':	//Tajikistan
		case '993':	//Turkmenistan
		case '994':	//Azerbaijan, Nagorno-Karabakh #2
		case '995':	//Georgia
		case '996':	//Kyrgyzstan
		case '998':	//Uzbekistan
			return $res;
	}

	//XXX This should not be able to happen. All existing country codes are handled above
	echo "Unknown country code: ".$anr."\n";
	return $res;
}

?>
