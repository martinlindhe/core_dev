#!/bin/bash
#
# sudo apt-get install php5-geoip
#
# Updates local geoip databases from the web, usually a new version is available every month

DST_DIR=/usr/share/GeoIP

wget http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz
gunzip GeoLiteCity.dat.gz
sudo mkdir -v $DST_DIR
sudo mv -v GeoLiteCity.dat $DST_DIR/GeoIPCity.dat

wget http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz
gunzip GeoIP.dat.gz
sudo mv -v GeoIP.dat $DST_DIR

wget http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz
gunzip GeoIPv6.dat.gz
sudo mv -v GeoIPv6.dat $DST_DIR
