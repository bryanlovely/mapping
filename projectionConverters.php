<?php

/**
 * Lambert conformal conic projection
 */

class LambertConformalConic
{
	public $standard_1,
		   $standard_2,
		   $referenceLat,
		   $referenceLon,
		   $n,
		   $F,
		   $rho_0;


	public function __construct ($standard_1, $standard_2, $referenceLat, $referenceLon) {
		$this->standard_1 = $standard_1;
		$this->standard_2 = $standard_2;
		$this->referenceLat = $referenceLat;
		$this->referenceLon = $referenceLon;
		$this->n = log(cos($standard_1) * $this->sec($standard_2)) / log(tan(M_PI_4 + $standard_2/2) * $this->cot(M_PI_4 + $standard_1/2));
		$this->F = (cos($standard_1) * pow(tan(M_PI_4 + $standard_1/2), $this->n)) / $this->n;
		$this->rho_0 = $this->F * pow($this->cot(M_PI_4 + $referenceLat/2), $this->n);
	}


	public function geoToCartesian ($lat, $lon, $scaleFactor = 1) {
		$rho = $this->F * pow($this->cot(M_PI_4 + $lat/2), $this->n);
		$x = $rho * sin ( $this->n * ($lon - $this->referenceLon) );
		$y = ( ( $rho * cos( $this->n * ($lon - $this->referenceLon) ) ) - $this->rho_0 );
		return array('x'=>$x * $scaleFactor, 'y'=>$y * $scaleFactor);
	}


	public function cartesiantoGeo ($x, $y, $scaleFactor = 1) {
		$lon = atan2($x / $scaleFactor, ($y / $scaleFactor + $this->rho_0)) / $this->n;
		$lat = 2 * $this->acot(pow(($y / $scaleFactor + $this->rho_0) / ($this->F * cos( $this->n * ($lon - $this->referenceLon) )), 1/$this->n)) - M_PI_2;
		return array('lat'=>$lat, 'lon'=>$lon);
	}



	private function cot($rad) {
		return 1 / tan($rad);
	}
	private function sec($rad) {
		return 1 / cos($rad);
	}
	private function sgn($num) {
		return $num < 0 ? -1 : 1;
	}
	private function acot($rad) {
		return M_PI_2 - atan($rad);
	}


}



class LambertEqualArea
{
	public $referenceLat,
		   $referenceLon,
		   $sinReferenceLat,
		   $cosReferenceLat;


	public function __construct ($referenceLat, $referenceLon) {
		$this->referenceLat = $referenceLat;
		$this->referenceLon = $referenceLon;
		$this->sinReferenceLat = sin($referenceLat);
		$this->cosReferenceLat = cos($referenceLat);
	}


	public function geoToCartesian ($lat, $lon, $scaleFactor = 1) {
		$k = sqrt( 2 / ( 1 + sin($this->referenceLat) * sin($lat) + cos($this->referenceLat) * cos($lat) * cos($lon - $this->referenceLon) ) );
		$x = $k * cos($lat) * sin($lon - $this->referenceLon);
		$y = $k * ( cos($this->referenceLat) * sin($lat) - sin($this->referenceLat) * cos($lat) * cos($lon - $this->referenceLon) );
		return array('x'=>$x * $scaleFactor, 'y'=>$y * $scaleFactor);
	}


	public function cartesiantoGeo ($x, $y, $scaleFactor = 1) {
		$rho = sqrt(pow($x / $scaleFactor,2) + pow($y / $scaleFactor,2));
		$c = 2 * asin($rho/2);
		$lat = asin( cos($c) * $this->sinreferenceLat + ($y / $scaleFactor) * sin($c) * $this->cosreferenceLat / $rho);
		$lon = $this->referenceLon + atan2( ($x / $scaleFactor) * sin($c), $rho * $this->cosreferenceLat * cos($c) - ($y / $scaleFactor) * $this->sinreferenceLat * sin($c));
		return array('lat'=>$lat, 'lon'=>$lon);
	}

}
