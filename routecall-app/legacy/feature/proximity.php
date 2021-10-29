<?php

// Theory part: http://www.movable-type.co.uk/scripts/latlong.html
// and the formula itself http://en.wikipedia.org/wiki/Haversine_formula

/* Given table 'zipcodes' with columns: 
   zipcode, latitude, longitude.
   Find zipcodes within radius from given zipcode.
   EXAMPLE:
   Coordinates for zip 91326 and radius 25 mi:

SET @location_lat = 34.2766,
    @location_lon = -118.544;

SELECT zipcode, ( 3959 * acos( cos( radians(@location_lat) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(@location_lon) ) + sin( radians(@location_lat) ) * sin( radians( latitude ) ) ) ) AS distance
FROM zipcodes
HAVING distance < 25;

	Result:
	+-------------+-------------------+
	| zipcode     | distance          |
	+-------------+-------------------+
	| 90004       | 19.32764527143567 |
	| 90005       | 20.34491933480445 |
	| 90006       | 21.56930375425860 |
	| ...         | ...               |
	+-------------+-------------------+
*/
