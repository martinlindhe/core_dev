<?php
/**
 * $Id$
 *
 * vCard reader class
 *
 * http://en.wikipedia.org/wiki/Vcard
 *
 * @author Martin Lindhe, 2011
 */

//STATUS: wip

require_once('HttpClient.php');
require_once('VcardAddress.php');

class VcardReader
{
    /** @return array of VcardAddress objects */
    static function parse($data)
    {
        if (is_url($data)) {
            $http = new HttpClient($data);
            $data = $http->getBody();

            //FIXME check http client return code for 404
            if (strpos($data, 'BEGIN:VCARD') === false) {
                throw new Exception ('VcardReader->parse FAIL: cant parse vcard from '.$http->getUrl() );
                return false;
            }
        }

        $res = array();

        do {
            $m1 = 'BEGIN:VCARD';
            $m2 = 'END:VCARD';
            $p1 = strpos($data, $m1);
            $p2 = strpos($data, $m2);

            if ($p1 === false || $p2 === false)
                break;

            $part = substr($data, $p1, $p2 - $p1 + strlen($m2) );
            $res[] = self::parseVcard($part);
            $data = substr($data, $p2 + strlen($m2) );

        } while ($data);

        return $res;
    }

    /**
     * Parses the content inside a VCARD:BEGIN + VCARD:END couple into a VcardAddress object
     */
    static private function parseVcard($data)
    {
        $data = str_replace("\r\n", "\n", $data);
        $data = str_replace("\r", "\n", $data);
        $rows = explode("\n", $data);

        $adr = new VcardAddress();

        foreach ($rows as $row) {
            $p1 = strpos($row, ':');
            if ($p1 === false)
                throw new Exception ('invalid vcard format: '.$row);

            $key = substr($row, 0, $p1);
            $val = explode(';', substr($row, $p1 + 1) );
            $params = array();

            $p2 = strpos($key, ';');
            if ($p2 !== false) {
                $params = explode(';', substr($key, $p2 + 1) );
                $key = substr($key, 0, $p2);
            }

            switch ($key) {
            case 'BEGIN': break;
            case 'END': break;
            case 'VERSION':
                if ($val[0] != '2.1')
                    throw new Exception ('unsupported vcard version '.$val[0]);
                break;

            case 'BDAY':
                $adr->birthdate = $val[0];
                break;

            case 'N':
                //val = Lastname;Firstname;??;??;??
                $adr->last_name  = $val[0];
                $adr->first_name = $val[1];
                break;

            case 'FN': // full name.. XXX FIXME parse ONLY if no "N" tag was found
                break;

            case 'TEL':
                switch ($params[ count($params) - 1]) {
                case 'CELL':
                    $adr->cellphone = formatMSID($val[0]);
                    break;
                case 'HOME':
                    $adr->homephone = formatMSID($val[0]);
                    break;
                case 'WORK':
                    $adr->cellphone = formatMSID($val[0]);
                    break;
                default:
                    dp('XXX VcardReader unhandled telephone type: '.$params[ count($params) - 1] );
                }
                break;

            case 'ADR':
                switch ($params[0]) {
                case 'HOME': // val = ;;Terrassebakken 14;Ålgård;;4330;
                    $adr->street  = $val[2];
                    $adr->city    = $val[3];
                    $adr->zipcode = $val[5];
                    break;
                case 'WORK': // ADR;WORK:;;Verkensveien 6;Hell;;7517;
                    // XXXX: store work adddress separately?
                    if (!$adr->street) {
                        $adr->street  = $val[2];
                        $adr->city    = $val[3];
                        $adr->zipcode = $val[5];
                    }
                    break;

                default:
                    dp('XXX VcardReader unhandled address type: '.$params[0]);
                }
                break;

            default:
                //echo "key ".$key."\t, params = ".implode('; ', $params)."\t, val = ".implode('; ', $val)."\n";
            }
        }

        return $adr;
    }
}

?>
