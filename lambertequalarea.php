<?php

// workflow:
// for HYDRO1 vector files
//      get x,y points from .e00 file
//      convert to lat, long with LEAAP inverse
//      store in converted file
//      read converted file
//      convert to x, y with LCCP forward
//      plot on canvas
// for DEM files
//      get x, y point of pixel on canvas
//      get lat, long with LCCP inverse
//      get x', y' with LEAAP forward
//      find x', y' in DEM (by interpolation or averaging, depending on scale)
//      subtract 100m from value
//      assign color from lookup table
//      draw on canvas


//forward
/*
$sinReferenceLat = sin($referenceLat);
$cosReferenceLat = cos($referenceLat);
$k = sqrt( 2 / ( 1 + $sinReferenceLat * sin($lat) + $cosReferenceLat * cos($lat) * cos($lon - $referenceLon) ) );
$x = $k * cos($lat) * sin($lon - $referenceLon);
$y = $k * ( $cosReferenceLat * sin($lat) - $sinReferenceLat * cos($lat) * cos($lon - $referenceLon) );


//inverse

$rho = sqrt(pow($x,2) + pow($y,2));
$c = 2 * asin($rho/2);
$lat = asin( cos($c) * sin($referenceLat) + $y * sin($c) * cos($referenceLat) / $rho);
$lon = $referenceLon + atan2( $x * sin($c), $rho * cos($referenceLat) * cos($c) - $y * sin($referenceLat) * sin($c));
*/



// Mt. McKinley
// 63°04′10″N 151°00′27″W = 63.0694444444444, -151.0075
define('EARTH_RADIUS',6371);
$lat = 63.069;
$lon = -151.0075;
$referenceLat = 45;
$referenceLon = -100;

$k = sqrt( 2 / ( 1 + sin(deg2rad($referenceLat)) * sin(deg2rad($lat)) + cos(deg2rad($referenceLat)) * cos(deg2rad($lat)) * cos(deg2rad($lon - $referenceLon)) ) );
$x = $k * cos(deg2rad($lat)) * sin(deg2rad($lon - $referenceLon)) * EARTH_RADIUS;
$y = $k * ( cos(deg2rad($referenceLat)) * sin(deg2rad($lat)) - sin(deg2rad($referenceLat)) * cos(deg2rad($lat)) * cos(deg2rad($lon - $referenceLon)) ) * EARTH_RADIUS;

$x = round($x);
$y = round($y);
echo "$x, $y\n";

$mapTopLeftX = -4462;
$mapTopLeftY =  4384;

echo ($x - $mapTopLeftX).", ".($mapTopLeftY - $y)."\n";

/*
$rows = 8384;
$cols = 9102;
$pixelX = round($cols / 2 + $x);
$pixelY = round($rows / 2 + $y);
echo "pixel $pixelX, $pixelY\n";
// = row 1925, col 178
$f = fopen('/Users/bryan.x.lovely/Downloads/na/na_dem.bil', 'rb');
fseek($f, ($pixelY * $cols + $pixelX) * 2);
$m = unpack('n*',fread($f,2));
print_r($m);

for ( $i=0; $i<10000; $i++ ) { echo (int)fread($f, 2)." "; }
while ( true ) { $m = unpack('n*',fread($f,2));echo $m[1].' '; }
*/
?>
