<?php
/**
 * $Id$
 *
 * Class to generate a Windows Icon (.ico) file from a GD2 resource
 *
 * Useful to create favicons on demand
 *
 * http://msdn.microsoft.com/en-us/library/ms997538.aspx
 * http://www.iconolog.org/info/icoFormat.html
 * http://en.wikipedia.org/wiki/ICO_(file_format)
 * http://en.wikipedia.org/wiki/Favicon
 *
 */

//STATUS: early wip..

//TODO later: allow adding multiple images in one .ico file

//TODO later: support Vista icon format (png:s inside .ico container)

namespace cd;

require_once('core.php');
require_once('Image.php');

class IconWriter
{
    protected $images = array(); ///< array of Image objects

    function addImage($file)
    {
        $this->images[] = new Image($file);
    }

    private function _packFileHeader()
    {
        return
        pack('vvv',
            0, // WORD Reserved (always 0)
            1, // WORD ResourceType (always 1)
            count($this->images) // WORD IconCount, Number of icon bitmaps in file
        );
    }

    private static function _packIconEntry($im, $offset, $data_size, $num_bits, $num_colors)
    {
        return
        pack('CCCCvvVV',
            $im->width,  // BYTE Width of icon in pixels
            $im->height, // BYTE Height of icon in pixels
            $num_colors, // BYTE ColorCount Maximum number of colors
            0,           // BYTE Reserved (always 0)
            1,           // WORD Planes (always 0 or 1)
            $num_bits,   // WORD BitCount (always 0)
            $data_size,  // DWORD BytesInRes Length of icon bitmap in bytes
            $offset      // DWORD ImageOffset Offset position of icon bitmap in file
        );
    }

