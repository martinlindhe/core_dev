<?php
/**
 * $Id$
 *
 * General functions for Portable Document Format (.pdf) format
 *
 * http://www.adobe.com/devnet/pdf/pdf_reference.html
 * http://www.adobe.com/devnet/acrobat/pdfs/PDF32000_2008.pdf
 * http://www.adobe.com/devnet/livecycle/articles/lc_pdf_overview_format.pdf
 *
 * @author Martin Lindhe, 2008 <martin@startwars.org>
 */

/**
 * Beginning of file marker
 */
function pdfBOF()
{
	return "%PDF-1.6\n";
}

/**
 * XXX
 *
 * @param $p is page content
 */
function pdfPage($p)
{
	//Page header
	$out  = "1 0 obj\n";	//XXX 1 is the page number
	$out .= "<</Type /Page\n";
	$out .= "/Parent 1 0 R\n";
	$out .= "/Resources 2 0 R\n";
	$out .= "/Contents 1 0 R>>\n";	//XXX 1 is page nr??? perhaps
	$out .= "endobj\n";

	//Page content
	$out .= "2 0 obj\n";	//XXX 2 is page nr
	$out .= "<</Length ".strlen($p).">>\n";
	$out .= "stream\n";
	$out .= $p."\n";
	$out .= "endstream\n";
	$out .= "endobj\n";

	//Page root
	$out .= "1 0 obj\n";	//XXX ??
	$out .= "<</Type /Pages\n";
/*
	$kids='/Kids [';
	for($i=0;$i<$nb;$i++)
		$kids.=(3+2*$i).' 0 R ';
	$this->_out($kids.']');
	$this->_out('/Count '.$nb);
	$this->_out(sprintf('/MediaBox [0 0 %.2f %.2f]',$wPt,$hPt));
* */
	$out .= ">>\n";
	$out .= "endobj\n";

	return $out;
}

/**
 * End of file
 */
function pdfTrailer($o)
{
	$out  = "trailer\n";
	$out .= "<<\n";

	$out .= "/Size ".($this->n+1)."\n";		//XXX??
	$out .= "/Root ".$this->n." 0 R\n";
	$out .= "/Info ".($this->n-1)." 0 R\n";

	$out .= ">>\n";
	$out .= "startxref\n";
	$out .= $o."\n";	//XXX vad e detta? längden på all data?
	$out .= "%%EOF\n";
}

?>
