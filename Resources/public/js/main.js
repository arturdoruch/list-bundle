
(function() {

    var bundlesDir = '../../../bundles',
        listBundle = bundlesDir + '/arturdoruchlist/js',
        jsBundle = bundlesDir + '/arturdoruchjs/js',
        jsVendorBundle = bundlesDir + '/arturdoruchjsvendor/js';

    requirejs.config({
        paths: {
            jquery: jsVendorBundle + '/jquery/jquery.min'
        },

        packages: [
            {
                name: 'arturdoruchJs',
                location: jsBundle
            },
            {
                name: 'arturdoruchList',
                location: listBundle
            }
        ]
    });

})();