var fs = require('fs');

exports.getDem = function ( fileNameStem ) {
    var filenameBase = '../' + fileNameStem + '/' + fileNameStem + '_dem',
        fd = fs.openSync(filenameBase + '.bil', 'r'),
        proj = fs.readFileSync(filenameBase + '.proj').toString().split('\n');
        blw = fs.readFileSync(filenameBase + '.blw').toString().split('\n'),
        hdr = fs.readFileSync(filenameBase + '.hdr').toString().split('\n');

    return {
        filename: filenameBase,
        filehandle: fd,
        projection: proj[0],
        referenceLat: parseFloat(proj[1]),
        referenceLon: parseFloat(proj[2]),
        scaleFactor: parseFloat(proj[3]),
        rows: parseInt(hdr[2].replace(/[^0-9]/g,'')),
        cols: parseInt(hdr[3].replace(/[^0-9]/g,'')),
        bytes: parseInt(hdr[5].replace(/[^0-9]/g,'') / 8),
        pixelSize: parseInt(blw[0]),
        bounds: {
            left: parseInt(blw[4])/parseInt(blw[0]),
            top: parseInt(-blw[5])/parseInt(blw[0]),
            right: parseInt(blw[4])/parseInt(blw[0]) + parseInt(hdr[3].replace(/[^0-9]/g,'')),
            bottom: -parseInt(blw[5])/parseInt(blw[0]) + parseInt(hdr[2].replace(/[^0-9]/g,''))
        }
    };
};
