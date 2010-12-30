#!/bin/bash
#

wget http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz
gunzip GeoLiteCity.dat.gz
sudo mkdir -v /usr/share/GeoIP
sudo mv -v GeoLiteCity.dat /usr/share/GeoIP/GeoIPCity.dat

wget http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz
gunzip GeoIP.dat.gz
sudo mv -v GeoIP.dat /usr/share/GeoIP/

wget http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz
gunzip GeoIPv6.dat.gz
sudo mv -v GeoIPv6.dat /usr/share/GeoIP/
