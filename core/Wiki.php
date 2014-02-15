<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2013 <martin@ubique.se>
 */

//STATUS: wip

namespace cd;

require_once('WikiConfig.php');
require_once('WikiViewer.php');

class Wiki
{
    var $id;
    var $name;
    var $text;
    var $time_created;
    var $time_edited;
    var $edited_by;         ///< tblUsers.id
    var $locked_by;
    var $time_locked;
    var $revision;          ///< counter

    protected static $tbl_name = 'tblWiki';

    public static function getByName($name)
    {
        $o = SqlObject::getByField($name, self::$tbl_name, __CLASS__, 'name');
        if (!$o)
            return new Wiki();   // XXX TODO: this is a workaround because becase SqlObject::loadObject dont always return a object!!!!
        return $o;
    }

    public function store()
    {
        $page_name = $this->name;

        // cleanups to make directory transversal impossible
        // TODO proper cleanup of name!
        $page_name = str_replace('.', '_', $page_name);
        $page_name = str_replace('/', '_', $page_name);
        $page_name = str_replace('\\', '_', $page_name);
        $page_name = str_replace(' ', '_', $page_name);

        $dst_file = WikiConfig::getDiskPath().'/'.$page_name;
        file_put_contents($dst_file, $this->text);

        return SqlObject::store($this, self::$tbl_name, 'name');
    }

}
