<?php
/**
 * $Id$
 */

namespace cd;

require_once('TempStoreRedis.php');
require_once('TempStoreMemcached.php');

class TempStore
{
    static    $_instance;            ///< singleton
    static    $_backend;

    private function __construct() {}

    private function __clone() {}      //singleton: prevent cloning of class

    /**
     * @return a reference to the backend instance
     */
    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }

        //self::$_backend = new TempStoreRedis();
        self::$_backend = new TempStoreMemcached();

        return self::$_backend;
    }

}
