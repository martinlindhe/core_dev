<?php
/**
 * $Id$
 *
 * Episode parser, handles input formats such as "Season 1, Episode 24", "S01E24", "1x24", "s1e24"
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: WIP

require_once('class.CoreProperty.php');

class Episode extends CoreProperty
{
    private $season, $episode;

    function __construct($s)
    {
        $this->set($s);
    }

    function set($s)
    {
        $s = strtolower($s);

        // "season 1, episode 24"
        $x = explode(', ', $s);
        if (count($x) == 2) {
            if (substr($x[0], 0, 6) == 'season')
                $this->season = trim(substr($x[0], 6));
            else
                throw new Exception ('season prob: '.$s);

            if (substr($x[1], 0, 7) == 'episode')
                $this->episode = trim(substr($x[1], 7));
            else
                throw new Exception ('episode prob: '.$s);

        } else
            throw new Exception ('Unhandled episode format: '.$s);
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
