<?php
/**
 * $Id$
 *
 * Uses GNU 'diff' to produce a visual diff between two texts
 *
 * Tested with gnu diff 2.8.1
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//XXX drop the render method detection, its not finished & was a stupid idea

class diff
{
	private $r1, $r2;        ///< data revisions
	private $diff = array();
	private $mimeType;       ///< detected mimetype of input data

	/**
	 * @param $r1 file location or data
	 */
	function __construct($r1, $r2)
	{
		$this->r1 = $r1;
		$this->r2 = $r2;
	}

	function getMimeType() { return $this->mimeType; }

	/**
	 * Fills internal buffer with diff output
	 *
	 * @param $file1 filename of revision 1
	 * @param $file2 filename of revision 2
	 */
	function diffFiles($file1, $file2)
	{
		$c = 'diff --unified '.escapeshellarg($file1).' '.escapeshellarg($file2);
		exec($c, $this->diff);

		//remove the first 2 lines of diff output (tmp filenames)
		array_shift($this->diff);
		array_shift($this->diff);
	}

	/**
	 * Generates a diff
	 */
	function getDiff()
	{
		if (is_file($this->r1) && is_file($this->r2))
			$this->diffFiles($this->r1, $this->r2);

		else
		{
			//TODO: is it possible to feed strings directly into diff? "If a FILE is ‘-’, read standard input."
			$f1 = tempnam('', 'diff');
			$f2 = tempnam('', 'diff');

			//XXX ugly hack to avoid "\ No newline at end of file" from diff command. since we dont know the line ending mode, this will probably break sometime :-(
			file_put_contents($f1, $this->r1."\n");
			file_put_contents($f2, $this->r2."\n");

			$this->diffFiles($f1, $f2);

			unlink($f2);
			unlink($f1);
		}

		return $this->diff;
	}

	function render()
	{
		$xml_dialect = (strpos($this->r1, '<?xml ') !== false)  ? true : false;

		//rss1: <rdf:RDF xmlns="http://purl.org/rss/1.0/">
		if ($xml_dialect && strpos($this->r1, 'http://purl.org/rss/1.0/') !== false) {
			echo '<h1>XXX render RSS 1.0</h1>';
		}

		//<rss version="2.0">
		if ($xml_dialect && strpos($this->r1, '<rss ') !== false) {
			echo '<h1>XXX render RSS 2.0</h1>';
		}

		//<feed xmlns="http://www.w3.org/2005/Atom" ...>
		if ($xml_dialect && strpos($this->r1, 'http://www.w3.org/2005/Atom') !== false) {
			echo '<h1>XXX render ATOM</h1>';
		}

		if ($xml_dialect) {
			$this->mimeType = 'text/xml';
			return $this->renderXml();
		}

		//XXX detect images

		//text:
		if (strpos($this->r1, "\n") !== false) {
			$this->mimeType = 'text/plain';
			return $this->renderText();
		}
	}

	function renderXml()
	{
		//Indents xml to generate better looking diffs
		$this->r1 = $this->indentXml($this->r1);
		$this->r2 = $this->indentXml($this->r2);

		$x = implode("\n", $this->getDiff());

		return '<pre>'.htmlspecialchars( $x  ).'</pre>';
	}

	function indentXml($xml)
	{
		// add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
		$xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);

		$token = strtok($xml, "\n");
		$res   = '';
		$pad   = 0;

		while ($token !== false)
		{
			//open and closing tags on same line - no change
			if (preg_match('/.+<\/\w[^>]*>$/', $token))
				$indent = 0;

			//closing tag - outdent now
			else if (preg_match('/^<\/\w/', $token))
				$pad--;

			//opening tag - don't pad this one, only subsequent tags
			else if (preg_match('/^<\w[^>]*[^\/]>.*$/', $token))
				$indent = 1;

			else
				$indent = 0;

			$line   = str_pad($token, strlen($token)+$pad, ' ', STR_PAD_LEFT);
			$res   .= $line."\n";
			$token  = strtok("\n");
			$pad   += $indent;
		}

		return $res;
	}

	function renderText()
	{
		$out = '';
		foreach ($this->getDiff() as $l) {
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
			$out .= '<div class="diff_line" style="color: '.$col.'; background-color: '.$bg.';">'.htmlspecialchars($l).'</div>';
		}
		return $out;
	}
}

?>
