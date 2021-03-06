<?php
/**
 * $Id$
 *
 * Renders the feed in Atom 1.0 format
 *
 * http://www.atomenabled.org/developers/syndication/
 * http://en.wikipedia.org/wiki/Atom_(standard)
 *
 * Output mostly comply with http://feedvalidator.org/
 *
 * @author Martin Lindhe, 2008-2011 <martin@ubique.se>
 */

//MIME: application/atom+xml

//STATUS: wip

//TODO: extend this class for specific video feeds
//XXX atom output: no way to embed video duration, <link length="x"> is size of the resource, in bytes.

namespace cd;

require_once('FeedWriter.php');

class AtomWriter extends FeedWriter
{
    function render()
    {
        $res =
        '<?xml version="1.0" encoding="UTF-8"?>'.
        '<feed xmlns="http://www.w3.org/2005/Atom">'.
            '<id>'.htmlspecialchars($this->url).'</id>'.
            '<title><![CDATA['.$this->title.']]></title>'.
            ($this->TimeUpdated ? '<updated>'.$this->TimeUpdated->getRFC3339().'</updated>' : '').
            '<link rel="self" href="'.htmlspecialchars($this->url).'"/>'.
            '<author><name>'.$this->author.'</name></author>'.
            '<generator>'.$this->version.'</generator>'."\n";

        foreach ($this->getItems() as $item)
        {
            //link directly to video if no webpage url was found
            if (!$item->getUrl() && $item->video_url)
                $item->Url->set( $item->video_url );

            $res .=
            '<entry>'.
                '<id>'.($item->guid ? $item->guid : $item->getUrl() ).'</id>'.
                '<title><![CDATA['.$item->getTitle().']]></title>'.
                '<link rel="alternate" href="'.$item->getUrl().'"/>'.
                '<content type="html"><![CDATA['.($item->desc ? $item->desc : ' ').']]></content>'.
                '<updated>'.$item->getTimestamp()->getRFC3339().'</updated>'.
                ($item->author ? '<author><name>'.$item->author.'</name></author>' : '').
//                ($item->video_url ? '<link rel="enclosure" type="'.$item->video_mime.'" href="'.htmlspecialchars($item->video_url).'"/>' : '').
//                ($item->image_url ? '<link rel="enclosure" type="'.$item->image_mime.'" href="'.htmlspecialchars($item->image_url).'"/>' : '').
            '</entry>'."\n";
        }
        $res .=
        '</feed>';
        return $res;
    }
}

?>
