## About
Display the worst service status of a host on OpenStreetMap.

## Requirements

* Icinga Web 2 (&gt;= 2.0.0)

## Licence

Icinga Web 2 and this Icinga Web 2 module are licensed under the terms of the GNU General Public License Version 2, you will find a copy of this license in the LICENSE file included in the source package.

## Installation

Copy all files/folders into .../icingaweb2/modules/osm. The foldername for the module must be osm!

## Configuration
Setting            | Description
-------------------|-------------------
latitude           | **Required** Latitude
longitude          | **Required** Longtidue
zoom               | **Required** Open Street Map Zoom Level

Example:
```
vim /etc/icingaweb2/modules/osm/config.ini

[osm]
latitude="51.418568"
longitude="6.884523"
zoom="8"

```
To get Geolocation data, best use a website like http://www.latlong.net or use you smartphone :)

### Geolocations

To get the hosts displayd on the map you have to add to each host configuration 
```
vars.geolocation = "<longitude>,latitude"
```
To get Geolocation data, best use a website like http://www.latlong.net or use you smartphone :)
