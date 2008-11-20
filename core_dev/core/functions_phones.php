<?php
/**
 * $Id$
 *
 * Utility functions for working with phone numbers
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

/**
 * All country codes from:
 * http://www.itu.int/publ/T-SP-E.164D-2007/en
 * as of 2008.10.29
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
 * Swedish area codes from
 * http://www.pts.se/upload/Documents/SE/Riktnummeromraden_nrordning.pdf
 * as of 2008.10.30
 *
 * See also
 * http://sv.wikipedia.org/wiki/Lista_över_svenska_riktnummer
 */
$area_code[46][10] = 'PBX'; //Lokalt direktval för företagsväxlar
$area_code[46][11] = 'Norrköping';
$area_code[46][120] = 'Åtvidaberg';
$area_code[46][121] = 'Söderköping';
$area_code[46][122] = 'Finspång';
$area_code[46][123] = 'Valdemarsvik';
$area_code[46][125] = 'Vikbolandet';
$area_code[46][13] = 'Linköping';
$area_code[46][140] = 'Tranås';
$area_code[46][141] = 'Motala';
$area_code[46][142] = 'Mjölby-Skänninge-Boxholm';
$area_code[46][143] = 'Vadstena';
$area_code[46][144] = 'Ödeshög';
$area_code[46][150] = 'Katrineholm';
$area_code[46][151] = 'Vingåker';
$area_code[46][152] = 'Strängnäs';
$area_code[46][155] = 'Nyköping-Oxelösund';
$area_code[46][156] = 'Trosa-Vagnhärad';
$area_code[46][157] = 'Flen-Malmköping';
$area_code[46][158] = 'Gnesta';
$area_code[46][159] = 'Mariefred';
$area_code[46][16] = 'Eskilstuna-Torshälla';
$area_code[46][171] = 'Enköping';
$area_code[46][173] = 'Öregrund-Östhammar';
$area_code[46][174] = 'Alunda';
$area_code[46][175] = 'Hallstavik-Rimbo';
$area_code[46][176] = 'Norrtälje';
$area_code[46][18] = 'Uppsala';
$area_code[46][19] = 'Örebro-Kumla';
$area_code[46][21] = 'Västerås';
$area_code[46][220] = 'Hallstahammar-Surahammar';
$area_code[46][221] = 'Köping';
$area_code[46][222] = 'Skinnskatteberg';
$area_code[46][223] = 'Fagersta-Norberg';
$area_code[46][224] = 'Sala-Heby';
$area_code[46][225] = 'Hedemora-Säter';
$area_code[46][226] = 'Avesta-Krylbo';
$area_code[46][227] = 'Kungsör';
$area_code[46][23] = 'Falun';
$area_code[46][240] = 'Ludvika-Smedjebacken';
$area_code[46][241] = 'Gagnef-Floda';
$area_code[46][243] = 'Borlänge';
$area_code[46][246] = 'Svärdsjö-Enviken';
$area_code[46][247] = 'Leksand-Insjön';
$area_code[46][248] = 'Rättvik';
$area_code[46][250] = 'Mora-Orsa';
$area_code[46][251] = 'Älvdalen';
$area_code[46][253] = 'Idre-Särna';
$area_code[46][258] = 'Furudal';
$area_code[46][26] = 'Gävle-Sandviken';
$area_code[46][270] = 'Söderhamn';
$area_code[46][271] = 'Alfta-Edsbyn';
$area_code[46][278] = 'Bollnäs';
$area_code[46][280] = 'Malung';
$area_code[46][281] = 'Vansbro';
$area_code[46][290] = 'Hofors-Storvik';
$area_code[46][291] = 'Hedesunda-Österfärnebo';
$area_code[46][292] = 'Tärnsjö-Östervåla';
$area_code[46][293] = 'Tierp-Söderfors';
$area_code[46][294] = 'Karlholmsbruk-Skärplinge';
$area_code[46][295] = 'Örbyhus-Dannemora';
$area_code[46][297] = 'Ockelbo-Hamrånge';
$area_code[46][300] = 'Kungsbacka';
$area_code[46][301] = 'Hindås';
$area_code[46][302] = 'Lerum';
$area_code[46][303] = 'Kungälv';
$area_code[46][304] = 'Orust-Tjörn';
$area_code[46][31] = 'Göteborg';
$area_code[46][320] = 'Kinna';
$area_code[46][321] = 'Ulricehamn';
$area_code[46][322] = 'Alingsås-Vårgårda';
$area_code[46][325] = 'Svenljunga-Tranemo';
$area_code[46][33] = 'Borås';
$area_code[46][340] = 'Varberg';
$area_code[46][345] = 'Hyltebruk-Torup';
$area_code[46][346] = 'Falkenberg';
$area_code[46][35] = 'Halmstad';
$area_code[46][36] = 'Jönköping-Huskvarna';
$area_code[46][370] = 'Värnamo';
$area_code[46][371] = 'Gislaved-Anderstorp';
$area_code[46][372] = 'Ljungby';
$area_code[46][380] = 'Nässjö';
$area_code[46][381] = 'Eksjö';
$area_code[46][382] = 'Sävsjö';
$area_code[46][383] = 'Vetlanda';
$area_code[46][390] = 'Gränna';
$area_code[46][392] = 'Mullsjö';
$area_code[46][393] = 'Vaggeryd';
$area_code[46][40] = 'Malmö';
$area_code[46][410] = 'Trelleborg';
$area_code[46][411] = 'Ystad';
$area_code[46][413] = 'Eslöv-Höör';
$area_code[46][414] = 'Simrishamn';
$area_code[46][415] = 'Hörby';
$area_code[46][416] = 'Sjöbo';
$area_code[46][417] = 'Tomelilla';
$area_code[46][418] = 'Landskrona-Svalöv';
$area_code[46][42] = 'Helsingborg-Höganäs-Åstorp';
$area_code[46][430] = 'Laholm';
$area_code[46][431] = 'Ängelholm-Båstad';
$area_code[46][433] = 'Markaryd-Strömsnäsbruk';
$area_code[46][435] = 'Klippan-Perstorp';
$area_code[46][44] = 'Kristianstad';
$area_code[46][451] = 'Hässleholm';
$area_code[46][454] = 'Karlshamn-Olofström';
$area_code[46][455] = 'Karlskrona';
$area_code[46][456] = 'Sölvesborg-Bromölla';
$area_code[46][457] = 'Ronneby';
$area_code[46][459] = 'Ryd';
$area_code[46][46] = 'Lund';
$area_code[46][470] = 'Växjö';
$area_code[46][471] = 'Emmaboda';
$area_code[46][472] = 'Alvesta-Rydaholm';
$area_code[46][474] = 'Åseda-Lenhovda';
$area_code[46][476] = 'Älmhult';
$area_code[46][477] = 'Tingsryd';
$area_code[46][478] = 'Lessebo';
$area_code[46][479] = 'Osby';
$area_code[46][480] = 'Kalmar';
$area_code[46][481] = 'Nybro';
$area_code[46][485] = 'Öland';
$area_code[46][486] = 'Torsås';
$area_code[46][490] = 'Västervik';
$area_code[46][491] = 'Oskarshamn-Högsby';
$area_code[46][492] = 'Vimmerby';
$area_code[46][493] = 'Gamleby';
$area_code[46][494] = 'Kisa';
$area_code[46][495] = 'Hultsfred-Virserum';
$area_code[46][496] = 'Mariannelund';
$area_code[46][498] = 'Gotland';
$area_code[46][499] = 'Mönsterås';
$area_code[46][500] = 'Skövde';
$area_code[46][501] = 'Mariestad';
$area_code[46][502] = 'Tidaholm';
$area_code[46][503] = 'Hjo';
$area_code[46][504] = 'Tibro';
$area_code[46][505] = 'Karlsborg';
$area_code[46][506] = 'Töreboda-Hova';
$area_code[46][510] = 'Lidköping';
$area_code[46][511] = 'Skara-Götene';
$area_code[46][512] = 'Vara-Nossebro';
$area_code[46][513] = 'Herrljunga';
$area_code[46][514] = 'Grästorp';
$area_code[46][515] = 'Falköping';
$area_code[46][520] = 'Trollhättan';
$area_code[46][521] = 'Vänersborg';
$area_code[46][522] = 'Uddevalla';
$area_code[46][523] = 'Lysekil';
$area_code[46][524] = 'Munkedal';
$area_code[46][525] = 'Grebbestad';
$area_code[46][526] = 'Strömstad';
$area_code[46][528] = 'Färgelanda';
$area_code[46][530] = 'Mellerud';
$area_code[46][531] = 'Bengtsfors';
$area_code[46][532] = 'Åmål';
$area_code[46][533] = 'Säffle';
$area_code[46][534] = 'Ed';
$area_code[46][54] = 'Karlstad';
$area_code[46][550] = 'Kristinehamn';
$area_code[46][551] = 'Gullspång';
$area_code[46][552] = 'Deje';
$area_code[46][553] = 'Molkom';
$area_code[46][554] = 'Kil';
$area_code[46][555] = 'Grums';
$area_code[46][560] = 'Torsby';
$area_code[46][563] = 'Hagfors-Munkfors';
$area_code[46][564] = 'Sysslebäck';
$area_code[46][565] = 'Sunne';
$area_code[46][570] = 'Arvika';
$area_code[46][571] = 'Charlottenberg-Åmotfors';
$area_code[46][573] = 'Årjäng';
$area_code[46][580] = 'Kopparberg';
$area_code[46][581] = 'Lindesberg';
$area_code[46][582] = 'Hallsberg';
$area_code[46][583] = 'Askersund';
$area_code[46][584] = 'Laxå';
$area_code[46][585] = 'Fjugesta-Svartå';
$area_code[46][586] = 'Karlskoga-Degerfors';
$area_code[46][587] = 'Nora';
$area_code[46][589] = 'Arboga';
$area_code[46][590] = 'Filipstad';
$area_code[46][591] = 'Hällefors-Grythyttan';
$area_code[46][60] = 'Sundsvall-Timrå';
$area_code[46][611] = 'Härnösand';
$area_code[46][612] = 'Kramfors';
$area_code[46][613] = 'Ullånger';
$area_code[46][620] = 'Sollefteå';
$area_code[46][621] = 'Junsele';
$area_code[46][622] = 'Näsåker';
$area_code[46][623] = 'Ramsele';
$area_code[46][624] = 'Backe';
$area_code[46][63] = 'Östersund';
$area_code[46][640] = 'Krokom';
$area_code[46][642] = 'Lit';
$area_code[46][643] = 'Hallen-Oviken';
$area_code[46][644] = 'Hammerdal';
$area_code[46][645] = 'Föllinge';
$area_code[46][647] = 'Åre-Järpen';
$area_code[46][650] = 'Hudiksvall';
$area_code[46][651] = 'Ljusdal';
$area_code[46][652] = 'Bergsjö';
$area_code[46][653] = 'Delsbo';
$area_code[46][657] = 'Los';
$area_code[46][660] = 'Örnsköldsvik';
$area_code[46][661] = 'Bredbyn';
$area_code[46][662] = 'Björna';
$area_code[46][663] = 'Husum';
$area_code[46][670] = 'Strömsund';
$area_code[46][671] = 'Hoting';
$area_code[46][672] = 'Gäddede';
$area_code[46][680] = 'Sveg';
$area_code[46][682] = 'Rätan';
$area_code[46][684] = 'Hede-Funäsdalen';
$area_code[46][687] = 'Svenstavik';
$area_code[46][690] = 'Ånge';
$area_code[46][691] = 'Torpshammar';
$area_code[46][692] = 'Liden';
$area_code[46][693] = 'Bräcke-Gällö';
$area_code[46][695] = 'Stugun';
$area_code[46][696] = 'Hammarstrand';
$area_code[46][70] = 'Cellphone';	//from 1996
$area_code[46][72] = 'Cellphone';	//from 2008.10.28 according to pts.se
$area_code[46][73] = 'Cellphone';	//from 2002
$area_code[46][74] = 'Pager';
$area_code[46][76] = 'Cellphone';	//from 2007 (0769 is still unallocated at 2008.10.30)
$area_code[46][8] = 'Stockholm';
$area_code[46][90] = 'Umeå';
$area_code[46][910] = 'Skellefteå';
$area_code[46][911] = 'Piteå';
$area_code[46][912] = 'Byske';
$area_code[46][913] = 'Lövånger';
$area_code[46][914] = 'Burträsk';
$area_code[46][915] = 'Bastuträsk';
$area_code[46][916] = 'Jörn';
$area_code[46][918] = 'Norsjö';
$area_code[46][920] = 'Luleå';
$area_code[46][921] = 'Boden';
$area_code[46][922] = 'Haparanda';
$area_code[46][923] = 'Kalix';
$area_code[46][924] = 'Råneå';
$area_code[46][925] = 'Lakaträsk';
$area_code[46][926] = 'Överkalix';
$area_code[46][927] = 'Övertorneå';
$area_code[46][928] = 'Harads';
$area_code[46][929] = 'Älvsbyn';
$area_code[46][930] = 'Nordmaling';
$area_code[46][932] = 'Bjurholm';
$area_code[46][933] = 'Vindeln';
$area_code[46][934] = 'Robertsfors';
$area_code[46][935] = 'Vännäs';
$area_code[46][940] = 'Vilhelmina';
$area_code[46][941] = 'Åsele';
$area_code[46][942] = 'Dorotea';
$area_code[46][943] = 'Fredrika';
$area_code[46][950] = 'Lycksele';
$area_code[46][951] = 'Storuman';
$area_code[46][952] = 'Sorsele';
$area_code[46][953] = 'Malå';
$area_code[46][954] = 'Tärnaby';
$area_code[46][960] = 'Arvidsjaur';
$area_code[46][961] = 'Arjeplog';
$area_code[46][970] = 'Gällivare';
$area_code[46][971] = 'Jokkmokk';
$area_code[46][973] = 'Porjus';
$area_code[46][975] = 'Hakkas';
$area_code[46][976] = 'Vuollerim';
$area_code[46][977] = 'Korpilombolo';
$area_code[46][978] = 'Pajala';
$area_code[46][980] = 'Kiruna';
$area_code[46][981] = 'Vittangi';

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

