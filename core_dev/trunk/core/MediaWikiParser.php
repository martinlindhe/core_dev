<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: early alpha, my own take at a mediawiki parser

class MediaWikiArticle
{
    var $summary;
}

class MediaWikiParser
{
    public static function parseArticle($markup)
    {
        $pos = strpos($markup, '==');
        if ($pos === false)
            throw new Exception ('unexpected wiki format '.$markup);

        $intro = substr($markup, 0, $pos);

        //XXX strip all text inside {{blabla}}



        $obj = new MediaWikiArticle();
        $obj->summary = $intro;

        return $obj;
    }
}

?>
