require('./extensions.js');
var fs = require('fs');
exports.equalArea = function (dem) {

    var referenceLat = Math.deg2rad(dem.referenceLat),
        referenceLon = Math.deg2rad(dem.referenceLon),
        mapBuffer = new Buffer(0);

    return {
        geoToCartesian: function (geoPoint) {
            var k, x, y;
            if ( dem.scaleFactor === undefined ) {
                dem.scaleFactor = 1;
            }
            k = Math.sqrt( 2 / ( 1 + Math.sin(referenceLat) * Math.sin(geoPoint.lat) + Math.cos(referenceLat) * Math.cos(geoPoint.lat) * Math.cos(geoPoint.lon - referenceLon) ) );
            x = k * Math.cos(geoPoint.lat) * Math.sin(geoPoint.lon - referenceLon);
            y = k * ( Math.cos(referenceLat) * Math.sin(geoPoint.lat) - Math.sin(referenceLat) * Math.cos(geoPoint.lat) * Math.cos(geoPoint.lon - referenceLon) );
            return {x: x * dem.scaleFactor, y: y * dem.scaleFactor};
        },

        cartesianToGeo: function (point) {
            var rho, c, lat, lon;
            if ( dem.scaleFactor === undefined ) {
                dem.scaleFactor = 1;
            }
            point.x = point.x / dem.scaleFactor;
            point.y = point.y / dem.scaleFactor;
            rho = Math.sqrt(Math.pow(point.x,2) + Math.pow(point.y,2));
            // avoid div by zero error
            if ( rho == 0 ) {
                rho = 0.0001;
            }
            c = 2 * Math.asin(rho/2);
            lat = Math.asin( Math.cos(c) * Math.sin(referenceLat) + point.y * Math.sin(c) * Math.cos(referenceLat) / rho);
            lon = referenceLon + Math.atan2( point.x * Math.sin(c), rho * Math.cos(referenceLat) * Math.cos(c) - point.y * Math.sin(referenceLat) * Math.sin(c));
            return {lat: lat, lon: lon};
        },

        getMapPoint: function (point) {
            return {x: Math.round(point.x - dem.bounds.left), y: Math.round(-dem.bounds.top - point.y)};
        },

        getFileValue: function (mapPoint) {
            var position = (mapPoint.y * dem.cols + mapPoint.x) * dem.bytes;

            // read whole file into buffer, once only
            if ( mapBuffer.length == 0 ) {
                mapBuffer = fs.readFileSync(dem.filename + ".bil");
            }

            // get value in mapBuffer, checking for bounds
            try {
            	if ( position < 0 ) {
            		throw "out of bounds";
            	}
                return mapBuffer.readInt16BE(position, true);
            } catch(e) {
                return -9999;
            }
        },

        getError: function (geoPoint) {
            pos = this.geoToCartesian(geoPoint);
            x = Math.round(pos.x - dem.bounds.left);
            y = Math.round(-dem.bounds.top - pos.y);
            coord = this.cartesianToGeo({x:Math.round(pos.x), y:Math.round(pos.y)});
            return {'lat': Math.round((coord.lat-geoPoint.lat)*dem.scaleFactor*1000), 'lon': Math.round((coord.lon-geoPoint.lon)*dem.scaleFactor*1000)};
        }
    }
};


/**
 * params = {standardLat_1, standardLat_2, referenceLat, referenceLon, scaleFactor}
 */
exports.lambertConformalConic = function (params) {

    var
        n = Math.log(Math.cos(params.standardLat_1)/Math.cos(params.standardLat_2)) / Math.log(Math.tan(Math.PI/4 + params.standardLat_2/2) / Math.tan(Math.PI/4 + params.standardLat_1/2)),
        F = Math.cos(params.standardLat_1) * Math.pow(Math.tan(Math.PI/4 + params.standardLat_1/2), n) / n,
        rho_0 = params.scaleFactor * F / Math.pow(Math.tan(Math.PI/4 + params.referenceLat/2), n);

    return {
        geoToCartesian: function (geoPoint) {
            var rho = params.scaleFactor * F / Math.pow(Math.tan(Math.PI/4 + geoPoint.lat/2), n),
                theta = n * (geoPoint.lon - params.referenceLon);
            return {x: rho * Math.sin(theta), y: rho_0 - rho * Math.cos(theta)};
        },

        cartesianToGeo: function (point) {
            var rho = Math.sqrt(Math.pow(point.x, 2) + Math.pow(rho_0 - point.y, 2)) * ( n < 0 ? -1 : 1),
                theta = Math.atan(point.x / (rho_0 - point.y)),
                lat = 2 * Math.pow(Math.atan(params.scaleFactor * F / rho), 1/n) - Math.PI/2,
                lon = theta / n + params.referenceLon;
            return {lat: lat, lon: lon};
        },

        /*
        getMapPoint: function (point) {
            return {x: Math.round(point.x - dem.bounds.left), y: Math.round(-dem.bounds.top - point.y)};
        },

        getFileValue: function (mapPoint) {
            var buf = new Buffer(dem.bytes),
                position = (mapPoint.y * dem.cols + mapPoint.x) * dem.bytes;

            // check for bounds
            if ( mapPoint.x < 0 || mapPoint.x > dem.cols || mapPoint.y < 0 || mapPoint.y > dem.rows ) {
                return -9999;
            }
            if ( fs.readSync(dem.filehandle, buf, 0, dem.bytes, position) == dem.bytes ) {
                return buf.readInt16BE(0);
            } else {
                return -9999;
            }
        },
        */

        getError: function (geoPoint) {
            return Math.cos(params.standardLat_1) * Math.pow(Math.tan(Math.PI/4 + params.standardLat_1/2), n) / (Math.cos(geoPoint.lat) * Math.pow(Math.tan(Math.PI/4 + geoPoint.lat/2), n));
        }
    }
};
