<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

namespace cd;

require_once('Episode.php');

class TvEpisode extends Episode
{
    var       $id;     // episode id
    var       $owner;  // id of tv show
    var       $title;  // episode title
    var       $date;   // sql date
    var       $info;
    var       $link;   // tvrage.com link to episode details

    protected static $tbl_name = 'oTvEpisodes';

    function setDate($s) { $this->date = $s; }
    function getDate() { return $this->date; }
    function setEpisode($s) { $this->set($s); }
    function getEpisode() { return $this->get(); }

    public static function getAllByOwner($id)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE owner = ?';
        $list = Sql::pSelect($q, 'i', $id);
        return SqlObject::loadObjects($list, __CLASS__);
    }

    public function store()
    {
        $q =
        'SELECT id FROM '.self::$tbl_name.
        ' WHERE owner = ? AND season = ? AND episode = ?';
        $this->id = Sql::pSelectItem($q, 'iii', $this->owner, $this->season, $this->episode);
        if ($this->id) {
            $q =
            'UPDATE '.self::$tbl_name.
            ' SET owner = ?, title = ?, date = ?, info = ?, season = ?, episode = ?, link = ? WHERE id = ?';
            Sql::pUpdate($q, 'isssiisi', $this->owner, $this->title, $this->date, $this->info, $this->season, $this->episode, $this->link, $this->id);
            return $this->id;
        }

        $q =
        'INSERT INTO '.self::$tbl_name.
        ' SET owner = ?, title = ?, date = ?, info = ?, season = ?, episode = ?, link = ?';
        return Sql::pInsert($q, 'isssiis', $this->owner, $this->title, $this->date, $this->info, $this->season, $this->episode, $this->link);
    }

    public function render()
    {
        $res = sql_date($this->date).': '. $this->get().' - '.$this->title;

        return $res;
    }
}

?>
