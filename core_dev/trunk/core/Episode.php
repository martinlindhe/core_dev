<?php
/**
 * $Id$
 *
 * Episode parser, handles input formats such as "Season 1, Episode 24", "S01E24", "1x24", "s1e24"
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: WIP

require_once('class.CoreProperty.php');

class Episode extends CoreProperty
{
    private $season, $episode;

    function __construct($s = '')
    {
        if ($s)
            $this->set($s);
    }

    function set($s)
    {
        // 1x24, 01x24
        preg_match('/(?<season>[0-9]+)x(?<episode>[0-9]+)/i', $s, $match);
        if (!empty($match['season']) && !empty($match['episode'])) {
            $this->season  = intval($match['season']);
            $this->episode = intval($match['episode']);
            return;
        }

        // S01E24, s1e24
        preg_match('/s(?<season>[0-9]+)e(?<episode>[0-9]+)/i', $s, $match);
        if (!empty($match['season']) && !empty($match['episode'])) {
            $this->season  = intval($match['season']);
            $this->episode = intval($match['episode']);
            return;
        }

        // season 1, episode 24
        preg_match('/season (?<season>[0-9]+), episode (?<episode>[0-9]+)/i', $s, $match);
        if (!empty($match['season']) && !empty($match['episode'])) {
            $this->season  = intval($match['season']);
            $this->episode = intval($match['episode']);
            return;
        }
    }

    /**
     * @return Episode in "1x7" format
     */
    function get()
    {
        return $this->season.'x'.$this->episode;
    }

    /**
     * @return Episode in "S01E07" format
     */
    function getFormatted()
    {
        return 'S'.str_pad($this->season, 2, '0', STR_PAD_LEFT).'E'.str_pad($this->episode, 2, '0', STR_PAD_LEFT);
    }
}

?>
