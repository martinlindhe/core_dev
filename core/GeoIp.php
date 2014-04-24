<?php
/**
 * http://geolite.maxmind.com/download/geoip/database/LICENSE.txt
 *
 * On Debian, install geoip-database-contrib to keep local database up-to-date.
 *
 * The files are available at
 * http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz
 * http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz
 * http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz
 *
 * @author Martin Lindhe, 2011-2014 <martin@ubique.se>
 */

//STATUS: wip

// several commercial databases also available, "ISP", "region", "organization", etc from maxmind.com

namespace cd;

require_once('sql_misc.php');
require_once('time.php');

class GeoIp
{
    private static function compat_check()
    {
        if (!extension_loaded('geoip'))
            throw new \Exception ('sudo apt-get install php5-geoip geoip-database-contrib');
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

    static function getDatabaseVersions()
    {
        $dbs = array(
        'GEOIP_COUNTRY_EDITION'     => GEOIP_COUNTRY_EDITION,
        'GEOIP_REGION_EDITION_REV0' => GEOIP_REGION_EDITION_REV0,
        'GEOIP_CITY_EDITION_REV0'   => GEOIP_CITY_EDITION_REV0,
        'GEOIP_ORG_EDITION'         => GEOIP_ORG_EDITION,
        'GEOIP_ISP_EDITION'         => GEOIP_ISP_EDITION,
        'GEOIP_CITY_EDITION_REV1'   => GEOIP_CITY_EDITION_REV1,
        'GEOIP_REGION_EDITION_REV1' => GEOIP_REGION_EDITION_REV1,
        'GEOIP_PROXY_EDITION'       => GEOIP_PROXY_EDITION,
        'GEOIP_ASNUM_EDITION'       => GEOIP_ASNUM_EDITION,
        'GEOIP_NETSPEED_EDITION'    => GEOIP_NETSPEED_EDITION,
        'GEOIP_DOMAIN_EDITION'      => GEOIP_DOMAIN_EDITION,
        );

        $res = array();

        foreach ($dbs as $name => $id)
        {
            if (!geoip_db_avail($id))
                continue;

            $info = geoip_database_info($id);

            $x = explode(' ', $info);

            $res[] = array(
            'name' => $name,
            'file' => geoip_db_filename($id),
            'date' => sql_date( ts($x[1]) ),
            'version' => $x[0]
            );
        }

        return $res;
    }
}
