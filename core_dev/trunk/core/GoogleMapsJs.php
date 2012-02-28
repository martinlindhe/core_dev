<?php
/**
 * $Id$
 *
 * Google Maps Javascript API V3
 *
 * Currently based on version "V3" of the API, which is
 * the latest release version as of 2012/02
 *
 * Tutorial:
 * http://code.google.com/apis/maps/documentation/javascript/tutorial.html
 *
 * Examples:
 * http://code.google.com/apis/maps/documentation/javascript/examples/index.html
 *
 * @author Martin Lindhe, 2011-2012 <martin@startwars.org>
 */

//STATUS: wip

//FIXME: add title text to map markers
//TODO: make default mapTypeId configurable: ROADMAP, SATELLITE, HYBRID, TERRAIN
//TODO: ability to use "css sprite" images somehow

require_once('html.php');

class GoogleMapMarker
{
    var $latitude;
    var $longitude;
    var $tooltip;
    var $icon;

    function __construct($lat = 0, $lng = 0)
    {
        $this->latitude  = $lat;
        $this->longitude = $lng;
    }

}

class GoogleMapsJs
{
    protected $latitude;                ///< OR use detect_location
    protected $longitude;
    protected $lang;                    ///< 2-letter language code, eg "en". if unset, it will autodetect
    protected $region;                  ///< ("US", or "SE") in order to force maps to assume it is viewed from this region, rather than detected region

    protected $detect_location = false; ///< shall google maps try to detect location of the user? instead of specifying coordinates
    protected $zoom = 3;                ///< 1 (whole world) to 20 (max zoom)

    protected $width  = 500;
    protected $height = 300;
    protected $markers = array();

    protected $api_key = 'AIzaSyC262ttP813tKVbb79fRHjv6oP-542KeEM';

    function __construct($lat = 0, $lng = 0)
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
            throw new Exception ('not array');

        foreach ($arr as $o)
        {
            if (!($o instanceof GoogleMapMarker))
                throw new Exception ('need GoogleMapMarker');

            $this->markers[] = $o;
        }
    }

    function render()
    {
        $header = XhtmlHeader::getInstance();
        $header->includeJs(
            'http://maps.google.com/maps/api/js'.
            '?key='.$this->api_key.
            ($this->lang   ? '&language='.$this->lang : '').
            ($this->region ? '&region='.$this->region : '').
            '&sensor='.sbool($this->detect_location)
        );

        $div_id = 'gmap_'.mt_rand();

        $res =
        'var myOptions={'.
            'center:new google.maps.LatLng('.$this->latitude.','.$this->longitude.'),'.
            'zoom:'.$this->zoom.','.
            'mapTypeId:google.maps.MapTypeId.ROADMAP'.
        '};'.
        'var myMap=new google.maps.Map(document.getElementById("'.$div_id.'"),myOptions);';

        if ($this->markers)
            foreach ($this->markers as $idx => $m)
                $res .=
                'var mk'.$idx.'=new google.maps.Marker({'.
                    'position:new google.maps.LatLng('.$m->latitude.','.$m->longitude.'),'.
                    ($m->icon ? 'icon:"'.$m->icon.'",' : '').
                    ($m->tooltip ? 'title:"'.$m->tooltip.'",' : '').
                    'map:myMap'.
                '});';

        $header->embedJsOnload($res);

        return
        '<div id="'.$div_id.'" style="width:'.$this->width.'px;height:'.$this->height.'px;"></div>';
//        '<br style="clear:both"/>';
    }

}

?>
