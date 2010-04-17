<?php
/**
 * $Id: IXMLComponent.php 201 2010-04-07 19:28:04Z ml $
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
