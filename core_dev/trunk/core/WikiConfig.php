<?php

namespace cd;

class WikiConfig
{
    static $_instance;                       ///< singleton

    var $disk_path;

    private function __construct() {}        ///< singleton

    private function __clone() {}            ///< singleton: prevent cloning of class

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    public static function setDiskPath($s)
    {
        $ref = self::getInstance();

        $s = realpath($s);
        if (!is_dir($s))
            throw new \Exception ('WikiConfig directory does not exist: '.$s);

        $ref->disk_path = $s;
    }

    public static function getDiskPath()
    {
        $ref = self::getInstance();

        return $ref->disk_path;
    }

}
