<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('MimeReader.php');

// TODO fix test

class MimeReaderTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $msg = 'Date: Sun, 06 Nov 2011 10:09:58 +0100
        From: =?iso-8859-1?Q?Tommy <tommy@xxx.no>
        Subject: Emailing: Bok2.txt
        To: tekstfiler <mail-import@xxx.no>
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
        22;6310;0;20111028
        23;7605;-998;20111028
        24;3872;-200;20111028
        51;8111;-1947;20111028
        55;6730;-1547;20111031
        58;6499;-1547;20111031
        59;6527;0;20111031
        66;6350;0;20111031
        67;5841;-500;20111031

        --Boundary_(ID_vPoI7CFh//8avI2MqJP0jQ)--';


        $mime = new MimeReader();
        $mime->parseMail($msg);

        $mail = $mime->getAsEMail(0);

        d($mail);

        //TODO: make MimeReader static class
    }
}
