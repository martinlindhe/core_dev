<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('VcardReader.php');

$vcard_v21 =
"BEGIN:VCARD
VERSION:2.1
BDAY:1987-01-21;
FN:Marius Sondresen
N:Sondresen;Marius;;;
ADR;HOME;CHARSET=ISO-8859-1:;;Terrassebakken 14;Ålgård;;4330;
KID:8394760
IDLINJE:P4ZXFS
LABEL;PARCEL;ENCODING=QUOTED-PRINTABLE;CHARSET=ISO-8859-1:Marius Sondresen=0D=0ATerrassebakken 14=0D=0A4330 Ålgård
LABEL;INTL;ENCODING=QUOTED-PRINTABLE;CHARSET=ISO-8859-1:Marius Sondresen=0D=0ATerrassebakken 14=0D=0AN-4330 Ålgård=0D=0ANorway
TEL;PREF;HOME: 47 64 94 02 70
TEL;CELL: 47 974 72 390
END:VCARD";


$vcard_v21 =
"BEGIN:VCARD
VERSION:2.1
FN:Din Helse Shop
N:Din Helse Shop;;;;
ADR;WORK:;;Verkensveien 6;Hell;;7517;
ORGNR:995072939
KID:12367944
IDLINJE:Z0I1IIDB|Z0I1IIDC|Z0I1IIDA|Z0I0WA76
LABEL;PARCEL;ENCODING=QUOTED-PRINTABLE:Din Helse Shop=0D=0AVerkensveien 6=0D=0A7517 Hell
LABEL;INTL;ENCODING=QUOTED-PRINTABLE:Din Helse Shop=0D=0AVerkensveien 6=0D=0AN-7517 Hell=0D=0ANorway
ORG:Din Helse Shop;
TEL;PREF;WORK: 47 971 62 623
EMAIL;INTERNET:post@dinhelseshop.no
URL;WORK:http://dinhelseshop.no
END:VCARD";


$vcard_v21 =
"BEGIN:VCARD
VERSION:2.1
FN:Motell Nor Kro As
N:Motell Nor Kro As;;;;
ADR;WORK:;;;Nesbyen;;3540;
ORGNR:963932138
KID:3786793
IDLINJE:N32070890|N32071421|N90011951|N90503242|N90610383|N90744049|N95149030|N97636985|N99702558
LABEL;PARCEL;ENCODING=QUOTED-PRINTABLE:Motell Nor Kro As=0D=0A=0D=0A3540 Nesbyen
LABEL;INTL;ENCODING=QUOTED-PRINTABLE:Motell Nor Kro As=0D=0A=0D=0AN-3540 Nesbyen=0D=0ANorway
ORG:Motell Nor Kro As;
TEL;PREF;WORK: 47 32 06 73 40
TEL;PREF;WORK: 47 32 07 08 90
TEL;PREF;WORK: 47 32 07 14 21
TEL;CELL: 47 900 11 951
TEL;CELL: 47 905 03 242
TEL;CELL: 47 906 10 383
TEL;CELL: 47 907 44 049
TEL;CELL: 47 951 49 030
TEL;CELL: 47 976 36 985
TEL;CELL: 47 997 02 558
END:VCARD";

$adr = VcardReader::parse($vcard_v21);
d($adr);



// XXX: unsupported vcard 3.0 format
$vcard_v30 =  // from 'http://agigen.se/agigen.vcf'
"BEGIN:VCARD
SOURCE:http://www.agigen.se/
NAME:Agigen Ltd.
VERSION:3.0
N:;;;;
ORG;CHARSET=UTF-8:Agigen Ltd.
FN;LANGUAGE=en;CHARSET=UTF-8:Agigen Ltd.
EMAIL:hello@agigen.se
ADR;LANGUAGE=en;CHARSET=UTF-8:;;Jungfrugatan 4;Stockholm;;11444;Sweden
TEL:+46 (0)733 445315
END:VCARD";

/*
$adr = VcardReader::parse($vcard_v30);
d($adr);
*/

?>
