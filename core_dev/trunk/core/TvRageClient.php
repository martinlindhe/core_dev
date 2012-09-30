<?php
/**
 * $Id$
 *
 * tvrage.com API client for fetching tv show metadata
 *
 * API docs: http://services.tvrage.com/info.php?page=main
 *
 * To get access to show summaries, request from http://services.tvrage.com/
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

//XXX: waiting for approval of request for access to show & episode summaries sent at 2011-02-10

//TODO: in getShow, also parse genres, and also parse "aka" titles

namespace cd;

require_once('HttpClient.php');
require_once('TempStore.php');

require_once('TvShow.php');
require_once('TvEpisode.php');

class TvRageClient extends \cd\HttpClient
{
    private $period_from, $period_to;

    private $api_key = '';

    function setApiKey($s) { $this->api_key = $s; }

    /**
     * Selector range for episodes to include. Used to reduce number episodes returned
     */
    function setPeriod($from, $to)
    {
        $from = ts($from);
        $to   = ts($to);
        if (!$from || !$to) return false;

        $this->period_from = $from;
        $this->period_to   = $to;
    }

    function getShow($id)
    {
        if (!$id || !is_numeric($id))
            throw new \Exception ('bad id: '.$id);

        $url = 'http://services.tvrage.com/feeds/full_show_info.php?sid='.$id;
        $this->setUrl($url);
        if ($this->api_key)
            $this->Url->setParam('key', $this->api_key);

        $data = $this->getBody();

        $xml = simplexml_load_string($data);
        if (!$xml) {
            echo "ERROR: getShow( ".$id." ) failed<br/>";
            return false;
        }

        $show = new TvShow();
        $show->id           = $id;
        $show->info_url     = strval($xml->showlink);
        $show->name         = strval($xml->name);
        $show->country      = strval($xml->origin_country);
        $show->thumb_url    = strval($xml->image);
        $show->started      = sql_date( self::parseDate( strval($xml->started) ) );
        $show->ended        = sql_date( self::parseDate( strval($xml->ended) ) );
        $show->status       = self::parseStatus($xml->status, $show->started, $show->ended);
        $show->time_updated = sql_datetime( time() );
        TvShow::store($show);

        if (!$xml->Episodelist)
            return $show;

        foreach ($xml->Episodelist->Season as $season)
        {
            $attrs = $season->attributes();

            foreach ($season as $e)
            {
                $ep = new TvEpisode();
                $ep->owner  = $show->id;
                $ep->title  = strval($e->title);
                $ep->link   = strval($e->link);
                $ep->setDate( strval($e->airdate) );
                $ep->setEpisode( $attrs['no'].'x'.$e->seasonnum );
                TvEpisode::store($ep);

                //only include episodes in period if it is set
                if (!$this->period_from && !$this->period_to || ($ep->getDate() >= $this->period_from && $ep->getDate() <= $this->period_to))
                    $show->addEpisode($ep);
            }
        }

        return $show;
    }

    /**
     * tvrage.com returns max 20 results
     *
     * @return list of matching show names & tvrage.com id:s
     */
    function searchShows($query)
    {
        if (!$query)
            return false;

        $temp = TempStore::getInstance();

        $key = 'TvRageClient/shows/'.$query;
        $res = $temp->get($key);

        if ($res)
            return unserialize($res);

        $url = 'http://services.tvrage.com/feeds/full_search.php?show='.urlencode($query);
        $this->setUrl($url);
        if ($this->api_key)
            $this->Url->setParam('key', $this->api_key);

        $data = $this->getBody();

        $xml = simplexml_load_string($data);

        $res = array();

        foreach ($xml->show as $s)
        {
            $show = new TvShow();
            $show->id      = strval($s->showid);
            $show->name    = strval($s->name);
            $show->country = strval($s->country);
            $show->started = sql_date( self::parseDate(strval($s->started)) );
            $show->ended   = sql_date( self::parseDate(strval($s->ended)) );
            $show->status  = self::parseStatus($s->status, $show->started, $show->ended);
            TvShow::store($show);

            $res[] = $show;
        }

        $temp->set($key, serialize($res), '24h');

        return $res;
    }

    static function parseStatus($s, $started, $ended)
    {
        switch (strval($s)) {
        case 'Canceled/Ended':
            return $started.' - '.$ended.' (ended)';
            break;

        case 'Final Season':
        case 'Returning Series':
        case 'New Series':
            return $started.' - now (running)';
            break;

        case 'On Hiatus':
        case 'TBD/On The Bubble':
            return $started.' - now (pause)';
            break;

        case 'Never Aired':
        case 'Pilot Rejected':
        case 'In Development':
            return '(not aired)';
            break;

        default:
            dp('FIXME: unhandled status: '.strval($s) );
            return 'XXX-ODDSTATUS '.strval($s);
        }
    }

    /**
     * @s input string such as "Sep/22/2004" or "Sep/2004"
     * @return Unix timestamp
     */
    static function parseDate($s)
    {
        if (!$s)
            return false;

        $x = explode('/', $s);
        $months = array('Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12);

        if (count($x) == 1 && $x[0] >= 1900 && $x[0] <= (date('Y')+10) )
            return mktime(0, 0, 0, 1, 1, $x[0]);

        if (!array_key_exists($x[0], $months))
            throw new \Exception ('unknown input: "'.$s.'", x0: '.$x[0].', x1: '.$x[1]);

        $mon = $months[ $x[0] ];
        if (count($x) == 2) {
            $day  = 1;
            $year = $x[1];
        } else if (count($x) == 3) {
            $day  = $x[1];
            $year = $x[2];
        } else
            throw new \Exception ('odd format: '. $s);

        return mktime(0, 0, 0, $mon, $day, $year);
    }

}

?>
