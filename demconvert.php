<?php

/*

colors:
5000	251,101,57
3000	252,171,71
1500	253,171,78
800	247,233,103
500	247,252,169
200	234,253,132
100	190,255,47
0	127,255,6
-100	172,255,247
-200	104,255,255
-1000	56,255,254
-4000	36,242,255
-10000	25,190,255


reference parallels:
25N, 45N

bounds:

20N - 50N
20E - 40W



*/
require_once 'projectionConverters.php';
require_once 'hydro1dem.php';


$northAmerica = new Hydro1DEM('na_dem');
//$asia = new Hydro1DEM('as_dem');




define('EARTH_RADIUS', 6371);

$standard_1 = deg2rad(25.0);
$standard_2 = deg2rad(45.0);
$referenceLat = deg2rad(50.0);
$referenceLon = deg2rad(-10.0);
$eqReferenceLat = deg2rad(45.0);
$eqReferenceLon = deg2rad(-100.0);
$westBound = deg2rad(-40.0);
$eastBound = deg2rad(20.0);
$northBound = $referenceLat;
$southBound = deg2rad(20.0);

$scaleFactor = EARTH_RADIUS / 2;// / 1000;

$conic = new LambertConformalConic($standard_1, $standard_2, $referenceLat, $referenceLon);
$eqarea = new LambertEqualArea($eqReferenceLat, $eqReferenceLon);

// calculate x,y bounds
$topleft = $conic->geoToCartesian($northBound, $westBound, $scaleFactor);
$topright = $conic->geoToCartesian($northBound, $eastBound, $scaleFactor);
$botleft = $conic->geoToCartesian($southBound, $westBound, $scaleFactor);
$botright = $conic->geoToCartesian($southBound, $eastBound, $scaleFactor);
$bottomCenter = $conic->geoToCartesian($southBound,$referenceLon, $scaleFactor);

$pixelBounds = array(
	'top' => floor($topleft['y']),
	'left' => floor($botleft['x']),
	'bottom' => ceil($bottomCenter['y']),
	'right' => ceil($botright['x'])
);

print_r($pixelBounds);



// test mt. mckinley
print_r($northAmerica);
$lat = deg2rad(63.069);
$lon = deg2rad(-151.0075);
echo $lat.',',$lon."\n";
$pos = $eqarea->geoToCartesian($lat,$lon,$scaleFactor);
print_r($pos);
echo $pos['x'] - $northAmerica->bounds['left']."\n";
echo -$northAmerica->bounds['top'] - $pos['y']."\n";
