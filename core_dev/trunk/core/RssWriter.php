<?php
/**
 * $Id$
 *
 * Renders the feed in RSS 2.0 format with Media RSS tags for media content
 *
 * http://www.rssboard.org/rss-specification
 * <media> extension: http://video.search.yahoo.com/mrss
 *
 * Output mostly comply with http://feedvalidator.org/
 *
 * MIME: application/rss+xml
 *
 * @author Martin Lindhe, 2008-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: extend this class for specific video feeds
//TODO: verify $item is of right class (NewsItem)

namespace cd;

require_once('FeedWriter.php');

class RssWriter extends FeedWriter
{
    function render()
    {
        $res =
        '<?xml version="1.0" encoding="UTF-8"?>'.
        '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/">'.
            '<channel>'.
                '<title><![CDATA['.$this->title.']]></title>'.
                '<link>'.htmlspecialchars($this->url).'</link>'.
                '<description><![CDATA['.$this->desc.']]></description>'.
                ($this->ttl ? '<ttl>'.$this->ttl.'</ttl>' : '').
                '<atom:link rel="self" type="application/rss+xml" href="'.htmlspecialchars($this->url).'"/>'.
                ($this->TimeUpdated ? '<lastBuildDate>'.$this->TimeUpdated->getRFC822().'</lastBuildDate>' : '').
                '<generator>'.$this->version.'</generator>'."\n";

        foreach ($this->getItems() as $item)
        {
            //link directly to video if no webpage url was found
            if (!$item->getUrl() && $item->video_url)
                $item->Url->set( $item->video_url );

            $res .=
            '<item>'.
                '<title><![CDATA['.$item->getTitle().']]></title>'.
                '<link>'.htmlspecialchars( $item->getUrl() ).'</link>'.
                '<description><![CDATA['.$item->desc.']]></description>'.
                '<pubDate>'.$item->getTimestamp()->getRFC822().'</pubDate>'.
                ($item->guid ? '<guid>'.$item->guid.'</guid>' : '').
//                ($item->video_url ? '<media:content medium="video" type="'.$item->video_mime.'" url="'.htmlspecialchars($item->video_url).'"'.($item->Duration->get() ? ' duration="'.$item->Duration->inSeconds().'"' : '').'/>' : '').
//                ($item->image_url ? '<media:content medium="image" type="'.$item->image_mime.'" url="'.htmlspecialchars($item->image_url).'"/>' : '').
            '</item>'."\n";
        }

        $res .=
            '</channel>'.
        '</rss>';

        return $res;
    }

}

?>
