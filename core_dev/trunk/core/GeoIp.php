<?php
/**
 * $Id$
 *
 * http://geolite.maxmind.com/download/geoip/database/LICENSE.txt
 *
 * Use tools/update_geoip.sh to update local database
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip

//XXX several commercial databases also available, "ISP", "region", "organization", etc

class GeoIp
{
    static function compat_check()
    {
        if (!extension_loaded('geoip'))
            throw new Exception ('sudo apt-get install php5-geoip');
    }

    static function getRecord($s)
    {
        self::compat_check();
        return geoip_record_by_name($s);
    }

    static function getCountry($s)
    {
        self::compat_check();
        return geoip_country_code_by_name($s);
    }

    static function getTimezone($s)
    {
        self::compat_check();
        $r = self::getRecord($s);
        return geoip_time_zone_by_country_and_region($r['country_code'], $r['region']);
    }

    static function renderVersion()
    {
        self::compat_check();
        return
        (geoip_db_avail(GEOIP_COUNTRY_EDITION) ? 'Country: '.geoip_database_info(GEOIP_COUNTRY_EDITION)."\n" : '').
        (geoip_db_avail(GEOIP_CITY_EDITION_REV0) ? 'City   : '.geoip_database_info(GEOIP_CITY_EDITION_REV0)."\n" : '');
    }
}

?>
