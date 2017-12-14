const {mix} = require('laravel-mix');

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

mix.js('resources/assets/js/core/core.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css');

/**
 * All apps here
 * */

mix.js('resources/assets/js/apps/shop/goods/goods.js', 'public/js/apps/shop/goods.js')
    .sass('resources/assets/sass/apps/shop/goods.scss', 'public/css/apps/shop/goods.css');

mix.js('resources/assets/js/apps/shop/vendors/vendors.js', 'public/js/apps/shop/vendors.js');

mix.js('resources/assets/js/apps/shop/htmlPages/htmlPages.js', 'public/js/apps/shop/htmlPages.js');