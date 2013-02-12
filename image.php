<?php

$continent = $argv[1]?:'na';
$heightAdjustment = $argv[2]?:0;

if ( !file_exists($continent.'/'.$continent.'_dem.bil') ) {
    die("++++++ No such continent.\n");
}

$f = fopen($continent.'/'.$continent.'_dem.bil', 'rb');

$hdr = file($continent.'/'.$continent.'_dem.hdr');
$stx = file($continent.'/'.$continent.'_dem.stx');
$stx = explode(' ',$stx[0]);

$rows = (int)preg_replace('/[^0-9]/','',$hdr[2]);
$cols = (int)preg_replace('/[^0-9]/','',$hdr[3]);
$max = $stx[2] - $heightAdjustment + 1;
$noDataValue = $stx[1];

$glob = 64;



$im = @imagecreate($cols, $rows);

$red = imagecolorallocate($im, 255, 0, 0); 
$white = imagecolorallocate($im, 255, 255, 255); 
$seablue = imagecolorallocate($im, 200, 200, 255); 
$gray = array();
for ( $i = 0; $i <= 255; $i++ ) {
    $gray[] = imagecolorallocate($im, $i, $i, $i);
}
$blue = array();
for ( $i = 0; $i <= 255; $i++ ) {
    $blue[] = imagecolorallocate($im, 200, 200, $i);
}


imagefill($im, 0, 0, $seablue);


for ( $r = 0; $r < $rows / $glob; $r++ ) {
    if ( $r % 10 == 0 ) { echo $r; }
    else { echo '.'; }
    $m = unpack('n*',fread($f,2*$cols*$glob));
    for ( $g = 0; $g < $glob; $g++ ) {
        for ( $c = 0; $c < $cols; $c++ ) {
            $index = $cols * ($g) + $c + 1;
            if ( array_key_exists($index, $m) ) {
                $height = $m[$index];// - $heightAdjustment;

                if ( $height > 32768 ) { $height = $height - 65536; }
                if ( $height < 0 ) { $height = $noDataValue; }
                $height = $height - $heightAdjustment;
            
                if ( $height >= 0 ) { imagesetpixel($im, $c, $r * $glob + $g, $gray[floor($height*256/$max)]); }
            }
        }
    }
}
echo "\n";



imagepng($im, $continent.'/'.$continent.'_dem_'.$heightAdjustment.'.png');
imagedestroy($im);

?>