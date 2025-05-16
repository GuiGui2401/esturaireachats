mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');

const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
require('laravel-mix-imagemin');

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/jquery-3.6.0.min.js','public/js/jquery-3.6.0.min.js')
    .js('resources/js/popper.min.js','public/js/popper.min.js')
    .js('resources/js/aiz-core.js', 'public/js/aiz-core.min.js')
    .js('resources/js/vendors.js', 'public/js/vendors.min.js')
    .js('resources/js/bootstrap.js', 'public/js/bootstrap.min.js')
    .sass('resources/sass/vendors.scss', 'public/css/vendors.min.css')
    .sass('resources/sass/aiz-core.scss', 'public/css/aiz-core.min.css')
    .sass('resources/sass/custom-style.scss', 'public/css/custom-style.min.css')
    .sass('resources/sass/app.scss', 'public/css')
    .imagemin('public/uploads/all/**/*', {
        context: 'resources',
        from: 'public/upload/all',
        to: 'public/upload/all',
    });

if (mix.inProduction()) {
    mix.version();
}// Activer la version des fichiers pour le cache busting
mix.version();

// Optimisation de la minification JS
mix.options({
    terser: {
        extractComments: false,
    }
});

// Activer la compression gzip
mix.webpackConfig({
    optimization: {
        minimize: true,
    },
    output: {
        chunkFilename: 'js/[name].[chunkhash].js',
    },
});

