<?php
/**
 * $Id$
 *
 * Hash calculation code based on snippet from
 * http://trac.opensubtitles.org/projects/opensubtitles/wiki/HashSourceCodes
 * LICENCE is gpl
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//STATUS: rewrite hash calculation, write class

require_once('class.CoreBase.php');
require_once('client_http.php');

class OpenSubtitlesMetadata extends CoreBase
{
}

/**
 * calculates: size + 64bit chksum of the first and last 64k
 * (even if they overlap because the file is smaller than 128k)
 */
function OpenSubtitlesHash($file)
{
	$handle = fopen($file, "rb");
	$fsize = filesize($file);

	$hash = array(
	3 => 0,
	2 => 0,
	1 => ($fsize >> 16) & 0xFFFF,
	0 => $fsize & 0xFFFF);

	for ($i = 0; $i < 8192; $i++)
	{
		$tmp = ReadUINT64($handle);
		$hash = AddUINT64($hash, $tmp);
	}

	$offset = $fsize - 65536;
	fseek($handle, $offset > 0 ? $offset : 0, SEEK_SET);

	for ($i = 0; $i < 8192; $i++)
	{
		$tmp = ReadUINT64($handle);
		$hash = AddUINT64($hash, $tmp);
	}

	fclose($handle);
	return UINT64FormatHex($hash);
}

function ReadUINT64($handle)
{
	$u = unpack("va/vb/vc/vd", fread($handle, 8));
	return array(0 => $u["a"], 1 => $u["b"], 2 => $u["c"], 3 => $u["d"]);
}

function AddUINT64($a, $b)
{
	$o = array(0 => 0, 1 => 0, 2 => 0, 3 => 0);

	$carry = 0;
	for ($i = 0; $i < 4; $i++)
	{
		if (($a[$i] + $b[$i] + $carry) > 0xffff )
		{
			$o[$i] += ($a[$i] + $b[$i] + $carry) & 0xffff;
			$carry = 1;
		}
		else
		{
			$o[$i] += ($a[$i] + $b[$i] + $carry);
			$carry = 0;
		}
	}

	return $o;
}

function UINT64FormatHex($n)
{
	return sprintf("%04x%04x%04x%04x", $n[3], $n[2], $n[1], $n[0]);
}