	// Will move $bitCount UP to $desiredBitCount if $bitCount is found to be less than it.
    private static function _packIconData($im, $desiredBitCount = 1, $pngIfWidthExceeds = 48)
    {
        if ($im->width > 1024 || $im->height > 1024)
            throw new Exception ('unsupported image resolution');

        $gd = $im->getResource();

		imagesavealpha($gd, true);
		imagealphablending($gd, false);

		// Parse resource to determine header and icon format

		// Find Palette information
		$is_32bit = false; // Start with an assumption and get proven wrong
		$hasTransparency = 0;
		$blackColor = false;
		$bitCount = 0;
		$realPalette = array();
		$realIndexPalette = array();

		for ($x = 0; $x < $im->width && !$is_32bit; $x++) {
			for ($y = 0; $y < $im->height && !$is_32bit; $y++) {
				$colorIndex = imagecolorat($gd, $x, $y);
				$color = imagecolorsforindex($gd, $colorIndex);
				if ($color["alpha"] == 0) {
					// No point continuing if there's more than 256 colors or it's 32bit
					if (count($realPalette) < 257 && !$is_32bit) {
						$inRealPalette = false;
						foreach($realPalette as $realPaletteKey => $realPaletteColor) {
							if ( $color["red"] == $realPaletteColor["red"] && $color["green"] == $realPaletteColor["green"] && $color["blue"] == $realPaletteColor["blue"] ) {
								$inRealPalette = $realPaletteKey;
								break;
							}
						}
						if ($inRealPalette === false) {
							$realIndexPalette[$colorIndex] = count($realPalette);
							if ( $blackColor === false && $color["red"] == 0 && $color["green"] == 0 && $color["blue"] == 0 ) {
								$blackColor = count($realPalette);
							}
							$realPalette[] = $color;
						} else {
							$realIndexPalette[$colorIndex] = $inRealPalette;
						}
					}
				} else {
					$hasTransparency = 1;
				}
				if ($color["alpha"] != 0 && $color["alpha"] != 127)
					$is_32bit = true;
			}
		}

		if ($is_32bit) {
			$colorCount = 0;
			$bitCount = 32;
		} else {
			if ($hasTransparency && $blackColor === false) {
				// We need a black color to facilitate transparency.  Unfortunately, this can
				// increase the palette size by 1 if there's no other black color
				$blackColor = count($realPalette);
				$color = array(
					"red" => 0,
					"blue" => 0,
					"green" => 0,
					"alpha" => 0
				);
				$realPalette[] = $color;
			}
			$colorCount = count($realPalette);
			if ($colorCount > 256 || $colorCount == 0)
				$bitCount = 24;
			elseif ($colorCount > 16)
				$bitCount = 8;
			elseif ($colorCount > 2)
				$bitCount = 4;
			else
				$bitCount = 1;

            if ($desiredBitCount > $bitCount)
				$bitCount = $desiredBitCount;

			switch ($bitCount) {
            case 24: $colorCount = 0; break;
            case 8: $colorCount = 256; break;
            case 4: $colorCount = 16; break;
            case 1: $colorCount = 2; break;
			}
		}

		$data = '';
		if ($bitCount < 24) {
			$iconPalette = array();
			// Save Palette
			foreach ($realIndexPalette as $colorIndex => $paletteIndex) {
				$color = $realPalette[$paletteIndex];
				$data .= pack("CCCC", $color["blue"], $color["green"], $color["red"], 0);
			}

			while (strlen($data) < $colorCount * 4)
				$data .= pack("CCCC", 0, 0, 0, 0);

			// Save Each Pixel as Palette Entry
			$byte = 0; // For $bitCount < 8 math
			$bitPosition = 0; // For $bitCount < 8 math
			for ($y = 0; $y < $im->height; $y++) {
				for ($x = 0; $x < $im->width; $x++) {
					$color = imagecolorat($gd, $x, $im->height-$y-1);
					if (isset($realIndexPalette[$color])) {
						$color = $realIndexPalette[$color];
					} else {
						$color = $blackColor;
					}

					if ($bitCount < 8) {
						$bitPosition += $bitCount;
						$colorAdjusted = $color * pow(2, 8 - $bitPosition);
						$byte += $colorAdjusted;
						if ($bitPosition == 8) {
							$data .= chr($byte);
							$bitPosition = 0;
							$byte = 0;
						}
					} else {
						$data .= chr($color);
					}
				}
				// Each row ends with dumping the remaining bits and filling up to the 32bit line with 0's
				if ($bitPosition) {
					$data .= chr($byte);
					$bitPosition = 0;
					$byte = 0;
				}
				if (strlen($data) % 4)
                    $data .= str_repeat(chr(0), 4-(strlen($data)%4));
			}
		} else {
			// Save each pixel
			for ($y = 0; $y < $im->height; $y++) {
				for ($x = 0; $x < $im->width; $x++) {
					$color = imagecolorat($gd, $x, $im->height-$y-1);
					$color = imagecolorsforindex($gd, $color);
					if ($bitCount == 24) {
						if ($color["alpha"]) {
							$data .= pack("CCC", 0, 0, 0);
						} else {
							$data .= pack("CCC", $color["blue"], $color["green"], $color["red"]);
						}
					} else {
						$color["alpha"] = round((127-$color["alpha"]) / 127 * 255);
						$data .= pack("CCCC", $color["blue"], $color["green"], $color["red"], $color["alpha"]);
					}
				}
				if (strlen($data) % 4)
                    $data .= str_repeat(chr(0), 4-(strlen($data)%4));
			}
		}

		// save AND map (transparency)
		$byte = 0; // For $bitCount < 8 math
		$bitPosition = 0; // For $bitCount < 8 math
		for ($y = 0; $y < $im->height; $y++) {
			for ($x = 0; $x < $im->width; $x++) {
				if ($bitCount < 32) {
					$color = imagecolorat($gd, $x, $im->height-$y-1);
					$color = imagecolorsforindex($gd, $color);
					$color = $color["alpha"] == 127?1:0;
				} else {
					$color = 0;
				}

				$bitPosition += 1;
				$colorAdjusted = $color * pow(2, 8 - $bitPosition);
				$byte += $colorAdjusted;
				if ($bitPosition == 8) {
					$data .= chr($byte);
					$bitPosition = 0;
					$byte = 0;
				}
			}
			// Each row ends with dumping the remaining bits and filling up to the 32bit line with 0's.
			if ($bitPosition) {
				$data .= chr($byte);
				$bitPosition = 0; // For $bitCount < 8 math
				$byte = 0;
			}

			while (strlen($data) % 4)
				$data .= chr(0);
		}
		if ($colorCount >= 256)
			$colorCount = 0;

		$header = pack("LLLSSLLLLLL",
			40,            // Size
			$im->width,    // Width
			$im->height*2, // Height
			1,             // Planes
			$bitCount,     // BitCount
			0,             // Compression
			strlen($data), // ImageSize
			0,             // XpixelsPerM
			0,             // YpixelsPerM
			$colorCount,   // ColorsUsed
			0              // ColorsImportant
		);

		return array('data' => $header.$data, 'num_bits' => $bitCount, 'num_colors' => $colorCount, 'size' => 40 + strlen($data) );
	}

    function create()
    {
        if (!count($this->images))
            throw new Exception ('no images added');

        if (count($this->images) != 1)
            throw new Exception ('XXX multiple images not yet supported');

        $head = $this->_packFileHeader();
        $data = '';

		foreach ($this->images as $im)
        {
			$im_offset = 6 + (count($this->images) * 16) + strlen($data);

			if ($im_offset > pow(256, 4)) // 4 bytes available for position
				throw new Exception ('meh');

            $tmp = self::_packIconData($im);

            // ICONENTRY  IconDir[]   Directory of icon entries
			$head .= self::_packIconEntry($im, $im_offset, $tmp['size'], $tmp['num_bits'], $tmp['num_colors']);

            // ICONDATA  IconData[]  Listing of ICO bitmaps
			$data .= $tmp['data'];
		}
        return $head.$data;
    }

    function write($out)
    {
        $data = $this->create();
		file_put_contents($out, $data);
    }

}

?>
