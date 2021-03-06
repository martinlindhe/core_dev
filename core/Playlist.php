<?php
/**
 * $Id$
 *
 * Renders a playlist in XSPF, PLS or M3U format
 *
 * References
 * ----------
 * http://validator.xspf.org/
 * http://en.wikipedia.org/wiki/Xspf
 * http://en.wikipedia.org/wiki/M3u
 * http://en.wikipedia.org/wiki/PLS_(file_format)
 *
 * http://schworak.com/programming/music/playlist_m3u.asp
 * http://gonze.com/playlists/playlist-format-survey.html
 *
 * XSPF Compatiblity (2009.08.05)
 * ------------------------------
 * ffmpeg/ffplay: dont support xspf playlists but SoC project (but only player for rtmp:// content)
 * VLC 1.0.1: works (not with rtmp:// content)
 * Totem 2.27: trouble loading xspf from certain url's: http://bugzilla.gnome.org/show_bug.cgi?id=590722
 * SMPlayer 0.67: dont support xspf playlists: https://sourceforge.net/tracker/index.php?func=detail&aid=1920553&group_id=185512&atid=913576
 * XBMC dont support xspf playlists: http://xbmc.org/trac/ticket/4763
 *
 * @author Martin Lindhe, 2009-2012 <martin@ubique.se>
 */

//STATUS: ok

//XXX deprecate pl->render($format) parameter, use ->setFormat() instead
//XXX TODO ability to load playlist from PLS files
//XXX TODO add input_xspf.php support, ability to fetch xspf from web

namespace cd;

require_once('CoreList.php');
require_once('Duration.php');
require_once('Url.php');
require_once('Timestamp.php');
require_once('AsxReader.php');
require_once('M3uReader.php'); //XXX: TODO support m3u playlists
require_once('XhtmlHeader.php');
require_once('MediaResource.php');

class Playlist extends CoreList
{
    private $headers = true;                ///< shall we send mime type?
    private $title   = 'Untitled playlist'; ///< name of playlist
    private $format  = 'xhtml';             ///< playlist output format

    private $org_url = '';                  ///< orginal source of the playlist (if external)

    function sendHeaders($bool = true) { $this->headers = $bool; }
    function setTitle($t) { $this->title = $t; }
    function setFormat($format) { $this->format = $format; }
    function setOrgUrl($s) { $this->org_url = $s; }

