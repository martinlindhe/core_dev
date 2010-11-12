<?php
/**
 * $Id$
 *
 * http://www.adobe.com/devnet/pdf/pdf_reference_archive.html
 *
 * PDF Reference, Sixth Edition, version 1.7:
 * http://www.adobe.com/devnet/acrobat/pdfs/pdf_reference_1-7.pdf
 *
 * + Errata to Sixth Edition:
 * http://www.adobe.com/devnet/pdf/pdfs/pdf_17_errata.pdf
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: very much WIP

//TODO: rewrite pdf_parse_dict() to handle multi-dimensional dictionaries

/**
 * Version status
 *
 * v1.3: dont work with the sample i have
 * v1.4: works with all samples
 */

require_once('PsPdfReader.php');

class PdfStream
{
    var $type;
    var $data;
}

class PdfReader
{
    private $filename;                  ///< input filename
    private $version, $major, $minor;   ///< PDF version details
    private $supported_versions = array('1.3', '1.4');
    private $streams = array();         ///< PdfStream objects
    private $fp;                        ///< file pointer

    function __construct($filename)
    {
        $this->filename = $filename;
    }

    function getStream($n) { return $this->streams[$n]; }

    function getStreams() { return $this->streams; }

    function getStreamCount() { return count($this->streams); }

    /** @return array of "text" type streams containing PostScript markup */
    function getPsStreams()
    {
        $t = array();

        foreach ($this->streams as $s)
            if ($s->type == 'text')
                $t[] = $s;

        return $t;
    }

    private function parseHeader($s)
    {
        if (substr($s, 0, 5) != '%PDF-')
            throw new Exception ('Not a pdf');

        $this->major = intval( substr($s, 5, 1) );
        $this->minor = intval( substr($s, 7, 1) );

        $this->version = $this->major.'.'.$this->minor;

        if (!in_array($this->version ,$this->supported_versions))
            throw new Exception ('Unsupported PDF version '.$this->version);

        // echo "DBG: PDF v".$this->major.".".$this->minor."\n";
    }

    function read()
    {
        if (!file_exists($this->filename))
            throw new Exception ('file not found: '.$this->filename);

        $this->fp = fopen($this->filename, "rb");

        $head = fread($this->fp, 9);

        $this->parseHeader($head);

        while (!feof($this->fp)) {
//            echo dechex(ftell($this->fp))." ";
            $row = $this->readRow();
            $this->parseRow($row);
        }

        fclose($this->fp);
    }

    private function readRow()
    {
        $row = '';

        while (!feof($this->fp)) {

            $b = fread($this->fp, 1);

            // skip all types of line feeds
            if ($b == "\r" || $b == "\n") {
                $b2 = fread($this->fp, 1);

                if ($b2 != "\r" && $b2 != "\n" && !feof($this->fp))
                    fseek($this->fp, -1, SEEK_CUR);

                return $row;
            }

            $row .= $b;
        }
    }

    private function parseRow($row)
    {
        if (!$row)
            return;

        //echo "RAW: (".strlen($row).") ".$row."\n";
//dh($row); echo "\n";

        if (substr($row, 0, 1) == '%') {
            //echo "%comment\n";
            //dh($row);
            return;
        }

        if ($row == "xref") { //XXX: code is unused
//            d($row);

            //"base index" "number of entries"
            $row = $this->readRow();
//d($row);
            list($idx, $cnt) = explode(' ', $row);

//            echo "DBG: Reading ".$cnt." lines of xref:\n";

            for ($i=0; $i<$cnt; $i++) {
                $row = trim( fgets($this->fp, 1000) );
//                echo "\t".($idx+$i).":\t".$row."\n";
            }
            return;
        }

        if ($row == "startxref") {  //XXX: code is unused
            //offset to xref header
            $row = trim( fgets($this->fp, 1000) );
//            echo "DBG STARTXREF: ".$row."\n";
            return;
        }

        if ($row == "trailer") {  //XXX: code is unused
            $row = trim( fgets($this->fp, 1000) );
//            echo "DBG TRAILER: ".$row."\n";
//            $dict = pdf_parse_dict($row);
            return;
        }

        // 1 0 obj <</Filter/DCTDecode/Type/XObject/Length 17619/BitsPerComponent 8/Height 181/ColorSpace/DeviceRGB/Subtype/Image/Width 420>>stream
        // 8 0 obj<</Type/Page/Contents 6 0 R/Parent 7 0 R/Resources<</XObject<</img0 1 0 R>>/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]/Font<</F1 2 0 R/F3 4 0 R/F2 3 0 R/F4 5 0 R>>>>/MediaBox[0 0 595 842]>>

        list($n1, $n2, $s) = explode(' ', $row, 3);

//        echo "DBG: ".$n1.",".$n2.": ".$s."\n";

        if (substr($s, 0, 3) == 'obj') {
            $s = trim(substr($s, 3));

            if (substr($s, -6) == "stream") {
                $s = substr($s, 0, -6);
                $this->parseStream($s);

//            } else {
//                dh($s);
//                $dict = pdf_parse_dict($s);
//d($dict);
            }

            $data = fgets($this->fp, 7);
            if ($data != "endobj")
                throw new Exception ('unexpected end obj: '.$data);

            return;

        } else {
            if (trim($row)) { // a pdf 1.3 sample has a row with 9 spaces+linefeed
                echo "DUNNO WHAT DO!\n";
                dh($row);
                die;
            }
        }
    }

    private function parseStream($s)
    {
        $dict = pdf_parse_dict($s);
//        d($dict);

        $stream = new PdfStream();

        $stream->data = fread($this->fp, $dict['Length']);
        $this->readRow(); //increase file pointer to next line

        switch ($dict['Filter']) {
        case 'FlateDecode':
//d($dict);
            $stream->type = 'text';
            $stream->data = gzuncompress($stream->data);
//            echo "STREAM: Decompressed from ".$dict['Length']." to ".strlen($stream->data)." bytes\n";
            break;

        case 'DCTDecode'; //jpeg image
            $stream->type = 'image';
//            echo "STREAM: Read JPEG image\n";
            break;

        default:
            throw new Exception ('unhandled stream type: '.$dict['Filter']);
        }

        //XXXX more friendly line reader????
        $data = $this->readRow();
        if ($data != "endstream")
            throw new Exception ('unexpected end stream: '.$data);

        /*
        if (isset($dict['Type']) && $dict['Type'] == 'XObject' && $dict['Subtype'] == 'Image')
            return;
        */

        $this->streams[] = $stream;
    }

}

//XXX assumes 2d dictionary. they can be multi-dimensonal with nested << >>
function pdf_parse_dict($s)
{
    if (substr($s, 0, 2) == '<<' && substr($s, -2) == '>>')
        $s = substr($s, 2, strlen($s) - 4);
    else {
        //throw new Exception ('Unexpected dict format: '.$s);
        echo "Unexpected dict format: ".$s."\n";
        return false;
    }

    $current_key = '';
    $dict = array();
    $x = explode('/', $s);

    foreach ($x as $val)
    {
        if (!$val) continue;
        if (strpos($val, ' ') !== false) {
            $xx = explode(' ', $val, 2);
            $dict[ $xx[0] ] = $xx[1];
        } else {
            if (!$current_key)
                $current_key = $val;
            else {
                $dict[ $current_key ] = $val;
                $current_key = '';
            }
        }
    }

    return $dict;
}

?>
