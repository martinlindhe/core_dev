<?php
/**
 * $Id$
 *
 * Common code for OpenStreetMap (OpenLayers) and Google Maps javascript widgets
 *
 * @author Martin Lindhe, 2012 <martin@startwars.org>
 */

namespace cd;

class MapMarker
{
    var $latitude;
    var $longitude;
    var $tooltip;
    var $icon;
    var $zIndex;
    var $flat = false;

    function __construct($lat = 0, $lng = 0)
    {
        $this->latitude  = $lat;
        $this->longitude = $lng;
    }

}

abstract class MapWidget
{
    protected $width  = 500;
    protected $height = 300;
    protected $zoom = 3;                ///< 1 (whole world) to ????? (max zoom)

    protected $markers = array();

    function __construct($lat = false, $lng = false)
    {
        $this->latitude  = $lat;
        $this->longitude = $lng;
    }

    function setZoomLevel($n) { $this->zoom = $n; }
    function setWidth($n) { $this->width = $n; }
    function setHeight($n) { $this->height = $n; }

    function addMarkers($arr)
    {
        if (!is_array($arr))
            throw new \Exception ('not array');

        foreach ($arr as $o)
        {
            if (!($o instanceof MapMarker))
                throw new \Exception ('need MapMarker');

            $this->markers[] = $o;
        }
    }

    abstract function render();
}

?>
