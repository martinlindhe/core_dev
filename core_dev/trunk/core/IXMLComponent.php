<?php
/**
 * $Id$
 *
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

interface IXMLComponent
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
