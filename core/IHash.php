<?php
/**
 * $Id$
 *
 * Hash algorithm interface
 *
 * @author Martin Lindhe, 2010-2013 <martin@ubique.se>
 */

namespace cd;

interface IHash
{
    public static function fromFile($file);

    public static function fromString($s);
}
