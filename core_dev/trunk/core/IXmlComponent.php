<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

interface IXmlComponent
{
    /**
     * Returns XML data from the object
     */
    public function render();

    /**
     * Handles POST data for the object
     */
    public function handlePost($p);
}

?>
