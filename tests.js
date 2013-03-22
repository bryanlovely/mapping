require('./extensions.js');
var fs = require('fs');

try {
	var config = fs.readFileSync(process.argv[2]);
	console.log(process.argv[2]);
	console.log('config',JSON.parse(config.toString('utf8')));
} catch (e) {
	console.log(e.toString());
	process.exit(1);
}




// Mt. McKinley
var mtmckinley = {
    lat: Math.deg2rad(63.069),
    lon: Math.deg2rad(-151.0075)
};

var testpoint = {
    lat: Math.deg2rad(63),
    lon: Math.deg2rad(-150)
};

/*
var demreader = require('./demreader.js');
var projections = require('./projections.js');

var map = {referenceLat: null, referenceLon: null},
    scaleFactor = 6371,
    lat, lon, pos, coord, x, y,
    northAmerica = projections.equalArea(demreader.getDem('na')),
    asia = projections.equalArea(demreader.getDem('as'));


// tests:


console.log("error (m):",equalArea.getError(testpoint));
*/


var fs = require('fs'),
	fileNameStem = 'na',
	filenameBase = '../' + fileNameStem + '/' + fileNameStem + '_dem',
	fd = fs.openSync(filenameBase + '.bil', 'r'),
	buf = new Buffer(2),
	bytes = 2,
	position = 0;

if ( fs.readSync(fd, buf, 0, bytes, position) == bytes ) {
	console.log(buf, buf.length);
	console.log(buf.readInt16BE(0));
}



var fileBuffer = fs.readFileSync(filenameBase + '.bil');
console.log(fileBuffer);
console.log(fileBuffer.length);
console.log(fileBuffer.readInt16BE(2));


var poleshifter = require('./poleshifter.js');
var rotator = poleshifter.poleShifter(Math.deg2rad(-20),Math.deg2rad(-14),Math.deg2rad(202), true);
var forwardPoint = rotator.translate(testpoint);
forwardPointDeg = {lat: Math.rad2deg(forwardPoint.lat), lon: Math.rad2deg(forwardPoint.lon)};
var backPoint = rotator.translate(forwardPoint, true);
backPointDeg = {lat: Math.rad2deg(backPoint.lat), lon: Math.rad2deg(backPoint.lon)};
//console.log('mtmckinley', mtmckinley);
console.log('testpoint', testpoint);
console.log('forwardPoint',forwardPointDeg);
console.log('backPoint',backPointDeg);
