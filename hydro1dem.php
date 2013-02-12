<?php


class Hydro1DEM {

	public $fileName,
		   $f,
		   $rows,
		   $cols,
		   $bytes,
		   $pixelSize,
		   $bounds;


	public function __construct($fileNameStem) {

		$this->fileName = $fileNameStem.'.bil';
		$this->f = fopen($this->fileName, 'rb');

		$blw = file($fileNameStem.'.blw');
		$hdr = file($fileNameStem.'.hdr');

		$this->rows = (int)preg_replace('/[^0-9]/','',$hdr[2]);
		$this->cols = (int)preg_replace('/[^0-9]/','',$hdr[3]);
		$this->bytes = (int)preg_replace('/[^0-9]/','',$hdr[5]);
		$this->pixelSize = (int)$blw[0];
		$this->bounds = array(
			'left' => $blw[4] / $this->pixelSize,
			'top' => $blw[5] / -$this->pixelSize,
			'right' => $blw[4] / $this->pixelSize + $this->cols,
			'bottom' => $blw[5] / -$this->pixelSize + $this->rows
		);

	}

}
