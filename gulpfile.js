var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

// TODO: Include all other CSS and JS files: JQuery, Bootstrap, Select2, etc...
elixir(function(mix) {

    var bowerLoc = 'bower_components/';
    var resourceLoc = 'public/';

    // Copy CSS Dependencies
    mix.copy(
        bowerLoc + 'CodeMirror/lib/codemirror.css',
        resourceLoc + 'css/codemirror.css'
    ).copy(
        bowerLoc + 'sweetalert/dist/sweetalert.css',
        resourceLoc + 'css/sweetalert.css'
    ).copy(
        bowerLoc + 'datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css',
        resourceLoc + 'css/dataTables.bootstrap.css'
    );

    // Copy JS Dependencies
    mix.copy(
        bowerLoc + 'jquery/dist/jquery.js',
        resourceLoc + 'js/jquery.js'
    ).copy(
        bowerLoc + 'datatables/media/js/jquery.dataTables.js',
        resourceLoc + 'js/dataTables.js'
    ).copy(
        bowerLoc + 'datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.js',
        resourceLoc + 'js/dataTables.bootstrap.js'
    ).copy(
        bowerLoc + 'CodeMirror/lib/codemirror.js',
        resourceLoc + 'js/codemirror.js'
    ).copy(
        bowerLoc + 'datatables-buttons/js',
        resourceLoc + 'js/'
    ).copy(
        bowerLoc + 'CodeMirror/mode/sql/sql.js',
        resourceLoc + 'js/codemirror-sql.js'
    ).copy(
        bowerLoc + 'sweetalert/dist/sweetalert.min.js',
        resourceLoc + 'js/sweetalert.min.js'
    );

    // Compile our SASS file to CSS.
    mix.sass('app.scss');

    // Combine the various CSS into one.
    mix.styles([
        //'vendor.css',
        'codemirror.css',
        'sweetalert.css',
        'dataTables.bootstrap.css',
        'app.css',
    ], null, 'public/css');

    // Compile our SASS file to CSS.
    mix.coffee('app.coffee');

    // Combine the various JS into one.
    mix.scripts([
       //'vendor.js',
        'jquery.js',
        'codemirror.js',
        'codemirror-sql.js',
        'close-modal.js',
        'dataTables.js',
        'sweetalert.min.js',
        'app.js',
    ], null, 'public/js');

    // Enable cache busting versions.
    mix.version(['public/css/all.css', 'public/js/all.js']);

});
