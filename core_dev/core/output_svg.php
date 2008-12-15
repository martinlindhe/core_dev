<?php
/**
 * $Id$
 *
 * Simple SVG renderer
 * Currently only capable of rendering a set of polygons
 *
 * Documentation:
 * http://www.w3.org/TR/SVG11/
 * http://www.w3.org/Graphics/SVG/
 * http://www.w3.org/TR/SVG/shapes.html
 *
 * SVG test suite:
 * http://www.w3.org/Graphics/SVG/Test/
 *
 * @author Martin Lindhe, 2008 <martin@startwars.org>
 */

//FIXME: opacity is not quite correct

class svg
{
	var $polygons = array();
	var $width, $height;
	var $bgcolor = false;	///< background color

	function __construct($width = 100, $height = 100)
	{
		$this->width = $width;
		$this->height = $height;
	}

	function addPoly($poly)
	{
		$this->polygons[] = $poly;
	}

	function addPolys($list)
	{
		if (!is_array($list)) return false;

		foreach ($list as $poly) {
			$this->polygons[] = $poly;
		}
	}

	function setBackground($col)
	{
		$this->bgcolor = $col;
	}

	function render()
	{
		$res =
		'<?xml version="1.0" encoding="UTF-8"?>'.
		'<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'.
		'<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"'.
			' version="1.1" width="'.$this->width.'px" height="'.$this->height.'px" viewBox="0 0 '.$this->width.' '.$this->height.'">';

		//SVG has a transparent background by default. simulate background color with a filled rectangle
		if ($this->bgcolor !== false) {
			$res .=
			'<rect x="0" y="0" width="'.$this->width.'" height="'.$this->height.'" fill="'.sprintf('%06x', $this->bgcolor).'"/>';
		}

		foreach ($this->polygons as $poly) {
			$fill_a = ($poly['color'] >> 24) & 0xFF;
			$fill_a = round($fill_a/127, 2);		//XXX loss of precision
			$poly['color'] = $poly['color'] & 0xFFFFFF;

			if (!empty($poly['border'])) {
				$stroke_a = ($poly['border'] >> 24) & 0xFF;
				$stroke_a = round($stroke_a/127, 2);
				$poly['border'] = $poly['border'] & 0xFFFFFF;
			}

			$res .=
			'<polygon'.
				' fill="#'.sprintf('%06x', $poly['color']).'"'.
				($fill_a < 1 ? ' fill-opacity="'.$fill_a.'"' : '');
				if (!empty($poly['border'])) {
					$res .=
					' stroke-width="1" stroke="#'.sprintf('%06x', $poly['border']).'"'.
					($stroke_a < 1 ? ' stroke-opacity="'.$stroke_a.'"': '');
				}

			$res .= ' points="';
			for ($i=0; $i<count($poly['coords']); $i+=2) {
				$res .= $poly['coords'][$i].','.$poly['coords'][$i+1];
				if ($i < count($poly['coords'])-2) $res .= ',';
			}
			$res .=
			'"/>';
		}

		$res .=
		'</svg>';

		return $res;
	}

	function output()
	{
		header('Content-type: image/svg+xml');

		echo $this->render();
	}

	function save($filename)
	{
		file_put_contents($filename, $this->render());
	}
}

?>