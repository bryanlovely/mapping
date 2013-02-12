<?php

//header('Content-type: image/png');
$im = @imagecreate(9102, 8384);

$red = imagecolorallocate($im, 255, 200, 200); 
$white = imagecolorallocate($im, 255, 255, 255); 
$seablue = imagecolorallocate($im, 200, 200, 255); 
$green = imagecolorallocate($im, 200, 255, 00); 
$yellow = imagecolorallocate($im, 255, 255, 200); 
$orange = imagecolorallocate($im, 255, 227, 200); 
$gray = array();
for ( $i = 0; $i <= 255; $i++ ) {
    $gray[] = imagecolorallocate($im, $i, $i, $i);
}
$blue = array();
for ( $i = 0; $i <= 255; $i++ ) {
    $blue[] = imagecolorallocate($im, 200, 200, $i);
}


$f = fopen('/Users/bryan.x.lovely/Downloads/na/na_dem.bil', 'rb');

$rows = 8384;
$cols = 9102;
$max = 6098;
$glob = 64;
$slopeAdjustment = 90;
$noDataValue = -9999;



for ( $r = 0; $r < $rows / $glob; $r++ ) {
    if ( $r % 10 == 0 ) { echo $r; }
    else { echo '.'; }
    $m = unpack('n*',fread($f,2*$cols*$glob));
    for ( $g = 0; $g < $glob; $g++ ) {
        for ( $c = 0; $c < $cols; $c++ ) {
            $index = $cols * ($g) + $c + 1;
            $slope = $m[$index];// - $slopeAdjustment;

            if ( $slope > 32768 ) { $slope = $slope - 65536; }

            if ( $slope < 0 ) { $slope = $noDataValue; }

            if ( $slope == $noDataValue ) { imagesetpixel($im, $c, $r * $glob + $g, $seablue); }
            else if ( $slope/100 > 20 ) { imagesetpixel($im, $c, $r * $glob + $g, $red); }
            else if ( $slope/100 > 10 ) { imagesetpixel($im, $c, $r * $glob + $g, $orange); }
            else if ( $slope/100 > 6 ) { imagesetpixel($im, $c, $r * $glob + $g, $yellow); }
            else { imagesetpixel($im, $c, $r * $glob + $g, $green); }
        }
    }
}
echo "\n";



imagepng($im, '/Users/bryan.x.lovely/Downloads/na/na_slope.png');
imagedestroy($im);


?>