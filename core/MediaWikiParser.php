<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@ubique.se>
 */

//STATUS: early alpha, my own take at a mediawiki parser

namespace cd;

class MediaWikiArticle
{
    var $summary;
}

class MediaWikiParser
{
    public static function parseArticle($markup)
    {
        $pos = strpos($markup, '==');
        if ($pos !== false)
            $intro = substr($markup, 0, $pos);
        else
        {
            //like: "#OMDIRIGERING [[Släkt#Släktskapstermer]] [[da:Faster]]"
            $intro = $markup;
        }


        //XXX strip all text inside {{blabla}}


        $obj = new MediaWikiArticle();
        $obj->summary = $intro;

        return $obj;
    }
}

?>