    /**
     * Adds a item to the feed list
     */
    function addItem($i)
    {
        if (!is_object($i))
            throw new \Exception ('not an object '.$i);

        /**
         * HACK: Needed to work around a limitation in xbmc, which needs rss
         * feeds in the format rss:// instead of http:// for all http links
         *
         * bug progress in xbmc: http://xbmc.org/trac/ticket/6186
         */
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'XBMC') !== false)
            $i->Url->setScheme('rss');

        switch (get_class($i)) {
        case 'VideoResource':
            $item = $i;
//            d($item);
            break;

        default:
            d('Playlist->addItem cant handle '.get_class($i) );
            return false;
        }
        parent::addItem($item);
    }

    /**
     * Loads input data from ASX playlists into VideoResource entries
     */
    function load($data)
    {
        if (is_url($data)) {
            $u = new HttpClient($data);
            $data = $u->getBody();
        }

        if (strpos($data, '<asx ') !== false) {
            $asx = new AsxReader();
            $asx->parse($data);
            $this->addItems( $asx->getItems() );
            return true;
        }

        echo "Playlist->load error: unhandled feed: ".substr($data, 0, 200)." ...".ln();
        return false;
    }

    /**
     * Sorts the list
     */
    function sort($callback = '')
    {
        if (!$callback) $callback = array($this, 'sortListDesc');

        uasort($this->items, $callback);
    }

    /**
     * List sort filter
     *
     * @return Internal list, sorted descending by published date
     */
    private function sortListDesc($a, $b)
    {
        if (!$a->Timestamp->get()) return 1;

        return ($a->Timestamp->get() > $b->Timestamp->get()) ? -1 : 1;
    }

    function render($format = '')
    {
        if ($format) {
            //echo "pl->render(FORMAT) is deprecated!! use ->setFormat()\n";
            $this->format = $format;
        }

        $page = XmlDocumentHandler::getInstance();

        switch ($this->format) {
        case 'xspf':
            if ($this->headers) {
                $page->setMimeType('application/xspf+xml');
                $page->disableDesign();
            }
            return $this->renderXSPF();

        case 'm3u':
            if ($this->headers) {
                $page->setMimeType('audio/x-mpegurl');
                $page->disableDesign();

            }
            return $this->renderM3U();

        case 'pls':
            if ($this->headers) {
                $page->setMimeType('audio/x-scpls');
                $page->disableDesign();

            }
            return $this->renderPLS();

        case 'sh':
            if ($this->headers) {
                $page->setMimeType('text/plain; charset=utf-8');
                $page->disableDesign();
            }
            return $this->renderSh();

        case 'xhtml':
        case 'html':
            return $this->renderXHTML();

        case 'atom':
            $feed = new NewsFeed();
            $feed->sendHeaders($this->headers);
            $feed->addItems( $this->getItems() );
            $feed->setTitle($this->title);
            return $feed->render('atom');

        case 'rss2':
        case 'rss':
            $feed = new NewsFeed();
            $feed->sendHeaders($this->headers);
            $feed->addItems( $this->getItems() );
            $feed->setTitle($this->title);
            return $feed->render('rss');
        }

        echo "Playlist->render: unknown format ".$this->format."\n";
        return false;
    }

    private function renderXSPF()
    {
        $res  = '<?xml version="1.0" encoding="UTF-8"?>';
        $res .= '<playlist version="1" xmlns="http://xspf.org/ns/0/">';
        $res .= '<trackList>'."\n";

        foreach ($this->getItems() as $item)
        {
            $res .= '<track>';
            $title = ($item->Timestamp ? $item->Timestamp->render().' ' : '').$item->title;
            //if ($item->desc) $title .= ' - '.$item->desc;
            $res .= '<title>'.cdata_embed($title).'</title>';

            $res .= '<location>'.$item->Url.'</location>';

            if ($item->Duration)
                $res .= '<duration>'.$item->Duration->inMilliseconds().'</duration>';

            if ($item->thumbnail)
                $res .= '<image>'.$item->thumbnail.'</image>';

            $res .= '</track>'."\n";
        }

        $res .= '</trackList>';
        $res .= '</playlist>';

        return $res;
    }

    private function renderM3U()
    {
        $res = "#EXTM3U\n";
        foreach ($this->getItems() as $item)
        {
            $res .=
            "#EXTINF:".($item->Duration ? round($item->Duration->inSeconds(), 0) : '-1').",".($item->title ? $item->title : 'Untitled track')."\n".
            $item->Url."\n";
        }

        return $res;
    }

    private function renderPLS()
    {
        $res =
        "[playlist]\n".
        "NumberOfEntries=".count($this->items)."\n".
        "\n";

        $i = 0;
        foreach ($this->getItems() as $item)
        {
            $i++;
            $res .=
            "File".  $i."=".$item->Url."\n".
            "Title". $i."=".($item->title ? $item->title : 'Untitled track')."\n".
            "Length".$i."=".($item->Duration ? $item->Duration->inSeconds() : '-1')."\n".
            "\n";
        }
        $res .= "Version=2\n";
        return $res;
    }

    /**
     * Creates BASH Shell script compatible download code, using wget, rtmpdump
     */
    private function renderSh()
    {
        $user_agent = 'QuickTime/7.6.2';

        $res = "# shell script downloader\n\n";
        foreach ($this->getItems() as $item)
        {
            $res .=
            "# ".($item->title ? $item->title : 'Untitled track')." (".sql_datetime($item->getTimestamp()).")\n";

            $outfile = basename($item->Url);
            $res .= "if [ ! -e ".$outfile." ]; then\n";

            switch (get_protocol($item->Url)) {
            case 'http':
                $c =
                'curl'.
                ' "'.$item->Url.'"'.
                ' --user-agent "'.$user_agent.'"'.
                ' --output '.$outfile;
                break;

            case 'rtmp':
            case 'rtmpe':
                $c =
                'rtmpdump'.
                // ' --verbose'.
                ' --rtmp '.$item->Url.
                ' --flv '.$outfile;
                break;

            case 'rtsp':
                /*
                $c = 'mplayer'.
                ' -dumpstream "'.$item->Url.'"'.
                ' -dumpfile '.$outfile;
                */

                $c = 'cvlc'.
                ' --rtsp-tcp '.$item->Url.
                ' --sout=file/mp4:'.$outfile;
                break;

            case 'mms':
                $c = 'mimms'.
                ' --verbose'.
                // ' --bandwidth='.(1024*1024*10). // 10 MiB/sec
                ' "'.$item->Url.'"'.
                ' '.$outfile;
                break;

            default:
                throw new \Exception ('unhandled protocol: '.get_protocol($item->Url) );
            }
            $res .=
            "\t".$c."\n".
            "fi\n\n";
        }

        return $res;
    }

    /**
     * Renders the playlist as a HTML table
     */
    private function renderXHTML()
    {
        $res = '';

        if ($this->org_url)
            $res .= '<a href="'.$this->org_url.'" target="_blank">Show orginal feed</a><br/><br/>';

        $res .= '<table summary="" border="1">';

        foreach ($this->getItems() as $item)
        {
            $title = $item->Timestamp ? $item->Timestamp->render().' ' : '';

            $title .=
                ($item->Url ? '<a href="'.htmlentities($item->Url).'">' : '').
                ($item->title ? $item->title : 'Untitled entry').
                ($item->Url ? '</a>' : '');

            $res .=
            '<tr><td>'.
            '<h2>'.$title.'</h2>'.
            ($item->Timestamp ? $item->Timestamp->getRelative().'<br/>' : '').
            ($item->thumbnail ? '<img src="'.$item->thumbnail.'" width="320" style="float: left; padding: 10px;"/>' : '').
            ($item->desc ? '<p>'.$item->desc.'</p>' : '').
            ($item->Duration ? t('Duration').': '.$item->Duration->render().'<br/>' : '').
            '</td></tr>';
        }

        $res .= '</table>';

        return $res;
    }

}

?>
