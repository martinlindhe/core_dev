<?php
/**
 * $Id$
 *
 * http://geolite.maxmind.com/download/geoip/database/LICENSE.txt
 *
 * Use tools/update_geoip.sh to update local database
 */

//STATUS: wip

//XXX several commercial databases also available, "ISP", "region", "organization", etc

class GeoIp
{
    function __construct()
    {
        if (!function_exists('geoip_db_avail'))
            throw new Exception ('sudo apt-get install php5-geoip');
    }

    function getRecord($s)
    {
        return geoip_record_by_name($s);
    }

    function getCountry($s)
    {
        return geoip_country_code_by_name($s);
    }

    function getTimezone($s)
    {
        $r = self::getRecord($s);
        return geoip_time_zone_by_country_and_region($r['country_code'], $r['region']);
    }

    function renderVersion()
    {
        return
        (geoip_db_avail(GEOIP_COUNTRY_EDITION) ? 'Country: '.geoip_database_info(GEOIP_COUNTRY_EDITION)."\n" : '').
        (geoip_db_avail(GEOIP_CITY_EDITION_REV0) ? 'City   : '.geoip_database_info(GEOIP_CITY_EDITION_REV0)."\n" : '');
    }
}

?>
