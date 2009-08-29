<?php
/**
 * $Id$
 *
 *	core                                            tblWiki
 *	for history-support: atom_revisions.php         tblRevisions
 *	for files-support: files_default.php $h->files  tblFiles
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

//XXX drop file!

require_once('class.Wiki.php');

/**
 * Display / edit wiki gadget
 *
 * Normally everyone can edit a wiki text and attach files to it (FIXME),
 * but you can override defaults with config settings.
 * Also, you can lock a specific wiki from editing by normal users.
 */
function wiki($wikiName = '')
{
	$w = new wiki('Index');
	$w->render();
}
?>
