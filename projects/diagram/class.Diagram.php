<?
/**
 *
 *
 */


class Diagram
{
	private $im;		///< gd render target
	private $width;		///< Width of output image
	private $height;	///< Height of output image
	private $bg_r = 150, $bg_g = 150, $bg_b = 150;	///< Background color
	private $txt_r = 233, $txt_g = 220, $txt_b = 110;	///< Text color
	private $font = 'arial.ttf';	///< fixme make changeable
	private $ttf_size = 14;			///< fixme make changeable
	private $txt_col;

	private $vline_min, $vline_max, $vline_step, $vline_text = '';	///< Settings for vertical line of diagram
	private $hline_min, $hline_max, $hline_step, $hline_text = '';	///< Settings for horizontal line of diagram


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

	private function Render()
	{
		$this->im = imagecreate($this->width, $this->height);

		$bg_col = imagecolorallocate($this->im, $this->bg_r, $this->bg_g, $this->bg_b);
		$this->txt_col = imagecolorallocate($this->im, $this->txt_r, $this->txt_g, $this->txt_b);

		$fh = $this->ttfHeight($this->vline_text);
		$v_len = $this->ttfWidth($this->vline_text);
		$h_len = $this->ttfWidth($this->hline_text);

		//Print vertical text centered to the left
		$vx = $fh;
		$vy = ($this->height/2) + $v_len/2;
		$this->ttfVText($vx, $vy, $this->vline_text);

		//Print horizontal text centered on the bottom
		$hx = ($this->width/2) - $h_len/2;
		$hy = $this->height-($fh/2);
		$this->ttfHText($hx, $hy, $this->hline_text);

		//Draw vertical scale - TODO

		//Draw horizontal scale1 - TODO

		//Draw diagram content - TODO
	}

	private function ttfHeight($txt)
	{
		$tmp = imagettfbbox($this->ttf_size, 0, $this->font, $txt);
		return $tmp[1] - $tmp[7];	//font height
	}

	private function ttfWidth($txt)
	{
		$tmp = imagettfbbox($this->ttf_size, 0, $this->font, $txt);
		return $tmp[2] - $tmp[0];	//font width
	}


	private function ttfVText($x, $y, $txt)
	{
		imagettftext($this->im, $this->ttf_size, 90, $x, $y, $this->txt_col, $this->font, $txt);
	}

	private function ttfHText($x, $y, $txt)
	{
		imagettftext($this->im, $this->ttf_size, 0, $x, $y, $this->txt_col, $this->font, $txt);
	}

}