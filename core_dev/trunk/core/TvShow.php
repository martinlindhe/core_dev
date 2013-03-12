<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

namespace cd;

require_once('SqlObject.php');

class TvShow
{
    var $id;           ///< show id (from tvrage.com database)
    var $name;
    var $country;      ///< 2-letter country code
    var $status;
    var $started;      ///< time when show premiered
    var $ended;        ///< time wehn show was put to sleep
    var $info_url;     ///< info about the show
    var $thumb_url;    ///< url for thumbnail
    var $time_updated; ///< time when tv show was last updated from tvrage.com

    protected static $tbl_name = 'oTvShows';

    protected $episodes = array(); ///< array of TvEpisode objects

    public function getEpisodes() { return $this->episodes; }

    public function addEpisode($o)
    {
        if (!($o instanceof TvEpisode))
            throw new \Exception ('not TvEpisode');

        $this->episodes[] = $o;
    }

    public function getPastEpisodes()
    {
        $res = array();
        foreach ($this->episodes as $ep)
            if ($ep->getDate() < time() )
                $res[] = $ep;

        return $res;
    }

    public function getFutureEpisodes()
    {
        $res = array();
        foreach ($this->episodes as $ep)
            if ($ep->getDate() >= time() )
                $res[] = $ep;

        return $res;
    }

    public static function get($id)
    {
        if (!$id || !is_numeric($id))
            return false;

        $db = SqlHandler::getInstance();
        $q = 'SELECT * FROM '.self::$tbl_name.' WHERE id = ?';
        $row = $db->pSelectRow($q, 'i', $id);
        $obj = SqlObject::loadObject( $row, __CLASS__);
        if (!$obj)
            throw new \Exception ('bad id '.$id);

        $obj->episodes = TvEpisode::getAllByOwner($id);

        return $obj;
    }

    public function store()
    {
        return SqlObject::store($this, self::$tbl_name, 'id');
    }

}
