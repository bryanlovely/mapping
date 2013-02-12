<?php

$f = fopen('/Users/bryan.x.lovely/Downloads/na/na_dem.bil', 'rb');

$rows = 8384;
$cols = 9102;
$max = 6098;
$glob = 64;
$heightAdjustment = 105;
$noDataValue = -9999;
$adjustmentfactor = 1.02;

$oldheights = array("6"=>0, "5"=>0, "4"=>0, "3"=>0, "2"=>0, "1"=>0, "0"=>0, "-1"=>0);
$newheights = array("6"=>0, "5"=>0, "4"=>0, "3"=>0, "2"=>0, "1"=>0, "0"=>0, "-1"=>0);
$adjustedheights = array("6"=>0, "5"=>0, "4"=>0, "3"=>0, "2"=>0, "1"=>0, "0"=>0, "-1"=>0);




for ( $r = 0; $r < $rows / $glob; $r++ ) {
    if ( $r % 10 == 0 ) { echo $r; }
    else { echo '.'; }
    $m = unpack('n*',fread($f,2*$cols*$glob));
    for ( $g = 0; $g < $glob; $g++ ) {
        for ( $c = 0; $c < $cols; $c++ ) {
            $index = $cols * ($g) + $c + 1;
            $height = $m[$index];

            if ( $height > 32768 ) { $height = $height - 65536; }
            if ( $height < 0 ) { $height = $noDataValue; }

            if ( $height != $noDataValue ) {
                $oldheight = floor($height/1000);
                $oldheights["$oldheight"]++;
            }
            $height = $height - $heightAdjustment;
            if ( $height < 0 ) { $height = $noDataValue; }
            if ( $height != $noDataValue ) {
                $newheight = floor($height/1000);
                $newheights["$newheight"]++;

                $adjustedheight = floor(pow($height/$max, $adjustmentfactor) * ($max) / 1000);
                $adjustedheights["$adjustedheight"]++;

            }
        }
    }
}

/*
$oldheights = array(
    '6' => 1,
    '5' => 95,
    '4' => 839,
    '3' => 65527,
    '2' => 1024420,
    '1' => 3736182,
    '0' => 17258904
);


$newheights = array(
    '6' => 0,
    '5' => 81,
    '4' => 554,
    '3' => 49565,
    '2' => 798491,
    '1' => 3543621,
    '0' => 14477134,
);
*/

echo "old heights:\n";
print_r($oldheights);
echo "new heights:\n";
print_r($newheights);
echo "adjusted heights:\n";
print_r($adjustedheights);

$oldheightpercentages = array();
$newheightpercentages = array();
$adjustedheightpercentages = array();

foreach ( $oldheights as $k => $h ) {
    $oldheightpercentages[$k] = $h * 100 / array_sum($oldheights);
}
foreach ( $newheights as $k => $h ) {
    $newheightpercentages[$k] = $h * 100 / array_sum($newheights);
}
foreach ( $adjustedheights as $k => $h ) {
    $adjustedheightpercentages[$k] = $h * 100 / array_sum($newheights);
}

echo "old height percentages:\n";
print_r($oldheightpercentages);
echo "new height percentages:\n";
print_r($newheightpercentages);
echo "adjusted height percentages:\n";
print_r($adjustedheightpercentages);

$factors = array();
$factorsadjusted = array();
foreach ( $newheightpercentages as $k => $h ) {
    if ( $oldheightpercentages[$k] == 0 ) {
        $factors[$k] = 0;
    } else {
        $factors[$k] = $newheightpercentages[$k] / $oldheightpercentages[$k];
    }
}
foreach ( $adjustedheightpercentages as $k => $h ) {
    if ( $oldheightpercentages[$k] == 0 ) {
        $factors[$k] = 0;
    } else {
        $factorsadjusted[$k] = $adjustedheightpercentages[$k] / $oldheightpercentages[$k];
    }
}
echo "factors:\n";
print_r($factors);
echo "factors adjusted $adjustmentfactor:\n";
print_r($factorsadjusted);

?>