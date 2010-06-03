<?php
/**
 * $Id$
 */

//STATUS: very much WIP

class PdfReader
{
    private $filename;                ///< input filename
    private $version, $major, $minor; ///< PDF version details
    private $supported_versions = array('1.4');
    private $stream_no = 0;           ///< internal counter for current stream
    private $fp; ///< file pointer

    function __construct($filename)
    {
        $this->filename = $filename;
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
    }

    function read()
    {
        if (!file_exists($this->filename))
            throw new Exception ('file not found');

        $this->fp = fopen($this->filename, "rb");

        $head = fread($this->fp, 9);

        $this->parseHeader($head);

        while (!feof($this->fp)) {
            $row = fgets($this->fp, 1000);
            $this->parseRow($row);
        }

        fclose($this->fp);
    }

    private function parseRow($row)
    {
        if (!$row)
            return;

        echo $row;

        if (substr($row, 0, 1) == '%') {
            //echo "%comment\n";
            //dh($row);
            return;
        }

        if ($row == "xref\n") {
//            d($row);

            //"base index" "number of entries"
            $row = trim( fgets($this->fp, 1000) );

            list($idx, $cnt) = explode(' ', $row);

            echo "Reading ".$cnt." lines of xref:\n";

            for ($i=0; $i<$cnt; $i++) {
                $row = trim( fgets($this->fp, 1000) );
                echo ($idx+$i).": ".$row."\n";
            }
            return;
        }

        if ($row == "startxref\n") {
            //offset to xref header
            $row = trim( fgets($this->fp, 1000) );
            echo $row."\n";
            return;
        }

        if ($row == "trailer\n") {

            $row = trim( fgets($this->fp, 1000) );
            echo $row."\n";
            $dict = pdf_parse_dict($row);
            d($dict);
            return;
        }

        // 1 0 obj <</Filter/DCTDecode/Type/XObject/Length 17619/BitsPerComponent 8/Height 181/ColorSpace/DeviceRGB/Subtype/Image/Width 420>>stream
        // 8 0 obj<</Type/Page/Contents 6 0 R/Parent 7 0 R/Resources<</XObject<</img0 1 0 R>>/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]/Font<</F1 2 0 R/F3 4 0 R/F2 3 0 R/F4 5 0 R>>>>/MediaBox[0 0 595 842]>>
        list($n1, $n2, $s) = explode(' ', $row, 3);

        if (substr($s, 0, 3) == 'obj') {
            $s = trim(substr($s, 3));

            if (substr($s, -6) == "stream") {
                $s = substr($s, 0, -6);

                $dict = pdf_parse_dict($s);

//                d($dict);

                $stream = fread($this->fp, $dict['Length']);

                if ($dict['Filter'] == 'FlateDecode') {
//                    echo "Writing decompressed stream\n";
                    $stream = gzuncompress($stream);
                }

                file_put_contents('stream.'.$this->stream_no, $stream);

                $this->stream_no++;

                $data = fread($this->fp, 11);
                if ($data != "\nendstream\n")
                    throw new Exception ('unexpected end stream '.$data);
            }

            $data = fread($this->fp, 7);
            if ($data != "endobj\n")
                throw new Exception ('unexpected end obj '.$data);

            return;

        } else {
            echo "DUNNO WHAT DO!\n";
            d($row);
            die;
        }

    }
}


//XXX assumes 2d dictionary. they can be multi-dimensonal with nested << >>
function pdf_parse_dict($s)
{
    // <</Filter/DCTDecode/Type/XObject/Length 17619/BitsPerComponent 8/Height 181/ColorSpace/DeviceRGB/Subtype/Image/Width 420>>
    if (substr($s, 0, 2) == '<<' && substr($s, -2) == '>>')
        $s = substr($s, 2, strlen($s) - 4);
    else
        throw new Exception ('Unexpected dict format '.$s);

    ///XXX hack fulparser
    $s = str_replace(' ', '/', substr($s, 1));
    $x = explode('/', $s);

    if (count($x) % 2 != 0)
        throw new Exception ('Failed dict parse');

    $dict = array();
    for ($i=0; $i<count($x); $i+=2)
        $dict[ $x[$i] ] = $x[$i+1];

    return $dict;
}

?>