function prettyAnr($country, $anr)
{
	global $e164_cc, $area_code;

	if ($country == 'SIP') return 'SIP:'.$anr;

	$parsed_anr = parseAnr($country.$anr);

	$out = '+'.$parsed_anr['country'];
	if ($parsed_anr['area_code']) $out .= '-'.$parsed_anr['area_code'];
	$out .= '-'.$parsed_anr['anr'];

	$suff = '';

	if (!empty($e164_cc[$parsed_anr['country']])) {
		$suff = ' ('. $e164_cc[$parsed_anr['country']];
		if ($parsed_anr['area_code']) {
			$suff .= ', '.$area_code[$parsed_anr['country']][$parsed_anr['area_code']];
		} else {
			$suff .= ', UNKNOWN AREA';
		}
		$suff .= ')';
	}
	return $out.$suff;
}

/**
 * Returns input phone number in MSID format
 * Defaults to swedish (+46) in case of missing country code
 *
 * @param $anr user typed phone number
 * @return MSID formatted phone number (46707123456)
 */
function formatMSID($anr)
{
	$anr = cleanupAnr($anr);

	if (substr($anr, 0, 1) == '0') {
		//Swedish numer without country code
		return '46'.substr($anr, 1);
	}
	return $anr;
}

/**
 * Separate country code (if any) from phone number
 * Defaults to swedish (+46) in case of missing country code
 *
 * See also:
 * http://en.wikipedia.org/wiki/Country_calling_code
 * http://en.wikipedia.org/wiki/E.164
 */
