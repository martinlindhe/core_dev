<?php
/**
 * $Id$
 *
 * This file is part of core_dev. For licencing, see docs/LICENCE.BSD
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

class Diagram
{
	private $im;			///< gd render target
	private $width = 400;	///< Width of output image
	private $height = 400;	///< Height of output image
	private $bg_r = 150, $bg_g = 150, $bg_b = 150;	///< Background color
	private $txt_r = 233, $txt_g = 220, $txt_b = 110;	///< Text color
	private $line_r = 0, $line_g = 0, $line_b = 0;	///< Line color
	private $font = './arial.ttf';	///< fixme make changeable
	private $ttf_size = 14;			///< fixme make changeable
	private $bg_col, $txt_col, $line_col;

	private $vline_min = 1, $vline_max = 10, $vline_step = 1, $vline_text = '';	///< Settings for vertical line of diagram
	private $hline_min = 1, $hline_max = 20, $hline_step = 0.5, $hline_text = '';	///< Settings for horizontal line of diagram

	private $h_spacing = 40, $v_spacing = 40;	///< ammount of space between border & horizontal line (in pixels)

	function __construct()
	{
	}

	function __destruct()
	{
		imagedestroy($this->im);
	}

	function VLine($min, $max, $step)
	{
		$this->vline_min = $min;
		$this->vline_max = $max;
		$this->vline_step = $step;
	}

	function HLine($min, $max, $step)
	{
		$this->hline_min = $min;
		$this->hline_max = $max;
		$this->hline_step = $step;
	}

	/**
	 * Sets the text to display vertically to the left of the diagram
	 */
	function VText($s)
	{
		$this->vline_text = $s;
	}

	/**
	 * Sets the text to display horizontally below the diagram
	 */
	function HText($s)
	{
		$this->hline_text = $s;
	}

	/**
	 * Sets the output dimensions of the resulting image
	 */
	function Size($w, $h)
	{
		$this->width = $w;
		$this->height = $h;
	}

	function BGCol($r, $g, $b)
	{
		$this->bg_r = $r;
		$this->bg_g = $g;
		$this->bg_b = $b;
	}

	function TextCol($r, $g, $b)
	{
		$this->txt_r = $r;
		$this->txt_g = $g;
		$this->txt_b = $b;
	}

	function LineCol($r, $g, $b)
	{
		$this->line_r = $r;
		$this->line_g = $g;
		$this->line_b = $b;
	}

	/**
	 * Outputs the resulting image to a PNG file
	 */
	function PngOut($filename)
	{
	}

	/**
	 * Outputs the resulting image to the browser
	 */
	function Display()
	{
		$this->Render();

		header('Content-type: image/png');
		imagepng($this->im);
	}



	/******************************************
	 ******************************************
	 **** PRIVATE API *************************
	 *****************************************/

	/**
	 * Draws the diagram based on the settings given
	 *
	 * \todo support built in ugly fonts, see functions_image.php: pngCenterText()
	 */
	private function Render()
	{
		$this->im = imagecreate($this->width, $this->height);

		$this->bg_col = imagecolorallocate($this->im, $this->bg_r, $this->bg_g, $this->bg_b);
		$this->txt_col = imagecolorallocate($this->im, $this->txt_r, $this->txt_g, $this->txt_b);
		$this->line_col = imagecolorallocate($this->im, $this->line_r, $this->line_g, $this->line_b);

		$fh = $this->ttfHeight($this->vline_text);
		$v_len = $this->ttfWidth($this->vline_text);
		$h_len = $this->ttfWidth($this->hline_text);




		//Print vertical text centered to the left
		$vx = $fh;
		$vy = ($this->height/2) + $v_len/2;
		$this->ttfVText($vx, $vy, $this->vline_text);

		//Draw vertical scale - TODO
		$v_steps = 0;
		for ($i = $this->vline_min; $i <= $this->vline_max; $i += $this->vline_step) {
			$v_steps++;
		}

		//Draw vertical line
		imagefilledrectangle($this->im, $vx + $fh-1, $this->h_spacing, $vx + $fh, $this->height-$this->h_spacing, $this->line_col);







		//Print horizontal text centered on the bottom
		$hx = ($this->width/2) - $h_len/2;
		$hy = $this->height-($fh/2);
		$this->ttfHText($hx, $hy, $this->hline_text);

		//Draw horizontal scale1 - WIP
		$h_steps = 1+($this->hline_max-$this->hline_min)/$this->hline_step;

		$h_width = $this->width - $this->h_spacing*2;	//40 px padding on each side
		$h_multiplier = $h_width / $h_steps;

		for ($i = 0; $i <= $h_steps; $i++) {
			imagesetpixel($this->im, $this->h_spacing+($i*$h_multiplier), $this->height-50,$this->txt_col);
			$txt = $this->hline_min+$i;
			//FIXME: 7 should be width of $txt
			$this->ttfHText($this->h_spacing+($i*$h_multiplier) - 7, $hy-$fh, $txt, 8);
		}

		//Draw horizontal line
		imagefilledrectangle($this->im, $this->h_spacing, $this->height - $this->v_spacing, $this->width-$this->h_spacing, $this->height-$this->h_spacing+1, $this->line_col);


		//Draw diagram content - TODO
	}

	private function ttfHeight($txt, $size = 0)
	{
		if (!$txt) return 0;
		if (!$size) $size = $this->ttf_size;
		$tmp = imagettfbbox($size, 0, $this->font, $txt);
		return $tmp[1] - $tmp[7];	//font height
	}

	private function ttfWidth($txt, $size = 0)
	{
		if (!$txt) return 0;
		if (!$size) $size = $this->ttf_size;
		$tmp = imagettfbbox($size, 0, $this->font, $txt);
		return $tmp[2] - $tmp[0];	//font width
	}


	private function ttfVText($x, $y, $txt, $size = 0)
	{
		if (!$txt) return;
		if (!$size) $size = $this->ttf_size;
		imagettftext($this->im, $size, 90, $x, $y, $this->txt_col, $this->font, $txt);
	}

	private function ttfHText($x, $y, $txt, $size = 0)
	{
		if (!$txt) return;
		if (!$size) $size = $this->ttf_size;
		imagettftext($this->im, $size, 0, $x, $y, $this->txt_col, $this->font, $txt);
	}

}
?>
