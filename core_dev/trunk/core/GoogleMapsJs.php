<?php
/**
 * $Id$
 *
 * http://code.google.com/intl/sv-SE/apis/maps/documentation/javascript/tutorial.html
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('output_js.php');

class GoogleMapsJs
{
    protected $latitude;
    protected $longitude;
    protected $lang;       ///< 2-letter language code, eg "en". if unset, it will autodetect
    protected $region;     ///< ("US", or "SE") in order to force maps to assume it is viewed from this region, rather than detected region

    protected $detect_location = false;
    protected $zoom_level = 14; //8;

    function __construct($lat, $lon)
    {
        $this->latitude  = $lat;
        $this->longitude = $lon;
    }

    function render()
    {
        $url =
        'http://maps.google.com/maps/api/js?sensor='.sbool($this->detect_location).
        ($this->lang   ? '&language='.$this->lang : '').
        ($this->region ? '&region='.$this->region : '');

        $header = XhtmlHeader::getInstance();
        $header->includeJs($url);

        $res =
        'var latlng = new google.maps.LatLng('.$this->latitude.', '.$this->longitude.');'.
        'var myOptions = {'.
            'center: latlng,'.
            'zoom: '.$this->zoom_level.','.
            'mapTypeId: google.maps.MapTypeId.ROADMAP'. //XXX: configurable ROADMAP, SATELLITE, HYBRID, TERRAIN
        '};'.
        'var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);';

        return
        '<div id="map_canvas" style="width:400px; height:400px"/>'.
        js_embed($res);
    }

}

?>
