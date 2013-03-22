require('./extensions.js');
var demreader = require('./demreader.js'),
    projections = require('./projections.js'),
    poleshifter = require('./poleshifter.js'),
    fs = require('fs'),

	configFileName = process.argv[2],
	config,
	x, y, height,
	newPole, newMap, newBuffer,
	position = 0,
	dems = {},
	startY, testY, incrementY,

    getHeightFromDem = function(dem, pole, map) {
		return dem.getFileValue(
        	dem.getMapPoint(
        		dem.geoToCartesian(
        			pole.translate(
	        			map.cartesianToGeo({x: x, y: y}),
	        			true
	        		)
        		)
        	)
        );
    };

// load all external files
try {
	config = JSON.parse(fs.readFileSync(configFileName).toString('utf8'));
    dems.northAmerica = projections.equalArea(demreader.getDem('na')),
    dems.asia = projections.equalArea(demreader.getDem('as'));
} catch (e) {
	console.log(e.toString());
	process.exit(1);
}


//process.exit(0);


// set params of new map from config
newPole = poleshifter.poleShifter(
	Math.deg2rad(config.newPole.originLatitude),
	Math.deg2rad(config.newPole.originLongitude),
	Math.deg2rad(config.newPole.finalRotation)
);
newMap = projections[config.newMapDem.projection](config.newMapDem);
newBuffer = new Buffer(config.newMapDem.rows * config.newMapDem.cols * config.newMapDem.bytes);

// set up loop functionality
if ( config.newPole.inverse === true ) {
	startY = config.newMapDem.bounds.right;
	testY = function () { return y <= config.newMapDem.bounds.left; }
	incrementY = function () { y++; }
} else {
	startY = config.newMapDem.bounds.left;
	testY = function () { return y >= config.newMapDem.bounds.right }
	incrementY = function () { y--; }
}

// loop through all map points
for ( y = startY; testY(); incrementY() ) {
	console.log(y);
	for ( x = config.newMapDem.bounds.top; x <= config.newMapDem.bounds.bottom; x++ ) {
		height = getHeightFromDem(dems.northAmerica, newPole, newMap);
		if ( height === -9999 ) {
			height = getHeightFromDem(dems.asia, newPole, newMap);
		}
		newBuffer.writeUInt16BE(height,position,true);
		position += config.newMapDem.bytes;
	}
}

// write to new file
fs.writeFileSync(config.newMapDem.filename, newBuffer);

