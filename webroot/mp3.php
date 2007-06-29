<?
/*
size_in_bytes / bitrate = length_in_seconds.

3,882,423 bytes = 31,059,384 bits
128 kbit/s = 131,072 bits/s

31,059,384 / 131,072 = 237 seconds.
----
I don't have the code in front of me, but I wrote a function a while ago to do this. First look for the VBR header, either XING or VBRI. If you find one, iterate through the file, adding up all the bitrates in each frame, and dividing by the number of frames. If you don't find a VBR header, you can simply use the bitrate found in the first frame.

*/

//die('fu');

	/* Returns a sting like: 4h10m3s */
	function shortTimePeriod($seconds)
	{
		if (is_float($seconds)) $seconds = round($seconds);
		$retval='';

		//years
		$a = date('Y', $seconds) - 1970;
		if ($a==1) $retval=$a.' year, ';
		else if ($a>0) $retval=$a.' years, ';
		$seconds -= (((($a*60)*60)*24)*30)*365;

		//months
		$a=date('n',$seconds)-1;
		if($a==1) $retval.=$a.' month, ';
		else if($a>0) $retval.=$a.' months, ';
		$seconds -= ((($a*60)*60)*24)*30;

		//days
		$a=date('j',$seconds)-1;
		if($a==1) $retval.=$a.' day, ';
		else if ($a>0) $retval.=$a.' days, ';
		$seconds -= (($a*60)*60)*24;

		//hours
		$a=date('H',$seconds)-1;
		if ($a>0) $retval.=$a.'h';
		$seconds -= ($a*60)*60;

		//minutes
		$a=date('i',$seconds)-0; //translate from 09 to 9 quickly ;)
		if ($a>0) $retval.=$a.'m';
		$seconds -= $a*60;

		//seconds
		$a=date('s',$seconds)-0; //translate from 09 to 9 quickly ;)
		if ($a>0) $retval.=$a.'s';

		if (substr($retval, -2) == ', ') $retval = substr($retval, 0, -2);
		if ($retval == '') $retval = '0s';

		return $retval;
	}

$path = 'path.mp3';
$mp3 = new mp3($path);
$mp3->setFileInfoExact();
echo shortTimePeriod($mp3->time);
echo '<br/>';


echo 'expected: 3:49<br/>';
echo round(xdebug_time_index(), 3);
die;


class mp3
{
    var $str;
    var $time;

    function mp3($path)
    {
			$this->str = file_get_contents($path);
    }

    function setFileInfoExact()
    {
        $maxStrLen = strlen($this->str);
        $currentStrPos = strpos($this->str,chr(255));
        $time = 0;
        while($currentStrPos < $maxStrLen)
        {
            $str = substr($this->str,$currentStrPos,4);
            $strlen = strlen($str);
            $parts = array();
            for($i=0;$i < $strlen;$i++)
            {
                $parts[] = $this->decbinFill(ord($str[$i]),8);
            }
            if($parts[0] != "11111111")
            {
                if(($maxStrLen-128) > $currentStrPos)
                {
                    return false;
                }
                else
                {
                    $this->time = $time;
                    return true;
                }
            }
            $a = $this->doFrameStuff($parts);
            $currentStrPos += $a[0];
            $time += $a[1];
        }
        $this->time = $time;
        return true;
    }
    
    function decbinFill($dec,$length=0)
    {
        $str = decbin($dec);
        $nulls = $length-strlen($str);
        if($nulls>0)
        {
            for($i=0;$i<$nulls;$i++)
            {
                $str = '0'.$str;
            }
        }
        return $str;
    }
    
    function doFrameStuff($parts)
    {
        //Get Audio Version
        $errors = array();
        switch(substr($parts[1],3,2))
        {
            case '01':
            $errors[]='Reserved audio version';
            break;
            case '00':
            $audio = 2.5;
            break;
            case '10':
            $audio = 2;
            break;
            case '11':
            $audio = 1;
            break;
        }
        //Get Layer
        switch(substr($parts[1],5,2))
        {
            case '01':
            $layer = 3;
            break;
            case '00':
            $errors[]='Reserved layer';
            break;
            case '10':
            $layer = 2;
            break;
            case '11':
            $layer = 1;
            break;
        }
        //Get Bitrate
        $bitFlag = substr($parts[2],0,4);
        $bitArray = array(
    '0000'    => array(-1,		-1,			-1,		-1,		-1),
    '0001'    => array(32,    32,    32,    32,    8),
    '0010'    => array(64,    48,    40,    48,    16),
    '0011'    => array(96,    56,    48,    56,    24),
    '0100'    => array(128,    64,    56,    64,    32),
    '0101'    => array(160,    80,    64,    80,    40),
    '0110'    => array(192,    96,    80,    96,    48),
    '0111'    => array(224,    112,    96,    112,    56),
    '1000'    => array(256,    128,    112,    128,    64),
    '1001'    => array(288,    160,    128,    144,    80),
    '1010'    => array(320,    192,    160,    160,    96),
    '1011'    => array(352,    224,    192,    176,    112),
    '1100'    => array(384,    256,    224,    192,    128),
    '1101'    => array(416,    320,    256,    224,    144),
    '1110'    => array(448,    384,    320,    256,    160),
    '1111'    => array(-1,			-1,			-1,			-1,			-1)
    );
        $bitPart = $bitArray[$bitFlag];
        if ($bitPart == -1) return false;
        $bitArrayNumber;
        if($audio==1)
        {
            switch($layer)
            {
                case 1:
                $bitArrayNumber=0;
                break;
                case 2:
                $bitArrayNumber=1;
                break;
                case 3:
                $bitArrayNumber=2;
                break;
            }
        }
        else
        {
            switch($layer)
            {
                case 1:
                $bitArrayNumber=3;
                break;
                case 2:
                $bitArrayNumber=4;
                break;
                case 3:
                $bitArrayNumber=4;
                break;
            }
        }
        $bitRate = $bitPart[$bitArrayNumber];
        //Get Frequency
        $frequencies = array(
        1=>array('00'=>44100,
        '01'=>48000,
        '10'=>32000,
        '11'=>'reserved'),
        2=>array(),
        2.5=>array());
        $freq = $frequencies[$audio][substr($parts[2],4,2)];
        //IsPadded?
        $padding = substr($parts[2],6,1);
        if($layer==3||$layer==2)
        {
            //FrameLengthInBytes = 144 * BitRate / SampleRate + Padding
            $frameLength = 144 * $bitRate * 1000 / $freq + $padding;
        }
        $frameLength = floor($frameLength);
        $seconds = $frameLength*8/($bitRate*1000);
        return array($frameLength,$seconds);
        //Calculate next when next frame starts.
        //Capture next frame.    
    }  
}
?>