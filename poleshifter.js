
/**
 * originLatitude, originLongitude = latitude and longitude of new pole in radians
 * finalRotation = final rotation (chi) of rotated frame in radians
 * positive origin latitudes are NORTH latitude
 * positive origin longitudes are EAST longitude
 */

exports.poleShifter = function (originLatitude, originLongitude, finalRotation) {

	var phi = originLongitude,
		theta = Math.PI/2 - originLatitude,
		chi = finalRotation,
		cosphi = Math.cos(phi),
		sinphi = Math.sin(phi),
		costheta = Math.cos(theta),
		sintheta = Math.sin(theta),
		coschi = Math.cos(chi),
		sinchi = Math.sin(chi),
		forwardMatrix = [		// rotation matrix
			[
				(cosphi * costheta * coschi) - (sinphi * sinchi),
				(sinphi * costheta * coschi) +  (cosphi * sinchi),
				-1 * sintheta * coschi
			],
			[
				(-1 * cosphi * costheta * sinchi) - (sinphi * coschi),
				(-1 * sinphi * costheta * sinchi) + (cosphi * coschi),
				sintheta * sinchi
			],
			[
				cosphi * sintheta,
				sinphi * sintheta,
				costheta
			]
		],
		backMatrix = [			// reverse rotation matrix
			[
				(cosphi * costheta * coschi) - (sinphi * sinchi),
				(-1 * cosphi * costheta * sinchi) - (sinphi * coschi),
				cosphi * sintheta
			],
			[
				(sinphi * costheta * coschi) + (cosphi * sinchi),
				(-1 * sinphi * costheta * sinchi) + (cosphi * coschi),
				sinphi * sintheta
			],
			[
				-1 * sintheta * coschi,
				sintheta * sinchi,
				costheta
			]
		],

	spherical2Cartesian = function ( geoPoint ) {
		var x = Math.sin(Math.PI/2 - geoPoint.lat) * Math.cos(geoPoint.lon),
			y = Math.sin(Math.PI/2 - geoPoint.lat) * Math.sin(geoPoint.lon),
			z = Math.cos(Math.PI/2 - geoPoint.lat);
		return {x: x, y: y, z: z};
	},

	cartesian2Spherical = function ( point ) {
		var lat = Math.acos(point.z),
			lon = Math.atan2(point.y, point.x);
		return {lat: Math.PI/2-lat, lon: lon};
	},

	matriculate = function ( geoPoint, reverse ) {
		var matrix = reverse ? backMatrix : forwardMatrix,
			x = (geoPoint.x * matrix[0][0]) + (geoPoint.y * matrix[0][1]) + (geoPoint.z * matrix[0][2]),
			y = (geoPoint.x * matrix[1][0]) + (geoPoint.y * matrix[1][1]) + (geoPoint.z * matrix[1][2]),
			z = (geoPoint.x * matrix[2][0]) + (geoPoint.y * matrix[2][1]) + (geoPoint.z * matrix[2][2]);
		return {x: x, y: y, z: z};
	};

	return {
		/**
		 * translate point via rotation frame
		 * @param object geoPoint {lat: lat, lon: lon) in radians
		 * @return object geoPoint {lat: lat, lon: lon) in radians
		 */
		translate: function (geoPoint, reverse) {
			var localGeoPoint, xyz, translated, newpoint;
			if ( reverse == undefined ) {
				reverse = false;
			}
			localGeoPoint = {lat: geoPoint.lat, lon: geoPoint.lon};
			xyz = spherical2Cartesian(geoPoint);
			translated = matriculate(xyz, reverse);
			newpoint = cartesian2Spherical(translated);
			return newpoint;
		}
	};


}
