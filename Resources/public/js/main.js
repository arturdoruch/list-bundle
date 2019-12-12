
(function() {

    var bundlesDir = '../../../bundles',
        listDir = bundlesDir + '/arturdoruchlist/js',
        jsDir = bundlesDir + '/arturdoruchjs/js',
        jsVendorDir = bundlesDir + '/arturdoruchjsvendor/js';

    requirejs.config({
        /*paths: {
            jquery: jsVendorDir + '/jquery/jquery.min'
        },*/
        packages: [
            {
                name: 'arturdoruchJs',
                location: jsDir
            },
            {
                name: 'arturdoruchJsVendor',
                location: jsVendorDir
            },
            {
                name: 'arturdoruchList',
                location: listDir
            }
        ]
    });

})();