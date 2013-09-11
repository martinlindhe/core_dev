<?php
/**
 * $Id$
 */

namespace cd;

interface ITempStore
{
    public function get($key);

    public function set($key, $val = '', $expire_time = 3600);

    public function setServer($host, $port);
}
