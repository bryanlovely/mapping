<?php

/*
IF 1° latitude == 110km exactly:  R = 0.989E, M = 1.021E, D = 1.044E ( = 5.758 g/cm^3)

OR R=1, M=1, D=1 and 1° latitude == 111.2km


distortion X = cos(standard_2/2 - standard_1/2) / cos(lat - standard_2/2 - standard_1/2) - 1
expressed as positive numbers for map > sphere, negative for map < sphere

*/

echo "<h3>Geometric Method</h3>";


// constructing a grid from the cone
// inputs are $scale ( "1:[$scale]" ), $standard_1, $standard_2
$scale = 5000000;
$standard_1 = deg2rad(35);
$standard_2 = deg2rad(65);
$referenceLat = deg2rad(60);
$referenceLon = deg2rad(0);

echo "reference: 60N0E<br><br>";



define('EARTH_RADIUS', 6317000000);  // millimeters
$theta = ($standard_1 + $standard_2) / 2;
    //echo "theta = ".rad2deg($theta)."<br><br>";
$costheta = cos($theta);
$C = ( sin($standard_2) + ( cos($standard_2) / tan($theta) ) ) * EARTH_RADIUS / $scale;     // multiply by earth radius divided by scale to get map measurement in millimeters
    //echo "C = $C<br><br>";
$H = $C / $costheta;
    //echo "H = $H<br><br>";
$lonFactor = tan($theta) * $costheta;                 // scale factor for lines of longitude; 10° * $lonFactor = degrees of arc on map between 10° longitude lines
    //echo "lonFactor = $lonFactor<br><br>";


// for each point ($lat, $lon), given map reference point ($referenceLat, $referenceLon) as the top center point
$lat = deg2rad(31.25);
$lon = deg2rad(10);

$Hlatreference = $C * cos($referenceLat) / cos( $referenceLat - $theta );        // diameter of reference latitude circle from map's pole
    //echo "$C * cos($referenceLat) / cos( $referenceLat - $theta )<br><br>";
    //echo "Hlatreference = $Hlatreference<br><br>";
$Hlat = $C * cos($lat) / cos( $lat - $theta );
    //echo "$C * cos($lat) / cos( $lat - $theta )<br><br>";
    //echo "Hlat = $Hlat<br><br>";
$x = $Hlat * sin(($lon - $referenceLon) * $lonFactor);
$y = $Hlat * cos(($lon - $referenceLon) * $lonFactor) - $Hlatreference;
$x = (round($x*1)/1);
$y = (round($y*1)/1);
echo "31.33N10E = ($x, $y)<br><br>";

// inverse case for each point ($x,y), given map reference point ($referenceLat, $referenceLon) as the top center point
//$x = 0;
//$y = 653.98396316505;

$Hlatreference = $C * cos($referenceLat) / cos( $referenceLat - $theta );        // diameter of reference latitude circle from map's pole
    //echo "Hlatreference = $Hlatreference<br><br>";
$dy = $y + $Hlatreference;
    //echo "dy = $dy<br><br>";
$Hlat = sqrt( pow($x, 2) + pow($y + $Hlatreference, 2) );
    //echo "Hlat = $Hlat<br><br>";
//$lon = asin($x / $Hlat) / $lonFactor;
$lon = atan2($x, $dy) / $lonFactor;
    //echo "lon = ".rad2deg($lon)."<br><br>";


// try reference lat
echo"<ol>";
$continue = true;
$maxlat = deg2rad(90);//$standard_2 + ($standard_1 + $standard_2)/2;
$minlat = deg2rad(00);$standard_1 - ($standard_1 + $standard_2)/2;
        $l = ($maxlat + $minlat) / 2;
$h = 0;
$count = 0;
        echo "<li>Hlat = $Hlat</li>";

while ( $continue and $count < 100) {
    $h = $C * cos($l) / cos( $l - $theta );
            echo "<li>l = ".rad2deg($l)." -> $h (".(1 - $h/$Hlat).")";
    if ( abs(1 - $h/$Hlat) < 0.000005 ) {
       $continue = false;
            echo "=== DONE";
    } else if ( $h < $Hlat ) {  // lat is greater than target
        $maxlat = $l;
        $l = ($maxlat + $minlat) / 2;
    } else if ( $h > $Hlat ) {  // lat is less than target
        $minlat = $l;
        $l = ($maxlat + $minlat) / 2;
    }
        $count++;
}









echo"</ol>";








echo "0x654y = (lat ".(round(rad2deg($l)*10000)/10000).", lon".(round(rad2deg($lon)*10000)/10000).")\n";






echo "<h3>Trig Method</h3>";
$lat = deg2rad(31.25);
$lon = deg2rad(10);


$n = log(cos($standard_1) * sec($standard_2)) / log(tan(M_PI_4 + $standard_2/2) * cot(M_PI_4 + $standard_1/2));
$F = (cos($standard_1) * pow(tan(M_PI_4 + $standard_1/2), $n)) / $n;
$rho_0 = $F * pow(cot(M_PI_4 + $referenceLat/2), $n);

// for each point $lat, $long:

$rho = $F * pow(cot(M_PI_4 + $lat/2), $n);
$x = $rho * sin ( $n * ($lon - $referenceLon) ) * EARTH_RADIUS / $scale;
$y = ( ( $rho * cos( $n * ($lon - $referenceLon) ) ) - $rho_0 ) * EARTH_RADIUS / $scale;

echo "referenceLat = ".rad2deg($referenceLat)."<br>";
echo "rho_0 = ".($rho_0 * EARTH_RADIUS / $scale)."<br><br>";
echo "rho = ".(( $rho * cos( $n * ($lon - $referenceLon) ) )  * EARTH_RADIUS / $scale)."<br>";
echo "lat, lon = ".rad2deg($lat).", ".rad2deg($lon)."<br>";
echo "x,y = ".round($x).", ".round($y)."<br><br>";




// remote sensing method

echo "<h3>Remote Sensing method</h3>";

//Easting,    
$E  = $EF + $r * sin($theta);
//Northing, 
$N = $NF + $r * $F –  $r * cos($theta);
where  
$m = cos($phi)/pow((1 – pow(sin($phi),2)),0.5);


for m1, phi1, and m2, phi2 where phi1  and phi2 are the latitudes of the standard parallels
t  = tan(pi/4 – phi/2)/[(1 – e sin phi)/(1 + e sin phi)]^e/2
for t1, t2, tF and t using phi1, phi2, phiF and phi respectively
n = (ln m1 –  ln m2)/(ln t1 –  ln t2)
F = m1/(n * t1^n)
r =  a F t^n         

for rF and r, where rF is the radius of the parallel of latitude of the false origin
theta = n(lon –  lonF)


echo "lat, lon = ".rad2deg($lat).", ".rad2deg($lon)."<br>";
echo "E, N = ".round($E).", ".round($N)."<br>";


function getM ( $lat) {
    return cos($lat)/pow((1 – pow(sin($lat),2)),0.5);
}
function getT ( $lat) {
    return tan(M_PI_4 - $lat/2) / sqrt((1 - sin($lat))/(1 + sin($lat)));
}
function getRho ($lat, $a, $F, $n) {
    return $a * $F * pow(getT($lat), $n);
}



function cot($rad) {
    return 1 / tan($rad);
}
function sec($rad) {
    return 1 / cos($rad);
}