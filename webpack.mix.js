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
   .sass('resources/sass/app.scss', 'public/css')
   .imagemin('public/uploads/all/**/*', {
       context: 'resources',
       from: 'public/upload/all',
       to: 'public/upload/all',
   });

if (mix.inProduction()) {
    mix.version();
}