function parseAnr($anr)
{
	global $e164_cc;

	$anr = cleanupAnr($anr);

	if (!is_numeric($anr)) {
		//SIP user address
		//FIXME users could perform call spoofing if we allow numerical usernames from sip callers
		$res['country'] = 'SIP';
		$res['anr'] = $anr;
		$res['area_code'] = '';
		return $res;
	}

	if (substr($anr, 0, 1) == '0') {
		//Swedish numer without country code
		$res['country'] = '46';
		$res['anr'] = substr($anr, 1);
		$res['area_code'] = parseAreaCode($res['country'], $res['anr']);
		return $res;
	}

	if (substr($anr, 0, 1) == '1') {
		//US, Canada
		//FIXME: identify correct country: http://en.wikipedia.org/wiki/List_of_NANP_area_codes
		$res['country'] = '1';
		$res['anr'] = substr($anr, 1);
		$res['area_code'] = parseAreaCode($res['country'], $res['anr']);
		return $res;
	}

	if (substr($anr, 0, 1) == '7') {
		//Russia, Kazakhstan
		//FIXME: identify correct country: http://en.wikipedia.org/wiki/+7
		$res['country'] = '7';
		$res['anr'] = substr($anr, 1);
		$res['area_code'] = parseAreaCode($res['country'], $res['anr']);
		return $res;
	}

	//2-digit country codes
	$res['country'] = substr($anr, 0, 2);
	$res['anr'] = substr($anr, 2);
	if (!empty($e164_cc[$res['country']])) {
		$res['area_code'] = parseAreaCode($res['country'], $res['anr']);
		return $res;
	}

	//3-digit country codes
	$res['country'] = substr($anr, 0, 3);
	$res['anr'] = substr($anr, 3);
	if (!empty($e164_cc[$res['country']])) {
		$res['area_code'] = parseAreaCode($res['country'], $res['anr']);
		return $res;
	}

	//XXX This should not be able to happen. All existing country codes are handled above
	echo "Unknown country code: ".$anr."\n";
	return $res;
}

function parseAreaCode($country, &$anr)
{
	global $area_code;

	if (empty($area_code[$country])) return false;

	//1-digit area codes
	$tmp = substr($anr, 0, 1);
	if (!empty($area_code[$country][$tmp])) {
		$anr = substr($anr, 1);
		return $tmp;
	}

	//2-digit area codes
	$tmp = substr($anr, 0, 2);
	if (!empty($area_code[$country][$tmp])) {
		$anr = substr($anr, 2);
		return $tmp;
	}

	//3-digit area codes
	$tmp = substr($anr, 0, 3);
	if (!empty($area_code[$country][$tmp])) {
		$anr = substr($anr, 3);
		return $tmp;
	}
	echo "parseAreaCode() ERROR: invalid area code for ".$country." - ".$anr."!\n";
	return '';
}

/**
 * Takes a text string representing a phone number
 * as input and returns it in a common format
 */
function cleanupAnr($anr)
{
	$anr = trim($anr);
	$anr = str_replace("\t", '', $anr);
	$anr = str_replace(' ', '', $anr);
	$anr = str_replace('-', '', $anr);
	$anr = str_replace('+', '', $anr);
	return $anr;
}

?>
