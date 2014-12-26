<?php
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

