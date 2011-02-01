<?php

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
TEL;CELL: 47 974 72 390
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
