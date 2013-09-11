<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('core.php');
require('MimeReader.php');

$msg = 'Date: Sun, 06 Nov 2011 10:09:58 +0100
From: =?iso-8859-1?Q?Tommy_J=F8nsson?= <tommy@oslocreditservice.no>
Subject: Emailing: Bok2.txt
To: Hot4chat tekstfiler <savak-import-ok@unicorn.se>
MIME-version: 1.0
X-Mailer: Microsoft Office Outlook 12.0
Content-type: multipart/mixed; boundary="Boundary_(ID_vPoI7CFh//8avI2MqJP0jQ)"
Content-language: no
Thread-index: AcycY2pawb1JDhc+RfewhS8ZTdEfxg==

This is a multi-part message in MIME format.

--Boundary_(ID_vPoI7CFh//8avI2MqJP0jQ)
Content-type: text/plain; charset=iso-8859-1
Content-transfer-encoding: 7BIT


Your message is ready to be sent with the following file or link
attachments:

Bok2.txt


Note: To protect against computer viruses, e-mail programs may prevent
sending or receiving certain types of file attachments.  Check your e-mail
security settings to determine how attachments are handled.

--Boundary_(ID_vPoI7CFh//8avI2MqJP0jQ)
Content-type: text/plain; name=Bok2.txt
Content-transfer-encoding: 7BIT
Content-disposition: attachment; filename=Bok2.txt

sak_referanse;sak_ref2;Status_Hovedstol;Status_Dato
3721;6310;0;20111028
4521;7605;-998;20111028
2313;3872;-200;20111028
4842;8111;-1947;20111028
3972;6730;-1547;20111031
3833;6499;-1547;20111031
3045;6527;0;20111031
3744;6350;0;20111031
3436;5841;-500;20111031
3972;6811;-2296;20111031
4051;6852;-500;20111031
3207;7315;0;20111031
5051;8428;-998;20111031
5080;8474;-1947;20111031
5070;8461;-998;20111101
2212;3705;-296,76;20111101
4662;7844;-998;20111102
4371;7383;-500;20111102
4369;7328;-998;20111101
3757;6369;-1300;20111101
3936;6680;-500;20111101
3960;6767;-1547;20111101
3540;5999;-1547;20111101
4471;7527;0;20111101
4689;7888;-1947;20111101
4111;6941;-798;20111101
4752;7974;-998;20111101
4533;7624;-1500;20111101
3901;6620;-1507;20111101
4662;7844;-1309,32;20111102
4708;7920;-1947;20111103
4180;7042;0;20111103
5130;8549;-998;20111103

--Boundary_(ID_vPoI7CFh//8avI2MqJP0jQ)--';


$mime = new MimeReader();
$mime->parseMail($msg);

$mail = $mime->getAsEMail(0);

d($mail);

//TODO: make MimeReader static class

?>
