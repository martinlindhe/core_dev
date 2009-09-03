<?php
/**
 * $Id$
 *
 * Uses GNU 'diff' to produce a visual diff between two texts
 *
 * Tested with: diff (GNU diffutils) 2.8.1
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

class diff
{
	private $diff = array();

	function getDiff() { return implode("\n", $this->diff); }

	function strings($str1, $str2)
	{
		//TODO: is it possible to feed strings directly into diff?  "If a FILE is ‘-’, read standard input."
		$f1 = tempnam('', 'diff');
		$f2 = tempnam('', 'diff');

		file_put_contents($f1, $str1);
		file_put_contents($f2, $str2);

		$this->files($f1, $f2);

		unlink($f2);
		unlink($f1);
		return $this->getDiff();
	}

	function files($file1, $file2)
	{
		$c = 'diff --unified '.escapeshellarg($file1).' '.escapeshellarg($file2);
		exec($c, $this->diff);
		return $this->getDiff();
	}

	function output()
	{
		foreach ($this->diff as $l) {
			if (substr($l, 0, 4) == '--- ') {
				$col = '#888';
				$bg  = '#fff';
			} else if (substr($l, 0, 4) == '+++ ') {
				$col = '#888';
				$bg  = '#fff';
			} else if (substr($l, 0, 3) == '@@ ') {
				$col = '#888';
				$bg  = '#fff';
			} else if (substr($l, 0, 1) == '+') {
				$col = '#000';
				$bg  = '#93e793';
			} else if (substr($l, 0, 1) == '-') {
				$col = '#000';
				$bg  = '#f17979';
			} else {
				$col = '#000';
				$bg  = '#fff';
			}
			echo '<div class="diff_line" style="color: '.$col.'; background-color: '.$bg.'">'.$l.'</div>';
		}
	}
}

?>
