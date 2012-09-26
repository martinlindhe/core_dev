<?php
/**
 * $Id$
 *
 * Uses UpenLayers JavaScript widget for OpenStreetMap embeddable map
 *
 * http://www.openlayers.org/
 * http://wiki.openstreetmap.org/wiki/OpenLayers_Simple_Example
 *
 * @author Martin Lindhe, 2012 <martin@startwars.org>
 */

//STATUS: works

//TODO: map markers dont support text label: http://dev.openlayers.org/docs/files/OpenLayers/Marker-js.html

namespace cd;

require_once('MapWidget.php');

class OpenStreetMapClient extends MapWidget
{
    function render()
    {
        $div_id = 'omap_'.mt_rand();

        $header = XhtmlHeader::getInstance();
        $header->includeJsLast('core_dev/js/ext/openlayers/OpenLayers.js'); // version 2.11

        $js =
        'map=new OpenLayers.Map("'.$div_id.'");'.
        'map.addLayer(new OpenLayers.Layer.OSM());'.

        'var ll=new OpenLayers.LonLat('.$this->longitude.','.$this->latitude.')'.
            '.transform('.
            'new OpenLayers.Projection("EPSG:4326"),'. // transform from WGS 1984
            'map.getProjectionObject()'.               // to Spherical Mercator Projection
            ');'.

        'var markers = new OpenLayers.Layer.Markers( "Markers" );'.
        'map.addLayer(markers);';

        foreach ($this->markers as $idx => $m)
        {
            $js .=
            'mll=new OpenLayers.LonLat('.$m->longitude.','.$m->latitude.')'.
                '.transform('.
                'new OpenLayers.Projection("EPSG:4326"),'. // transform from WGS 1984
                'map.getProjectionObject()'.               // to Spherical Mercator Projection
                ');';

            if ($m->icon) {
                $js .=
                'var size = new OpenLayers.Size(16,11);'.  // XXXX stupid api requires me to specify icon dimensions
                'var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);'.

                'icon=new OpenLayers.Icon("'.$m->icon.'",size,offset);'.
                'markers.addMarker(new OpenLayers.Marker(mll,icon));';

            } else {
                $js .=
                'markers.addMarker(new OpenLayers.Marker(mll));';
            }
        }

        $js .=
        'map.setCenter(ll,'.$this->zoom.');';

        $header->embedJsOnload($js);

        return
        '<div id="'.$div_id.'" style="width:'.$this->width.'px;height:'.$this->height.'px;"></div>';
    }

}

?>
