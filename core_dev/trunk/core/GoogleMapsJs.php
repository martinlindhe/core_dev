<?php
/**
 * $Id$
 *
 * Google Maps Javascript API
 *
 * Currently based on version 3.2 of the API, which is
 * the latest release version as of 2011-02-12
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

//XXXX: map div messes up page

//TODO: make mapTypeId configurable: ROADMAP, SATELLITE, HYBRID, TERRAIN

//TODO: ability to use "css sprite" images somehow

require_once('html.php');

class GoogleMapMarker
{
    var $latitude;
    var $longitude;
    var $tooltip;
    var $icon;
}

class GoogleMapsJs
{
    protected $latitude;                ///< OR use detect_location
    protected $longitude;
    protected $lang;                    ///< 2-letter language code, eg "en". if unset, it will autodetect
    protected $region;                  ///< ("US", or "SE") in order to force maps to assume it is viewed from this region, rather than detected region

    protected $detect_location = false; ///< shall google maps try to detect location of the user? instead of specifying coordinates
    protected $zoom = 3;                ///< 1 (whole world) to 20 (max zoom)

    protected $width;
    protected $height;
    protected $markers = array();

    protected $api_key = 'AIzaSyC262ttP813tKVbb79fRHjv6oP-542KeEM';

    function __construct($lat = 0, $lng = 0)
    {
        $this->latitude  = $lat;
        $this->longitude = $lng;
    }

    /** set to "3" for current "V3 trunk" api */
    function setApiVersion($s) { $this->api_version = $s; }

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
        $url =
        'http://maps.google.com/maps/api/js'.
        '?key='.$this->api_key.
        ($this->lang   ? '&language='.$this->lang : '').
        ($this->region ? '&region='.$this->region : '').
        '&sensor='.sbool($this->detect_location);

        $header = XhtmlHeader::getInstance();
        $header->includeJs($url);

        $div_id = 'gmap_'.mt_rand();

         $header->embedCss(
        '#'.$div_id.' {'.
            ($this->width  ? 'width:'.$this->width.'px;' : '' ).
            ($this->height ? 'height:'.$this->height.'px;' : '').
        '}'
        );

        $res =
        'var myOptions = {'.
            'center: new google.maps.LatLng('.$this->latitude.','.$this->longitude.'),'.
            'zoom: '.$this->zoom.','.
            'mapTypeId: google.maps.MapTypeId.ROADMAP'.
        '};'.
        'var myMap = new google.maps.Map(document.getElementById("'.$div_id.'"), myOptions);';

        if ($this->markers)
            foreach ($this->markers as $idx => $m)
                $res .=
                'var mk'.$idx.' = new google.maps.Marker({'.
                    'position:new google.maps.LatLng('.$m->latitude.','.$m->longitude.'),'.
                    ($m->icon ? 'icon:"'.$m->icon.'",' : '').
                    ($m->tooltip ? 'title:"'.$m->tooltip.'",' : '').
                    'map:myMap'.
                '});'."\n";

//        js_embed($res)

        $header->embedJsOnload($res);

        return
        '<div id="'.$div_id.'"/>';
//        '<br style="clear:both"/>';
    }

}

?>
